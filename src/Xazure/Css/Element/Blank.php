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
 * This element exists for the sole purpose of providing
 * something that plugins can return when they want to remove
 * the element they were given.
 */
class Blank implements ElementInterface
{
    /**
     * Converts Blank to an empty string.
     *
     * @return string
     */
    public function __toString()
    {
        return '';
    }
}