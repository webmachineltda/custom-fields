<?php
namespace Webmachine\CustomFields;

use Illuminate\Support\ServiceProvider;

class CustomFieldsServiceProvider extends ServiceProvider {
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot() {
        if (! class_exists('CustomFieldsSetupTables')) {
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__ . '/database/migrations/custom_fields_setup_tables.php.stub' => database_path("migrations/{$timestamp}_custom_fields_setup_tables.php"),
            ], 'migrations');
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register() {
        return \App::bind('custom_fields', function(){
            return new CustomFields();
        });
    }
}