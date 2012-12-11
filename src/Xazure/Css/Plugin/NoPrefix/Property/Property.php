<?php
/**
 * This file is part of the XazureCSS package.
 *
 * (c) Christian Snodgrass <csnodgrass3147+github@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Xazure\Css\Plugin\NoPrefix\Property;

use Xazure\Css\Plugin\NoPrefix\Property\PropertyInterface;
use Xazure\Css\Element\ElementInterface;

/**
 * Property is an abstract implementation of PropertyInterface.
 *
 * Property implements some of the basics and adds a few helpful functions. Other Properties can
 * extend this if it is helpful, or they can just implement PropertyInterface.
 */
abstract class Property implements PropertyInterface
{
    /**
     * An array of browser shortcode/supported version pairs.
     *
     * @var array
     */
    protected $browsers;

    /**
     * {@inheritdoc}
     *
     * @param array $browsers An array of browser shortcode/supported version pairs.
     */
    public function __construct(array $browsers = array())
    {
        $this->browsers = $browsers;
    }

    /**
     * {@inheritdoc}
     *
     * @param \Xazure\Css\Element\ElementInterface $element
     * @return \Xazure\Css\Element\ElementInterface
     */
    abstract public function process(ElementInterface $element);

    /**
     * Given an array of browsers/version pairs, checks against
     * browsers to indicate if this should be supported.
     *
     * This can be used to determine if any of a related group of browsers is supported,
     * so you can output the appropriate prefixed properties.
     *
     * If any of the browsers in $browsers matches a key in $this->browsers and
     * the version in $browsers is >= $this->browsers version, this function return true.
     *
     * @param array $browsers An array of browser shortcodes/supported versions to check against.
     * @return bool Indicates if any of the supplied browsers are supported.
     */
    public function includeSupport(array $browsers)
    {
        foreach ($browsers as $browser => $version) {
            if (isset($this->browsers[$browser]) && $version >= $this->browsers[$browser]) {
                return true;
            }
        }

        return false;
    }
}