<?php

require __DIR__ . '/../vendor/autoload.php';

if (!function_exists('\\get_site_option')):
    function get_site_option($option, $default = false)
    {
        return $default;
    }
endif;

if (!function_exists('\\update_site_option')):
    function update_site_option($option, $value)
    {
        return true;
    }
endif;

if (!function_exists('\\delete_site_option')):
    function delete_site_option($option)
    {
        return true;
    }
endif;

if (!function_exists('\\current_datetime')):
    function current_datetime()
    {
        return new DateTimeImmutable('now');
    }
endif;

if (!function_exists('\\get_role')):
    function get_role($role)
    {
        return true;
    }
endif;

if (!function_exists('\\taxonomy_exists')):
    function taxonomy_exists($taxonomy)
    {
        return true;
    }
endif;

if (!function_exists('\\post_type_exists')):
    function post_type_exists($post_type)
    {
        return true;
    }
endif;

if (!function_exists('\\current_user_can')):
    function current_user_can($capability, ...$args)
    {
        return true;
    }
endif;

if (!function_exists('\\get_current_user_id')):
    function get_current_user_id()
    {
        return 1;
    }
endif;

if (!function_exists('\\rmr_test_notice')):
    function rmr_test_notice()
    {
        return '<p>Hello from Testing Function</p>';
    }
endif;

if (!function_exists('\\get_current_screen')):
    function get_current_screen()
    {
        return (object)[
            'base' => 'tools',
            'taxonomy' => 'category',
            'post_type' => 'post',
            'parent_base' => 'tools',
            'parent_file' => 'tools.php',
            'is_user' => false,
            'is_network' => false,
            'is_block_editor' => false,
        ];
    }
endif;
