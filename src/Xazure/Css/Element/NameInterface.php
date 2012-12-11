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
 * An interface any element which uses a name should implement.
 *
 * Implementing this is required to target it with a NameCallback.
 */
interface NameInterface
{
    /**
     * Get the name.
     *
     * @return string
     */
    function getName();

    /**
     * Set the name.
     *
     * @param string $name
     */
    function setName($name);
}