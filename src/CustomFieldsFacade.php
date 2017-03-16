<?php
namespace Webmachine\CustomFields;

use Illuminate\Support\Facades\Facade;

class CustomFieldsFacade extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'custom_fields';
    }
}