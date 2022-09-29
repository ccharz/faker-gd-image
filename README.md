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

// Stores a generated image in the systems tmp folder
$image_path = $faker->gdImage($dir = null, $width = 640, $height = 480);

// Returns a gd image object
$gd_image = $faker->gdImageObject($width = 640, $height = 480, $text = 'Test', $background_color = '6A6A6A');
```

## Usage in Laravel

To use it in laravel factories you could add this to the `AppServiceProvider.php`:

```php
/**
 * Register any application services.
 *
 * @return void
 */
public function register()
{
    $this->app->singleton(\Faker\Generator::class, function () {
        $faker = \Faker\Factory::create();
        $faker->addProvider(new \Faker\Provider\GdImage($faker));
        return $faker;
    });
}
```

## Acknowledgment

This project uses https://github.com/googlefonts/opensans which is published under the SIL Open Font License https://github.com/googlefonts/opensans/blob/main/OFL.txt 