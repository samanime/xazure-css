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

/**
 * The base Callback which all other plugin callbacks should extends.
 *
 * This is an abstract class and cannot be used directly.
 */
abstract class Callback
{
    /**
     * The callback function to call.
     *
     * @var callable
     */
    protected $callback;

    /**
     * The element class the callback targets.
     *
     * @var string
     */
    protected $elementClass;

    /**
     * Constructor.
     *
     * @param string $elementClass The full class the tested elements must be an instance of.
     * @param callable $callback The callback to call when this callback is run.
     */
    public function __construct($elementClass, $callback)
    {
        if (!is_callable($callback)) {
            throw new \Exception('Uncallable callback: ' . $this->callbackToString($callback));
        }

        $this->callback = $callback;
        $this->elementClass = $elementClass;
    }

    /**
     * Get the callback.
     *
     * @return callable
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * Get the element class string.
     *
     * @return string
     */
    public function getElementClass()
    {
        return $this->elementClass;
    }

    /**
     * Given an Element, it should indicate if this matches the callback.
     *
     * @param \Xazure\Css\Element\ElementInterface $element
     * @return Boolean
     */
    public function isMatch(ElementInterface $element)
    {
        return $element instanceof $this->elementClass;
    }

    /**
     * Given an Element, it should run the callback against it and
     * return the results of the Callback.
     *
     * The callback should return an ElementInterface.
     *
     * If more than one Element needs to be returned, return them in an ElementGroup.
     *
     * @param \Xazure\Css\Element\ElementInterface $element
     * @return ElementInterface
     */
    function run(ElementInterface $element)
    {
        $result = call_user_func($this->callback, $element);

        if (!($result instanceof ElementInterface)) {
            throw new \Exception('Plugin Callback: ' . $this . ' did not return an instace of ElementInterface. Returned ' . get_class($result));
        }

        return $result;
    }

    /**
     * Converts a callback to a string.
     *
     * @param callable $callback
     * @return string
     */
    protected function callbackToString($callback)
    {
        if (is_array($callback)) {
            return get_class($callback[0]) . '::' . $callback[1];
        }

        return $callback;
    }

    /**
     * Gives the callback as a string for easy printing.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->callbackToString($this->callback);
    }
}