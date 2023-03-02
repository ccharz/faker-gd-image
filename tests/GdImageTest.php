<?php declare(strict_types=1);

namespace Faker\Tests\Provider;

use Faker\Factory as FakerFactory;
use Faker\Provider\GdImage;
use PHPUnit\Framework\TestCase;

class GdImageTest extends TestCase
{
    public static function gdImageParameterProvider(): array
    {
        return [
            'basic' => [
                [],
                640,
                480,
                'image/png'
            ],
            'all' => [
                [
                    null,
                    300,
                    200,
                    'animal',
                    true,
                    true,
                    'word-test',
                    true,
                    'jpg'
                ],
                300,
                200,
                'image/jpeg'
            ],
        ];
    }

    /**
     * @dataProvider gdImageParameterProvider
     * 
     * @param array $parameters
     * @param int $width
     * @param int $height
     * @param string $mime
     * 
     * @return void
     */
    public function testGeneric(array $parameters, int $width, int $height, string $mime): void
    {
        $faker = FakerFactory::create();
        $provider = new GdImage($faker);
        $result = $provider->gdImage(...$parameters);

        $this->assertNotNull($result);
        $this->assertTrue(\file_exists($result));

        $image_info = \getimagesize($result);

        $this->assertSame($width, $image_info[0]);
        $this->assertSame($height, $image_info[1]);
        $this->assertSame($mime, $image_info['mime']);
        $this->assertSame(0, strpos($result, sys_get_temp_dir()));
        unlink($result);
    }

    /**
     * @return void
     */
    public function testFullPath(): void
    {
        $faker = FakerFactory::create();
        $provider = new GdImage($faker);
        $result = $provider->gdImage(null, 640, 480, null, false);

        $this->assertStringNotContainsString(sys_get_temp_dir(), $result);
        unlink(sys_get_temp_dir() . '/' . $result);
    }

    /**
     * @return void
     */
    public function testGrayBackground(): void
    {
        $faker = \Faker\Factory::create();
        $faker->addProvider(new \Faker\Provider\GdImage($faker));
        $gd_image = $faker->gdImageObject(200, 150, 'ASD asdjajsdjkasdnasd asd as d asd as d asd as d asd asdsaadsasdasdasd asdasd');

        $this->assertNotNull($gd_image);

        $bg_color = \imagecolorat($gd_image, 0, 0);
        $colors = \imagecolorsforindex($gd_image, $bg_color);

        \imagedestroy($gd_image);

        $this->assertSame(204, $colors['red']);
        $this->assertSame(204, $colors['green']);
        $this->assertSame(204, $colors['blue']);
    }

    /**
     * @return void
     */
    public function testNotWritableException(): void
    {
        $this->expectException('InvalidArgumentException');
        $faker = FakerFactory::create();
        $provider = new GdImage($faker);
        $provider->gdImage(__FILE__, 200, 150, 'test', true, false, null, false);
    }
}