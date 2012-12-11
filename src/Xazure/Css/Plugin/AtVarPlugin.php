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

use Xazure\Css\Plugin\Callback\PropertyValueCallback;
use Xazure\Css\Plugin\Callback\AtRuleCallback;
use Xazure\Css\Plugin\Callback\AtRuleValueCallback;
use Xazure\Css\Plugin\Callback\AtRuleBlockValueCallback;
use Xazure\Css\Element\ElementInterface;
use Xazure\Css\Element\Blank;

/**
 * The AtVarPlugin provides simple CSS variables using the @var rule.
 *
 * Currently, @var only supports the assignment operator (=).
 *
 * To use @var, write a value in the form of @var varName = value:
 *
 * @var myColor = "#F00";
 *
 * To output the variable anywhere in any AtRule, AtRuleBlock or Property value,
 * write in in the form [$varName]:
 *
 * body {
 *  color: [$myColor];
 * }
 *
 * @todo Add more operators and proper distinction between string and numeric values, as well as other variables.
 */
class AtVarPlugin implements PluginInterface
{
    /**
     * The regex pattern we use to spot a variable in a value.
     */
    const VALUE_REGEX = '/\[\$([^\]]*)\]/';

    /**
     * All currently defined variable values.
     *
     * @var array
     */
    protected $vars;

    /**
     * Constructor.
     *
     * @param array $settings An array of plugin settings.
     */
    public function __construct(array $settings) {
        $this->vars = array();
    }

    /**
     * Registers callbacks against AtRule to process @var, and AtRuleValue, AtRuleBlockValue and PropertyValue
     * to output variables.
     *
     * {@inheritdoc}
     */
    public function registerCallbacks()
    {
        return array(
            new AtRuleCallback('var', array($this, 'processVar')),
            new AtRuleValueCallback(self::VALUE_REGEX, array($this, 'echoVar'), true),
            new AtRuleBlockValueCallback(self::VALUE_REGEX, array($this, 'echoVar'), true),
            new PropertyValueCallback(self::VALUE_REGEX, array($this, 'echoVar'), true)
        );
    }

    /**
     * Process an @var AtRule.
     *
     * @param \Xazure\Css\Element\ElementInterface $element
     * @return \Xazure\Css\Element\Blank
     */
    public function processVar(ElementInterface $element)
    {
        $value = $element->getValue();

        list($var, $operator, $value) = preg_split('/\s*(=)\s*/', $value, -1, \PREG_SPLIT_DELIM_CAPTURE);

        $this->vars[$var] = $value;

        return new Blank();
    }

    /**
     * Replaces the variable reference with its value.
     *
     * @param \Xazure\Css\Element\ElementInterface $element
     * @return \Xazure\Css\Element\ElementInterface
     */
    public function echoVar(ElementInterface $element)
    {
        $value = $element->getValue();

        $value = preg_replace_callback(self::VALUE_REGEX, array($this, 'replaceMatchWithValue'), $value);

        $element->setValue($value);

        return $element;
    }

    /**
     * Performs the replacement on variable reference matches.
     *
     * @param array $matches A match array from preg_replace_callback, with $matches[1] being the variable name.
     * @return mixed The value of the variable.
     * @throws \Exception If the referenced variable does not exist.
     */
    protected function replaceMatchWithValue($matches)
    {
        if (!isset($this->vars[$matches[1]])) {
            throw new \Exception('Undefined variable: ' . $matches[1]);
        }

        return $this->vars[$matches[1]];
    }
}