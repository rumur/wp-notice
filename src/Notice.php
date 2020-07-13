<?php

namespace Rumur\WordPress\Notice;

/**
 * Class Notice
 *
 * @package Rumur\WordPress\Notice
 *
 * @method static void render()
 * @method static Manager flush()
 * @method static Manager withoutWrapping()
 * @method static Manager resolveConditions(callable $checker)
 * @method static Manager registerIntoWordPress(string $action = 'all_admin_notices', int $priority = 10)
 * @method static PendingNotice info(string|Noticeable $info, $dismissible = false)
 * @method static PendingNotice warning(string|Noticeable $warning, $dismissible = false)
 * @method static PendingNotice success(string|Noticeable $success, $dismissible = false)
 * @method static PendingNotice error(string|Noticeable|\WP_Error $error, $dismissible = false)
 */
class Notice
{
    /**
     * Default option name.
     *
     * @var string|null
     */
    protected static $name;

    /** @var Manager[] */
    protected static $instances;

    /**
     * Factory method.
     *
     * @param string|null $name
     *
     * @return Manager
     */
    public static function make(string $name): Manager
    {
        if (isset(static::$instances[$name])) {
            return static::$instances[$name];
        }

        return static::$instances[$name] = new Manager(new Repository($name), new Renderer());
    }

    /**
     * Facade that just is proxying to Manager.
     *
     * @param $method
     * @param $arguments
     * @return mixed|null
     */
    public static function __callStatic($method, $arguments)
    {
        // Default manager
        static $manger;

        if (! $manger) {
            /**
             * We're getting a substring with length max 190,
             * because the max length of the name in DB could only be 191 long.
             */
            $name = static::$name ?? substr(Utils\Str::snake(static::class), 0, 190);

            $manger = static::make($name);
        }

        if (! method_exists($manger, $method)) {
            throw new \InvalidArgumentException(sprintf('The `%s` method not exists', $method));
        }

        return $manger->$method(...$arguments);
    }
}
