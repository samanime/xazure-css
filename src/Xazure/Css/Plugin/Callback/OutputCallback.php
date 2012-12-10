<?php
namespace Xazure\Css\Plugin\Callback;

use Xazure\Css\Element\ElementInterface;

/**
 * Functionally similar to GlobalCallback, except it overrides the run safety check so it can return a string.
 *
 * This is simply here so we can find the callback to use for outputting.
 */
class OutputCallback extends GlobalCallback
{
    public function __construct($callback)
    {
        parent::__construct($callback);
    }

    /**
     * @param \Xazure\Css\Element\ElementInterface $element
     * @return mixed|\Xazure\Css\Element\ElementInterface
     */
    public function run(ElementInterface $element) {
        return call_user_func($this->callback, $element);
    }
}