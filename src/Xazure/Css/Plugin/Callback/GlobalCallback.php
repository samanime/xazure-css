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

/**
 * Callback which will be run against the root ElementGroup of a stylesheet.
 *
 * There is nothing special about GlobalCallback that makes it match only the root node.
 * Instead, there is a special case in Generator::applyPluginRecurse() to ensure this.
 */
class GlobalCallback extends Callback
{
    /**
     * {@inheritdoc}
     * @param callable $callback The callback function to run.
     */
    public function __construct($callback)
    {
        parent::__construct('\Xazure\Css\Element\ElementGroup', $callback);
    }
}