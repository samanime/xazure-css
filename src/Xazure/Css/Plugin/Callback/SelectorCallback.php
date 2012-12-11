<?php
/**
 * This file is part of the XazureCSS package.
 *
 * (c) Christian Snodgrass <csnodgrass3147+github@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Xazure\Css\Plugin\Callback;

use Xazure\Css\Element\ElementInterface;
use Xazure\Css\Element\SelectorInterface;

/**
 * A callback which targets a elements by their selector that implement
 * the SelectorInterface.
 *
 * This is an abstract class and cannot be used directly.
 */
abstract class SelectorCallback extends Callback
{
    /**
     * Array of string selectors.
     *
     * @var array
     */
    protected $selectors;

    /**
     * Indicates if an element must match all selectors.
     *
     * @var bool
     */
    protected $matchAll;

    /**
     * Indicates if we should treat selectors as PCRE patterns.
     *
     * @var bool
     */
    protected $isRegex;

    /**
     * Constructor.
     *
     * If $matchAll, the block must contain all selectors that are found in $selectors (though it can also
     * contain more). If $matchAll is false, it must only match one.
     *
     * If $isRegex, each selector in $selectors is treated as a PCRE pattern and matched against each block
     * selector.
     *
     * If $isRegex is false, each selector is treated as a string literal and a simple comparison is performed.
     *
     * @param array $selectors An array of string selectors, either PCRE patterns or string literals.
     * @param string $elementClass The full class that the element must be an instance of.
     * @param callable $callback The callback function to call.
     * @param bool $matchAll Indicates if a block must match all $selectors to match.
     * @param bool $isRegex Indicates how to treat $selectors.
     */
    public function __construct(array $selectors, $elementClass, $callback, $matchAll = false, $isRegex = false)
    {
        parent::__construct($elementClass, $callback);

        $this->selectors = $selectors;
        $this->matchAll = (bool)$matchAll;
        $this->isRegex = $isRegex;
    }

    /**
     * Compares the element to the criteria of this callback.
     *
     * To return true, the element must:
     * - Be an instance of the class specified by $elementClass in the constructor.
     * - Be an instance of SelectorInterface.
     * - If $matchAll, it must match all selectors found in $selector OR
     * - If $matchAll is false, it must match at least one selector.
     * - If $isRegex, it must match selectors as a PCRE pattern OR
     * - If $isRegex is false, it must equal selectors as string literals.
     *
     * @param \Xazure\Css\Element\ElementInterface $element
     * @return bool Indicates if the element matches the criteria of this callback.
     */
    public function isMatch(ElementInterface $element)
    {
        // Do the base tests and return false if any don't base.
        if (!(parent::isMatch($element) && $element instanceof SelectorInterface)) {
            return false;
        }

        $elementSelectors = $element->getSelector();

        // If they aren't regex, just compare them as normal strings by doing an in_array.
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
        } else { // If they are regex, we need to loop through both sets and check preg_match.
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