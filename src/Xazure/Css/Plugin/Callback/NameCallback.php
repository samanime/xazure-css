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
use Xazure\Css\Element\NameInterface;

/**
 * A callback which targets elements based on their name that implement
 * the NameInterface.
 *
 * This is an abstract class, so it can not be used directly.
 */
abstract class NameCallback extends Callback
{
    /**
     * The name to compare against.
     *
     * @var string
     */
    protected $name;

    /**
     * Indicates if we should treat name as a PCRE pattern.
     *
     * @var bool
     */
    protected $isRegex;

    /**
     * Constructor.
     *
     * If $isRegex, $name should be a value PCRE string.
     * It will then be evaluated against the elements name for testing.
     *
     * If $isRegex is false, $name should be a string literal which is compared to the element.
     *
     * If $name is empty, an element with any name can pass that criteria.
     *
     * @param string $name The name to compare against, either a PCRE pattern or a string literal.
     * @param string $elementClass The full class which the element should be compared against.
     * @param callable $callback The callback function to run.
     * @param bool $isRegex If $name should be treated as regex or not.
     */
    public function __construct($name, $elementClass, $callback, $isRegex = false)
    {
        parent::__construct($elementClass, $callback);

        $this->name = $name;
        $this->isRegex = $isRegex;
    }

    /**
     * Compares $element against a set of criteria.
     *
     * To return true, $element must:
     * - Be an instance of the class give specified by $elementClass in the constructor.
     * - Be an instance of NameInterface.
     * - $isRegex must be true and the element's name must preg_match() $name from the constructor OR
     * - $isRegex must be false and the element's name must be equal to $name from the constructor OR
     * - The $name supplied to the constructor is empty.
     *
     * @param \Xazure\Css\Element\ElementInterface $element
     * @return bool Indicates if $element met the criteria.
     */
    public function isMatch(ElementInterface $element)
    {
        return parent::isMatch($element) && ($element instanceof NameInterface)
            && (empty($this->name)
                || (!$this->isRegex && $this->name == $element->getName())
                || ($this->isRegex && preg_match($this->name, $element->getName())));
    }
}