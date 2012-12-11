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

use Xazure\Css\Plugin\Callback\OutputCallback;
use Xazure\Css\Element\ElementInterface;
use Xazure\Css\Element\AtRule;
use Xazure\Css\Element\Property;
use Xazure\Css\Element\Block;
use Xazure\Css\Element\AtRuleBlock;
use Xazure\Css\Element\ElementGroup;

/**
 * The BeautifyPlugin provides three modes for "pretty" output of the resultant CSS.
 *
 * BeautifyPlugin should be configured as the output_plugin if used.
 */
class BeautifyPlugin implements PluginInterface
{
    /**
     * Expanded tabs everything and expands all blocks to multiple lines and adds extra whitespace.
     *
     * html, body {
     *      color: #F00;
     * }
     *
     * p {
     *      color: #000;
     * }
     */
    const MODE_EXPANDED = "expanded";

    /**
     * Removes all tabs, some extra spaces, and puts each block on it's own line.
     *
     * html, body { color: #F00; }
     * p { color: #000; }
     */
    const MODE_COMPACT = "compact";

    /**
     * Removes all unnecessary characters, including the last ; of a block.
     *
     * html,body{color:#F00}p{color:#000}
     */
    const MODE_MINIFIED = "minified";

    /**
     * Indicates the mode of output we should use.
     *
     * @var string
     */
    protected $mode;

    /**
     * Constructor.
     *
     * The only valid setting is mode, which should be either "expanded", "compact" or "minified".
     *
     * If mode is omitted or invalid, "compact" is the default.
     *
     * The mode specifies how the CSS is output.
     *
     * @param array $settings An array of plugin settings.
     * @see BeautifyPlugin::MODE_EXPANDED
     * @see BeautifyPlugin::MODE_COMPACT
     * @see BeautifyPlugin::MODE_MINIFIED
     */
    public function __construct(array $settings)
    {
        if (isset($settings['mode'])) {
            $this->mode = $settings['mode'];
        } else {
            $this->mode = self::MODE_COMPACT;
        }
    }

    /**
     * Registers a single OutputCallback.
     *
     * {@inheritdoc}
     */
    public function registerCallbacks()
    {
        return array(
            new OutputCallback(array($this, 'beautify'))
        );
    }

    /**
     * Performs the beautification of the CSS.
     *
     * @param \Xazure\Css\Element\ElementInterface $element
     * @return string The beautified CSS source, ready for outputting.
     */
    public function beautify(ElementInterface $element)
    {
        switch (strtolower($this->mode)) {
            case self::MODE_EXPANDED:
                return $this->printExpanded($element);
            case self::MODE_MINIFIED:
                return $this->printMinified($element);
            case self::MODE_COMPACT:
            default:
                if ($this->mode != self::MODE_COMPACT) {
                    echo '/* Unrecognized mode: ' . $this->mode . ', defaulting to COMPACT. */';
                }

                return $this->printCompact($element);
        }
    }

    /**
     * Generates the "expanded" mode CSS output.
     *
     * @param \Xazure\Css\Element\ElementInterface $element
     * @param string $output
     * @param int $tabCount
     * @return string The "expanded" CSS, ready for output.
     * @see BeautifyPlugin::MODE_EXPANDED
     */
    protected function printExpanded(ElementInterface $element, $output = '', $tabCount = 0)
    {
        if ($element instanceof AtRule) { // Output an at-rule: @rule <value>;\n
            $value = $element->getValue();

            if (!empty($value)) {
                $value = ' ' . $value;
            }

            $output .= str_repeat("\t", $tabCount) . '@' . trim($element->getName()) . $value . ";\n";
        } else if ($element instanceof Property) { // Output a property: property: value;\n
            $output .= str_repeat("\t", $tabCount) . trim($element->getName()) . ': ' . trim($element->getValue()) . ";\n";
        } else if ($element instanceof Block) { // Output a block: selector, ... {\nelements\n}\n\n
            $output .= str_repeat("\t", $tabCount) . trim(implode(', ', $element->getSelectors())) . " {\n";

            foreach ($element->getElements() as $child) {
                $output = $this->printExpanded($child, $output, $tabCount+1);
            }

            $output = preg_replace('/\n\n$/', "\n", $output);
            $output .= str_repeat("\t", $tabCount) . "}\n\n";
        } else if ($element instanceof AtRuleBlock) { // Output an at-rule block: @rule <value> {\nelements\n}\n\n
            $value = $element->getValue();

            if (!empty($value)) {
                $value = ' ' . $value;
            }

            $output .= str_repeat("\t", $tabCount) . '@' . trim($element->getName()) . $value . " {\n";

            foreach ($element->getElements() as $child) {
                $output = $this->printExpanded($child, $output, $tabCount+1);
            }

            $output = preg_replace('/\n\n$/', "\n", $output);
            $output .= str_repeat("\t", $tabCount) . "}\n\n";
        } else if ($element instanceof ElementGroup) { // Just output each of the elements within the group.
            foreach ($element->getElements() as $child) {
                $output = $this->printExpanded($child, $output, $tabCount);
            }
        }

        return $output;
    }

    /**
     * Generates the "compact" mode CSS output.
     *
     * @param \Xazure\Css\Element\ElementInterface $element
     * @param string $output
     * @return string The "compact" CSS, ready for output.
     * @see BeautifyPlugin::MODE_COMPACT
     */
    protected function printCompact(ElementInterface $element, $output = '')
    {
        if ($element instanceof AtRule) { // Output an at-rule: @rule <value>;
            $value = $element->getValue();

            if (!empty($value)) {
                $value = ' ' . $value;
            }

            $output .= '@' . trim($element->getName()) . $value . ";";
        } else if ($element instanceof Property) { // Output a property: property: value;
            $output .= trim($element->getName()) . ': ' . trim($element->getValue()) . "; ";
        } else if ($element instanceof Block) { // Output a block: selectors, ... { elements }\n
            $output .= trim(implode(', ', $element->getSelectors())) . " { ";

            foreach ($element->getElements() as $child) {
                $output = $this->printCompact($child, $output);
            }

            $output .= "} \n";
        } else if ($element instanceof AtRuleBlock) { // Output an at-rule block: @rule <value> { elements }\n
            $value = $element->getValue();

            if (!empty($value)) {
                $value = ' ' . $value;
            }

            $output .= '@' . trim($element->getName()) . $value . " { ";

            foreach ($element->getElements() as $child) {
                $output = $this->printCompact($child, $output);
            }

            $output = substr($output, 0, -1);
            $output .= "} \n";
        } else if ($element instanceof ElementGroup) { // Output the elements of an ElementGroup.
            foreach ($element->getElements() as $child) {
                $output = $this->printCompact($child, $output);
            }
        }

        return $output;
    }

    /**
     * Generates the "minified" mode CSS output.
     *
     * @param \Xazure\Css\Element\ElementInterface $element
     * @param string $output
     * @return string The "minified" CSS, ready for output.
     * @see BeautifyPlugin::MODE_MINIFIED
     */
    protected function printMinified(ElementInterface $element, $output = '')
    {
        if ($element instanceof AtRule) {
            $value = $element->getValue();

            if (!empty($value)) {
                $value = ' ' . $value;
            }

            $output .= '@' . trim($element->getName()) . $value . ";"; // Output an at-rule: @rule <value>;
        } else if ($element instanceof Property) {
            $output .= trim($element->getName()) . ':' . trim($element->getValue()) . ";"; // Output a property: property:value;
        } else if ($element instanceof Block) { // Output a block: selector,...{elements}
            $output .= trim(implode(',', $element->getSelectors())) . "{";

            foreach ($element->getElements() as $child) {
                $output = $this->printMinified($child, $output);
            }

            $output = preg_replace('/;$/', '', $output);
            $output .= "}";
        } else if ($element instanceof AtRuleBlock) { // Output an at-rule block: @rule <value>{elements}
            $value = $element->getValue();

            if (!empty($value)) {
                $value = ' ' . $value;
            }

            $output .= '@' . trim($element->getName()) . $value . "{";

            foreach ($element->getElements() as $child) {
                $output = $this->printMinified($child, $output);
            }

            $output = preg_replace('/;$/', '', $output);
            $output .= "}";
        } else if ($element instanceof ElementGroup) { // Output the elements of an ElementGroup
            foreach ($element->getElements() as $child) {
                $output = $this->printMinified($child, $output);
            }
        }

        return $output;
    }
}