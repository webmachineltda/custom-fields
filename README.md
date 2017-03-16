# CustomFields for Laravel 5

## Install

Via Composer

``` bash
$ composer require webmachine/custom-fields
```

Next, you must install the service provider and facade alias:

```php
// config/app.php
'providers' => [
    ...
    Webmachine\CustomFields\CustomFieldsServiceProvider::class,
];

...

'aliases' => [
    ...
    'CustomFields' => Webmachine\CustomFields\CustomFieldsFacade::class,
];
```

Publish

``` bash
$ php artisan vendor:publish --provider="Webmachine\CustomFields\CustomFieldsServiceProvider"
```

## Usage

In your Controller, save your custom fields for a given table record:
``` php
...
use Webmachine\CustomFields\CustomFieldsFacade as CustomFields;

class FooController extends Controller {
    ...
    public function storage() {
        ...
        $foo->save();
        CustomFields::save($foo->id);
    }
}
```

In your Request, validate your cutom field
``` php
...
use Webmachine\CustomFields\CustomFieldsFacade as CustomFields;

class FooRequest extends Request {
    ...
    public function rules() {
        $rules = [
            ...
        ];       
        $custom_rules = CustomFields::rules('table', 'form_scope');      
        return array_merge($rules, $custom_rules);
    }
    ...
    public function attributes() {
        $attributes = [];
        $custom_attributes = CustomFields::attributes('table', 'form_scope');
        return array_merge($attributes, $custom_attributes);
    }
}
```

In your view
```blade
{!! CustomFields::show('table', 'form_scope') !!}
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
