<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\ManifestStrategy;

use DOMDocument;
use InvalidArgumentException;
use SimpleXMLElement;
use SprykerSdk\Integrator\Dependency\Console\InputOutputInterface;
use SprykerSdk\Integrator\IntegratorConfig;
use Symfony\Component\Filesystem\Filesystem;

abstract class AbstractWireXmlManifestStrategy implements ManifestStrategyInterface
{
    /**
     * @var string
     */
    protected const NAME_ATTRIBUTE = 'name';

    /**
     * @var string
     */
    protected const MANIFEST_KEY_TARGET = 'target';

    /**
     * @var string
     */
    protected const MANIFEST_KEY_DATA = 'data';

    /**
     * @var string
     */
    protected const MANIFEST_KEY_NODE_NAME = 'name';

    /**
     * @var string
     */
    protected const MANIFEST_KEY_NODE_ATTRIBUTES = 'attributes';

    /**
     * @var string
     */
    protected const MANIFEST_KEY_SUB_NODES = 'sub-nodes';

    /**
     * @var \SprykerSdk\Integrator\IntegratorConfig
     */
    private IntegratorConfig $config;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private Filesystem $filesystem;

    /**
     * @param \SprykerSdk\Integrator\IntegratorConfig $config
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     */
    public function __construct(IntegratorConfig $config, Filesystem $filesystem)
    {
        $this->config = $config;
        $this->filesystem = $filesystem;
    }

    /**
     * @param array<mixed> $manifest
     * @param string $moduleName
     * @param \SprykerSdk\Integrator\Dependency\Console\InputOutputInterface $inputOutput
     * @param bool $isDry
     *
     * @return bool
     */
    public function apply(array $manifest, string $moduleName, InputOutputInterface $inputOutput, bool $isDry): bool
    {
        foreach ($this->config->getProjectNamespaces() as $namespace) {
            $targetFilePath = $this->getProjectTargetXmlFilePath($namespace, $manifest[static::MANIFEST_KEY_TARGET]);
            $rootNode = $this->getRootNode($targetFilePath, $manifest);
            $this->addMissedElementsToProjectXmlNode($rootNode, $manifest[static::MANIFEST_KEY_DATA], $rootNode);

            if (!$isDry) {
                $this->saveNode($rootNode, $targetFilePath);
            }

            $inputOutput->writeln(sprintf('Xml manifest is applied for "%s"', $targetFilePath));
        }

        return true;
    }

    /**
     * @param string $targetFilePath
     * @param array $manifest
     *
     * @return \SimpleXMLElement
     */
    public function getRootNode(string $targetFilePath, array $manifest): SimpleXMLElement
    {
        $xmlData = $this->readFile($targetFilePath);

        if ($xmlData !== null) {
            return $this->loadXmlString($xmlData);
        }

        $rootNode = $this->loadXmlString($this->getNewFileTemplate());

        $manifestRootNode = $manifest[static::MANIFEST_KEY_DATA];

        if ($rootNode->getName() !== $manifestRootNode[static::MANIFEST_KEY_NODE_NAME]) {
            return $rootNode;
        }

        foreach ($manifestRootNode[static::MANIFEST_KEY_NODE_ATTRIBUTES] ?? [] as $attribute => $value) {
            $rootNode->addAttribute($attribute, $value);
        }

        return $rootNode;
    }

    /**
     * @param \SimpleXMLElement|null $projectNode
     * @param array $manifestNode
     * @param \SimpleXMLElement $parentNode
     *
     * @return void
     */
    public function addMissedElementsToProjectXmlNode(?SimpleXMLElement $projectNode, array $manifestNode, SimpleXMLElement $parentNode): void
    {
        if ($projectNode === null) {
            $projectNode = $parentNode->addChild($manifestNode[static::MANIFEST_KEY_NODE_NAME]);

            foreach ($manifestNode[static::MANIFEST_KEY_NODE_ATTRIBUTES] ?? [] as $attribute => $value) {
                $projectNode->addAttribute($attribute, $value);
            }
        }

        foreach ($manifestNode[static::MANIFEST_KEY_SUB_NODES] ?? [] as $manifestChildNode) {
            $childProjectNode = $this->getProjectChildNodeByManifestNode($manifestChildNode, $projectNode);

            $this->addMissedElementsToProjectXmlNode($childProjectNode, $manifestChildNode, $projectNode);
        }
    }

    /**
     * @param array $manifestNode
     * @param \SimpleXMLElement $projectNode
     *
     * @return \SimpleXMLElement|null
     */
    protected function getProjectChildNodeByManifestNode(array $manifestNode, SimpleXMLElement $projectNode): ?SimpleXMLElement
    {
        foreach ($projectNode->children() as $childNode) {
            if (
                $childNode->getName() === $manifestNode[static::MANIFEST_KEY_NODE_NAME]
                && $this->getAttributeByName($childNode, static::NAME_ATTRIBUTE) === $manifestNode[static::MANIFEST_KEY_NODE_ATTRIBUTES][static::NAME_ATTRIBUTE]
            ) {
                return $childNode;
            }
        }

        return null;
    }

    /**
     * @param string $xmlData
     *
     * @throws \InvalidArgumentException
     *
     * @return \SimpleXMLElement
     */
    protected function loadXmlString(string $xmlData): SimpleXMLElement
    {
        $xmlElement = simplexml_load_string($xmlData);

        if ($xmlElement === false) {
            throw new InvalidArgumentException(sprintf('Unable to parse xml "%s"', $xmlData));
        }

        return $xmlElement;
    }

    /**
     * @param \SimpleXMLElement $projectNode
     * @param string $attribute
     *
     * @return string|null
     */
    protected function getAttributeByName(SimpleXMLElement $projectNode, string $attribute): ?string
    {
        $projectNodeAttributes = $projectNode->attributes();

        if ($projectNodeAttributes === null) {
            return null;
        }

        foreach ($projectNodeAttributes as $nodeAttribute => $value) {
            if ($nodeAttribute === $attribute) {
                return (string)$value;
            }
        }

        return null;
    }

    /**
     * @param string $filePath
     *
     * @throws \InvalidArgumentException
     *
     * @return string|null
     */
    protected function readFile(string $filePath): ?string
    {
        if (!is_file($filePath)) {
            return null;
        }

        $content = file_get_contents($filePath);

        if ($content === false) {
            throw new InvalidArgumentException(sprintf('Unable to read file "%s"', $filePath));
        }

        return $content;
    }

    /**
     * @param \SimpleXMLElement $node
     * @param string $filePath
     *
     * @return void
     */
    protected function saveNode(SimpleXMLElement $node, string $filePath): void
    {
        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML((string)$node->asXML());

        $content = (string)$dom->saveXML();
        // make double spaces indent
        $content = (string)preg_replace('/^( *)/m', '$1$1', $content);

        $this->filesystem->dumpFile($filePath, $content);
    }

    /**
     * @param string $projectNamespace
     * @param string $target
     *
     * @return string
     */
    protected function getProjectTargetXmlFilePath(string $projectNamespace, string $target): string
    {
        return $this->config->getProjectRootDirectory()
            . 'src'
            . DIRECTORY_SEPARATOR
            . $projectNamespace
            . DIRECTORY_SEPARATOR
            . ltrim($target, DIRECTORY_SEPARATOR);
    }

    /**
     * @return string
     */
    abstract protected function getNewFileTemplate(): string;
}
