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
 * Represents a single CSS property name/value pair.
 */
class Property implements ElementInterface, NameInterface, ValueInterface
{
    /**
     * The name of the CSS property.
     *
     * @var string
     */
    protected $name;

    /**
     * The value of the CSS property.
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
    public function __construct($name = '', $value = '') {
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
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Set the value.
     *
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Converts the Property to a string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name . ': ' . $this->value . ';';
    }
}