<?php
/**
 * This file is part of the XazureCSS package.
 *
 * (c) Christian Snodgrass <csnodgrass3147+github@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Xazure\Css\Plugin\NoPrefix\Property;

use Xazure\Css\Element\ElementInterface;

/**
 * All NoPrefixPlugin Properties must implement this interface.
 */
interface PropertyInterface
{
    /**
     * Construct.
     *
     * @param array $browsers An array of browser shortcodes/supported version pairs.
     */
    function __construct(array $browsers = array());

    /**
     * Processes the element and returns the processed elements.
     *
     * @param \Xazure\Css\Element\ElementInterface $element
     * @return ElementInterface The processed elements.
     */
    function process(ElementInterface $element);

    /**
     * The CSS property which this class handles.
     *
     * @return string
     */
    function getPropertyName();
}