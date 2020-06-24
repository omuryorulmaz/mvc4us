<?php
namespace Mvc4us\Config;

use Mvc4us\Utils\ArrayUtils;
use Yosymfony\Toml\Toml;

final class Config
{

    private static $environment;

    private static $config = [
        'app' => [
            'debug' => true
        ]
    ];

    /**
     * This class should not be instantiated.
     */
    private function __construct()
    {}

    public static function load($projectDir, $environment = null)
    {
        if ($environment === null || empty($environment)) {
            if (self::$environment !== null) {
                $environment = self::$environment;
            } elseif (isset($_ENV['MVC4P_ENV'])) {
                $environment = $_ENV['MVC4P_ENV'];
            } elseif (isset($_SERVER['MVC4P_ENV'])) {
                $environment = $_SERVER['MVC4P_ENV'];
            } elseif (isset($_SERVER['HTTP_HOST']) && isset($_SERVER['SERVER_PORT'])) {
                $environment = $_SERVER['HTTP_HOST'] . $_SERVER['SERVER_PORT'];
            }
            if ($environment === null || empty($environment)) {
                // throw new InvalidConfigException('Unable to determine environment');
                return;
            }
        }
        self::$environment = $environment;

        $configPath = $projectDir . '/config/env';

        if (is_dir($configPath . DIRECTORY_SEPARATOR . $environment)) {
            self::setConfigByPath($configPath, $environment);
            return;
        }

        $envFile = $configPath . '/env.toml';
        if (! is_file($envFile)) {
            return;
        }
        $envList = Toml::parseFile($envFile);
        if ($envList === null || empty($envList)) {
            // throw new InvalidConfigException('No environment is defined in "env.toml".');
            return;
        }

        $envSelector = $environment;
        $environment = null;
        if (isset($envList[$envSelector])) {
            $environment = $envList[$envSelector];
        } else {
            foreach ($envList as $key => $value) {
                if (fnmatch($key, $envSelector)) {
                    $environment = $value;
                    break;
                }
            }
        }
        if ($environment === null || empty($environment)) {
            // throw new InvalidConfigException(sprintf('No environment is defined for selector "%s".', $envSelector));
            return;
        }
        self::$environment = $environment;

        if (is_dir($configPath . DIRECTORY_SEPARATOR . $environment)) {
            self::setConfigByPath($configPath, $environment);
            return;
        }

        // throw new InvalidConfigException(sprintf('Environment "%s" is not defined.', $environment));
    }

    /**
     * Get environment name
     *
     * @return string
     */
    public static function environment(): ?string
    {
        return self::$environment;
    }

    /**
     * Check if in debug mode
     *
     * @return boolean
     */
    public static function isDebug(): bool
    {
        return self::get('app', 'debug') === true;
    }

    /**
     * Get all configuration options of section
     *
     * @param string $section
     *            config section name
     * @return array
     */
    public static function getAll($section): array
    {
        if (isset(self::$config[$section])) {
            return self::$config[$section];
        }
        // throw new InvalidConfigException(sprintf('Missing configuration section "%s".', $section));
        return [];
    }

    /**
     * Get a configuratin option
     *
     * @param string $section
     *            config section name
     * @param string $option
     *            config option in section
     * @return mixed|null
     */
    public static function get($section, $option)
    {
        $config = self::getAll($section);
        if (isset($config[$option])) {
            return $config[$option];
        }
        // throw new InvalidConfigException(
        // sprintf('Missing configuration option "%s" in section "%s".', $option, $section));
        return null;
    }

    private static function setConfigByPath($configPath, $environment)
    {
        $config = [];
        foreach (glob($configPath . DIRECTORY_SEPARATOR . $environment . '/*.toml') as $configFile) {
            $conf = Toml::parseFile($configFile);
            if (is_array($conf)) {
                $config = ArrayUtils::mergeRecursive($config, $conf);
            }
            // throw new InvalidConfigException(sprintf('Error loading config file "%s".', $configFile));
        }

        self::$config = ArrayUtils::mergeRecursive(self::$config, $config);
        self::$environment = $environment;
    }
}
