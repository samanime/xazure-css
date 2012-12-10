<?php
namespace Xazure\Css\Plugin\Callback;

use Xazure\Css\Element\ElementInterface;
use Xazure\Css\Element\NameInterface;

abstract class NameCallback extends Callback
{
    protected $name;
    protected $isRegex;

    public function __construct($name, $elementClass, $callback, $isRegex = false)
    {
        parent::__construct($elementClass, $callback);

        $this->name = $name;
        $this->isRegex = $isRegex;
    }

    public function isMatch(ElementInterface $element)
    {
        return parent::isMatch($element) && ($element instanceof NameInterface)
            && (empty($this->name)
                || (!$this->isRegex && $this->name == $element->getName())
                || ($this->isRegex && preg_match($this->name, $element->getName())));
    }
}