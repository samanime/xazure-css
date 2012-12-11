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
 * An interface any element which uses a value should implement.
 *
 * This interface must be implemented by an element to target it with a ValueCallback.
 */
interface ValueInterface
{
    /**
     * Get the value.
     *
     * @return string
     */
    function getValue();

    /**
     * Set the value.
     *
     * @param $value string
     */
    function setValue($value);
}