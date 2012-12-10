<?php
namespace Xazure\Css\Setting;

class SettingsContainer
{
    protected $settings;

    /**
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
     * If $configFile is empty, it will attempt to load __DIR__ . '/settings.ini'.
     * If $configFileType is empty, it will attempt to auto-detect the type based on extension, defaulting to ini type
     * if it fails to auto-detect.
     *
     * Three config file types are supported:
     * - ini - An ini-style configuration.
     * - yml/yaml - A YAML-style configuration.
     * - xml - An XML style configuration.
     *
     * @param string $configFile The absolute path to the config file to load.
     * @param string $configFileType The config file type. Defaults to auto-detect.
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

    protected function loadXmlConfigFile($configFilePath)
    {

    }

    protected function loadIniConfigFile($configFilePath)
    {

    }
}