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
 * Any interface any element which uses selectors should implement.
 *
 * This must be implemented in order to target an element with a SelectorCallback.
 */
interface SelectorInterface
{
    /**
     * Get the selectors.
     *
     * @return string
     */
    function getSelectors();

    /**
     * Set the selectors
     *
     * @param array $selectors An array of string selectors.
     */
    function setSelectors(array $selectors);

    /**
     * Add a selector.
     *
     * @param string $selector
     */
    function addSelector($selector);

    /**
     * Remove all selectors that match the given value.
     *
     * @param string $selector
     */
    function removeSelector($selector);
}