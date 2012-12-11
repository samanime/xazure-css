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

use Xazure\Css\Element\ElementInterface;
use Xazure\Css\Element\ElementGroup;
use Xazure\Css\Element\Property;

/**
 * Implements the border-radius property for NoPrefixPlugin.
 */
class BorderRadiusProperty extends Property
{
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
     */
    public function getPropertyName()
    {
        return 'border-radius';
    }

    /**
     * Adds the -webkit-prefixed border-radius if iOS 3.1 or Android 2.1 are supported.
     *
     * {@inheritdoc}
     *
     * @param \Xazure\Css\Element\ElementInterface $element
     * @return \Xazure\Css\Element\ElementInterface
     */
    public function process(ElementInterface $element)
    {
        // We'll be creating a new ElementGroup to hold these.
        $group = new ElementGroup();

        if ($this->includeSupport(array('ios' => 3.2, 'android' => 2.1))) {
            $group->addElement(new Property('-webkit-border-radius', $element->getValue()));
        }

        $group->addElement($element); // add the unprefixed version.
        return $group;
    }
}