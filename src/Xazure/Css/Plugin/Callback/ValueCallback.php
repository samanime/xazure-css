<?php
namespace Xazure\Css\Plugin\Callback;

use Xazure\Css\Element\ElementInterface;
use Xazure\Css\Element\ValueInterface;

abstract class ValueCallback extends Callback
{
    protected $value;
    protected $isRegex;

    public function __construct($value, $elementClass, $callback, $isRegex = false)
    {
        parent::__construct($elementClass, $callback);

        $this->value = $value;
        $this->isRegex = (bool)$isRegex;
    }

    public function isMatch(ElementInterface $element)
    {
        return parent::isMatch($element) && $element instanceof ValueInterface
            && ($this->isRegex && preg_match($this->value, $element->getValue())
                || (!$this->isRegex && $this->value == $element->getValue()));
    }
}