<?php
namespace Xazure\Css\Plugin\Callback;

class BlockCallback extends Callback
{
    public function __construct($callback)
    {
        parent::__construct('\Xazure\Css\Element\Block', $callback);
    }
}