<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Transfer;

class FileInformationTransfer extends AbstractTransfer
{
    /**
     * @var string|null $filePath
     */
    protected ?string $filePath = null;

    /**
     * @var string|null $content
     */
    protected ?string $content = null;

    /**
     * @var array $tokenTree
     */
    protected array $tokenTree = [];

    /**
     * @var array $originalTokenTree
     */
    protected array $originalTokenTree = [];

    /**
     * @var array $tokens
     */
    protected array $tokens = [];

    /**
     * @param string|null $filePath
     *
     * @return $this
     */
    public function setFilePath(?string $filePath)
    {
        $this->filePath = $filePath;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    /**
     * @return string
     */
    public function getFilePathOrFail(): string
    {
        if ($this->filePath === null) {
            $this->throwNullValueException('filePath');
        }

        return $this->filePath;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string|null $content
     *
     * @return void
     */
    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    /**
     * @param array|null $tokenTree
     *
     * @return $this
     */
    public function setTokenTree(?array $tokenTree = null)
    {
        if ($tokenTree === null) {
            $tokenTree = [];
        }

        $this->tokenTree = $tokenTree;

        return $this;
    }

    /**
     * @return array
     */
    public function getTokenTree(): array
    {
        return $this->tokenTree;
    }

    /**
     * @param array|null $originalTokenTree
     *
     * @return $this
     */
    public function setOriginalTokenTree(?array $originalTokenTree = null)
    {
        if ($originalTokenTree === null) {
            $originalTokenTree = [];
        }

        $this->originalTokenTree = $originalTokenTree;

        return $this;
    }

    /**
     * @return array
     */
    public function getOriginalTokenTree(): array
    {
        return $this->originalTokenTree;
    }

    /**
     * @param array|null $tokens
     *
     * @return $this
     */
    public function setTokens(?array $tokens = null)
    {
        if ($tokens === null) {
            $tokens = [];
        }

        $this->tokens = $tokens;

        return $this;
    }

    /**
     * @return array
     */
    public function getTokens(): array
    {
        return $this->tokens;
    }

    /**
     * @param mixed $token
     *
     * @return $this
     */
    public function addToken($token)
    {
        $this->tokens[] = $token;

        return $this;
    }
}
