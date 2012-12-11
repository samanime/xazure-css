<?php
/**
 * This file is part of the XazureCSS package.
 *
 * (c) Christian Snodgrass <csnodgrass3147+github@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Xazure\Css\Setting;

/**
 * Provides a container for all settings as well as handles logic to load configuration files.
 */
class SettingsContainer
{
    /**
     * string key/mixed value pairs.
     *
     * @var array
     */
    protected $settings;

    /**
     * Constructor.
     *
     * Loads the config file if one is provided.
     *
     * @param string $configFilePath The absolute file path to a config file. If specified, it will attempt to load.
     * @param string $configFileType The type of config file, if empty it will auto-detect.
     */
    public function __construct($configFilePath = '', $configFileType = '')
    {
        $this->settings = array();

        if (!empty($configFilePath)) {
            $this->loadConfigFile($configFilePath, $configFileType);
        }
    }

    /**
     * Indicates if there is a setting specified by the given key.
     *
     * @param string $key
     * @return bool Indicates if the given key has a stored setting.
     */
    public function has($key)
    {
        return isset($this->settings[$key]);
    }

    /**
     * Retrieves a single setting based on the given key.
     *
     * If false is a possible value, you'll need to check has() to determine if it
     * means false is the value or if false indicates the key doesn't exist.
     *
     * @param string $key The key to the value to get.
     * @return mixed The object which matches the key or FALSE if it isn't found.
     */
    public function get($key)
    {
        if (!isset($this->settings[$key])) {
            return false;
        }

        return $this->settings[$key];
    }

    /**
     * Sets a single key/value pair.
     *
     * If the given key already exists, that value is overridden.
     *
     * @param string $key The key to store the value at.
     * @param mixed $value The value to store.
     */
    public function set($key, $value)
    {
        $this->settings[$key] = $value;
    }

    /**
     * Loads an array of settings in to the current settings bank.
     *
     * This can be useful to load settings directly during runtime.
     *
     * @param array $settings An array of key/value pairs to set.
     */
    public function load(array $settings = array())
    {
        foreach ($settings as $key => $value) {
            $this->settings[$key] = $value;
        }
    }

    /**
     * Loads the specified configuration file.
     *
     * If $configFilePath is empty, it will attempt to load __DIR__ . '/settings.ini'.
     * If $configFileType is empty, it will attempt to auto-detect the type based on extension, defaulting to ini type
     * if it fails to auto-detect.
     *
     * Three config file types are supported:
     * - ini - An ini-style configuration.
     * - yml/yaml - A YAML-style configuration.
     * - xml - An XML style configuration.
     *
     * @param string $configFilePath The absolute path to the config file to load.
     * @param string $configFileType The config file type. Defaults to auto-detect.
     * @throws \Exception If $configFileType isn't a usable type.
     */
    public function loadConfigFile($configFilePath, $configFileType = '')
    {
        if (empty($configFileType)) {
            $configFileType = $this->detectConfigType($configFilePath);
        }

        switch ($configFileType) {
            case 'yml':
            case 'yaml':
                $this->loadYamlConfigFile($configFilePath);
                break;
            case 'xml':
                $this->loadXmlConfigFile($configFilePath);
                break;
            case 'ini':
                $this->loadIniConfigFile($configFilePath);
                break;
            default:
                throw new \Exception('Unsupported config file type: ' . $configFileType);
        }
    }

    /**
     * Given a $configFile path, attempts to auto-detect one of the supported types.
     *
     * If it can't find a supported type, it defaults to ini-type.
     *
     * @param $configFile The absolute path of a configFile.
     * @return string The config type that was matched, which will be "yml", "yaml", "xml", or "ini".
     */
    protected function detectConfigType($configFile)
    {
        if (!preg_match('/\.(ya?ml|xml|ini)$/i', $configFile, $matches)) {
            return 'ini'; // default to INI type.
        }

        return strtolower($matches[1]);
    }

    /**
     * Parses the given Yaml file and stores the resulting data in $this->settings.
     *
     * @param string $configFilePath Absolute path to config file.
     * @throws \Exception If the file fails to load or yaml_parse_file is unavailable.
     */
    protected function loadYamlConfigFile($configFilePath)
    {
        if (!function_exists('yaml_parse_file')) {
            throw new \Exception('Cannot use Yaml config files, Yaml extension is not installed.');
        }

        $settings = yaml_parse_file($configFilePath, 0, $ndocs);

        if ($settings === false) {
            throw new \Exception('Unable to parse Yaml config file: ' . $configFilePath);
        }

        $this->settings = $settings;
    }

    /**
     * Parses the given XML file and stores the resulting data in $this->settings.
     *
     * @param string $configFilePath Absolute path to config file.
     * @throws \Exception Always, since it isn't implemented.
     * @todo Implement this when someone requests it or someone is really bored.
     */
    protected function loadXmlConfigFile($configFilePath)
    {
        throw new \Exception('XML Configuration files are not implemented yet. Will be implemented when first requested. ;)');
    }

    /**
     * Parses the given INI file and stores the resulting data in $this->settings.
     *
     * @param string $configFilePath Absolute path to config file.
     * @throws \Exception If the file fails to load.
     */
    protected function loadIniConfigFile($configFilePath)
    {
        $settings = parse_ini_file($configFilePath, true);

        if ($settings === false) {
            throw new \Exception('Unable to parse INI config file: ' . $configFilePath);
        }

        $this->settings = $settings;
    }
}