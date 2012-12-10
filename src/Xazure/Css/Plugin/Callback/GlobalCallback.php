<?php
namespace Xazure\Css\Plugin\Callback;

/**
 * Adds a check to make sure it is an ElementGroup, not a more generic element.
 */
class GlobalCallback extends Callback
{
    public function __construct($callback)
    {
        parent::__construct('\Xazure\Css\Element\ElementGroup', $callback);
    }
}