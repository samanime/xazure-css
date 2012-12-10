<?php
namespace Xazure\Css\Plugin;

use Xazure\Css\Plugin\Callback\OutputCallback;
use Xazure\Css\Element\ElementInterface;
use Xazure\Css\Element\AtRule;
use Xazure\Css\Element\Property;
use Xazure\Css\Element\Block;
use Xazure\Css\Element\AtRuleBlock;
use Xazure\Css\Element\ElementGroup;

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

    protected $mode;

    public function __construct(array $settings)
    {
        if (isset($settings['mode'])) {
            $this->mode = $settings['mode'];
        } else {
            $this->mode = self::MODE_COMPACT;
        }
    }

    public function registerCallbacks()
    {
        return array(
            new OutputCallback(array($this, 'beautify'))
        );
    }

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

    protected function printExpanded(ElementInterface $element, $output = '', $tabCount = 0)
    {
        if ($element instanceof AtRule) {
            $value = $element->getValue();

            if (!empty($value)) {
                $value = ' ' . $value;
            }

            $output .= str_repeat("\t", $tabCount) . '@' . trim($element->getName()) . $value . ";\n";
        } else if ($element instanceof Property) {
            $output .= str_repeat("\t", $tabCount) . trim($element->getName()) . ': ' . trim($element->getValue()) . ";\n";
        } else if ($element instanceof Block) {
            $output .= str_repeat("\t", $tabCount) . trim(implode(', ', $element->getSelectors())) . " {\n";

            foreach ($element->getElements() as $child) {
                $output = $this->printExpanded($child, $output, $tabCount+1);
            }

            $output = preg_replace('/\n\n$/', "\n", $output);
            $output .= str_repeat("\t", $tabCount) . "}\n\n";
        } else if ($element instanceof AtRuleBlock) {
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
        } else if ($element instanceof ElementGroup) {
            foreach ($element->getElements() as $child) {
                $output = $this->printExpanded($child, $output, $tabCount);
            }
        }

        return $output;
    }

    protected function printCompact(ElementInterface $element, $output = '')
    {
        if ($element instanceof AtRule) {
            $value = $element->getValue();

            if (!empty($value)) {
                $value = ' ' . $value;
            }

            $output .= '@' . trim($element->getName()) . $value . ";";
        } else if ($element instanceof Property) {
            $output .= trim($element->getName()) . ': ' . trim($element->getValue()) . "; ";
        } else if ($element instanceof Block) {
            $output .= trim(implode(', ', $element->getSelectors())) . " { ";

            foreach ($element->getElements() as $child) {
                $output = $this->printCompact($child, $output);
            }

            $output .= "} \n";
        } else if ($element instanceof AtRuleBlock) {
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
        } else if ($element instanceof ElementGroup) {
            foreach ($element->getElements() as $child) {
                $output = $this->printCompact($child, $output);
            }
        }

        return $output;
    }

    protected function printMinified(ElementInterface $element, $output = '')
    {
        if ($element instanceof AtRule) {
            $value = $element->getValue();

            if (!empty($value)) {
                $value = ' ' . $value;
            }

            $output .= '@' . trim($element->getName()) . $value . ";";
        } else if ($element instanceof Property) {
            $output .= trim($element->getName()) . ':' . trim($element->getValue()) . ";";
        } else if ($element instanceof Block) {
            $output .= trim(implode(',', $element->getSelectors())) . "{";

            foreach ($element->getElements() as $child) {
                $output = $this->printMinified($child, $output);
            }

            $output = preg_replace('/;$/', '', $output);
            $output .= "}";
        } else if ($element instanceof AtRuleBlock) {
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
        } else if ($element instanceof ElementGroup) {
            foreach ($element->getElements() as $child) {
                $output = $this->printMinified($child, $output);
            }
        }

        return $output;
    }
}