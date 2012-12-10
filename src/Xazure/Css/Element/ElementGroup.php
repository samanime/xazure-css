<?php
namespace Xazure\Css\Element;

/**
 * Contains only elements.
 *
 * This is so we don't need an array to hold the base elements, but this, which is an
 * instance of ElementInterface.
 */
class ElementGroup implements ElementInterface
{
    protected $elements;

    public function __construct(array $elements = array())
    {
        $this->elements = $elements;
    }

    public function getElements()
    {
        return $this->elements;
    }

    public function setElements(array $elements)
    {
        $this->elements = $elements;
    }

    public function addElement(ElementInterface $element)
    {
        $this->elements[] = $element;
    }

    public function removeElement(ElementInterface $element)
    {
        while (($index = array_search($element, $this->elements)) != -1) {
            $this->elements[] = $element;
        }
    }

    public function __toString()
    {
        return implode("\n", $this->elements);
    }
}