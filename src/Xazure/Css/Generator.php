<?php
/**
 * This file is part of the XazureCSS package.
 *
 * (c) Christian Snodgrass <csnodgrass3147+github@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Xazure\Css;

use Xazure\Css\Element\AtRule;
use Xazure\Css\Element\AtRuleBlock;
use Xazure\Css\Element\Block;
use Xazure\Css\Element\Property;
use Xazure\Css\Element\ElementGroup;
use Xazure\Css\Setting\SettingsContainer;
use Xazure\Css\Plugin\PluginInterface;
use Xazure\Css\Element\ElementInterface;
use Xazure\Css\Plugin\Callback\Callback;
use Xazure\Css\Plugin\Callback\OutputCallback;
use Xazure\Css\Plugin\Callback\GlobalCallback;

/**
 * XazureCSS is designed to provide easily pluggable custom CSS actions.
 *
 * Essentially XazureCSS does nothing on it's own, but provides a framework for
 * just about any type of plugin to be built and run against CSS elements, which can
 * then do nearly anything.
 *
 * XazureCSS can have the following plugin types:
 * - Output   - A plugin which is run to generate the final output.
 * - Global   - A plugin which is run against the whole stack of CSS elements.
 * - Block    - Against all CSS blocks.
 * - Name     - Against the name of an AtRule, AtRuleBlock or Property.
 * - Value    - Against the value of an AtRule, AtRuleBlock or Property.
 * - Selector - Against the selectors of a Block.
 */
class Generator
{
    /**
     * A container for all of our settings.
     *
     * @var Setting\SettingsContainer
     */
    protected $settingsContainer;

    /**
     * An array of all loaded Plugin objects.
     *
     * @var array
     */
    protected $plugins;

    /**
     * An array of all registered Callbacks from the objects in $plugins.
     *
     * @var array
     */
    protected $pluginCallbacks;

    /**
     * Attempts to load the configuration file on construction.
     *
     * If $configFilePath is empty, it will attempt to load __DIR__ . '/settings.ini'.
     * If $configFileType is empty, it will attempt to auto-detect the type based on extension, defaulting to ini type
     * if that fails.
     *
     * @param string $configFilePath The config file to load.
     * @param string $configFileType The config file type. Defaults to auto-detect.
     */
    public function __construct($configFilePath = '', $configFileType = '')
    {
        $this->plugins = array();
        $this->settingsContainer = new SettingsContainer();

        if (!empty($configFilePath)) {
            $this->loadConfigFile($configFilePath, $configFileType);
        }
    }

    /**
     * Loads the given config file.
     *
     * @param string $configFilePath
     * @param string $configFileType
     */
    public function loadConfigFile($configFilePath = '', $configFileType = '')
    {
        $this->settingsContainer->loadConfigFile($configFilePath, $configFileType);
        $this->loadPlugins();
    }

    /**
     * Loads the plugin classes from the plugins setting.
     */
    public function loadPlugins()
    {
        // Clear the plugins array.
        $this->plugins = array();
        $this->pluginCallbacks = array();

        $pluginsData = $this->settingsContainer->get('plugins');

        if (!$pluginsData) {
            return;
        }

        foreach ($pluginsData as $pluginName => $pluginData) {
            $plugin = $this->loadPlugin($pluginName, $pluginData);
            $this->plugins[$pluginName] = $plugin;
            $this->pluginCallbacks = array_merge($this->pluginCallbacks, $plugin->registerCallbacks());
        }
    }

    /**
     * Get SettingsContainer.
     *
     * @return SettingsContainer
     */
    public function getSettingsContainer()
    {
        return $this->settingsContainer;
    }

    /**
     * Set SettingsContainer.
     *
     * This allows you to replace the default SettingsContainer with a custom one, for dependency injection
     * purposes.
     *
     * @param SettingsContainer $container
     */
    public function setSettingsContainer(SettingsContainer $container)
    {
        $this->settingsContainer = $container;
    }

    /**
     * Builds the generated stylesheet from a stylesheet.
     *
     * @param string $stylesheet_path The absolute path to the stylesheet to be processed.
     * @return string The generated CSS.
     * @throws \Exception If the stylesheet file cannot be opened.
     */
    public function buildStyleSheet($stylesheet_path)
    {
        $source = file_get_contents($stylesheet_path);

        if ($source === false) {
            throw new \Exception('Unable to load stylesheet.');
        }

        return $this->build($source);
    }

    /**
     * Builds the generated stylesheet from source.
     *
     * @param string $source The stylesheet source to process.
     * @return string The generated CSS.
     * @throws \Exception If an output plugin is specified, but it contains more than one or less than one OutputCallback.
     */
    public function build($source)
    {
        $source = $this->stripComments($source);
        $root = $this->processElements($source);
        $root = $this->applyPlugins($root);

        $outputPluginData = $this->settingsContainer->get('output_plugin');

        if ($outputPluginData !== false) {
            $outputPlugin = $this->loadPlugin('output_plugin', $outputPluginData);
            $callbacks = $outputPlugin->registerCallbacks();

            $outputCallbacks = array();

            foreach ($callbacks as $callback) {
                if ($callback instanceof OutputCallback) {
                    $outputCallbacks[] = $callback;
                }
            }

            if (count($outputCallbacks) > 1) {
                throw new \Exception('Output plugin ' . $outputPluginData['class'] . ' registered more than one OutputCallback.');
            } else if (count($outputCallbacks) == 0) {
                throw new \Exception('Output plugin ' . $outputPluginData['class'] . ' did not register an OutputCallback.');
            }

            return $outputCallbacks[0]->run($root);
        }

        return $root->__toString();
    }

    /**
     * Does the heavy lifting to load a single plugin.
     *
     * @param string $pluginName The name of the plugin.
     * @param array $pluginData The pluginData array from settings, which should contain at least class.
     * @return Plugin\PluginInterface
     * @throws \Exception If class isn't in pluginData, the class couldn't be found, or the class isn't an instance of PluginInterface.
     */
    protected function loadPlugin($pluginName, array $pluginData)
    {
        if (!isset($pluginData['class'])) {
            throw new \Exception('Class not defined for plugin: ' . $pluginName);
        }

        $pluginClass = $pluginData['class'];

        if (!class_exists($pluginClass)) {
            throw new \Exception('Plugin class not found: ' . $pluginClass . ' for ' . $pluginName);
        }

        $plugin = new $pluginClass($pluginData);

        if (!($plugin instanceof PluginInterface)) {
            throw new \Exception('Plugin class: ' . $pluginClass . ' is not an instance of PluginInterface.');
        }

        return $plugin;
    }

    /**
     * Strips all CSS comments from the given source.
     *
     * @param string $source The stylesheet source.
     * @return string The stylesheet source with comments stripped.
     */
    protected function stripComments($source)
    {
        return preg_replace('/\/\*[\s\S]*\*\//U', '', $source);
    }

    /**
     * Given the stylesheet source, builds it into ElementInterfaces.
     *
     * @param $source The stylesheet source.
     * @return ElementGroup
     * @throws \Exception If a } is found with no block open for it to pair against, or not all { are paired with a {.
     */
    protected function processElements($source)
    {
        // The RootBlock, which we'll be returning.
        $rootGroup = new ElementGroup();

        // A stack of blocks we'll use while processing.
        $groups = array($rootGroup);

        // The last block in $blocks, just a shortcut.
        $lastGroup = $rootGroup;

        // Break the source into parts based on the delimiters {, } and ;.
        $parts = preg_split('/\s*([{};])\s*/', $source, -1, \PREG_SPLIT_DELIM_CAPTURE|\PREG_SPLIT_NO_EMPTY);

        // Go through all of the parts.
        while (count($parts) > 0) {
            $part = array_shift($parts);

            // If it is a }, then we should end the last block.
            if ($part == '}') {
                // If we haven't started a block, we can't have one to end.
                // Also a problem if $rootBlock is about to be closed.
                if (!$lastGroup || $lastGroup == $rootGroup) {
                    throw new \Exception('Unexpected }.');
                }

                // Remove the last block from the stack.
                array_pop($groups);

                $lastGroup = $groups[count($groups)-1];
            } else { // it's not a block closer, so we need the next part, which is the delimiter.
                    $delim = array_shift($parts);

                    // If the delimiter is a {, we'll need to start a new Block or AtRuleBlock.
                    if ($delim == '{') {
                            $block = $this->processElementBlock($part);

                            $lastGroup->addElement($block);

                            // Also add it to the blocks stack and mark it as our last block.
                            $groups[] = $block;
                            $lastGroup = $block;
                    } else { // $delim == ';', it should be a Property or AtRule.
                        $lastGroup->addElement($this->processElementLine($part));
                    }
            }
        }

        if (count($groups) > 1) { // if there is more than just the root group.
            throw new \Exception('Unexpected EOF, expecting }.');
        }

        return $rootGroup;
    }

    /**
     * Given a line of source, creates either an AtRule or Property.
     *
     * @param string $lineSource The line to get element from.
     * @return Element\AtRule|Element\Property The AtRule or Property we generate.
     * @throws \Exception If it doesn't match the at-rule or property pattern.
     */
    protected function processElementLine($lineSource)
    {
        // Check it against our patterns to build the proper element.
        if (preg_match('/^\s*@([^\s]+)\s*([^;]*)\s*$/', $lineSource, $matches)) {
            return new AtRule($matches[1], $matches[2]);
        } else if (preg_match('/\s*([^\s]+):\s*(.*)\s*$/U', $lineSource, $matches)) {
            return new Property($matches[1], $matches[2]);
        } else { // Doesn't seem to be a property or at-rule, so throw an error.
            throw new \Exception('Unable to process line: ' . $lineSource);
        }
    }

    /**
     * Given a line of source, creates either an AtRuleBlock or Block.
     *
     * @param string $lineSource The line to start the block from.
     * @return Element\AtRuleBlock|Element\Block The AtRuleBlock or Bloick we generate.
     */
    protected function processElementBlock($lineSource)
    {
        // If it matches the AtRuleBlock pattern, create one.
        if (preg_match('/^\s*@([^\s]+)\s*(.*)\s*$/', $lineSource, $matches)) {
            return new AtRuleBlock($matches[1], $matches[2]);
        } else { // Assume it's a collection of selectors.
            return new Block(preg_split('/\s*,\s*/', $lineSource));
        }
    }

    /**
     * Applies all of the plugins to their respective elements.
     *
     * For each plugin callback, we will make one pass and apply it to any relevant
     * elements.
     *
     * While it is possible that a plugin will be applied to a generated plugin,
     * generally plugins should not rely on this as the order of the plugin loading
     * dictates how this will work.
     *
     * @param ElementGroup $root The root ElementGroup.
     * @return \Xazure\Css\Element\ElementGroup The root element, with plugins applied.
     */
    protected function applyPlugins(ElementGroup $root)
    {
        foreach ($this->pluginCallbacks as $callback) {
            // The GlobalCallback should only be called against $root itself.
            if ($callback instanceof GlobalCallback) {
                $callback->run($root);
            } else if (!($callback instanceof OutputCallback)) { // also make sure we don't run any OutputCallbacks.
                $root = $this->applyPluginRecurse($root, $callback);
            }
        }

        return $root;
    }

    /**
     * This function does the heavy lifting. It traverses all of the elements children and
     * attempts to apply the supplied plugin.
     *
     * @param \Xazure\Css\Element\ElementInterface $element The element to apply the plugin to.
     * @param Plugin\Callback\Callback $callback
     * @throws \Exception If the plugin callback does not return an instance of ElementInterface.
     * @return \Xazure\Css\Element\ElementInterface The processed element.
     */
    protected function applyPluginRecurse(ElementInterface $element, Callback $callback)
    {
        // Traverse the elements tree, if it has one.
        if ($element instanceof ElementGroup) {
            $children = $element->getElements();
            foreach ($children as &$child) {
                $child = $this->applyPluginRecurse($child, $callback);
            }

            $element->setElements($children);
        }

        // Apply to the current element.
        if ($callback->isMatch($element)) {
            $element = $callback->run($element);

            if (!($element instanceof ElementInterface)) {
                throw new \Exception('Plugin callback did not return an instance of ElementInterface: ' . $callback);
            }
        }

        // Return the element.
        return $element;
    }
}