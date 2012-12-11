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
use Xazure\Css\Element\ValueInterface;

/**
 * A callback which targets elements based on their value that implement
 * the ValueInterface.
 *
 * This is an abstract class, so it can not be used directly.
 */
abstract class ValueCallback extends Callback
{
    /**
     * The value to compare against.
     *
     * @var string
     */
    protected $value;

    /**
     * Indicates if we should treat value as a PCRE pattern.
     *
     * @var bool
     */
    protected $isRegex;

    /**
     * Constructor.
     *
     * If $isRegex, $value should be a valid PCRE regex string.
     * It will then be considered to match this callback if it matches in a preg_match().
     *
     * If $isRegex is false, $value can be any string and will only match
     * if the value of the tested element is identical.
     *
     * @param string $value If $isRegex, a PCRE regex string to match; if !$isRegex, a string to compare against value.
     * @param string $elementClass The full namespace and class of the element to target.
     * @param callable $callback The callback function to run.
     * @param bool $isRegex Indicates if $value should be used as a regex pattern or a string literal.
     */
    public function __construct($value, $elementClass, $callback, $isRegex = false)
    {
        parent::__construct($elementClass, $callback);

        $this->value = $value;
        $this->isRegex = (bool)$isRegex;
    }

    /**
     * Compares $element against a set of criteria.
     *
     * To return true, $element must:
     * - Be an instance of the $elementClass provided in constructor.
     * - Be an instance of ValueInterface.
     * - $isRegex must be true and $element->getValue() must match the $value pattern OR
     * - $isRegex must be false and $element->getValue() must equal $value.
     *
     * @param \Xazure\Css\Element\ElementInterface $element The element to test.
     * @return bool If the element matched the criteria.
     */
    public function isMatch(ElementInterface $element)
    {
        return parent::isMatch($element) && $element instanceof ValueInterface
            && ($this->isRegex && preg_match($this->value, $element->getValue())
                || (!$this->isRegex && $this->value == $element->getValue()));
    }
}