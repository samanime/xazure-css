<?php
/**
 * This file is part of the XazureCSS package.
 *
 * (c) Christian Snodgrass <csnodgrass3147+github@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Xazure\Css\Plugin;

/**
 * This is the base for all plugins.
 *
 * All plugins must declare registerCallbacks, which should return an
 * array of Callback\Callback objects, which will then be called at the appropriate
 * time.
 */
interface PluginInterface
{
    /**
     * Constructor.
     *
     * @param array $settings An array of settings that are defined elsewhere, usually a config file.
     */
    function __construct(array $settings);

    /**
     * Registers an array of callbacks.
     *
     * Any non-abstract instance of Callback can be registered. Which instance you use
     * dictates what it will match. For example, the PropertyValueCallback can only match
     * ElementInterfaces of Property.
     *
     * All objects returns in this array must be an instance of Callback.
     *
     * @return array An array of Callback\Callbacks.
     */
    function registerCallbacks();
}