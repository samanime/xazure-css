<?php
namespace Xazure\Css\Plugin\NoPrefix;

use Xazure\Css\Plugin\Callback\PropertyCallback;
use Xazure\Css\Plugin\NoPrefix\Property\PropertyInterface;
use Xazure\Css\Element\ElementInterface;
use Xazure\Css\Plugin\PluginInterface;

class NoPrefixPlugin implements PluginInterface
{
    protected $properties;
    protected $browsers;

    protected $include;
    protected $exclude;

    public function __construct(array $settings)
    {
        $this->loadSettings($settings);
    }

    public function registerCallbacks()
    {
        // We'll pass all properties in.
        return array(
            new PropertyCallback('', array($this, 'processProperty'))
        );
    }

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

        if (isset($settings['include']) && !isset($settings['exclude'])) {
            $this->include = $settings['include'];
        } else {
            $this->include = array();
        }

        if (isset($settings['exclude']) && !isset($settings['include'])) {
            $this->exclude = $settings['exclude'];
        } else {
            $this->exclude = array();
        }

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