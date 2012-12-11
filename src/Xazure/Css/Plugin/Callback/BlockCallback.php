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
 * Callback which targets all Blocks.
 */
class BlockCallback extends Callback
{
    /**
     * {@inheritdoc}
     *
     * @param callable $callback The callback to call when this callback is run.
     */
    public function __construct($callback)
    {
        parent::__construct('\Xazure\Css\Element\Block', $callback);
    }
}