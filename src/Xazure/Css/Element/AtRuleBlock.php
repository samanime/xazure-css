<?php
/**
 * This file is part of the XazureCSS package.
 *
 * (c) Christian Snodgrass <csnodgrass3147+github@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Xazure\Css\Element;

/**
 * Represents an at-rule which contains a block.
 */
class AtRuleBlock extends ElementGroup implements NameInterface, ValueInterface
{
    /**
     * The name of the at-rule block.
     *
     * @var string
     */
    protected $name;

    /**
     * The value of the at-rule block.
     *
     * @var string
     */
    protected $value;

    /**
     * Constructor.
     *
     * @param string $name
     * @param string $value
     */
    public function __construct($name = "", $value = "")
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the value.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the name.
     *
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Set the value.
     *
     * @param $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Converts AtRuleBlock to a string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return '@' . $this->name . ' ' . $this->value . " {\n" . implode("\n", $this->elements) . "\n}";
    }
}