<?php
/**
 * This file is part of the XazureCSS package.
 *
 * (c) Christian Snodgrass <csnodgrass3147+github@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Xazure\Css\Plugin\NoPrefix;

use Xazure\Css\Plugin\Callback\PropertyCallback;
use Xazure\Css\Plugin\NoPrefix\Property\PropertyInterface;
use Xazure\Css\Element\ElementInterface;
use Xazure\Css\Plugin\PluginInterface;

/**
 * NoPrefixPlugin implements functionality which will automatically add
 * prefixed versions of non-prefixed properties as needed.
 */
class NoPrefixPlugin implements PluginInterface
{
    /**
     * An array of registered properties.
     *
     * @var array
     */
    protected $properties;

    /**
     * An array of supported browsers, in the form of
     * browser shortcode/supported version pairs.
     *
     * @var array
     */
    protected $browsers;

    /**
     * An array of CSS property names to include.
     *
     * @var array
     */
    protected $include;

    /**
     * An array of CSS property names to exclude.
     *
     * @var array
     */
    protected $exclude;

    /**
     * Constructor.
     *
     * Valid settings for NoPrefixPlugin are:
     * - browsers - An array of browser shortcode/supported version pairs.
     * - include - An array of CSS property names to include.
     * - exclude - An array of CSS property names to exclude.
     * - properties - An array of CSS property names/class names to manually be loaded.
     *
     * browsers should be an associative array of browser shortcodes and the minimum browser
     * version to support.
     *
     * Supported browsers are (default version in parentheses):
     * - Internet Explorer: ie(7)
     * - Firefox: ff(15)
     * - Chrome: chrome(22)
     * - Opera: opera(12.1)
     * - Opera Mini: omini(5.0)
     * - Safari: safari(5.1)
     * - Android: android(2.1)
     * - Blackberry: bb(7.0)
     * - iOS Safari: ios(3.2)
     *
     * include and exclude both are a normal array of CSS property names.
     * If include is provided, only those properties can be used (if available).
     * If exclude is provided, all properties except those properties can be used (if available).
     * If both include and exclude are provided, they are both essentially ignored as that makes no sense.
     * By default, both are empty so all are available.
     *
     * properties can be any CSS property (including non-standard ones). They should come in the
     * form of an associative array with the CSS property being the key and the full class path the value.
     *
     * Any manually loaded properties will override automatically loaded ones.
     *
     * {@inheritdoc}
     *
     * @param array $settings An array of settings that are defined elsewhere, usually a config file.
     */
    public function __construct(array $settings)
    {
        $this->loadSettings($settings);
    }

    /**
     * Registers a single PropertyCallback against all properties.
     *
     * {@inheritdoc}
     */
    public function registerCallbacks()
    {
        // We'll pass all properties in.
        return array(
            new PropertyCallback('', array($this, 'processProperty'))
        );
    }

    /**
     * Processes the $element against the related NoPrefix\Property, if one is available.
     *
     * If the property isn't found in $properties, it will be attempted to autoload.
     *
     * All autoloaded properties will be searched for within the NoPrefix\Property directory.
     *
     * Manually loaded properties can be passed in via the settings for NoPrefix. Manually loaded
     * properties may exist anywhere.
     *
     * If a property is found, it's process() function is then called.
     *
     * @param \Xazure\Css\Element\ElementInterface $element
     * @return \Xazure\Css\Element\ElementInterface The processed $element.
     * @throws \Exception If a class property is found, but it doesn't implement PropertyInterface.
     */
    public function processProperty(ElementInterface $element)
    {
        $property = trim($element->getName());

        // If it is in exclude or not in include, just return the untouched element.
        if (count($this->exclude) > 0 && in_array($property, $this->exclude)
            || count($this->include) > 0 && !in_array($property, $this->include)) {
            return $element;
        }

        // If it hasn't been loaded, try to load it.
        if (!isset($this->properties[$property])) {
            $className = $this->propertyToClassName($property);

            // Can't find it, so don't try to load it.
            if (!class_exists($className)) {
                return $element;
            }

            $propertyClass = new $className($this->browsers);

            if (!($propertyClass instanceof PropertyInterface)) {
                throw new \Exception('Class found for property ' . $property . ', but it does not implement PropertyInterface.');
            }

            $this->properties[$property] = $propertyClass;
        }

        // Call process on the property.
        return call_user_func(array($this->properties[$property], 'process'), $element);
    }

    /**
     * Loads the default settings.
     *
     * @param array $settings An array of key/value pairs for settings.
     * @throws \Exception
     * @see NoPrefixPlugin::__construct()
     */
    protected function loadSettings(array $settings)
    {
        // Build some defaults.
        $this->browsers = array(
            'ie' => 7,
            'ff' => 15,
            'chrome' => 22,
            'safari' => 5.1,
            'ios' => 3.2,
            'android' => 2.1,
            'opera' => 12.1,
            'omini' => 5.0,
            'bb' => 7.0
        );

        // If we have include and not exclude, use include. If we have both, ignore include.
        if (isset($settings['include']) && !isset($settings['exclude'])) {
            $this->include = $settings['include'];
        } else {
            $this->include = array();
        }

        // If we have exclude and not include, use exclude. If we have both, ignore exclude.
        if (isset($settings['exclude']) && !isset($settings['include'])) {
            $this->exclude = $settings['exclude'];
        } else {
            $this->exclude = array();
        }

        // If we were given browsers, merge them in to our defaults.
        if (isset($settings['browsers'])) {
            $settingBrowsers = $settings['browsers'];

            foreach ($this->browsers as $browser => $version) {
                if (isset($settingBrowsers[$browser])) {
                    $this->browsers[$browser] = $settingBrowsers[$browser];
                }
            }
        }

        // Load any manual properties.
        if (isset($settings['properties'])) {
            foreach ($settings['properties'] as $property => $className) {
                if (!class_exists($className)) {
                    throw new \Exception('Unable to find manually specified Property class: ' . $className);
                }

                $propertyClass = new $className($this->browsers);

                if (!($propertyClass instanceof PropertyInterface)) {
                    throw new \Exception('Manually specified Property class: ' . $className . ' does not implement PropertyInterface.');
                }

                $this->properties[$property] = $propertyClass;
            }
        }
    }

    /**
     * Helper function to convert a property to what it's class name would be for autoloading.
     *
     * @param string $property A CSS property name.
     * @return string The class name it would be.
     */
    protected function propertyToClassName($property)
    {
        $parts = preg_split('/-/', $property);

        $className = '';

        foreach ($parts as $part) {
            $className .= ucfirst($part);
        }

        return __NAMESPACE__ . '\Property\\' . $className . 'Property';
    }
}