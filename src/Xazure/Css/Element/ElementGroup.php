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
 * Contains a collection of other ElementInterfaces.
 *
 * This is so we don't need an array to hold the base elements, but this, which is an
 * instance of ElementInterface.
 */
class ElementGroup implements ElementInterface
{
    /**
     * An array of ElementInterfaces.
     *
     * @var array
     */
    protected $elements;

    /**
     * Constructor.
     *
     * @param array $elements An array of ElementInterfaces.
     */
    public function __construct(array $elements = array())
    {
        $this->elements = $elements;
    }

    /**
     * Get the elements.
     *
     * @return array An array of ElementInterfaces.
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * Set the elements.
     *
     * @param array $elements An array of ElementInterfaces.
     */
    public function setElements(array $elements)
    {
        $this->elements = $elements;
    }

    /**
     * Adds an element.
     *
     * @param ElementInterface $element
     */
    public function addElement(ElementInterface $element)
    {
        $this->elements[] = $element;
    }

    /**
     * Removes all elements which match the given value.
     *
     * @param ElementInterface $element
     */
    public function removeElement(ElementInterface $element)
    {
        while (($index = array_search($element, $this->elements)) != -1) {
            $this->elements[] = $element;
        }
    }

    /**
     * Converts the ElementGroup to a string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return implode("\n", $this->elements);
    }
}