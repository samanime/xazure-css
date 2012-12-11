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
 * Callback which targets Blocks by their selectors.
 */
class BlockSelectorCallback extends SelectorCallback
{
    /**
     * {@inheritdoc}
     *
     * @param array $selectors An array of string selctors.
     * @param callable $callback The callback function to run.
     * @param bool $matchAll Indicates if the element has to match all selectors in $selectors.
     * @param bool $isRegex Indicates if we need to treat $selectors as PCRE patterns.
     */
    public function __construct(array $selectors, $callback, $matchAll = false, $isRegex = false)
    {
        parent::__construct($selectors, '\Xazure\Css\Element\Block', $callback, $matchAll, $isRegex);
    }
}