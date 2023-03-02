<?php declare(strict_types=1);

namespace Faker\Provider;

class GdImage extends Base
{   
    /**
     * @param string $hex
     * 
     * @return array
     */
    protected function splitHexString(string $hex): array
    {
        $hex = \str_replace('#', '', $hex);

        if (strlen($hex) == 3) {
            $hex = \str_repeat(\substr($hex, 0, 1), 2)
                .\str_repeat(\substr($hex, 1, 1), 2)
                .\str_repeat(\substr($hex, 2, 1), 2);
        }

        $color_parts = \str_split($hex, 2);

        $r = \hexdec($color_parts[0]);
        $g = \hexdec($color_parts[1]);
        $b = \hexdec($color_parts[2]);

        return [
            $r,
            $g,
            $b
        ];
    }

    /**
     * @link https://stackoverflow.com/a/42921358/10611740
     *
     * @param array $hex
     *
     * @return array
     */
    protected function luminosityContrast(array $hex): array
    {
        [$r, $g, $b] = $hex;

        $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;

        return ($yiq >= 128) ? [0, 0, 0] : [255, 255, 255];
    }

    /**
     * @param int $width
     * @param int $height
     * @param string|null $text
     * @param bool $gray
     * 
     * @return \GdImage
     */
    public function gdImageObject(
        int $width = 640,
        int $height = 480,
        ?string $text = null,
        string $background_color = 'CCC'
    ): \GdImage
    {
        $image = \imagecreate($width, $height);

        $background_color_rgb = $this->splitHexString($background_color);

        \imagecolorallocate(
            $image,
            $background_color_rgb[0],
            $background_color_rgb[1],
            $background_color_rgb[2]
        );

        if (!empty($text)) {
            $font =  __DIR__ . DIRECTORY_SEPARATOR . 'opensans' . DIRECTORY_SEPARATOR . 'OpenSans-Regular.ttf';
            $text_color_rgb = $this->luminosityContrast($background_color_rgb);
            $text_color = \imagecolorallocate($image, ...$text_color_rgb);
            $text_x = 0;
            $text_y = 0;

            if (\function_exists('imageftbbox')) {
                $font_size = 15;
                $font_angle = 0;
                $text_bbox = \imageftbbox($font_size, $font_angle, $font, $text);

                $text_x = max(0, intval($text_bbox[0] + ($width / 2) - ($text_bbox[4] / 2) - ($font_size / 2)));
                $text_y = max(0, intval($text_bbox[1] + ($height / 2) - ($text_bbox[5] / 2) - ($font_size / 2)));
                \imagefttext($image, $font_size, $font_angle, $text_x, $text_y, $text_color, $font, $text);
                
            } else {
                \imagestring($image, 5, $text_x, $text_y, $text, $text_color);
            }
        }        

        return $image;
    }

    /**
     * Generate a random image, store it on the disk and return its location
     *
     * The function signature is identical to the https://fakerphp.github.io/formatters/image/ so
     * it can be used as a direct replacement to the original function
     *
     * @param string|null $dir Directory where the file will be stored
     * @param int $width
     * @param int $height
     * @param string|null $category
     * @param bool $fullPath
     * @param bool $randomize
     * @param string $word
     * @param bool $gray
     * @param string $format File Format (Supported are png and jpeg)
     * 
     * @return string
     */
    public function gdImage(
        ?string $dir = null,
        int $width = 640,
        int $height = 480,
        ?string $category = null,
        bool $fullPath = true,
        bool $randomize = true,
        ?string $word = null,
        bool $gray = false,
        string $format = 'png'
    ): string
    {
        $dir = null === $dir ? sys_get_temp_dir() : $dir; // GNU/Linux / OS X / Windows compatible

        if (!is_dir($dir) || !is_writable($dir)) {
            throw new \InvalidArgumentException(sprintf('Cannot write to directory "%s"', $dir));
        }

        $name = md5(uniqid(empty($_SERVER['SERVER_ADDR']) ? '' : $_SERVER['SERVER_ADDR'], true));
        $filename = $name . '.' . $format;
        $filepath = $dir . DIRECTORY_SEPARATOR . $filename;

        $image_text = ''
            . ($category ? ' ' . $category : '')
            . ($word ? ' ' . $word : '')
            . ($randomize ? ' ' . $this->generator->word() : '');

        $background_color = $gray === true ? 'CCC' : $this->generator->safeHexColor();

        $gd_image = $this->gdImageObject($width, $height, $image_text, $background_color);

        switch ($format) {
            case 'png':
                \imagepng($gd_image, $filepath);
                break;
            case 'jpeg':
            case 'jpg':
                \imagejpeg($gd_image, $filepath);
                break;
        }

        \imagedestroy($gd_image);

        return $fullPath ? $filepath : $filename;
    }
}