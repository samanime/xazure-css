<?php
namespace Xazure\Css\Plugin\NoPrefix\Property;

use Xazure\Css\Element\ElementInterface;

interface PropertyInterface
{
    function __construct(array $browsers = array());

    function process(ElementInterface $element);

    function getPropertyName();
}