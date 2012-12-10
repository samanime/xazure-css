<?php
namespace Xazure\Css\Plugin\Callback;

class AtRuleValueCallback extends ValueCallback
{
    public function __construct($value, $callback, $isRegex = false)
    {
        parent::__construct($value, '\Xazure\Css\Element\AtRule', $callback, $isRegex);
    }
}