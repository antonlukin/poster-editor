<?php
/**
 * Wrapper for PHP's GD Library for easy image manipulation to resize, crop
 * and draw images on top of each other preserving transparency, writing text
 * with transparency and drawing shapes.
 * php version 7.1
 *
 * @category PHP
 * @package  PosterEditor
 * @author   Anton Lukin <anton@lukin.me>
 * @license  MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link     https://github.com/antonlukin/poster-editor
 */

namespace PosterEditor;

use Exception;

 /**
  * Draw images, text and shapes using php-gd.
  *
  * @category PHP
  * @package  PosterEditor
  * @author   Anton Lukin <anton@lukin.me>
  * @license  MIT License (http://www.opensource.org/licenses/mit-license.php)
  * @version  Release: 5.4
  * @link     https://github.com/antonlukin/poster-editor
  */
class PosterEditor
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
    public function get()
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
    public function set($resource)
    {
        $this->resource = $resource;

        $this->width = imagesx($resource);
        $this->height = imagesy($resource);

        return $this;
    }

    /**
     * Make new image instance from file or binary data.
     *
     * @param string $data Binary data or path to file.
     *
     * @return $this
     */
    public function make($data)
    {
        switch (true) {
            case $this->isBinary($data):
                $image = $this->createFromString($data);
                break;

            default:
                $image = $this->createFromFile($data);
        }

        list($width, $height, $type, $source) = $image;

        $this->copyResampled($source, 0, 0, 0, 0, $width, $height, $width, $height);
        $this->type = $type;

        return $this;
    }

    /**
     * Paste over another image.
     *
     * Paste a given image source over the current image with an optional position.
     *
     * @param string $data    Binary data or path to file or another class instance.
     * @param array  $options List of x/y relative offset coords from top left corner. Default: centered.
     *
     * @return $this
     */
    public function insert($data, $options = array())
    {
        $defaults = array(
            'x' => null,
            'y' => null,
        );

        $options = array_merge($defaults, $options);

        switch (true) {
            case $this->isInstance($data):
                $image = $this->createFromInstance($data);
                break;

            case $this->isBinary($data):
                $image = $this->createFromString($data);
                break;

            default:
                $image = $this->createFromFile($data);
        }

        list($width, $height, $type, $source) = $image;

        $options = $this->calcPosition($options, $width, $height);

        imagecopyresampled($this->resource, $source, $options['x'], $options['y'], 0, 0, $width, $height, $width, $height);
        imagedestroy($source);

        return $this;
    }

    /**
     * Intialise the canvas by width and height.
     *
     * @param integer $width   Canvas width.
     * @param integer $height  Canvas height.
     * @param string  $options Optional. Background color options. Default: black.
     *
     * @return $this
     */
    public function canvas($width, $height, $options = array())
    {
        $defaults = array(
            'color'   => array(0, 0, 0),
            'opacity' => 100,
        );

        $options = array_merge($defaults, $options);

        unset($this->resource);

        $this->resource = imagecreatetruecolor($width, $height);

        // Set the flag to save full alpha channel information
        imagesavealpha($this->resource, true);

        // Turn off transparency blending (temporarily)
        imagealphablending($this->resource, false);

        // Get color from options.
        $color = $this->getColor($options);

        // Completely fill the background with transparent color
        imagefilledrectangle($this->resource, 0, 0, $width, $height, $color);

        // Restore transparency blending
        imagealphablending($this->resource, true);

        $this->width = $width;
        $this->height = $height;

        return $this;
    }

    /**
     * Sends HTTP response with current image in given format and quality.

     * @param string  $format  Optional. File image extension. By default used type from make or insert function.
     * @param integer $quality Optional. Define optionally the quality of the image. From 0 to 100. Default: 90.
     *
     * @return void
     */
    public function show($format = null, $quality = 90)
    {
        $this->setType($format);

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
    }

    /**
     * Save the image.
     *
     * @param string  $path    Path to the file where to write the image data.
     * @param integer $quality Optional. Define optionally the quality of the image. From 0 to 100. Default: 90.
     * @param string  $format  Optional. File image extension. By default use from path.
     *
     * @return $this
     */
    public function save($path, $quality = 90, $format = null)
    {
        $folder = dirname($path);

        if (!is_writable($folder)) {
            return $this->handleError('Folder is not writable');
        }

        if (empty($format)) {
            $format = pathinfo($path, PATHINFO_EXTENSION);
        }

        $this->setType($format);

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
    }

    /**
     * Destroy image resource.
     *
     * @return void
     */
    public function destroy()
    {
        imagedestroy($this->resource);
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
     * Returns the height in pixels of the current image.
     *
     * @return int
     */
    public function height()
    {
        return $this->height;
    }

    /**
     * Resizes current image based on given width and height.
     *
     * @param integer $width  Target image width.
     * @param integer $height Target image height.
     *
     * @return $this
     */
    public function resize($width, $height)
    {
        $this->copyResampled($this->resource, 0, 0, 0, 0, $width, $height, $this->width, $this->height);

        return $this;
    }

    /**
     * Upsize image on the largest side.
     *
     * @param integer $width  Optional. Target image width. By default calculated by ratio.
     * @param integer $height Optional. Target image height. By default calculated by ratio.
     *
     * @return $this
     */
    public function upsize($width = null, $height = null)
    {
        $ratio = $this->width / $this->height;

        list($width, $height) = $this->calcResizes($width, $height, $ratio);

        if ($width / $height > $ratio) {
            $height = intval($width / $ratio);
        } else {
            $width = intval($height * $ratio);
        }

        $this->copyResampled($this->resource, 0, 0, 0, 0, $width, $height, $this->width, $this->height);

        return $this;
    }

    /**
     * Downside image on the lowest side.
     *
     * @param integer $width  Optional. Target image width. By default calculated by ratio.
     * @param integer $height Optional. Target image height. By default calculated by ratio.
     *
     * @return $this
     */
    public function downsize($width = null, $height = null)
    {
        $ratio = $this->width / $this->height;

        list($width, $height) = $this->calcResizes($width, $height, $ratio);

        if ($width / $height > $ratio) {
            $width = intval($height * $ratio);
        } else {
            $height = intval($width / $ratio);
        }

        $this->copyResampled($this->resource, 0, 0, 0, 0, $width, $height, $this->width, $this->height);

        return $this;
    }

    /**
     * Crop an image.
     *
     * Cut out a rectangular part of the current image with given width and height.
     * Define optional x,y coordinates to move the top-left corner of the cutout to a certain position.
     *
     * @param integer $width   Width of the rectangular cutout.
     * @param integer $height  Height of the rectangular cutout.
     * @param array   $options Optional. List of crop coords. By default crop from center.
     *
     * @return $this
     */
    public function crop($width, $height, $options = array())
    {
        $defaults = array(
            'x'      => null,
            'y'      => null,
        );

        $options = array_merge($defaults, $options);

        // Update X and Y for nulled arguments.
        $options = $this->calcPosition($options, $width, $height);

        $this->copyResampled($this->resource, 0, 0, $options['x'], $options['y'], $width, $height, $width, $height);

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
        $this->upsize($width, $height);

        switch ($position) {
            case 'top-left':
                $x = 0;
                $y = 0;
                break;

            case 'top':
                $x = intval(($this->width - $width) / 2);
                $y = 0;
                break;

            case 'top-right':
                $x = intval($this->width - $width);
                $y = 0;
                break;

            case 'bottom-left':
                $x = 0;
                $y = intval($this->height - $height);
                break;

            case 'bottom':
                $x = intval(($this->width - $width) / 2);
                $y = intval($this->height - $height);
                break;

            case 'bottom-right':
                $x = intval($this->width - $width);
                $y = intval($this->height - $height);
                break;

            case 'right':
                $x = intval($this->width - $width);
                $y = intval(($this->height - $height) / 2);
                break;

            case 'left':
                $x = 0;
                $y = intval(($this->height - $height) / 2);
                break;
            default:
                $x = intval(($this->width - $width) / 2);
                $y = intval(($this->height - $height) / 2);
        }

        $this->crop($width, $height, compact('x', 'y'));

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
            'color'     => array(0, 0, 0),
            'opacity'   => 0,
            'thickness' => 1,
            'outline'   => false,
        );

        $options = array_merge($defaults, $options);

        // Get color from options.
        $color = $this->getColor($options);

        imagesetthickness($this->resource, $options['thickness']);

        if (false === $options['outline']) {
            imagefilledrectangle($this->resource, $x, $y, $x + $width, $y + $height, $color);
        } else {
            imagerectangle($this->resource, $x, $y, $x + $width, $y + $height, $color);
        }

        imagesetthickness($this->resource, 1);

        return $this;
    }

    /**
     * Draw an ellipse.
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

        if (true === $options['outline']) {
            imageellipse($this->resource, $x, $y, $width, $height, $color);
        } else {
            imagefilledellipse($this->resource, $x, $y, $width, $height, $color);
        }

        return $this;
    }

    /**
     * Change the brightness of the current image by the given level.
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
     * Change the contrast of the current image by the given level.
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
     * Apply a blur image effect.
     *
     * Original version from Martijn Frazer based on
     * https://stackoverflow.com/a/20264482
     *
     * @return $this
     */
    public function blur()
    {
        $width  = $this->width;
        $height = $this->height;

        // Scale by 25% and apply Gaussian blur.
        $this->resize($width / 4, $height / 4);
        imagefilter($this->resource, IMG_FILTER_GAUSSIAN_BLUR);

        // Scale result by 200% and blur again.
        $this->resize($width / 2, $height / 2);
        imagefilter($this->resource, IMG_FILTER_GAUSSIAN_BLUR);

        // Scale result back to original size and blur one more time.
        $this->resize($width, $height);
        imagefilter($this->resource, IMG_FILTER_GAUSSIAN_BLUR);

        return $this;
    }

    /**
     * Invert colors of an image.
     *
     * @return $this
     */
    public function invert()
    {
        imagefilter($this->resource, IMG_FILTER_NEGATE);

        return $this;
    }

    /**
     * Draw black opactity rectangle on image.
     *
     * @param integer $level Optional. Blackout level. Default: 0.
     *
     * @return $this
     */
    public function blackout($level = 0)
    {
        $level = $this->getParam($level, 0, 100);

        $this->rectangle(
            0, 0, $this->width, $this->height,
            array(
                'color'   => '#000',
                'opacity' => 100 - $level,
            )
        );

        return $this;
    }

    /**
     * Rotate image.
     *
     * @param float $angle   Rotation angle.
     * @param int   $options Optional. Optional. List of rotation options.
     *
     * @return $this
     */
    public function rotate($angle, $options = array())
    {
        $defaults = array(
            'color'   => array(0, 0, 0),
            'opacity' => 100,
        );

        $options = array_merge($defaults, $options);

        // Get color from options.
        $color = $this->getColor($options);

        $this->resource = imagerotate($this->resource, $angle, $color);

        $this->width = imagesx($this->resource);
        $this->height = imagesy($this->resource);

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
            'lineheight' => 1.5,
            'opacity'    => 1,
            'horizontal' => 'left',
            'vertical'   => 'top',
            'fontpath'   => null,
            'debug'      => false,
        );

        $options = array_merge($defaults, $options);

        if (!is_readable($options['fontpath'])) {
            $this->handleError('Font is not a valid file');
        }

        // Set default width if undefined
        if (null === $options['width']) {
            $options['width'] = $this->width - $options['x'];
        }

        // Set default height if undefined
        if (null === $options['height']) {
            $options['height'] = $this->height - $options['y'];
        }

        // Draw debug rectangle.
        if (true === $options['debug']) {
            $this->drawDebug($options);
        }

        // Get color from options.
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
            imagefttext($this->resource, $options['fontsize'], 0, $x, $y, $color, $options['fontpath'], $line);

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
     * @param array  $options List of text options.
     *
     * @return string
     */
    protected function wrapText($text, &$options)
    {
        do {
            $wrapped = $this->addBreaklines($text, $options);

            // Get lines from wrapped text.
            $lines = explode("\n", $wrapped);

            // Get text width.
            $width = $this->getTextWidth($wrapped, $options);

            // Sum of all lines heights.
            $height = $options['fontsize'] * $options['lineheight'] * count($lines);

            if ($width <= $options['width'] && $height <= $options['height']) {
                break;
            }

            $options['fontsize'] = $options['fontsize'] - 1;
        } while ($options['fontsize'] > 0);

        return $wrapped;
    }

    /**
     * Calculates text width.
     *
     * @param string $text    Text to draw.
     * @param array  $options List of text options.
     *
     * @return int
     */
    protected function getTextWidth($text, $options)
    {
        $box = imageftbbox($options['fontsize'], 0, $options['fontpath'], $text);

        return $box[2];
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
        $line = '';

        // Split text to words.
        $words = explode(' ', $text);

        foreach ($words as $word) {
            $sentence = $line . ' ' . $word;

            if (empty($line)) {
                $sentence = $word;
            }

            $box = imageftbbox($options['fontsize'], 0, $options['fontpath'], $sentence);

            // Add new line to output.
            if ($box[2] > $options['width']) {
                $output = $output . $line . "\n";

                // Reset line.
                $line = $word;
                continue;
            }

            $line = $sentence;
        }

        // Add last line to output.
        $output = $output . $line;

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
            $rgb = array_map(
                function ($c) {
                    return hexdec(str_pad($c, 2, $c));
                },
                str_split(ltrim($rgb, '#'), strlen($rgb) > 4 ? 2 : 1)
            );
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
        $box = imageftbbox($options['fontsize'], 0, $options['fontpath'], $lines[$index]);

        $width  = abs($box[6] - $box[4]);
        $height = $options['fontsize'] * $options['lineheight'];

        // Smart offset for the first line respecting line height.
        $offset = $options['fontsize'] + (($height - $options['fontsize']) / 2);

        $x = $options['x'];
        $y = $options['y'] + $offset + $index * $height;

        if (0 === $index) {
            $y = $options['y'] + $offset;
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

            case 'png':
                $this->type = IMAGETYPE_PNG;
                break;

            case 'webp':
                $this->type = IMAGETYPE_WEBP;
                break;

            case 'jpg':
                $this->type = IMAGETYPE_JPEG;
                break;
        }
    }

    /**
     * Create image using file path.
     *
     * @param string $file Path to image file.
     *
     * @return array
     */
    protected function createFromFile($file)
    {
        list($width, $height, $type) = getimagesize($file);

        $source = $this->getSource($file, $type);

        return array($width, $height, $type, $source);
    }

    /**
     * Create a new image from the image stream in the string.
     *
     * @param string $data A string containing the image data.
     *
     * @return array
     */
    protected function createFromString($data)
    {
        // Get image dimensions.
        list($width, $height, $type) = getimagesizefromstring($data);

        $source = imagecreatefromstring($data);

        return array($width, $height, $type, $source);
    }

    /**
     * Get image data from instance.
     *
     * @param string $instance Instance of PosterEditor class.
     *
     * @return array
     */
    protected function createFromInstance($instance)
    {
        return array($instance->width, $instance->height, $instance->type, $instance->resource);
    }

    /**
     * Get image source using file.
     *
     * @param string $file Image file.
     * @param int    $type File type.
     *
     * @return instance
     */
    protected function getSource($file, $type)
    {
        switch ($type) {
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($file);
                break;

            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($file);
                break;

            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($file);
                break;

            case IMAGETYPE_WEBP:
                $source = imagecreatefromwebp($file);

            default:
                return $this->handleError('Unsupported image type');
        }

        return $source;
    }

    /**
     * Find image center usin from and to values.
     *
     * @param integer $from Source size.
     * @param integer $to   Destination size.
     *
     * @return integer
     */
    protected function findCenter($from, $to)
    {
        return ceil(($from - $to) * 0.5);
    }

    /**
     * Calculate new width and height values for resize.
     *
     * @param integer $width  Current image width.
     * @param integer $height Current image height.
     * @param float   $ratio  Width to height relation.
     *
     * @return array
     */
    protected function calcResizes($width, $height, $ratio)
    {
        if (null === $width) {
            $width = $this->width;

            // Try to calc new width by ratio.
            if (null !== $height) {
                $width = $height * $ratio;
            }
        }

        if (null === $height) {
            $height = $this->height;

            // Try to calc new height by ratio.
            if (null !== $width) {
                $height = $width / $ratio;
            }
        }

        return array($width, $height);
    }

    /**
     * Update position options for nulled x/y arguments.
     *
     * @param array $options Position options.
     * @param int   $width   Calculated image width.
     * @param int   $height  Calculated image height.
     *
     * @return array
     */
    protected function calcPosition($options, $width, $height)
    {
        if (null === $options['x']) {
            $options['x'] = $this->findCenter($this->width, $width);
        }

        if (null === $options['y']) {
            $options['y'] = $this->findCenter($this->height, $height);
        }

        return $options;
    }

    /**
     * Helper function to copy and resize part of an image with resampling.
     *
     * @param resource $source Source image resource.
     * @param int      $dx     X-coordinate of destination point.
     * @param int      $dy     Y-coordinate of destination point.
     * @param int      $sx     X-coordinate of source point.
     * @param int      $sy     Y-coordinate of source point.
     * @param int      $dw     Destination width.
     * @param int      $dh     Destination height.
     * @param int      $sw     Source width.
     * @param int      $sh     Source height.
     *
     * @return $this
     */
    protected function copyResampled($source, $dx, $dy, $sx, $sy, $dw, $dh, $sw, $sh)
    {
        $this->canvas($dw, $dh);

        imagecopyresampled($this->resource, $source, $dx, $dy, $sx, $sy, $dw, $dh, $sw, $sh);
        imagedestroy($source);

        return $this;
    }

    /**
     * Determines if source data is binary data.
     *
     * @param string $data File binary data.
     *
     * @return boolean
     */
    protected function isBinary($data)
    {
        if (is_string($data)) {
            $mime = finfo_buffer(finfo_open(FILEINFO_MIME_TYPE), $data);
            return (substr($mime, 0, 4) != 'text' && $mime != 'application/x-empty');
        }

        return false;
    }

    /**
     * Determines if source data is instance of current class.
     *
     * @param string $insance Instance of class.
     *
     * @return boolean
     */
    protected function isInstance($insance)
    {
        if ($insance instanceof PosterEditor) {
            return true;
        }

        return false;
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
