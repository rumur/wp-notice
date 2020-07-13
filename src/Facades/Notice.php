<?php

namespace Rumur\WordPress\Notice\Facades;

use Illuminate\Support\Facades\Facade;
use Rumur\WordPress\Notice\Manager;
use Rumur\WordPress\Notice\Noticeable;
use Rumur\WordPress\Notice\PendingNotice;

/**
 * Class Notice
 *
 * @package Rumur\WordPress\Notice\Facades
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
class Notice extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'rumur_wp_notice';
    }
}
