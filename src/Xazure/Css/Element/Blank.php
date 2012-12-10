<?php
namespace Xazure\Css\Element;

/**
 * This element exists for the sole purpose of providing
 * something that plugins can return when they want to remove
 * the element they were given.
 */
class Blank implements ElementInterface
{
    public function __toString()
    {
        return '';
    }
}