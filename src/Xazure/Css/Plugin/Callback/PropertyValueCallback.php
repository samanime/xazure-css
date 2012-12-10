<?php
namespace Xazure\Css\Plugin\Callback;

class PropertyValueCallback extends ValueCallback
{
    public function __construct($value, $callback, $isRegex = false)
    {
        parent::__construct($value, '\Xazure\Css\Element\Property', $callback, $isRegex);
    }
}