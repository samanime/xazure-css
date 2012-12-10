<?php
namespace Xazure\Css\Plugin\Callback;

class AtRuleBlockCallback extends NameCallback
{
    public function __construct($name, $callback, $isRegex = false)
    {
        parent::__construct($name, '\Xazure\Css\Element\AtRuleBlock', $callback, $isRegex);
    }
}