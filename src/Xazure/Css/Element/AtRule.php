<?php
namespace Xazure\Css\Element;

/**
 * Represents a single at-rule and it's value.
 *
 * Functionally the same, just adds an '@' before the name.
 */
class AtRule extends Property {
    /**
     * @param string $name
     * @param string $value
     */
    public function __construct($name = '', $value = '') {
        parent::__construct($name, $value);
    }

    public function __toString()
    {
        return '@' . $this->name . ' ' . $this->value . ';';
    }
}