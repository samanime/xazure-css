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
 * Callback which can only be run against the root element during output.
 *
 * This callback can only be called when it is implemented as an output_plugin.
 * This callback is completely skipped (via a special case in Generator::applyPluginRecurse()) during
 * normal plugin processing.
 *
 * Only one of these should be configured. Multiple OutputCallbacks registered at once in an
 * output_plugin will throw an error.
 */
class OutputCallback extends GlobalCallback
{
    /**
     * {@inheritdoc}
     *
     * @param callable $callback The callback to call when this callback is run.
     */
    public function __construct($callback)
    {
        parent::__construct($callback);
    }

    /**
     * Runs the callback function against the element.
     *
     * This version of run() overrides the normal safety check to make
     * sure that the callback function returned an ElementInterface.
     *
     * It should return a string, or something that can be cast as a string.
     *
     * @param \Xazure\Css\Element\ElementInterface $element
     * @return mixed|\Xazure\Css\Element\ElementInterface
     */
    public function run(ElementInterface $element) {
        return call_user_func($this->callback, $element);
    }
}