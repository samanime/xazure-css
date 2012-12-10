<?php
namespace Xazure\Css\Plugin\Callback;

class PropertyCallback extends NameCallback
{
    public function __construct($name, $callback, $isRegex = false)
    {
        parent::__construct($name, '\Xazure\Css\Element\Property', $callback, $isRegex);
    }
}