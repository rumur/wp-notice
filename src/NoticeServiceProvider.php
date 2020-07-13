<?php

namespace Rumur\WordPress\Notice;

use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;

class NoticeServiceProvider extends ServiceProvider
{
    /**
     * Register notice services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('rumur_wp_notice', function () {
            // As far as some of the info of notices will be stored in the options table
            // we need to make sure that the `option_name` is fit to 191 chars long.
            $app_name = Str::limit(Str::snake($this->app->make('config')->get('app.name')), 180, '');

            return new Manager(new Repository("{$app_name}_notices"), new Renderer());
        });
    }
}
