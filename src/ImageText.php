<?php
/**
 * Wrapper for PHP's GD Library for easy image manipulation to resize, crop
 * and draw images on top of each other preserving transparency, writing text
 * with transparency and drawing shapes.
 * php version 7.1
 *
 * @category PHP
 * @package  ImageText
 * @author   Anton Lukin <anton@lukin.me>
 * @license  MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link     https://github.com/antonlukin/image-text
 */

namespace ImageText;

use Exception;

 /**
  * Draw images, text and shapes using php-gd.
  *
  * @category PHP
  * @package  ImageText
  * @author   Anton Lukin <anton@lukin.me>
  * @license  MIT License (http://www.opensource.org/licenses/mit-license.php)
  * @version  Release: 1.0.0
  * @link     https://github.com/antonlukin/image-text
  */
class ImageText
{
    /**
     * Canvas resource
     *
     * @var resource
     */
    protected $resource;

    /**
     * Canvas width
     *
     * @var integer
     */
    protected $width;

    /**
     * Canvas height
     *
     * @var integer
     */
    protected $height;

    /**
     * Image type
     *
     * @var integer
     */
    protected $type;

    /**
     * Initialise the image.
     */
    public function __construct()
    {
        if (!extension_loaded('gd') && !extension_loaded('gd2')) {
            return new handleError('Extension php-gd is not loaded');
        }
    }

    /**
     * Get image resource to use raw gd commands.
     *
     * @return resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Set image resource after using raw gd commands.
     *
     * @param instance $resource Image resource.
     *
     * @return $this
     */
    public function setResource($resource)
    {
        $this->resource = $resource;

        $this->width = imagesx($resource);
        $this->height = imagesy($resource);

        return $this;
    }

    /**
     * Create resource using file path.
     *
     * @param string  $file Path to image file.
     * @param integer $type Optional. File image type.
     *
     * @return instance
     */
    public function createResource($file, $type = null)
    {
        if (is_null($type)) {
            list($width, $height, $type) = getimagesize($file);
        }

        switch ($type) {
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($file);
                break;

            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($file);
                break;

            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($file);
                break;

            case IMAGETYPE_WEBP:
                $image = imagecreatefromwebp($file);

            default:
                $this->handleError('Unsupported image type');
        }

        return $image;
    }

    /**
     * Make new image instance from file.
     *
     * @param string $file Path to file.
     *
     * @return $this
     */
    public function make($file)
    {
        if (!is_readable($file)) {
            return $this->handleError($file . ' is not a valid image');
        }

        // Get image dimensions.
        list($width, $height, $type) = getimagesize($file);

        $this->canvas($width, $height);
        $this->insert($file);

        $this->type = $type;

        return $this;
    }

    /**
     * Intialise the canvas by width and height.
     * Create square if the height param is empty.
     *
     * @param integer $width  Canvas width.
     * @param integer $height Optional. Canvas height.
     *
     * @return $this
     */
    public function canvas($width, $height = null)
    {
        if ($height === null) {
            $height = $width;
        }

        unset($this->resource);

        $this->resource = imagecreatetruecolor($width, $height);

        // Set the flag to save full alpha channel information
        imagesavealpha($this->resource, true);

        // Turn off transparency blending (temporarily)
        imagealphablending($this->resource, false);

        $color = imagecolorallocatealpha($this->resource, 0, 0, 0, 127);

        // Completely fill the background with transparent color
        imagefilledrectangle($this->resource, 0, 0, $width, $height, $color);

        // Restore transparency blending
        imagealphablending($this->resource, true);

        $this->width = $width;
        $this->height = $height;

        return $this;
    }

    /**
     * Attach image to new HTTP response.
     *
     * Sends HTTP response with current image in given format and quality.
     * Quality is not applied for PNG compression.
     *
     * @param integer $quality Define optionally the quality of the image. From 0 to 100. Default: 90.
     * @param string  $format  File image extension. By default use type from make or insert function.
     *
     * @return void
     */
    public function show($quality = 90, $format = null)
    {
        if (!is_null($format)) {
            $this->setType($format);
        }

        $quality = $this->getParam($quality, 0, 100);

        switch ($this->type) {
            case IMAGETYPE_GIF:
                header('Content-type: image/gif');
                imagegif($this->resource, null);
                break;

            case IMAGETYPE_PNG:
                header('Content-type: image/png');
                imagepng($this->resource, null, min(floor(10 - $quality / 10), 9));
                break;

            case IMAGETYPE_WEBP:
                header('Content-type: image/webp');
                imagewebp($this->resource, null, $quality);
                break;

            default:
                header('Content-type: image/jpeg');
                imagejpeg($this->resource, null, $quality);
                break;
        }

        return $this->cleanup();
    }

    /**
     * Save the image
     *
     * @param string  $path    Path to the file where to write the image data.
     * @param integer $quality Define optionally the quality of the image. From 0 to 100. Default: 90.
     * @param string  $format  File image extension. By default use from path.
     *
     * @return $this
     */
    public function save($path, $quality = 90, $format = null)
    {
        $folder = dirname($path);

        if (!is_writable($folder)) {
            $this->handleError($folder . ' is not writable');
        }

        if (is_null($format)) {
            $format = pathinfo($path, PATHINFO_EXTENSION);
        }

        if (strlen($format) > 0) {
            $this->setType($format);
        }

        $quality = $this->getParam($quality, 0, 100);

        switch ($this->type) {
            case IMAGETYPE_GIF:
                imagegif($this->resource, $path);
                break;

            case IMAGETYPE_PNG:
                imagepng($this->resource, $path, min(floor(10 - $quality / 10), 9));
                break;

            case IMAGETYPE_WEBP:
                imagewebp($this->resource, $path, $quality);
                break;

            default:
                imagejpeg($this->resource, $path, $quality);
        }

        return $this->cleanup();
    }

    /**
     * Destroy image resource.
     *
     * @return void
     */
    public function cleanup()
    {
        imagedestroy($this->resource);
    }

    /**
     * Returns the height in pixels of the current image.
     *
     * @return int
     */
    public function height()
    {
        return $this->height;
    }

    /**
     * Returns the width in pixels of the current image.
     *
     * @return int
     */
    public function width()
    {
        return $this->width;
    }

    /**
     * Resize image to desired dimensions.
     *
     * Resizes current image based on given width and height.
     * Scale param set constraint the current aspect-ratio of the image.
     *
     * @param integer $width  Target image width.
     * @param integer $height Target image height.
     * @param boolean $scale  Optional. Constraint the current aspect-ratio of the image.
     * @param boolean $upsize Optional. Keep image from being upsized. Aplies on scale true.
     *
     * @return $this
     */
    public function resize($width, $height, $scale = true, $upsize = true)
    {
        $info = array(
            'width'  => $this->width,
            'height' => $this->height,
        );

        if ($scale) {
            $ratio = $info['width'] / $info['height'];

            if ($upsize) {
                if ($width / $height > $ratio) {
                    $width = intval($height * $ratio);
                } else {
                    $height = intval($width / $ratio);
                }
            } else {
                if ($width / $height > $ratio) {
                    $height = intval($width / $ratio);
                } else {
                    $width = intval($height * $ratio);
                }
            }
        }

        $temp = $this->resource;
        $this->canvas($width, $height);

        imagecopyresampled($this->resource, $temp, 0, 0, 0, 0, $width, $height, $info['width'], $info['height']);
        imagedestroy($temp);

        return $this;
    }

    /**
     * Crop an image.
     *
     * Cut out a rectangular part of the current image with given width and height.
     * Define optional x,y coordinates to move the top-left corner of the cutout to a certain position.
     *
     * @param integer      $width  Width of the rectangular cutout.
     * @param integer      $height Height of the rectangular cutout.
     * @param integer|null $x      Optional. X coordinate from left. By default will be centered on the current image.
     * @param integer|null $y      Optional. Y coordinate from top. By default will be centered on the current image.
     *
     * @return $this
     */
    public function crop($width, $height, $x = null, $y = null)
    {
        $info = array(
            'width'  => $this->width,
            'height' => $this->height,
        );

        $ratio = $info['width'] / $info['height'];

        if (is_null($x)) {
            $x = intval(($info['width'] - $width) / 2);
        }

        if (is_null($y)) {
            $y = intval(($info['height'] - $height) / 2);
        }

        $temp = $this->resource;
        $this->canvas($width, $height);

        imagecopyresampled($this->resource, $temp, 0, 0, $x, $y, $width, $height, $width, $height);
        imagedestroy($temp);

        return $this;
    }

    /**
     * Crop and resize combined.
     *
     * Combine cropping and resizing to format image in a smart way.
     * The method will find the best fitting aspect ratio on the current image automatically,
     * cut it out and resize it to the given dimension.
     *
     * @param integer $width    Target image width.
     * @param integer $height   Target image height.
     * @param string  $position Optional. Crop position.
     *
     * @return $this
     */
    public function fit($width, $height, $position = 'center')
    {
        // Resize without upsizing.
        $this->resize($width, $height, true, false);

        $info = array(
            'width'  => $this->width,
            'height' => $this->height,
        );

        $x = 0;
        $y = 0;

        switch ($position) {
            case 'top-left':
                break;

            case 'top':
                $x = intval(($info['width'] - $width) / 2);
                break;

            case 'top-right':
                $x = intval($info['width'] - $width);
                break;

            case 'bottom-left':
                $y = intval($info['height'] - $height);
                break;

            case 'bottom':
                $x = intval(($info['width'] - $width) / 2);
                $y = intval($info['height'] - $height);
                break;

            case 'bottom-right':
                $x = intval($info['width'] - $width);
                $y = intval($info['height'] - $height);
                break;

            case 'right':
                $x = intval($info['width'] - $width);
                $y = intval(($info['height'] - $height) / 2);
                break;

            case 'left':
                $y = intval(($info['height'] - $height) / 2);
                break;
            default:
                $x = intval(($info['width'] - $width) / 2);
                $y = intval(($info['height'] - $height) / 2);
        }

        $this->crop($width, $height, $x, $y);

        return $this;
    }

    /**
     * Draw a line from x,y point 1 to x,y point 2 on current image.
     *
     * @param integer $x1      X-Coordinate of the starting point.
     * @param integer $y1      Y-Coordinate of the starting point.
     * @param integer $x2      X-Coordinate of the end point.
     * @param integer $y2      Y-Coordinate of the end point.
     * @param array   $options Optional. List of line options.
     *
     * @return $this
     */
    public function line($x1, $y1, $x2, $y2, $options = array())
    {
        $defaults = array(
            'color'   => array(0, 0, 0),
            'opacity' => 0,
            'width'   => 1,
        );

        $options = array_merge($defaults, $options);

        // Get color from options.
        $color = $this->getColor($options);

        imagesetthickness($this->resource, $options['width']);

        // Draw new line.
        imageline($this->resource, $x1, $y1, $x2, $y2, $color);

        imagesetthickness($this->resource, 1);

        return $this;
    }

    /**
     * Draw a colored rectangle on current image.
     *
     * @param integer $x       X-Coordinate of the starting point.
     * @param integer $y       Y-Coordinate of the starting point.
     * @param integer $width   Width in pixels.
     * @param integer $height  Height in pixels.
     * @param array   $options Optional. List of line options.
     *
     * @return $this
     */
    public function rectangle($x, $y, $width, $height, $options = array())
    {
        $defaults = array(
            'color'   => array(0, 0, 0),
            'opacity' => 0,
            'width'   => 1,
            'outline' => false,
        );

        $options = array_merge($defaults, $options);

        // Get color from options.
        $color = $this->getColor($options);

        imagesetthickness($this->resource, $options['width']);

        if ($options['outline']) {
            imagerectangle($this->resource, $x, $y, $x + $width, $y + $height, $color);
        } else {
            imagefilledrectangle($this->resource, $x, $y, $x + $width, $y + $height, $color);
        }

        imagesetthickness($this->resource, 1);

        return $this;
    }

    /**
     * Draw an ellipse
     *
     * @param integer $x       X-Coordinate of the center point.
     * @param integer $y       Y-Coordinate of the center point.
     * @param integer $width   Width in pixels.
     * @param integer $height  Height in pixels.
     * @param array   $options Optional. List of line options.
     *
     * @return $this
     */
    public function ellipse($x, $y, $width, $height, $options = array())
    {
        $defaults = array(
            'color'   => array(0, 0, 0),
            'opacity' => 0,
            'outline' => false,
        );

        $options = array_merge($defaults, $options);

        // Get color from options.
        $color = $this->getColor($options);

        if ($options['outline']) {
            imageellipse($this->resource, $x, $y, $width, $height, $color);
        } else {
            imagefilledellipse($this->resource, $x, $y, $width, $height, $color);
        }

        return $this;
    }

    /**
     * Change image brightness.
     *
     * Changes the brightness of the current image by the given level.
     * Use values between -100 for min. brightness 0 for no change and +100 for max.
     *
     * @param integer $level Optional. The level of brightness. Default: 0.
     *
     * @return $this
     */
    public function brightness($level = 0)
    {
        $level = $this->getParam($level, -100, 100);

        imagefilter($this->resource, IMG_FILTER_BRIGHTNESS, $level * 2.55);

        return $this;
    }

    /**
     * Change the contrast of an image.
     *
     * Changes the contrast of the current image by the given level.
     * Use values between -100 for min contrast 0 for no change and +100 for max.
     *
     * @param integer $level Optional. The level of contrast. Default: 0.
     *
     * @return $this
     */
    public function contrast($level = 0)
    {
        $level = $this->getParam($level, -100, 100);

        imagefilter($this->resource, IMG_FILTER_CONTRAST, $level);

        return $this;
    }

    /**
     * Turn an image into a grayscale version.
     *
     * @return $this
     */
    public function grayscale()
    {
        imagefilter($this->resource, IMG_FILTER_GRAYSCALE);

        return $this;
    }

    /**
     * Apply a gaussian blur filter with a optional amount on the current image.
     * Note: Performance intensive on larger amounts of blur with GD driver.
     *
     * @param integer $amount Optional. The amount of the blur strength (0-100). Default: 1
     *
     * @return $this
     */
    public function blur($amount = 1)
    {
        $amount = $this->getParam($amount, 0, 100);

        for ($i = 0; $i < intval($amount); $i++) {
            imagefilter($this->resource, IMG_FILTER_GAUSSIAN_BLUR);
        }

        return $this;
    }

    /**
     * Invert colors of an image.
     *
     * Reverses all colors of the current image.
     *
     * @return $this
     */
    public function invert()
    {
        imagefilter($this->resource, IMG_FILTER_NEGATE);

        return $this;
    }

    /**
     * Paste over another image.
     *
     * Paste a given image source over the current image with an optional position and dimensions.
     *
     * @param String $file    Absolute path to image file.
     * @param array  $options Optional. List of image options.
     *
     * @return $this
     */
    public function insert($file, $options = array())
    {
        $defaults = array(
            'x'      => '50%',
            'y'      => '50%',
            'width'  => null,
            'height' => null,
        );

        $options = array_merge($defaults, $options);

        if (!is_readable($file)) {
            return $this->handleError($file . ' is not a valid image');
        }

        list($width, $height, $type) = getimagesize($file);

        $ratio = $width / $height;

        if (is_null($options['width'])) {
            $options['width'] = $width;

            // Try to calc new width by ratio.
            if (!is_null($options['height'])) {
                $options['width'] = $options['height'] * $ratio;
            }
        }

        if (is_null($options['height'])) {
            $options['height'] = $height;

            // Try to calc new height by ratio.
            if (!is_null($options['width'])) {
                $options['height'] = $options['width'] / $ratio;
            }
        }

        if (substr($options['x'], -1) === '%') {
            $options['x'] = ceil(($this->width - $options['width']) * (trim($options['x'], '%') / 100));
        }

        if (substr($options['y'], -1) === '%') {
            $options['y'] = ceil(($this->height - $options['height']) * (trim($options['y'], '%') / 100));
        }

        $temp = $this->createResource($file, $type);

        imagecopyresampled($this->resource, $temp, $options['x'], $options['y'], 0, 0, $options['width'], $options['height'], $width, $height);
        imagedestroy($temp);

        return $this;
    }

    /**
     * Draw text on image.
     *
     * @param string $text     Text strings. Multiline availible.
     * @param array  $options  Optional. List of text settings.
     * @param array  $boundary Optional. Actual dimensions of the drawn text box.
     *
     * @return $this
     */
    public function text($text, $options = array(), &$boundary = array())
    {
        $defaults = array(
            'x'          => 0,
            'y'          => 0,
            'width'      => null,
            'height'     => null,
            'fontsize'   => 48,
            'color'      => array(0, 0, 0),
            'lineheight' => '1.5',
            'opacity'    => 1,
            'horizontal' => 'left',
            'vertical'   => 'top',
            'fontfile'   => null,
            'debug'      => false,
        );

        $options = array_merge($defaults, $options);

        if ($options['debug']) {
            $this->drawDebug($options);
        }

        $color = $this->getColor($options);

        // Get wrapped text and updated font-size.
        $text = $this->wrapText($text, $options);

        // Get text lines as array.
        $lines = explode("\n", $text);

        // Set default boundary vaules.
        $boundary = array_merge(array('width' => 0, 'height' => 0));

        foreach ($lines as $index => $line) {
            list($x, $y, $width, $height) = $this->getOffset($options, $lines, $index);

            // Draw text line.
            imagefttext($this->resource, $options['fontsize'], 0, $x, $y, $color, $options['fontfile'], $line);

            $boundary = array(
                'width'  => max($width, $boundary['width']),
                'height' => $boundary['height'] + $height,
            );
        }

        return $this;
    }

    /**
     * Wrap text to box and update font-size if necessary.
     *
     * @param string $text    Text to draw.
     * @param array  $options Optional. List of image options.
     *
     * @return string
     */
    protected function wrapText($text, &$options)
    {
        do {
            $wrapped = $this->addBreaklines($text, $options);

            // Get lines from wrapped text.
            $lines = explode("\n", $wrapped);

            // Sum of all lines heights.
            $height = $options['fontsize'] * $options['lineheight'] * count($lines);

            if ($height <= $options['height']) {
                break;
            }

            $options['fontsize'] = $options['fontsize'] - 1;
        } while ($options['fontsize'] > 0);

        return $wrapped;
    }

    /**
     * Add break line to text according font settings.
     *
     * @param string $text    Text to draw.
     * @param array  $options Optional. List of image options.
     * @param string $output  Optional. Non-breaklined output.
     *
     * @return string
     */
    protected function addBreaklines($text, $options, $output = '')
    {
        $words = explode(' ', $text);

        foreach ($words as $word) {
            $sentence = $output . ' ' . $word;

            if (strlen($output) === 0) {
                $sentence = $word;
            }

            $box = imageftbbox($options['fontsize'], 0, $options['fontfile'], $sentence);

            if ($box[2] > $options['width']) {
                $output = $output . PHP_EOL . $word;

                continue;
            }

            $output = $sentence;
        }

        return $output;
    }

    /**
     * Get color from options array using opacity.
     *
     * @param array $options List of image options.
     *
     * @return integer
     */
    protected function getColor($options)
    {
        $rgb = $options['color'];

        if (is_string($rgb)) {
            $rgb = sscanf(ltrim($rgb, '#'), "%02x%02x%02x");
        }

        $opacity = $options['opacity'] / 100 * 127;

        // Create image color width opacity.
        return imagecolorallocatealpha($this->resource, $rgb[0], $rgb[1], $rgb[2], $opacity);
    }

    /**
     * Get param using min max values.
     *
     * @param integer $value Initial value.
     * @param integer $min   Minimulm value.
     * @param integer $max   Maximum value.
     *
     * @return integer
     */
    protected function getParam($value, $min, $max)
    {
        $value = (int) $value;

        return max(min($value, $max), $min);
    }

    /**
     * Get offset for text to draw.
     *
     * @param integer $options List of image options.
     * @param array   $lines   List of text lines.
     * @param integer $index   Current line index in the loop.
     *
     * @return array
     */
    protected function getOffset($options, $lines, $index)
    {
        $box = imageftbbox($options['fontsize'], 0, $options['fontfile'], $lines[$index]);

        $width  = abs($box[6] - $box[4]);
        $height = $options['fontsize'] * $options['lineheight'];

        $x = $options['x'];
        $y = $options['y'] + ($index + 1) * $height;

        if ($index === 0) {
            $y = $options['y'] + $options['fontsize'] + (($height - $options['fontsize']) / 2);
        }

        switch ($options['horizontal']) {
            case 'center':
                $x = $x + (($options['width'] - $width) / 2);
                break;

            case 'right':
                $x = $x + ($options['width'] - $width);
                break;
        }

        switch ($options['vertical']) {
            case 'center':
                $y = $y + (($options['height'] - ($height * count($lines))) / 2);
                break;

            case 'bottom':
                $y = $y + ($options['height'] - ($height * count($lines)));
                break;
        }

        return array($x, $y, $width, $height);
    }

    /**
     * Draw debug box for text by options.
     *
     * @param array $options List of text options.
     *
     * @return void
     */
    protected function drawDebug($options)
    {
        $styles = array(
            'color'   => array(rand(150, 255), rand(150, 255), rand(150, 255)),
            'opacity' => 50,
        );

        $this->rectangle($options['x'], $options['y'], $options['width'], $options['height'], $styles);
    }

    /**
     * Set output image type using file format.
     *
     * @param string $format File format extension. Can be jpg, gif or png.
     *
     * @return void
     */
    protected function setType($format)
    {
        $format = strtolower($format);

        switch ($format) {
            case 'gif':
                $this->type = IMAGETYPE_GIF;
                break;

            case 'jpg':
                $this->type = IMAGETYPE_JPEG;
                break;

            case 'png':
                $this->type = IMAGETYPE_PNG;
                break;

            case 'webp':
                $this->type = IMAGETYPE_WEBP;
                break;
        }
    }

    /**
     * Handle errors
     *
     * @param string $error Error message.
     *
     * @return Exception
     */
    protected function handleError($error)
    {
        throw new Exception($error);
    }
}
