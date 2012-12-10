<?php
namespace Xazure\Css\Element;

interface SelectorInterface
{
    function getSelectors();
    function setSelectors(array $selectors);

    function addSelector($selector);
    function removeSelector($selector);
}