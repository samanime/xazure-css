<?php
namespace Xazure\Css\Plugin;

use Xazure\Css\Plugin\Callback\PropertyValueCallback;
use Xazure\Css\Plugin\Callback\AtRuleCallback;
use Xazure\Css\Plugin\Callback\AtRuleValueCallback;
use Xazure\Css\Plugin\Callback\AtRuleBlockValueCallback;
use Xazure\Css\Element\ElementInterface;
use Xazure\Css\Element\Blank;

class AtVarPlugin implements PluginInterface
{
    const VALUE_REGEX = '/\[\$([^\]]*)\]/';

    protected $vars;

    public function __construct(array $settings) {
        $this->vars = array();
    }

    public function registerCallbacks()
    {
        return array(
            new AtRuleCallback('var', array($this, 'processVar')),
            new AtRuleValueCallback(self::VALUE_REGEX, array($this, 'echoVar'), true),
            new AtRuleBlockValueCallback(self::VALUE_REGEX, array($this, 'echoVar'), true),
            new PropertyValueCallback(self::VALUE_REGEX, array($this, 'echoVar'), true)
        );
    }

    public function processVar(ElementInterface $element)
    {
        $value = $element->getValue();

        list($var, $operator, $value) = preg_split('/\s*(=)\s*/', $value, -1, \PREG_SPLIT_DELIM_CAPTURE);

        $this->vars[$var] = $value;

        return new Blank();
    }

    public function echoVar(ElementInterface $element)
    {
        $value = $element->getValue();

        $value = preg_replace_callback(self::VALUE_REGEX, array($this, 'replaceMatchWithValue'), $value);

        $element->setValue($value);

        return $element;
    }

    public function replaceMatchWithValue($matches)
    {
        if (!isset($this->vars[$matches[1]])) {
            throw new \Exception('Undefined variable: ' . $matches[1]);
        }

        return $this->vars[$matches[1]];
    }
}