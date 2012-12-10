<?php
namespace Xazure\Css\Plugin\Callback;

use Xazure\Css\Element\ElementInterface;
use Xazure\Css\Element\SelectorInterface;

abstract class SelectorCallback extends Callback
{
    protected $selectors;
    protected $matchAll;
    protected $isRegex;

    public function __construct(array $selectors, $elementClass, $callback, $matchAll = false, $isRegex = false)
    {
        parent::__construct($elementClass, $callback);

        $this->selectors = $selectors;
        $this->matchAll = (bool)$matchAll;
        $this->isRegex = $isRegex;
    }

    public function isMatch(ElementInterface $element)
    {
        if (!(parent::isMatch($element) && $element instanceof SelectorInterface)) {
            return false;
        }

        $elementSelectors = $element->getSelector();

        if (!$this->isRegex) {
            foreach ($this->selectors as $selector) {
                if (in_array($selector, $elementSelectors)) {
                    if (!$this->matchAll) {
                        return true;
                    }
                } else if ($this->matchAll) {
                    return false;
                }
            }
        } else {
            foreach ($this->selectors as $pattern) {
                foreach ($elementSelectors as $selector) {
                    if (preg_match($pattern, $selector)) {
                        if (!$this->matchAll) {
                            return true;
                        }
                    } else if ($this->matchAll) {
                        return false;
                    }
                }
            }
        }

        // If it is matchAll, then we should return true since we didn't fail.
        // If it isn't matchAll, we should return false since none passed.
        // So, we can just return matchAll.
        return $this->matchAll;
    }
}