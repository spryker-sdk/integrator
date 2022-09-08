<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\ModuleFinder\Package\PackageFinder;

use Laminas\Filter\FilterChain;
use Laminas\Filter\Word\DashToCamelCase;
use SprykerSdk\Integrator\IntegratorConfig;
use SprykerSdk\Integrator\Transfer\PackageTransfer;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class PackageFinder implements PackageFinderInterface
{
    /**
     * @var \SprykerSdk\Integrator\IntegratorConfig
     */
    protected $config;

    /**
     * @param \SprykerSdk\Integrator\IntegratorConfig $config
     */
    public function __construct(IntegratorConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @return array<\SprykerSdk\Integrator\Transfer\PackageTransfer>
     */
    public function getPackages(): array
    {
        $packageTransferCollection = [];

        foreach ($this->getPackageFinder() as $directoryInfo) {
            if (in_array($directoryInfo->getFilename(), $this->config->getInternalPackagePathFragments())) {
                continue;
            }
            $packageTransfer = $this->getPackageTransfer($directoryInfo);

            if ($this->isModule($packageTransfer)) {
                continue;
            }

            $packageCollectionKey = sprintf('%s.%s', $packageTransfer->getOrganizationName(), $packageTransfer->getPackageName());
            $packageTransferCollection[$packageCollectionKey] = $packageTransfer;
        }

        return $packageTransferCollection;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function camelCase(string $value): string
    {
        $filterChain = new FilterChain();
        $filterChain->attach(new DashToCamelCase());

        return ucfirst($filterChain->filter($value));
    }

    /**
     * @return \Symfony\Component\Finder\Finder<\Symfony\Component\Finder\SplFileInfo>
     */
    protected function getPackageFinder(): Finder
    {
        return (new Finder())
            ->directories()
            ->depth('== 0')
            ->in($this->config->getVendorDirectory() . 'spryker/');
    }

    /**
     * @param \Symfony\Component\Finder\SplFileInfo $directoryInfo
     *
     * @return \SprykerSdk\Integrator\Transfer\PackageTransfer
     */
    protected function getPackageTransfer(SplFileInfo $directoryInfo): PackageTransfer
    {
        $composerJsonAsArray = $this->getComposerJsonAsArray($directoryInfo->getPathname());
        $composerName = $composerJsonAsArray['name'];
        [$organizationNameDashed, $packageNameDashed] = explode('/', $composerName);

        $organizationName = $this->camelCase($organizationNameDashed);
        $packageName = $this->camelCase($packageNameDashed);

        $packageTransfer = new PackageTransfer();
        $packageTransfer
            ->setComposerName($composerName)
            ->setOrganizationName($organizationName)
            ->setOrganizationNameDashed($organizationNameDashed)
            ->setPackageName($packageName)
            ->setPackageNameDashed($packageNameDashed)
            ->setPath($directoryInfo->getPathname());

        return $packageTransfer;
    }

    /**
     * @param string $path
     *
     * @return array
     */
    protected function getComposerJsonAsArray(string $path): array
    {
        $pathToComposerJson = sprintf('%s/composer.json', $path);
        $fileContent = file_get_contents($pathToComposerJson);
        if (!$fileContent) {
            return [];
        }

        return json_decode($fileContent, true);
    }

    /**
     * Packages which are standalone, can also be normal modules. This can be detected by the composer.json description
     * which contains `module` at the end of the description.
     *
     * @param \SprykerSdk\Integrator\Transfer\PackageTransfer $packageTransfer
     *
     * @return bool
     */
    protected function isModule(PackageTransfer $packageTransfer): bool
    {
        $composerJsonAsArray = $this->getComposerJsonAsArray($packageTransfer->getPathOrFail());
        $description = $composerJsonAsArray['description'];

        return (bool)preg_match('/\smodule$/', $description);
    }
}
