<?php
namespace Xazure\Css\Plugin\NoPrefix\Property;

use Xazure\Css\Element\ElementInterface;
use Xazure\Css\Element\ElementGroup;
use Xazure\Css\Element\Property;

class BorderRadiusProperty implements PropertyInterface
{
    protected $browsers;

    public function __construct(array $browsers = array())
    {
        $this->browsers = $browsers;
    }

    public function getPropertyName()
    {
        return 'border-radius';
    }

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

    /**
     * Given an array of browsers/version pairs, checks against
     * browsers to indicate if this should be supported.
     * @param array $browsrs
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