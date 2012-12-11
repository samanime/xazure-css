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
 * Callback which targets a Property by its value.
 */
class PropertyValueCallback extends ValueCallback
{
    /**
     * {@inheritdoc}
     *
     * @param string $value The value to compare against.
     * @param callable $callback The callback function to run.
     * @param bool $isRegex Indicates if we should treat $value as a PCRE pattern.
     */
    public function __construct($value, $callback, $isRegex = false)
    {
        parent::__construct($value, '\Xazure\Css\Element\Property', $callback, $isRegex);
    }
}