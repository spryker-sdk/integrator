<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerSdk\Integrator\Transfer;

use ArrayObject;

class ClassInformationTransfer extends FileInformationTransfer
{
    /**
     * @var string|null
     */
    protected ?string $fullyQualifiedClassName = null;

    /**
     * @var string|null
     */
    protected ?string $className = null;

    protected ?ClassInformationTransfer $parent = null;

    protected ArrayObject $methods;

    /**
     * @param string|null $fullyQualifiedClassName
     *
     * @return $this
     */
    public function setFullyQualifiedClassName(?string $fullyQualifiedClassName)
    {
        $this->fullyQualifiedClassName = $fullyQualifiedClassName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFullyQualifiedClassName(): ?string
    {
        return $this->fullyQualifiedClassName;
    }

    /**
     * @return string
     */
    public function getFullyQualifiedClassNameOrFail(): string
    {
        if ($this->fullyQualifiedClassName === null) {
            $this->throwNullValueException('fullyQualifiedClassName');
        }

        return $this->fullyQualifiedClassName;
    }

    /**
     * @param string|null $className
     *
     * @return $this
     */
    public function setClassName(?string $className)
    {
        $this->className = $className;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClassName(): ?string
    {
        return $this->className;
    }

    /**
     * @param static|null $parent
     *
     * @return $this
     */
    public function setParent(?ClassInformationTransfer $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return \SprykerSdk\Integrator\Transfer\ClassInformationTransfer|null
     */
    public function getParent(): ?ClassInformationTransfer
    {
        return $this->parent;
    }

    /**
     * @param \ArrayObject $methods
     *
     * @return $this
     */
    public function setMethods(ArrayObject $methods)
    {
        $this->methods = $methods;

        return $this;
    }

    /**
     * @return \ArrayObject
     */
    public function getMethods(): ArrayObject
    {
        return $this->methods;
    }

    /**
     * @param \SprykerSdk\Integrator\Transfer\MethodInformationTransfer $method
     *
     * @return $this
     */
    public function addMethod(MethodInformationTransfer $method)
    {
        $this->methods[] = $method;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getParentClassNames(): array
    {
        if (!$this->getParent() || !$this->getParent()->getFullyQualifiedClassName()) {
            return [];
        }
        $parents = [ltrim($this->getParent()->getFullyQualifiedClassName(), '\\')];

        return array_merge($parents, $this->getParent()->getParentClassNames());
    }
}
