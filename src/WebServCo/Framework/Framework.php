<?php

declare(strict_types=1);

namespace WebServCo\Framework;

use WebServCo\Framework\Exceptions\ApplicationException;

final class Framework
{
    public const TYPE_FRAMEWORK = 'Framework';
    public const TYPE_PROJECT = 'Project';

    /**
     * Stores Project Library instances.
     *
     * @var array<string,\WebServCo\Framework\Interfaces\LibraryInterface>
     */
    protected static array $projectLibraries = [];

    /**
     * Stores Framework Library instances.
     *
     * @var array<string,\WebServCo\Framework\Interfaces\LibraryInterface>
     */
    private static array $frameworkLibraries = [];

    /**
     * Returns the path the framework project is located in.
     */
    public static function getPath(): string
    {
        return \str_replace(
            \sprintf('src%sWebServCo%sFramework', \DIRECTORY_SEPARATOR, \DIRECTORY_SEPARATOR),
            '',
            __DIR__
        );
    }

    /**
    * @return mixed
    */
    public static function library(string $className, ?string $storageKey = null, ?string $configName = null)
    {
        $fullClassName = self::getFullClassName($className, self::TYPE_FRAMEWORK);

        $storageKey ??= $fullClassName;

        if (!isset(self::$frameworkLibraries[$storageKey])) {
            self::$frameworkLibraries[$storageKey] = self::loadLibrary($className, $fullClassName, $configName);
        }

        return self::$frameworkLibraries[$storageKey];
    }

    /**
    * @return mixed
    */
    public static function projectLibrary(string $className, ?string $storageKey = null, ?string $configName = null)
    {
        $fullClassName = self::getFullClassName($className, self::TYPE_PROJECT);

        $storageKey ??= $fullClassName;

        if (!isset(self::$projectLibraries[$storageKey])) {
            self::$projectLibraries[$storageKey] = self::loadLibrary($className, $fullClassName, $configName);
        }

        return self::$projectLibraries[$storageKey];
    }

    protected static function loadHelper(string $className): bool
    {
        $path = \sprintf(
            '%ssrc%sWebServCo%sFramework%sLibraryHelpers%s%sHelper.php',
            self::getPath(),
            \DIRECTORY_SEPARATOR,
            \DIRECTORY_SEPARATOR,
            \DIRECTORY_SEPARATOR,
            \DIRECTORY_SEPARATOR,
            $className
        );

        if (!\is_readable($path)) {
            throw new ApplicationException(
                \sprintf('Helper for %s Library not found.', $className)
            );
        }
        require $path;
        return true;
    }

    private static function getFullClassName(string $className, ?string $classType = null): string
    {
        switch ($classType) {
            case self::TYPE_FRAMEWORK:
                return \sprintf('\\%s\\Libraries\\%s', __NAMESPACE__, $className);
            case self::TYPE_PROJECT:
                return \sprintf('\\Project\\Libraries\\%s', $className);
            default:
                return $className;
        }
    }

    /**
    * @return array<mixed>
    */
    private static function loadLibraryConfiguration(string $configName): array
    {
        if ('Config' === $configName) {
            return [];
        }
        $projectPath = self::library('Config')->get(
            \sprintf(
                'app%1$spath%1$sproject',
                \WebServCo\Framework\Settings::DIVIDER
            )
        );
        if (empty($projectPath)) {
            return [];
        }
        return self::library('Config')->load($configName, $projectPath);
    }

    /**
    * @return mixed
    */
    private static function loadLibrary(string $className, string $fullClassName, ?string $configName = null)
    {
        if (!\class_exists($fullClassName)) {
            throw new ApplicationException(
                \sprintf('Library %s not found.', $fullClassName)
            );
        }

        switch ($className) {
            case 'I18n':
                self::loadHelper($className);
                break;
        }

        $configName ??= $className;
        $config = self::loadLibraryConfiguration($configName);
        /**
         * Libraries can have custom parameters to constructor,
         * however the configuration array is always the first.
         * $args = is_array($args) ? array_merge([$config], $args): [$config];
         */
        switch ($className) {
            case 'Request':
                // phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
                $args = [$config, $_SERVER, $_POST];
                break;
            default:
                $args = [$config];
                break;
        }

        $reflection = new \ReflectionClass($fullClassName);
        return $reflection->newInstanceArgs($args);
    }
}
