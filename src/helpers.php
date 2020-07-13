<?php

if (! function_exists('notice')) {
    /**
     * Get the available notice Manager instance.
     *
     * @return \Rumur\WordPress\Notice\Manager
     */
    function notice()
    {
        return app('rumur_wp_notice');
    }
}