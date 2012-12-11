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
 * Represents a single at-rule element (which does not contain a block).
 */
class AtRule extends Property {
    /**
     * Constructor.
     *
     * @param string $name
     * @param string $value
     */
    public function __construct($name = '', $value = '') {
        parent::__construct($name, $value);
    }

    /**
     * Converts AtRule to a string.
     *
     * @return string
     */
    public function __toString()
    {
        return '@' . $this->name . ' ' . $this->value . ';';
    }
}