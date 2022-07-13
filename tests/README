# Faker-GD-Image

FakerPHP (https://fakerphp.github.io/) Provider to create random images with the help of gd. This provider uses the same function signature as the build-in image provider image function (https://fakerphp.github.io/formatters/image/#image_1) so it can be used as a direct replacement

## Installation

```sh
composer require ccharz/faker-gd-image
```

## Usage
```php
$faker = \Faker\Factory::create();
$faker->addProvider(new \Faker\Provider\GdImage($faker));

$image_path = $provider->gdImage(null, 640, 480);
```

## Usage in Laravel

To use it in laravel factories you can add this to the `AppServiceProvider.php` boot method:

```php
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $faker = $this->app->make(\Faker\Generator::class);
        $faker->addProvider(new \Faker\Provider\GdImage($faker));
    }
```

## Acknowledgment

This project uses https://github.com/googlefonts/opensans which is published under the SIL Open Font License https://github.com/googlefonts/opensans/blob/main/OFL.txt 