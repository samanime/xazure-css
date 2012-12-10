<?php
namespace Xazure\Css\Plugin\Callback;

class AtRuleCallback extends NameCallback
{
    public function __construct($name, $callback, $isRegex = false)
    {
        parent::__construct($name, '\Xazure\Css\Element\AtRule', $callback, $isRegex);
    }
}