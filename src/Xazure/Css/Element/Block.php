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
     * Constructor.
     *
     * @param array $selectors An array of string selectors.
     */
    public function __construct(array $selectors = array())
    {
        $this->selectors = $selectors;
    }

    /**
     * Get selectors.
     *
     * @return array An array of string selectors.
     */
    public function getSelectors()
    {
        return $this->selectors;
    }

    /**
     * Set selectors.
     *
     * @param array $selectors An array of string selectors
     */
    public function setSelectors(array $selectors)
    {
        $this->selectors = $selectors;
    }

    /**
     * Add a selector.
     *
     * @param string $selector
     */
    public function addSelector($selector)
    {
        $this->selectors[] = $selector;
    }

    /**
     * Removes all selectors with the given value.
     *
     * @param string $selector
     */
    public function removeSelector($selector)
    {
        while (($index = array_search($selector, $this->selectors)) != -1) {
            array_splice($this->selectors, $index, 1);
        }
    }

    /**
     * Converts Block to a string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return implode(', ', $this->selectors) . " {\n" . implode("\n", $this->elements) . "\n}";
    }
}