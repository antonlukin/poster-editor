<?php
/**
 * Wrapper for PHP's GD Library for easy image manipulation to resize, crop
 * and draw images on top of each other preserving transparency, writing text
 * with transparency and drawing shapes.
 *
 * Based on https://github.com/kus/php-image
 *
 * @version 1.0.0
 * @author Anton Lukin <anton@lukin.me>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

class ImageText
{
    /**
     * Canvas resource
     *
     * @var resource
     */
    protected $img;

    /**
     * Canvas resource
     *
     * @var resource
     */
    protected $imgCopy;

    /**
     * PNG Compression level: from 0 (no compression) to 9.
     * JPEG Compression level: from 0 to 100 (no compression).
     *
     * @var integer
     */
    protected $quality = 90;

    /**
     * Global font file
     *
     * @var String
     */
    protected $fontFile;

    /**
     * Global font size
     *
     * @var integer
     */
    protected $fontSize = 12;

    /**
     * Global line height
     *
     * @var float
     */
    protected $lineHeight = 1.25;

    /**
     * Global text vertical alignment
     *
     * @var String
     */
    protected $alignVertical = 'top';

    /**
     * Global text horizontal alignment
     *
     * @var String
     */
    protected $alignHorizontal = 'left';

    /**
     * Global font color
     *
     * @var array
     */
    protected $textColor = [255, 255, 255];

    /**
     * Global text opacity
     *
     * @var float
     */
    protected $textOpacity = 1;

    /**
     * Global text angle
     *
     * @var integer
     */
    protected $textAngle = 0;

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
     * Default folder mode to be used if folder structure needs to be created
     *
     * @var String
     */
    protected $folderMode = 0755;

    /**
     * Initialise the image with a file path, or dimensions, or pass no dimensions and
     * use setDimensionsFromImage to set dimensions from another image.
     *
     * @param string|integer $mixed (optional) file or width
     * @param integer $height (optional)
     * @return $this
     */
    public function __construct($mixed = null, $height = null)
    {
        //Check if GD extension is loaded
        if (!extension_loaded('gd') && !extension_loaded('gd2')) {
            return $this->handleError('GD is not loaded');
        }

        if ($mixed !== null) {
            if ($height !== null) {
                return $this->initialiseCanvas($mixed, $height);
            }

            if (is_string($mixed)) {
                $image = $this->setDimensionsFromImage($mixed);
                $image->draw($mixed);

                return $image;
            }
        }
    }

    /**
     * Intialise the canvas
     *
     * @param integer $width
     * @param integer $height
     * @return $this
     */
    protected function initialiseCanvas($width, $height, $resource = 'img')
    {
        $this->width = $width;
        $this->height = $height;

        unset($this->$resource);

        $this->$resource = imagecreatetruecolor($this->width, $this->height);

        // Set the flag to save full alpha channel information
        imagesavealpha($this->$resource, true);

        // Turn off transparency blending (temporarily)
        imagealphablending($this->$resource, false);

        // Completely fill the background with transparent color
        imagefilledrectangle($this->$resource, 0, 0, $this->width, $this->height, imagecolorallocatealpha($this->$resource, 0, 0, 0, 127));

        // Restore transparency blending
        imagealphablending($this->$resource, true);

        return $this;
    }

    /**
     * After we update the image run this function
     */
    protected function afterUpdate()
    {
        $this->shadowCopy();
    }

    /**
     * Store a copy of the image to be used for clone
     */
    protected function shadowCopy()
    {
        $this->initialiseCanvas($this->width, $this->height, 'imgCopy');

        imagecopy($this->imgCopy, $this->img, 0, 0, 0, 0, $this->width, $this->height);
    }

    /**
     * Enable cloning of images in their current state
     *
     * $one = clone $image;
     */
    public function __clone()
    {
        $this->initialiseCanvas($this->width, $this->height);

        imagecopy($this->img, $this->imgCopy, 0, 0, 0, 0, $this->width, $this->height);
    }

    /**
     * Get image height
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Get image width
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Get image resource (used when using a raw gd command)
     *
     * @return resource
     */
    public function getResource()
    {
        return $this->img;
    }

    /**
     * Set image resource (after using a raw gd command)
     *
     * @param $resource
     * @return $this
     */
    public function setResource($resource)
    {
        $this->img = $resource;
        $this->width = imagesx($resource);
        $this->height = imagesy($resource);

        return $this;
    }

    /**
     * Set image dimensions from an image source
     *
     * @param String $file
     * @return $this
     */
    public function setDimensionsFromImage($file)
    {
        if ($info = $this->getImageInfo($file, false)) {
            $this->initialiseCanvas($info->width, $info->height);

            return $this;
        }

        $this->handleError($file . ' is not readable!');
    }

    /**
     * Check if an image (remote or local) is a valid image and return type, width, height and image resource
     *
     * @param string $file
     * @param boolean $returnResource
     * @return \stdClass
     */
    protected function getImageInfo($file, $returnResource = true)
    {
        if ($file instanceof ImageText) {
            $file->resource = $file->img;

            return $file;
        }

        $info = new \stdClass();

        if (!is_readable($file)) {
            return false;
        }

        list($width, $height, $type) = getimagesize($file);

        switch ($type) {
            case IMAGETYPE_GIF:
                if ($returnResource) {
                    $info->resource = imagecreatefromgif($file);
                }

                break;

            case IMAGETYPE_JPEG:
                if ($returnResource) {
                    $info->resource = imagecreatefromjpeg($file);
                }

                break;

            case IMAGETYPE_PNG:
                if ($returnResource) {
                    $info->resource = imagecreatefrompng($file);
                }

                break;

            default:
                return false;
        }

        $info->type = $type;

        if ($this->type === null) {
            $this->type = $type;
        }

        $info->width = $width;
        $info->height = $height;

        return $info;
    }

    /**
     * Handle errors
     *
     * @param String $error
     *
     * @throws Exception
     */
    protected function handleError($error)
    {
        throw new \Exception($error);
    }

    /**
     * Crop an image
     *
     * @param integer $x
     * @param integer $y
     * @param integer $width
     * @param integer $height
     * @return $this
     */
    public function crop($targetWidth, $targetHeight)
    {
        $width = $this->width;
        $height = $this->height;

        $ratio = $width / $height;

        $x = 0;
        $y = 0;

        if ($targetWidth / $targetHeight > $ratio) {
            // crop top/bottom
            $newHeight = intval($targetWidth / $ratio);
            $newWidth = $targetWidth;

            $y = intval((($newHeight - $targetHeight) / 2) * ($height / $newHeight));
        } else {
            // crop sides
            $newWidth = intval($targetHeight * $ratio);
            $newHeight = $targetHeight;

            $x = intval((($newWidth - $targetWidth) / 2) * ($width / $newWidth));
        }

        $tmp = $this->img;

        $this->initialiseCanvas($targetWidth, $targetHeight);

        imagecopyresampled($this->img, $tmp, 0, 0, $x, $y, $newWidth, $newHeight, $width, $height);
        imagedestroy($tmp);

        $this->afterUpdate();

        return $this;
    }

    /**
     * Resize image to desired dimensions.
     *
     * Optionally crop the image using the quadrant.
     *
     * This function attempts to get the image to as close to the provided dimensions as possible, and then crops the
     * remaining overflow using the quadrant to get the image to be the size specified.
     *
     * @param integer $targetWidth
     * @param integer $targetHeight
     * @param boolean $upscale
     * @return $this
     */
    public function resize($targetWidth, $targetHeight, $upscale = false)
    {
        $width = $this->width;
        $height = $this->height;

        $ratio = $width / $height;

        $x = 0;
        $y = 0;

        if ($targetWidth / $targetHeight > $ratio) {
            $newWidth = intval($targetHeight * $ratio);
            $newHeight = $targetHeight;
        } else {
            $newHeight = intval($targetWidth / $ratio);
            $newWidth = $targetWidth;
        }

        if ($upscale === false) {
            if ($newWidth > $width) {
                $newWidth = $width;
            }

            if ($newHeight > $height) {
                $newHeight = $height;
            }
        }

        $tmp = $this->img;
        $this->initialiseCanvas($newWidth, $newHeight);

        imagecopyresampled($this->img, $tmp, 0, 0, $x, $y, $newWidth, $newHeight, $width, $height);
        imagedestroy($tmp);

        $this->afterUpdate();

        return $this;
    }

    /**
     * Shows the resulting image and cleans up.
     */
    public function show()
    {
        switch ($this->type) {
            case IMAGETYPE_GIF:
                header('Content-type: image/gif');
                imagegif($this->img, null);

                break;
            case IMAGETYPE_PNG:
                header('Content-type: image/png');
                imagepng($this->img, null, $this->quality);

                break;
            default:
                header('Content-type: image/jpeg');
                imagejpeg($this->img, null, $this->quality);

                break;
        }

        $this->cleanup();
    }

    /**
     * Cleanup
     */
    public function cleanup()
    {
        imagedestroy($this->img);
    }

    /**
     * Save the image
     *
     * @param String $path
     * @param boolean $show
     * @param boolean $destroy
     * @return $this
     */
    public function save($path, $show = false, $destroy = true)
    {
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), $this->folderMode, true);
        }

        if (!is_writable(dirname($path))) {
            return $this->handleError(dirname($path) . ' is not writable and failed to create directory structure!');
        }

        switch ($this->type) {
            case IMAGETYPE_GIF:
                imagegif($this->img, $path);

                break;

            case IMAGETYPE_PNG:
                imagepng($this->img, $path, $this->quality);

                break;

            default:
                imagejpeg($this->img, $path, $this->quality);
        }

        if ($destroy) {
            $this->cleanup();
        }

        if (!$show) {
            return $this;
        }

        $this->show();
    }

    /**
     * Save the image and return object to continue operations
     *
     * @param string $path
     * @return $this
     */
    public function snapshot($path)
    {
        return $this->save($path, false, false);
    }

    /**
     * Save the image and show it
     *
     * @param string $path
     */
    public function showAndSave($path)
    {
        $this->save($path, true);
    }

    /**
     * Draw a line
     *
     * @param integer $x1
     * @param integer $y1
     * @param integer $x2
     * @param integer $y2
     * @param array $color
     * @param float $opacity
     * @param boolean $dashed
     * @return $this
     */
    public function line($x1 = 0, $y1 = 0, $x2 = 100, $y2 = 100, $color = [0, 0, 0], $opacity = 1.0, $dashed = false)
    {
        if ($dashed === true) {
            imagedashedline($this->img, $x1, $y1, $x2, $y2, imagecolorallocatealpha($this->img, $color[0], $color[1], $color[2], (1 - $opacity) * 127));
        } else {
            imageline($this->img, $x1, $y1, $x2, $y2, imagecolorallocatealpha($this->img, $color[0], $color[1], $color[2], (1 - $opacity) * 127));
        }

        $this->afterUpdate();

        return $this;
    }

    /**
     * Draw a rectangle
     *
     * @param integer $x
     * @param integer $y
     * @param integer $width
     * @param integer $height
     * @param array $color
     * @param float $opacity
     * @param boolean $outline
     * @see http://www.php.net/manual/en/function.imagefilledrectangle.php
     * @return $this
     */
    public function rectangle($x = 0, $y = 0, $width = 100, $height = 50, $color = [0, 0, 0], $opacity = 1.0, $outline = false)
    {
        if ($outline === true) {
            imagerectangle($this->img, $x, $y, $x + $width, $y + $height, imagecolorallocatealpha($this->img, $color[0], $color[1], $color[2], (1 - $opacity) * 127));
        } else {
            imagefilledrectangle($this->img, $x, $y, $x + $width, $y + $height, imagecolorallocatealpha($this->img, $color[0], $color[1], $color[2], (1 - $opacity) * 127));
        }

        $this->afterUpdate();

        return $this;
    }

    /**
     * Draw an ellipse
     *
     * @param integer $x
     * @param integer $y
     * @param integer $width
     * @param integer $height
     * @param array $color
     * @param float $opacity
     * @param boolean $outline
     * @see http://www.php.net/manual/en/function.imagefilledellipse.php
     * @return $this
     */
    public function ellipse($x = 0, $y = 0, $width = 100, $height = 50, $color = [0, 0, 0], $opacity = 1.0, $outline = false)
    {
        if ($outline === true) {
            imageellipse($this->img, $x, $y, $width, $height, imagecolorallocatealpha($this->img, $color[0], $color[1], $color[2], (1 - $opacity) * 127));
        } else {
            imagefilledellipse($this->img, $x, $y, $width, $height, imagecolorallocatealpha($this->img, $color[0], $color[1], $color[2], (1 - $opacity) * 127));
        }

        $this->afterUpdate();

        return $this;
    }

    /**
     * Draw a polygon
     *
     * @param array $points
     * @param array $color
     * @param float $opacity
     * @param boolean $outline
     * @see http://www.php.net/manual/en/function.imagefilledpolygon.php
     * @return $this
     */
    public function polygon($points = [], $color = [0, 0, 0], $opacity = 1.0, $outline = false)
    {
        if (count($points) > 0) {
            if ($outline === true) {
                imagepolygon($this->img, $points, count($points) / 2, imagecolorallocatealpha($this->img, $color[0], $color[1], $color[2], (1 - $opacity) * 127));
            } else {
                imagefilledpolygon($this->img, $points, count($points) / 2, imagecolorallocatealpha($this->img, $color[0], $color[1], $color[2], (1 - $opacity) * 127));
            }

            $this->afterUpdate();
        }

        return $this;
    }

    /**
     * Draw an arc
     *
     * @param integer $x
     * @param integer $y
     * @param integer $width
     * @param integer $height
     * @param integer $start
     * @param integer $end
     * @param array $color
     * @param float $opacity
     * @param boolean $outline
     * @see http://www.php.net/manual/en/function.imagefilledarc.php
     * @return $this
     */
    public function arc($x = 0, $y = 0, $width = 100, $height = 50, $start = 0, $end = 180, $color = [0, 0, 0], $opacity = 1.0, $outline = false)
    {
        if ($outline === true) {
            imagearc($this->img, $x, $y, $width, $height, $start, $end, imagecolorallocatealpha($this->img, $color[0], $color[1], $color[2], (1 - $opacity) * 127));
        } else {
            imagefilledarc($this->img, $x, $y, $width, $height, $start, $end, imagecolorallocatealpha($this->img, $color[0], $color[1], $color[2], (1 - $opacity) * 127), IMG_ARC_PIE);
        }
        $this->afterUpdate();

        return $this;
    }

    /**
     * Draw an image from file
     *
     * Accepts x/y properties from CSS background-position (left, center, right, top, bottom, percentage and pixels)
     *
     * @param String $file
     * @param String|integer $x
     * @param String|integer $y
     * @see http://www.php.net/manual/en/function.imagecopyresampled.php
     * @see http://www.w3schools.com/cssref/pr_background-position.asp
     * @return $this
     */
    public function draw($file, $x = '50%', $y = '50%')
    {
        $info = $this->getImageInfo($file);

        if (!$info) {
            return $this->handleError($file . ' is not a valid image!');
        }

        $image = $info->resource;
        $width = $info->width;
        $height = $info->height;

        if (!is_numeric($x)) {
            $x = '50%';

            if (strpos($x, '%') !== false) {
                $x = ceil(($this->width - $width) * (trim($x, '%') / 100));
            }
        }

        if (!is_numeric($y)) {
            $y = '50%';

            if (strpos($y, '%') !== false) {
                $y = ceil(($this->height - $height) * (trim($y, '%') / 100));
            }
        }

        // Draw image
        imagecopyresampled($this->img, $image, $x, $y, 0, 0, $width, $height, $width, $height);
        imagedestroy($image);
        $this->afterUpdate();

        return $this;
    }

    /**
     * Draw multi-line text box and auto wrap text
     *
     * @param String $text
     * @param array $options
     * @return $this
     */
    public function text($options = [], &$boundary = [])
    {
        // Unset null values so they inherit defaults
        foreach ($options as $k => $v) {
            if ($options[$k] === null) {
                unset($options[$k]);
            }
        }

        $defaults = [
            'text' => '',
            'x' => 0,
            'y' => 0,
            'width' => null,
            'height' => null,
            'fontSize' => $this->fontSize,
            'fontColor' => $this->textColor,
            'opacity' => $this->textOpacity,
            'alignHorizontal' => $this->alignHorizontal,
            'alignVertical' => $this->alignVertical,
            'angle' => $this->textAngle,
            'fontFile' => $this->fontFile,
            'lineHeight' => $this->lineHeight,
            'debug' => false,
        ];

        $options = array_merge($defaults, $options);

        // Wrap text and find font size
        $options = $this->fitTobounds($options);

        extract($options);

        if ($debug) {
            $this->rectangle($x, $y, $width, $height, [0, 255, 255], 0.5);
        }

        // Split lines
        $lines = explode("\n", $text);

        $fontHeight = $this->getFontHeight($fontSize, $angle, $fontFile, $lines);
        $textHeight = $fontSize * $lineHeight;

        // Set default boundary
        $boundary = [
            'height' => 0,
            'width' => 0,
        ];

        foreach ($lines as $index => $line) {
            $offsetx = 0;
            $offsety = $fontHeight;

            // Get Y offset as it 0 Y is the lower-left corner of the character
            $testbox = imageftbbox($fontSize, $angle, $fontFile, $line);

            $textWidth = abs($testbox[6] - $testbox[4]);
            $lineY = $y + ($textHeight * $index);

            switch ($alignHorizontal) {
                case 'center':
                    $offsetx += (($width - $textWidth) / 2);
                    break;
                case 'right':
                    $offsetx += ($width - $textWidth);
                    break;
            }

            switch ($alignVertical) {
                case 'center':
                    $offsety += (($height - ($textHeight * count($lines))) / 2);
                    break;
                case 'bottom':
                    $offsety += ($height - ($textHeight * count($lines)));
                    break;
            }

            if ($debug) {
                $blockColor = [rand(150, 255), rand(150, 255), rand(150, 255)];
                $this->rectangle($x + $offsetx, $lineY + $offsety - $fontHeight, $textWidth, $textHeight, $blockColor, 0.5);
            }

            $textColor = imagecolorallocatealpha($this->img, $fontColor[0], $fontColor[1], $fontColor[2], (1 - $opacity) * 127);

            // Draw text
            $textSize = imagefttext($this->img, $fontSize, $angle, $x + $offsetx, $lineY + $offsety, $textColor, $fontFile, $line);

            // Calc block height
            $boundary['height'] += $textHeight;

            // Calc block width
            $boundary['width'] = max($textWidth, $boundary['width']);
        }

        $boundary = array_map('intval', $boundary);

        $this->afterUpdate();

        return $this;
    }

    /**
     * Reduce font size to fit to width and height
     *
     * @param String $text
     * @param Array $options
     * @return integer
     */
    protected function fitToBounds($options)
    {
        extract($options);

        if (!is_int($width)) {
            $options['width'] = $this->width - $x;
        }

        if (!is_int($height)) {
            $options['height'] = $this->height - $y;
        }

        do {
            $wrapped = $this->wrap($options);
            $testbox = imageftbbox($fontSize, $angle, $fontFile, $wrapped);
            $textHeight = abs($testbox[1] - $testbox[7]);

            if ($textHeight <= $options['height']) {
                break;
            }

            $options['fontSize'] = --$fontSize;
        } while ($fontSize > 0);

        $options['text'] = $wrapped;

        return $options;
    }

    /**
     * Get font height
     *
     * @param integer $fontSize
     * @param integer $angle
     * @param String $fontFile
     * @param array lines
     * @return integer
     */
    protected function getFontHeight($fontSize, $angle, $fontFile, $lines)
    {
        $height = 0;

        foreach ($lines as $index => $line) {
            $testbox = imageftbbox($fontSize, $angle, $fontFile, $line);
            $textHeight = abs($testbox[1] - $testbox[7]);

            if ($textHeight > $height) {
                $height = $textHeight;
            }
        }

        return $height;
    }

    /**
     * Helper to wrap text
     *
     * @param String $text
     * @param integer $width
     * @param integer $fontSize
     * @param integer $angle
     * @param String $fontFile
     * @return String
     */
    protected function wrap($options, $output = '')
    {
        extract($options);

        $words = explode(' ', $text);

        foreach ($words as $word) {
            $testbox = imageftbbox($fontSize, $angle, $fontFile, $output . ' ' . $word);

            // Declare empty seprator
            $separator = '';

            if (strlen($output) > 0) {
                $separator = ' ';
            }

            if ($testbox[2] > $width) {
                $separator = "\n";
            }

            $output = $output . $separator . $word;
        }

        return $output;
    }

    /**
     * Set's global folder mode if folder structure needs to be created
     *
     * @param integer $mode
     * @return $this
     */
    public function setFolderMode($mode = 0755)
    {
        $this->folderMode = $mode;

        return $this;
    }

    /**
     * Set's global text size
     *
     * @param integer $size
     * @return $this
     */
    public function setFontSize($size = 12)
    {
        $this->fontSize = $size;

        return $this;
    }

    /**
     * Set's global line height
     *
     * @param float $lineHeight
     * @return $this
     */
    public function setLineHeight($lineHeight = 1.25)
    {
        $this->lineHeight = $lineHeight;

        return $this;
    }

    /**
     * Set's global text vertical alignment
     *
     * @param String $align
     * @return $this
     */
    public function setAlignVertical($align = 'top')
    {
        $this->alignVertical = $align;

        return $this;
    }

    /**
     * Set's global text horizontal alignment
     *
     * @param String $align
     * @return $this
     */
    public function setAlignHorizontal($align = 'left')
    {
        $this->alignHorizontal = $align;

        return $this;
    }

    /**
     * Set's global text color using RGB
     *
     * @param array $color
     * @return $this
     */
    public function setTextColor($color = [255, 255, 255])
    {
        $this->textColor = $color;

        return $this;
    }

    /**
     * Set's global text angle
     *
     * @param integer $angle
     * @return $this
     */
    public function setTextAngle($angle = 0)
    {
        $this->textAngle = $angle;

        return $this;
    }

    /**
     * Set's global text opacity
     *
     * @param float $opacity
     * @return $this
     */
    public function setTextOpacity($opacity = 1.0)
    {
        $this->textOpacity = $opacity;

        return $this;
    }

    /**
     * Set's global font file for text from .ttf font file (TrueType)
     *
     * @param string $fontFile
     * @return $this
     */
    public function setFont($fontFile)
    {
        $this->fontFile = $fontFile;

        return $this;
    }

    /**
     * Set's global quality for PNG output
     *
     * @param string $quality
     * @return $this
     */
    public function setQuality($quality)
    {
        $this->quality = $quality;

        return $this;
    }

    /**
     * Set's global output type
     *
     * @param String $type
     * @param String $quality
     * @return $this
     */
    public function setOutput($type, $quality = null)
    {
        switch (strtolower($type)) {
            case 'gif':
                $this->type = IMAGETYPE_GIF;
                break;
            case 'jpg':
                $this->type = IMAGETYPE_JPEG;
                break;
            case 'png':
                $this->type = IMAGETYPE_PNG;
                break;
        }

        if ($quality !== null) {
            $this->setQuality($quality);
        }

        return $this;
    }
}
