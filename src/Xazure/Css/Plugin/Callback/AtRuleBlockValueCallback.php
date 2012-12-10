<?php
namespace Xazure\Css\Plugin\Callback;

class AtRuleBlockValueCallback extends ValueCallback
{
    public function __construct($value, $callback, $isRegex = false)
    {
        parent::__construct($value, '\Xazure\Css\Element\AtRuleBlock', $callback, $isRegex);
    }
}