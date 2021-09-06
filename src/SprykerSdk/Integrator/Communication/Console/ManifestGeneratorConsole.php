<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace SprykerSdk\Integrator\Communication\Console;

use Shared\Transfer\OrganizationTransfer;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Stmt\Return_;
use RuntimeException;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\DiffOnlyOutputBuilder;
use Spryker\Zed\Kernel\Communication\Console\Console;
use SprykerSdk\Integrator\Business\Composer\ComposerLockReader;
use SprykerSdk\Integrator\Business\Helper\ClassHelper;
use SprykerSdk\Integrator\Business\IntegratorBusinessFactory;
use SprykerSdk\Integrator\Business\Manifest\ManifestWriter;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @method \SprykerSdk\Integrator\Business\IntegratorFacade getFacade()
 * @method \SprykerSdk\Integrator\Communication\IntegratorCommunicationFactory getFactory()
 */
class ManifestGeneratorConsole extends Console
{
    protected const FLAG_DRY = 'dry';

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('integrator:manifest:generate')
            ->addOption(static::FLAG_DRY)
            ->setDescription('');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $isDry = $this->getDryOptionValue($input);
        $projectModuleList = [];
        foreach ($this->getFactory()->getConfig()->getProjectNamespaces() as $projectNamespace) {
            $projectModuleList = array_merge($projectModuleList, $this->getFactory()->getModuleFinderFacade()->getProjectModules(
                (new \Shared\Transfer\ModuleFilterTransfer())
                    ->setOrganization(
                        (new OrganizationTransfer())->setName($projectNamespace)->setNameDashed($projectNamespace)
                    )
            ));
        }

        $coreModuleList = $this->getFactory()->getModuleFinderFacade()->getModules();
        $indexedCoreModuleList = $this->indexModulesByName($coreModuleList);

        $configs = [];
        $dependencyProviders = [];
        foreach ($projectModuleList as $moduleTransfer) {
            foreach ($moduleTransfer->getApplications() as $applicationTransfer) {
                $configPath = $moduleTransfer->getPath()
                    . 'src'
                    . DIRECTORY_SEPARATOR
                    . $moduleTransfer->getOrganization()->getName()
                    . DIRECTORY_SEPARATOR
                    . $applicationTransfer->getName()
                    . DIRECTORY_SEPARATOR
                    . $moduleTransfer->getName()
                    . DIRECTORY_SEPARATOR
                    . $moduleTransfer->getName()
                    . 'Config.php';

                if (file_exists($configPath)) {
                    $configs[] = '\\'
                        . $moduleTransfer->getOrganization()->getName()
                        . '\\'
                        . $applicationTransfer->getName()
                        . '\\'
                        . $moduleTransfer->getName()
                        . '\\'
                        . $moduleTransfer->getName()
                        . 'Config';
                }

                $dependencyProviderPath = $moduleTransfer->getPath()
                    . 'src'
                    . DIRECTORY_SEPARATOR
                    . $moduleTransfer->getOrganization()->getName()
                    . DIRECTORY_SEPARATOR
                    . $applicationTransfer->getName()
                    . DIRECTORY_SEPARATOR
                    . $moduleTransfer->getName()
                    . DIRECTORY_SEPARATOR
                    . $moduleTransfer->getName()
                    . 'DependencyProvider.php';

                if (file_exists($dependencyProviderPath)) {
                    $dependencyProviders[] = '\\'
                        . $moduleTransfer->getOrganization()->getName()
                        . '\\'
                        . $applicationTransfer->getName()
                        . '\\'
                        . $moduleTransfer->getName()
                        . '\\'
                        . $moduleTransfer->getName()
                        . 'DependencyProvider';
                }
            }
        }

        $manifests = [];
        $unsupportable = [];

        $classLoader = $this->getBusinessFactory()->createClassLoader();
        $classNodeFinder = $this->getBusinessFactory()->createClassNodeFinder();
        $classHelper = new ClassHelper();

        foreach ($configs as $config) {
            $moduleName = $classHelper->getModuleName($config);
            $coreModuleName = $indexedCoreModuleList[$moduleName] ?? null;
            if (!$coreModuleName) {
                continue;
            }
            $organisationName = $coreModuleName->getOrganization()->getName();
            $manifestModuleKey = $organisationName . '.' . $moduleName;
            $classInformationTransfer = $classLoader->loadClass($config);
            $classNode = $classNodeFinder->findClassNode($classInformationTransfer);
            if (!$classNode) {
                continue;
            }

            $targetClass = $config;
            if ($classInformationTransfer->getParent()) {
                $targetClass = $classInformationTransfer->getParent()->getFullyQualifiedClassName();
            }

            $constants = $classNode->getConstants();
            if ($constants) {
                foreach ($constants as $constant) {
                    foreach ($constant->consts as $constantBody) {
                        $manifests[$manifestModuleKey]['config'][] = [
                            'target' => $targetClass . '::' . $constantBody->name,
                            'value' => $this->exprToValue($constantBody->value),
                        ];
                    }
                }
            }

            $methods = $classNode->getMethods();
            if ($methods) {
                foreach ($methods as $method) {
                    if (!($method->stmts[0] instanceof Return_)) {
                        $unsupportable[] = $config . '::' . $method->name;
                        continue;
                    }
                    try {
                        $manifests[$manifestModuleKey]['config'][] = [
                            'target' => $targetClass . '::' . $method->name,
                            'value' => $this->exprToValue($method->stmts[0]->expr),
                        ];
                    } catch (RuntimeException $exception) {
                        $unsupportable[] = $config . '::' . $method->name;
                        continue;
                    }
                }
            }
        }

        foreach ($dependencyProviders as $dependencyProvider) {
            $moduleName = $classHelper->getModuleName($dependencyProvider);
            $coreModuleName = $indexedCoreModuleList[$moduleName] ?? null;
            if (!$coreModuleName) {
                continue;
            }
            $organisationName = $coreModuleName->getOrganization()->getName();
            $manifestModuleKey = $organisationName . '.' . $moduleName;
            $classInformationTransfer = $classLoader->loadClass($dependencyProvider);
            $classNode = $classNodeFinder->findClassNode($classInformationTransfer);
            if (!$classNode) {
                continue;
            }

            $targetClass = $dependencyProvider;
            if ($classInformationTransfer->getParent()) {
                $targetClass = $classInformationTransfer->getParent()->getFullyQualifiedClassName();
            }

            $methods = $classNode->getMethods();
            if ($methods) {
                foreach ($methods as $method) {
                    $relations = [];
                    if ($method->name->name === 'getResourceRelationshipPlugins') {
                        try {
                            $relations = $this->extractRelationList($method->stmts);
                        } catch (RuntimeException $exception) {
                            $relations = [];
                        }
                    }

                    if ($relations) {
                        $manifests[$manifestModuleKey]['relations'] = array_merge(
                            $manifests[$manifestModuleKey]['relations'] ?? [],
                            $relations
                        );
                        continue;
                    }

                    if (!($method->stmts[0] instanceof Return_)) {
                        $unsupportable[] = $dependencyProvider . '::' . $method->name;
                        continue;
                    }

                    try {
                        $plugins = $this->extractPluginList($method->stmts[0]->expr);
                    } catch (RuntimeException $exception) {
                        $plugins = [];
                    }
                    $widgets = [];
                    if ($method->name->name === 'getGlobalWidgets') {
                        try {
                            $widgets = $this->extractWidgetList($method->stmts[0]->expr);
                        } catch (RuntimeException $exception) {
                            $widgets = [];
                        }
                    }

                    if ($plugins) {
                        $manifests[$manifestModuleKey]['plugins'][] = [
                            'target' => $targetClass . '::' . $method->name,
                            'value' => $plugins,
                        ];
                        continue;
                    }

                    if ($widgets) {
                        $manifests[$manifestModuleKey]['widgets'][] = [
                            'value' => $widgets,
                        ];
                        continue;
                    }

                    $unsupportable[] = $dependencyProvider . '::' . $method->name;
                }
            }
        }

        $existingManifests = $this->getBusinessFactory()->createManifestReader()->readManifests($coreModuleList);
        $originalManifests = $existingManifests;

        $aggregatedCurrentManifestData = $this->aggregateManifestData($existingManifests);
        $projectOrgs = $this->getFactory()->getConfig()->getProjectNamespaces();

        foreach ($manifests as $manifestModuleKey => $moduleManifest) {
            foreach ($moduleManifest['plugins'] ?? [] as $pluginManifest) {
                $target = $pluginManifest['target'];
                foreach ($pluginManifest['value'] as $key => $source) {
                    $organisationName = $classHelper->getOrganisationName($source);
                    if (in_array($organisationName, $projectOrgs, true)) {
                        continue;
                    }
                    $moduleName = $classHelper->getModuleName($source);

                    if (isset($aggregatedCurrentManifestData['wire-plugin'][$target]) && in_array($source, $aggregatedCurrentManifestData['wire-plugin'][$target], true)) {
                        continue;
                    }

                    $data = [
                        'target' => $target,
                        'source' => $source,
                    ];

                    if (is_string($key)) {
                        $data['key'] = $key;
                    }
                    $existingManifests[$organisationName . '.' . $moduleName]['wire-plugin'][] = $data;
                }
            }

            foreach ($moduleManifest['widgets'] ?? [] as $widgetManifest) {
                foreach ($widgetManifest['value'] as $source) {
                    $organisationName = $classHelper->getOrganisationName($source);
                    if (in_array($organisationName, $projectOrgs, true)) {
                        continue;
                    }
                    $moduleName = $classHelper->getModuleName($source);

                    if (isset($aggregatedCurrentManifestData['wire-widget']) && in_array($source, $aggregatedCurrentManifestData['wire-widget'], true)) {
                        continue;
                    }

                    $existingManifests[$organisationName . '.' . $moduleName]['wire-widget'][] = [
                        'source' => $source,
                    ];
                }
            }

            foreach ($moduleManifest['config'] ?? [] as $configManifest) {
                $target = $configManifest['target'];
                $value = $configManifest['value'];

                if (!is_array($value)
                    && isset($aggregatedCurrentManifestData['configure-module'][$target])
                    && in_array($value, $aggregatedCurrentManifestData['configure-module'][$target], true)
                ) {
                    continue;
                }
                if (is_array($value)
                    && isset($aggregatedCurrentManifestData['configure-module'][$target])
                    && !$this->arrayDiffAssocRecursive($value, $aggregatedCurrentManifestData['configure-module'][$target])
                ) {
                    continue;
                }

                $existingManifests[$manifestModuleKey]['configure-module'][] = [
                    'target' => $target,
                    (is_array($value)) ? 'value' : 'default' => $value,
                ];
            }

            foreach ($moduleManifest['relations'] ?? [] as $key => $relations) {
                foreach ($relations as $relation) {
                    $organisationName = $classHelper->getOrganisationName($relation);
                    if (in_array($organisationName, $projectOrgs, true)) {
                        continue;
                    }

                    if (isset($aggregatedCurrentManifestData['wire-glue-relationship'][$key]) && in_array($relation, $aggregatedCurrentManifestData['wire-glue-relationship'][$key], true)) {
                        continue;
                    }
                    $moduleName = $classHelper->getModuleName($relation);

                    $existingManifests[$organisationName . '.' . $moduleName]['wire-glue-relationship'][]['source'] = [
                        $key => $relation,
                    ];
                }
            }
        }

        if (isset($unsupportable)) {
            $io->writeln(str_repeat('-', 15));
            $io->writeln('Unsupported code');
            foreach ($unsupportable as $line) {
                $io->writeln($line);
            }
            $io->writeln(str_repeat('-', 15));
        }

        if (!$isDry) {
            (new ManifestWriter($this->createComposerLockReader()))
                ->storeManifest($indexedCoreModuleList, $existingManifests);
        } else {
            $builder = new DiffOnlyOutputBuilder(
                "--- Original\n+++ New\n"
            );
            $differ = new Differ($builder);
            foreach ($existingManifests as $module => $manifest) {
                $source = $originalManifests[$module] ?? [];
                $diff = $differ->diff(
                    json_encode($source, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT),
                    json_encode($manifest, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
                );
                if ($diff === "--- Original\n+++ New\n") {
                    continue;
                }

                $io->writeln($module);
                $io->writeln($diff);
            }
        }

        return 0;
    }

    /**
     * @return \SprykerSdk\Integrator\Business\IntegratorBusinessFactory
     */
    protected function getBusinessFactory(): IntegratorBusinessFactory
    {
        return new IntegratorBusinessFactory();
    }

    /**
     * @param \Shared\Transfer\ModuleTransfer[] $moduleTransfers
     *
     * @return \Shared\Transfer\ModuleTransfer[]
     */
    protected function indexModulesByName(array $moduleTransfers): array
    {
        $indexedModuleTransfer = [];
        foreach ($moduleTransfers as $moduleTransfer) {
            if (isset($indexedModuleTransfer[$moduleTransfer->getName()])) {
                dump(sprintf(
                    'Module Name must be unique violation: %s.%s and %s.%s',
                    $moduleTransfer->getOrganization()->getName(),
                    $moduleTransfer->getName(),
                    $indexedModuleTransfer[$moduleTransfer->getName()]->getOrganization()->getName(),
                    $indexedModuleTransfer[$moduleTransfer->getName()]->getName()
                ));
            }

            $indexedModuleTransfer[$moduleTransfer->getName()] = $moduleTransfer;
        }

        return $indexedModuleTransfer;
    }

    /**
     * @param \PhpParser\Node\Expr $expr
     *
     * @return int|bool|float|string|array
     */
    protected function exprToValue(\PhpParser\Node\Expr $expr)
    {
        if ($expr instanceof \PhpParser\Node\Expr\ConstFetch) {
            return $expr->name->toString();
        } elseif ($expr instanceof \PhpParser\Node\Expr\ClassConstFetch) {
            return (strpos($expr->class->toString(), '\\') ? '\\' : '') .  $expr->class->toString() . "::" . $expr->name->toString();
        } elseif ($expr instanceof \PhpParser\Node\Scalar\LNumber) {
            return $expr->value;
        } elseif ($expr instanceof \PhpParser\Node\Scalar\DNumber) {
            return $expr->value;
        } elseif ($expr instanceof \PhpParser\Node\Scalar\String_) {
            return $expr->value;
        } elseif ($expr instanceof \PhpParser\Node\Expr\BinaryOp\Concat) {
            return $this->exprToValue($expr->left) . $this->exprToValue($expr->right);
        } elseif ($expr instanceof \PhpParser\Node\Expr\Array_) {
            $array = [];
            foreach ($expr->items as $item) {
                if ($item->key && $item->key instanceof \PhpParser\Node\Scalar\String_) {
                    $array[$item->key->value] = $this->exprToValue($item->value);
                } else {
                    $array[] = $this->exprToValue($item->value);
                }
            }
            return $array;
        } elseif ($expr instanceof \PhpParser\Node\Expr\FuncCall) {
            foreach ($expr->args as $arg) {
                if ($arg->value instanceof \PhpParser\Node\Expr\Array_) {
                    return $this->exprToValue($arg->value);
                }
            }
        }

        throw new RuntimeException(get_class($expr) . ' is not supported');
    }

    /**
     * @param \PhpParser\Node\Expr $expr
     *
     * @return string[]
     */
    protected function extractPluginList(\PhpParser\Node\Expr $expr): array
    {
        if ($expr instanceof \PhpParser\Node\Expr\Array_) {
            $array = [];
            foreach ($expr->items as $item) {
                if ($item->value instanceof New_) {
                    if ($item->key) {
                        $array[$this->exprToValue($item->key)] = '\\' . $item->value->class->toString();
                        continue;
                    }
                    $array[] = '\\' . $item->value->class->toString();
                }
            }

            return $array;
        }

        if ($expr instanceof \PhpParser\Node\Expr\New_) {
            return ['\\' . $expr->class->toString()];
        }

        if ($expr instanceof \PhpParser\Node\Expr\FuncCall) {
            foreach ($expr->args as $arg) {
                if ($arg->value instanceof \PhpParser\Node\Expr\Array_) {
                    return $this->extractPluginList($arg->value);
                }
            }
        }

        throw new RuntimeException(get_class($expr) . ' is not supported');
    }

    /**
     * @param \PhpParser\Node\Expr[] $exprs
     *
     * @return string[]
     */
    protected function extractRelationList(array $exprs): array
    {
        $data = [];
        foreach ($exprs as $expr) {
            if (!($expr instanceof \PhpParser\Node\Stmt\Expression) || !($expr->expr instanceof \PhpParser\Node\Expr\MethodCall) || $expr->expr->name->name !== 'addRelationship') {
                continue;
            }

            $data[$this->exprToValue($expr->expr->args[0]->value)][] = '\\' . $expr->expr->args[1]->value->class->toString();
        }

        return $data;
    }

    /**
     * @param \PhpParser\Node\Expr $expr
     *
     * @return string[]
     */
    protected function extractWidgetList(\PhpParser\Node\Expr $expr): array
    {
        if ($expr instanceof \PhpParser\Node\Expr\Array_) {
            $array = [];
            foreach ($expr->items as $item) {
                if ($item->key and $item->key instanceof \PhpParser\Node\Scalar\String_) {
                    throw new RuntimeException('Associative plugin array is not supported');
                }

                if ($item->value instanceof \PhpParser\Node\Expr\ClassConstFetch && $item->value->name->name === 'class') {
                    $array[] = '\\' . $item->value->class->toString();
                }
            }

            return $array;
        }

        if ($expr instanceof \PhpParser\Node\Expr\New_) {
            return [];
        }

        if ($expr instanceof \PhpParser\Node\Expr\FuncCall) {
            foreach ($expr->args as $arg) {
                if ($arg->value instanceof \PhpParser\Node\Expr\Array_) {
                    return $this->extractWidgetList($arg->value);
                }
            }
        }

        throw new RuntimeException(get_class($expr) . ' is not supported');
    }

    /**
     * @param array $existingManifests
     *
     * @return array
     */
    protected function aggregateManifestData(array $existingManifests): array
    {
        $data = [];
        foreach ($existingManifests as $existingManifest) {
            foreach ($existingManifest['wire-plugin'] ?? [] as $manifest) {
                if (isset($manifest['key'])) {
                    $data['wire-plugin'][$manifest['target']][$manifest['key']] = $manifest['source'];
                    continue;
                }
                $data['wire-plugin'][$manifest['target']][] = $manifest['source'];
            }
            foreach ($existingManifest['unwire-plugin'] ?? [] as $manifest) {
                $data['unwire-plugin'][$manifest['target']][] = $manifest['source'];
            }
            foreach ($existingManifest['wire-widget'] ?? [] as $manifest) {
                $data['wire-widget'][] = $manifest['source'];
            }
            foreach ($existingManifest['unwire-widget'] ?? [] as $manifest) {
                $data['unwire-widget'][] = $manifest['source'];
            }
            foreach ($existingManifest['configure-module'] ?? [] as $manifest) {
                $value = $manifest['value'] ?? $manifest['default'] ?? null;

                if (is_array($value)) {
                    $data['configure-module'][$manifest['target']] = array_merge(($data['configure-module'][$manifest['target']] ?? []), $value);
                    continue;
                }

                if (is_null($value)) {
                    $data['configure-module'][$manifest['target']] = $data['configure-module'][$manifest['target']] ?? [];
                }

                $data['configure-module'][$manifest['target']][] = $value;
            }
            foreach ($existingManifest['wire-glue-relationship'] ?? [] as $manifest) {
                foreach ($manifest['source'] as $key => $relation) {
                    $data['wire-glue-relationship'][$key][] = $relation;
                }
            }
            foreach ($existingManifest['unwire-glue-relationship'] ?? [] as $manifest) {
                foreach ($manifest['source'] as $key => $relation) {
                    $data['unwire-glue-relationship'][$key][] = $relation;
                }
            }
        }

        return $data;
    }

    /**
     * @param array $array1
     * @param array $array2
     *
     * @return array
     */
    protected function arrayDiffAssocRecursive(array $array1, array $array2): array
    {
        $difference = [];
        foreach ($array1 as $key => $value) {
            if (is_array($value)) {
                if (!isset($array2[$key]) || !is_array($array2[$key])) {
                    $difference[$key] = $value;
                } else {
                    $arrayDiffAssocRecursive = $this->arrayDiffAssocRecursive($value, $array2[$key]);
                    if (!empty($arrayDiffAssocRecursive)) {
                        $difference[$key] = $arrayDiffAssocRecursive;
                    }
                }
            } elseif (!array_key_exists($key, $array2) || $array2[$key] !== $value) {
                $difference[$key] = $value;
            }
        }

        return $difference;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return bool
     */
    protected function getDryOptionValue(InputInterface $input): bool
    {
        return (bool)$input->getOption(static::FLAG_DRY);
    }

    /**
     * @return \SprykerSdk\Integrator\Business\Composer\ComposerLockReader
     */
    protected function createComposerLockReader(): ComposerLockReader
    {
        return new ComposerLockReader($this->getFactory()->getConfig());
    }
}
