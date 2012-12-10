<?php
namespace Xazure\Css\Plugin\Callback;

class BlockSelectorCallback extends SelectorCallback
{
    public function __construct(array $selectors, $callback, $matchAll = false, $isRegex = false)
    {
        parent::__construct($selectors, '\Xazure\Css\Element\Block', $callback, $matchAll, $isRegex);
    }
}