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
 * Callback which targets a Property by its name.
 */
class PropertyCallback extends NameCallback
{
    /**
     * {@inheritdoc}
     *
     * @param string $name The name to compare against.
     * @param callable $callback The callback function to run.
     * @param bool $isRegex Indicates if we should treat $name as a PCRE pattern.
     */
    public function __construct($name, $callback, $isRegex = false)
    {
        parent::__construct($name, '\Xazure\Css\Element\Property', $callback, $isRegex);
    }
}