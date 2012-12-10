<?php
namespace Xazure\Css\Element;

/**
 * Represents a CSS block of elements and selectors.
 */
class Block extends ElementGroup implements SelectorInterface
{
    /**
     * An array of string selectors.
     *
     * @var array
     */
    protected $selectors;

    /**
     * @param array $selectors
     * @param array $properties
     */
    public function __construct(array $selectors = array())
    {
        $this->selectors = $selectors;
    }

    /**
     * @return array
     */
    public function getSelectors()
    {
        return $this->selectors;
    }

    /**
     * @param array $selectors
     */
    public function setSelectors(array $selectors)
    {
        $this->selectors = $selectors;
    }

    /**
     * @param string $selector
     */
    public function addSelector($selector)
    {
        $this->selectors[] = $selector;
    }

    /**
     * @param string $selector
     */
    public function removeSelector($selector)
    {
        while (($index = array_search($selector, $this->selectors)) != -1) {
            array_splice($this->selectors, $index, 1);
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode(', ', $this->selectors) . " {\n" . implode("\n", $this->elements) . "\n}";
    }
}