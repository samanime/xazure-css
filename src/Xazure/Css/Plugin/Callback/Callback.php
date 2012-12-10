<?php
namespace Xazure\Css\Plugin\Callback;

use Xazure\Css\Element\ElementInterface;

abstract class Callback
{
    protected $callback;
    protected $elementClass;

    public function __construct($elementClass, $callback)
    {
        if (!is_callable($callback)) {
            throw new \Exception('Uncallable callback: ' . $this->callbackToString($callback));
        }

        $this->callback = $callback;
        $this->elementClass = $elementClass;
    }

    public function getCallback()
    {
        return $this->callback;
    }

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
     * If more than one Elements need to be returned, return them in an ElementGroup.
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

    protected function callbackToString($callback)
    {
        if (is_array($callback)) {
            return get_class($callback[0]) . '::' . $callback[1];
        }

        return $callback;
    }

    public function __toString()
    {
        return $this->callbackToString($this->callback);
    }
}