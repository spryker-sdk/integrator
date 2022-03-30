<?php

namespace SprykerSdk\Integrator\ManifestGenerator\ValueExtractor;

class ExtractedValueObject
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var bool
     */
    protected bool $isLiteral = false;

    /**
     * @param mixed $value
     * @param bool $isLiteral
     */
    public function __construct($value, bool $isLiteral = false)
    {
        $this->value = $value;
        $this->isLiteral = $isLiteral;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isLiteral(): bool
    {
        return $this->isLiteral;
    }
}
