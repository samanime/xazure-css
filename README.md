xazure-css
==========

A CSS pre-processor which supports plugins.

Original Author: Christian Snodgrass
License: MIT (others available upon request)

XazureCSS is a CSS pre-processor (it does work to the CSS before it is displayed). By itself it doesn't do much of
anything. What makes it special is that you can include just about any plugin to add all sorts of functionality.

Installing
==========

To install, simply include the files in src in to your project. Set up an autoloader to automatically include
the files. You can find a sample autoloader in build.php.

Once it is autoloading, you just have to make use of it.

Using
==========
To make use of it, you need to create an instance of Xazure\Css\Generator. You can load then call build() and give it
the CSS source code directly, or call buildStyleSheet() and pass it the path to a stylesheet. Both of these will
then return a string with the processed CSS.

    // Load directly with CSS.
    $generator = new Xazure\Css\Generator();
    $generator->build('body { color: #F00; }');

    // Load from a source file.
    // Note the .xcss extension is completely option. It is recommended to use this so you can
    // easily identify unprocessed files.
    $generator = new Xazure\Css\Generator();
    $generator->buildStyleSheet(__DIR__ . '/styles/test.xcss');

You can also use build.php from a browser to test it out by giving it a xcss parameter, which is the path (relative
to build.php) to the source CSS.

    http://localhost/build.php?xcss=styles/test.xcss

While it is completely possible to use XazureCSS on the fly, it is highly recommended you cache the file and rebuild
the cache each time the source files are changed.

A Symfony 2 Bundle will be available shortly to do just such a thing (as well as allow you to easily integrate
it with Symfony 2).

Configuration
=============

As mentioned earlier, by itself XazureCSS does very little. It is in the plugins where the power lies. XazureCSS
ships with a few default plugins.

You need to configure these plugins in order to use them. You can configure XazureCSS directly within the code,
or by providing a configuration file in one of three formats: yaml, xml, ini.

Direct Configuration
--------------------

While direct configuration isn't recommended for normal usage, it can be handy for testing or a super light-weight
deploying.

To configure directly, call getSettingsContainer() on Generator. You can then use the set() function to set any
parameters you need. If you change any plugin-related settings, you'll also need to call loadPlugins() on Generator,
which will reload any plugins based on the new settings.

    $settings = $generator->getSettingsContainer();
    $settings->set('plugins', array('plugin' => array('class' => 'Some\Plugin')));
    $generator->loadPlugins();

Configuration Files
-------------------

You can use a configuration file in one of three formats to specify configuration: Yaml, XML, and INI. To specify these,
you can either provide the path to them in the constructor of Generator, or via the loadConfigFile() method. Both
methods take the same two parameters, $configFilePath and the optional $configFileType. If you omit $configFileType, it
will attempt to auto-detect based on the extension of the file you provided.

    $generator->loadConfigFile(__DIR__ . '/config.yml');

Note: When you call loadConfigFile(), it will wipe any previously loaded or directly set settings.

Configuration Values
--------------------

The following is a rundown of the basic configurations for XazureCSS. Each plugin is free to add to these settings, so
each plugin below will also define it's own configuration. All of the examples are in Yaml, but should be easy enough
to rewrite in other formats.

    output_plugin:
        class: \Some\Class

    plugins:
        some.plugin.name:
            class: \Some\Class

The "some.plugin.name" and "\Some\Class" are variables and can be just about anything. Any number of plugins can be
defined, but only one output_plugin can be defined.

Default Plugins
===============

Below are details for the plugins which are shipped with XazureCSS by default.

AtVarPlugin
-----------

The AtVar Plugin adds the ability to have simple variables in your CSS, which are processed before hand. This is
super handy for doing things like defining commonly used sizes or colors near the top of your document in one place,
then referencing them elsewhere.

To create a variable, simply use the @var command.

    @var someVar = 5;

Then to reference it, use [$someVar] wherever you want.

    body {
        border-radius: [$someVar]px;
    }

which will be converted to:

    body {
        border-radius: 5px;
    }

###Configuration

AtVar only needs the basic class name defined.

    plugins:
        xazure.css.atvar:
            class: \Xazure\Css\Plugin\AtVarPlugin

BeautifyPlugin
--------------

The BeautifyPlugin is an output plugin, so it is used at the final stage of processing, right before it is returned.

The BeautifyPlugin has three modes: expanded, compact, and minified.

Expanded presents it in the way typical for when you want easy viewing.
Compact keeps many extra spaces, but compresses each block on to one line.
Minified removes all unnecessary characters to achieve the smallest possible size.

###Configuration

    output_plugin:
        class: \Xazure\Css\Plugin\BeautifyPlugin
        mode: expanded

You can substitute the mode for "expanded", "compact" or "minified". If omitted, it defaults to compact.

NoPrefixPlugin
--------------

The NoPrefixPlugin is an attempt to remove the need to add vendor-specific prefixes by hand.

This one is a bit complicated and in it's early stages (so it's not really useful just yet). We'll document it
better once it is.

Creating Plugins
================

A Plugin can do just about anything to the CSS. The CSS is processed in to various component elements: Block, AtRule,
AtRuleBlock, and Property, which correspond to the various components that make up a CSS document. These pieces (and
more global pieces) can then be targeted by plugins based on various rules to transform them in various ways.

Creating plugins is relatively straightforward. There are three basic steps:

- Configure the plugin.
- Register callbacks.
- Write the callbacks.

All Plugins must implement the PluginInterface, which specifies the constructor must accept an array as it's first
parameter, and the registerCallbacks() function, which returns an array of callbacks.

    namespace My;

    use Xazure\Css\Plugin\PluginInterface;

    class Plugin implements PluginInterface
    {
        public function __construct(array $settings)
        {
            // do something with settings if you need.
        }

        public function registerCallbacks()
        {
            return array(
                // Add some callbacks.
            );
        }
    }

Configure the Plugin
--------------------

All plugins have the same basic configuration. If it is a normal plugin, it needs to specify a name and class in the
plugins section:

    plugins:
        my.plugin:
            class: My\Plugin

If it is an output plugin, it just needs to define a class in output_plugin.

    output_plugin:
        class: My\Plugin

You can also specify optional settings for your plugin which will be passed to the plugin in an array to the
constructor.

    plugins:
        my.plugin:
            class: My\Plugin
            someVar: cookie

Registering Callbacks
---------------------

In the registerCallbacks() function, you return an array of objects which extends the base Callback class.

Each type of callback targets a specific portion of an element and they may have slightly different attributes.

###Abstract Callbacks
The abstract Callbacks are the basis for all of the others, but can't be used themselves.

They are:
- Callback
- NameCallback
- SelectorCallback
- ValueCallback

###Always Callbacks
These callbacks always match the target element. The signature of the constructor is:

    public function __construct($callback)

The callbacks that fall under this category are:
- GlobalCallback - Targets the root node, which essentially give it access to everything.
- BlockCallback - Targets all CSS blocks.
- OutputCallback - Basically the same as GlobalCallback, but only usable as an output plugin.

###Name Callbacks
These callbacks all target the name of an element. All of these have the same constructor signature:

    public function __construct(string $name, $callback, $isRegex = false)

If you provide no name, it will match all of the target elements.
If you provide a name and isRegex is false, it will try to match exactly with the element's name.
If you provide a name and isRegex is true, it will perform a preg_match() to detect a match.

The Name callbacks are:
- PropertyCallback - Targets a CSS property (e.g., the representation of "color: #F00"), based on the name (e.g., "color").
- AtRuleCallback - Targets a CSS at-rule (e.g., the representation of "@charset utf-8"), based on the name (e.g., "charset").
- AtRuleBlockCallback - Targets a CSS at-rule which has a block (like "font-family"), based on the name.

###SelectorCallback
This only applies to one type of element, Block. It's signature is:

    public function __construct(array $selectors, $callback, $matchAll = false, $isRegex = true);

$selectors is an array of selectors. If $matchAll, the block must have all selectors that $selectors has. If not
$matchAll, it must only have one.

If $isRegex, it will compare selectors with preg_match(). If not, it will compare selectors as strings.

The SelectorCallback is:
- BlockSelectorCallback - Targets the selectors of a Block element.

###ValueCallback
The value callback compares against the value of an element. It's signature is:

    public function __construct($value, $callback, $isRegex = false)

This is similar to $name, except it checks the value of the element instead of the name. Otherwise, it functions
the same.

The Value callbacks are:
- PropertyValueCallback - Targets the value of a Property element.
- AtRuleValueCallback - Targets the value of an AtRule element.
- AtRuleBlockValueCallback - Targets the value of an AtRuleBlock element.

Write the Callbacks
-------------------

The callbacks of a plugin can do just about anything. They should accept an ElementInterface and return one.

    use Xazure\Css\Element\ElementInterface;
    //...
    public function someCallback(ElementInterface $element)

If you are just modifying something simple, you can change the element and return the same element.

    public function someCallback(ElementInterface $element)
    {
        $element->setValue('cookie');
        return $element;
    }

If you want to simply remove the element (because it is an indicator of something, like the @var for AtVarPlugin), you
can return a Blank element, which is designed just for such a purpose.

    use Xazure\Css\Element\Blank;
    //...
    public function someCallback(ElementInterface $element)
    {
        // do something
        return new Blank();
    }

If you want to create multiple elements from one (like what NoPrefixPlugin does), you can return all of the new
elements in an ElementGroup, which is a group of elements and doesn't affect visual output.

    use Xazure\Css\Element\ElementGroup;
    //...
    public function someCallback(ElementInterface $element)
    {
        $group = new ElementGroup();
        $group->addElement($element);
        // add more elements.
        return $group;
    }