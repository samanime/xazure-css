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
 * The interface which all Elements must implement.
 */
interface ElementInterface
{
    /**
     * Converts the ElementInterface in to a string representation.
     *
     * @return string
     */
    function __toString();
}