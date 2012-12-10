<?php
namespace Xazure\Css\Element;

/**
 * Represents an at-rule which contains a block.
 */
class AtRuleBlock extends ElementGroup implements NameInterface, ValueInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $value;

    /**
     * @param string $name
     * @param string $value
     * @param array $properties
     */
    public function __construct($name = "", $value = "")
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    public function __toString()
    {
        return '@' . $this->name . ' ' . $this->value . " {\n" . implode("\n", $this->elements) . "\n}";
    }
}