<?php

/**
 * <h4>A light-weight PHP image resizer with dynamic caching.</h4>
 *
 * @license GPL
 * @package	ImageLite
 */

/**
 * <h4>A light-weight PHP image resizer with dynamic caching.</h4>
 *
 * <p>A simple solution to fulfil the most common image resizing tasks with <a href="http://php.net/manual/en/book.image.php" target="_blank">GD</a>.</p>
 *
 * <h2>Features</h2>
 * <ul>
 *	<li><strong>Supported Image Types</strong>
 *		<ul>
 *			<li>JPEG</li>
 *			<li>GIF + Transparency</li>
 *			<li>PNG + Transparency</li>
 *		</ul>
 *  </li>
 *	<li><strong>Resize</strong>
 *		<ul>
 *			<li>Standard/Proportional</li>
 *			<li>Crop-to-fit</li>
 *			<li>Letterbox</li>
 *			<li>Percentage</li>
 *		</ul>
 *  </li>
 *	<li><strong>Manipulation</strong>
 *		<ul>
 *			<li>Sharpen</li>
 *			<li>Rotate</li>
 *			<li>Aspect Ratio toggle</li>
 *			<li>Contrain dimensions</li>
 *			<li>Quality/Compression</li>
 *		</ul>
 *  </li>
 *	<li><strong>File Saving</strong>
 *		<ul>
 *			<li>Custom destination path</li>
 *			<li>Automated cache with custom expiration setting</li>
 *		</ul>
 *  </li>
 * </ul>
 *
 * <h2>Example 1:</h2>
 * <p>Resize and save an image to a specfic location.</p>
 * <code>
 *	try {
 *		include_once("imagelite.class.php");
 *
 *		$srcUri = ImageLite::inst("./src.jpg")->sharpen(7)->resize(100,200)->save("./new.jpg")->getUri();
 *
 *		echo "&lt;img src='{$srcUri}' /&gt;";
 *	}
 *	catch (Exception $e) {
 *		echo $e->getMessage();
 *	}
 * </code>
 *
 * <h2>Example 2:</h2>
 * <p>Resize an image and store it in the cache.</p>
 * <code>
 *	try {
 *		include_once("imagelite.class.php");
 *
 *		ImageLite::setCacheRootDirectory("/path/to/images/cache/directory", true);
 *
 *		$img = ImageLite::inst("/path/to/src/image.jpg")->quality(70)->resize(100,200)->save();
 *
 *		echo "&lt;img src='". $img->getUri() ."' /&gt;";
 *	}
 *	catch (Exception $e) {
 *		echo $e->getMessage();
 *	}
 * </code>
 *
 * <h2>Example 3:</h2>
 * <p>Resize an image and store it in the cache (note that chaining is not used).</p>
 * <code>
 *	try {
 *		include_once("imagelite.class.php");
 *
 *		ImageLite::setCacheRootDirectory("/path/to/cache/directory", true);
 *		ImageLite::setMode(0777); 
 *		ImageLite::setCacheUri("/assets/cache/");
 *
 *		$img = ImageLite::inst("/path/to/original/image.jpg");
 *		$img->quality(70);
 *		$img->aspectRatio(false);
 *		$img->sharpen(8);
 *		$img->resize(100,200);
 *		$img->save();
 *		echo "&lt;img src='". $img->getUri() ."' /&gt;";
 *	}
 *	catch (Exception $e) {
 *		echo $e->getMessage();
 *	}
 * </code>
 *
 * @package ImageLite
 * @version 1.0.0
 * @license GPL
 * @link http://php.net/manual/en/function.imageconvolution.php Image sharpening uses imageconvolution (available in PHP 5.1+ only)
 * @todo Unit tests
 */
class ImageLite {


	/**
	 * Array of instances for each image object
	 * @var array
	 * @access protected
	 */
	protected static $instances = array();


	/**
	 * Path to the root directory of the cache
	 * @var string
	 * @example /home/website/public_html/assets/cache
	 * @access protected
	 */
	protected static $cacheRootDir = null;


	/**
	 * Absolute/relative URI path to the cache
	 * @var string
	 * @example /assets/cache
	 * @access protected
	 */
	protected static $cacheUri = null;


	/**
	 * Path to the document root directory
	 * @var string
	 * @example /home/website/public_html
	 * @access protected
	 */
	protected static $documentRoot = null;


	/**
	 * Lifetime of a cached image
	 * @var string
	 * @link http://www.php.net/manual/en/datetime.formats.relative.php Value is used with strtotime()
	 * @example "-4 months"
	 * @access protected
	 */
	protected static $imgLifetime = null;


	/**
	 * Default mode for directories and files
	 * @var integer An octal number
	 * @access protected
	 */
	protected static $mode = 0755;


	/**
	 * Source image file path
	 * @var string
	 * @access protected
	 */
	protected $srcFilePath = null;


	/**
	 * The type of resize to perform
	 * @example standard
	 * @example crop-to-fit
	 * @example letterbox
	 * @example percent
	 * @var boolean
	 * @access protected
	 */
	protected $resizeType = "standard";


	/**
	 * Resource for source image
	 * @var resource
	 * @access protected
	 */
	protected $srcImg = null;

	/**
	 * Resource for destination image
	 * @var resource
	 * @access protected
	 */
	protected $dstImg = null;


	/**
	 * The width of the source image
	 * @var integer
	 * @access protected
	 */
	protected $srcWidth = null;


	/**
	 * The height of the source image
	 * @var integer
	 * @access protected
	 */
	protected $srcHeight = null;


	/**
	 * The max width of the new image
	 * @var integer
	 * @access protected
	 */
	protected $maxWidth = null;


	/**
	 * The max height of the new image
	 * @var integer
	 * @access protected
	 */
	protected $maxHeight = null;


	/**
	 * The calculated width for the destination image
	 * @var integer
	 * @access protected
	 */
	protected $dstWidth = 1;


	/**
	 * The calculated height for the destination image
	 * @var integer
	 * @access protected
	 */
	protected $dstHeight = 1;


	/**
	 * X-coordinate of destination point
	 * @link http://php.net/manual/en/function.imagecopyresampled.php See imagecopyresampled()
	 * @var integer
	 * @access protected
	 */
	protected $dstX = 0;


	/**
	 * Y-coordinate of destination point
	 * @link http://php.net/manual/en/function.imagecopyresampled.php See imagecopyresampled()
	 * @var integer
	 * @access protected
	 */
	protected $dstY = 0;


	/**
	 * X-coordinate of source point
	 * @link http://php.net/manual/en/function.imagecopyresampled.php See imagecopyresampled()
	 * @var integer
	 * @access protected
	 */
	protected $srcX = 0;


	/**
	 * Y-coordinate of source point
	 * @link http://php.net/manual/en/function.imagecopyresampled.php See imagecopyresampled()
	 * @var integer
	 * @access protected
	 */
	protected $srcY = 0;


	/**
	 * Image type
	 *
	 * ie. gif, jpg or png
	 * @var string
	 * @access protected
	 */
	protected $imageType = null;


	/**
	 * The status of whether the aspect ratio of the image should be maintained
	 * @var boolean
	 * @access protected
	 */
	protected $keepAspectRatio = true;


	/**
	 * Quality of the image when resized
	 *
	 * Range: 0 (worst quality/small file size) to 100 (best quality/large file size)
	 * @var integer
	 * @access protected
	 */
	protected $quality = 75;


	/**
	 * The angle in degrees in which to rotate an image
	 *
	 * Range: 0 degrees to 359 degrees (support for -270, -180, -90, 0, 90, 180, 270)
	 * @var integer
	 * @access protected
	 */
	protected $rotation = 0;


	/**
	 * The level of sharpening that should be applied to an image
	 *
	 * Range: 0 (no sharpening) to 100 (maximum sharpening)
	 * @var integer
	 * @access protected
	 */
	protected $sharpen = 0;


	/**
	 * Contrain the re-sizing of an image to it's original dimensions
	 * @var boolean
	 * @access protected
	 */
	protected $constrain = true;


	/**
	 * Save resized image to this custom file path rather than the cache
	 * @var boolean
	 * @access protected
	 */
	protected $customDestinationFilePath = null;


	/**
	 * Constructor
	 * @param string $filePath Path to the image file
	 */
	public function __construct($filePath = null)
	{
		// Throw an exception if GD is not installed
		if (!extension_loaded("gd"))
		{
			throw new RuntimeException("GD is not enabled on your server (see: http://www.php.net/manual/en/book.image.php)");
		}

		$this->setFilePath($filePath);
	}


	/**
	 * Get an instance
	 * @param string $filePath The path to the image file
	 * @return object
	 */
	public static function getInstance($filePath = null)
	{
		$filePath = trim($filePath);
		if (!isset(self::$instances[$filePath]))
		{
			return self::$instances[$filePath] = new self($filePath);
		}
		return self::$instances[$filePath];
	}


	/**
	 * Shorthand alias for getInstance()
	 * @param string $filePath The path to the image file
	 * @return object
	 */
	public static function inst($filePath = null)
	{
		return self::getInstance($filePath);
	}


	/**
	 * Print/output debug information
	 *
	 * This is for developer purposes only. Call this method to output helpful information during development.
	 * @return void
	 */
	public function debug()
	{
		echo "<fieldset>\r\n";
		echo "	<legend>Debug: Object Variables</legend>\r\n";
		var_dump(get_class_vars(__CLASS__));

		if (function_exists("debug_backtrace"))
		{
			$trace = debug_backtrace();
			if (isset($trace[1]["function"]))
			{
				echo "<p>Previous method call: <strong>". $trace[1]["class"] . "::". $trace[1]["function"] ." - Line: ". $trace[1]["line"] ."</strong></p>";
			}
		}
		echo "getCacheDestinationDirectoryPath(): " . $this->getCacheDestinationDirectoryPath() . "<br />\r\n";
		echo "getCacheDestinationFilename(): " . $this->getCacheDestinationFilename() . "<br />\r\n";
		echo "getCacheDestinationFilePath(): " . $this->getCacheDestinationFilePath() . "<br />\r\n";
		echo "</fieldset>\r\n";
	}


	/**
	 * Get bytes from shorthand byte notation
	 *
	 * If a suffix is not found, then the original value is returned
	 * @see http://www.php.net/manual/en/faq.using.php#faq.using.shorthandbytes
	 * @example getBytes("1G"); <br /> Returns: 1073741824
	 * @example getBytes("1M"); <br /> Returns: 1048576
	 * @example getBytes("1K"); <br /> Returns: 1024
	 * @example getBytes("1024"); <br /> Returns: 1024
	 * @param string|integer $val A number followed by a suffix of 'G' (Gig), 'M' (Megabytes) or 'K' (Kilobytes)
	 * @return integer
	 */
	protected function getBytes($val)
	{
		$val = trim($val);
		$suffix = strtolower($val[strlen($val)-1]);
		if ($suffix == 'g' || $suffix == 'm' || $suffix == 'k')
		{
			switch($suffix)
			{
				case 'g': $val *= 1024;
				case 'm': $val *= 1024;
				case 'k': $val *= 1024;
			}
		}
		return $val;
	}


	/**
	 * Get a memory exhausted message
	 *
	 * Based on the width/height of the source image and the current memory limit we must estimate
	 * how much memory will be required to resize the image without exhausting the memory.
	 * ie. Prevent an error such as "Fatal error: Allowed memory size of 76532345 bytes exhausted (tried to allocate 345 bytes) in page.php
	 * @param string $filePath A file path can be added to the message (optional)
	 * @return boolean|string String: Memory will potentially be exhausted. False: Memory will not be exhausted.
	 */
	protected function getMemoryExhaustedMsg($filePath = null)
	{
		// Obtain the memory limit in bytes
		$memLimit = $this->getBytes(ini_get("memory_limit"));

		// Obtain the maximum x/y dimension for a square image that would not exhaust the memory limit.
		// We divide by 4 to account for each pixel with a value for red, green, blue and an alpha channel.
		$recommendedXY = intval(sqrt($memLimit)/4);

		// Obtain the x/y dimension for the current image (as a square)
		$currentXY = intval(sqrt($this->srcWidth*$this->srcHeight));

		// Generate an error message if the current image is too large to process with the current memory
		if ($currentXY > $recommendedXY)
		{
			// Estimate file size and required memory (lets deal with megabytes only for now)
			$potenialFileSize = round(($this->srcWidth*$this->srcHeight)/1024/1024, 1);
			
			// Memory usage is not 100% accurate, hence a range of 1.4 to 1.8 is applied because additional memory is always required
			$memStart = round(($potenialFileSize*4)*1.4);
			$memEnd = round(($potenialFileSize*4)*1.8);

			$error = array();
			if (!empty($filePath))
			{
				$error[] = "Image File: " . $filePath;
			}
			$error[] = "Current memory limit: " . ini_get("memory_limit");
			$error[] = "Current memory limit could process a ". $recommendedXY ."x". $recommendedXY ." square image safely";
			$error[] = "Estimated required memory limit: " . $memStart ."M to ". $memEnd . "M";
			$error[] = "Estimated required memory limit could process a ". $currentXY ."x". $currentXY ." square image safely, or in this case ". $this->srcWidth . "x". $this->srcHeight;
			$error[] = "Avoid a memory exhausted fatal error by using ini_set(\"memory_limit\", \"". $memEnd ."M\");";

			return implode("\r\n<br />", $error);
		}
		return false;
	}


	/**
	 * Set cache directory
	 * @param string $path Path to the root cache directory
	 * @param string $create Create the directory
	 * @example setCacheRootDirectory("/home/website/public_html/assets/cache");
	 * @example setCacheRootDirectory("../assets/cache");
	 * @example setCacheRootDirectory("/home/website/public_html/assets/cache", true); <br />Create the directory path if not found and if 2nd argument is TRUE
	 * @return void
	 */
	public static function setCacheRootDirectory($path = null, $create = false)
	{
		$path = trim($path);

		// Create the directory if it doesn't exist and $create is set to TRUE
		if (!is_dir($path) && $create === true)
		{
			self::createDirectory($path);
		}

		// Set the path to the cache root directory
		if (is_dir($path))
		{
			if (is_writable($path))
			{
				self::$cacheRootDir = realpath($path) . DIRECTORY_SEPARATOR;
			}
			else
				throw new Exception(__METHOD__."(): Cache directory is not writeable: \"$path\"");
		}
		else
		{
			// Throw an exception if the directory doesn't exist (it's only auto-generated if $create is TRUE)
			throw new Exception(__METHOD__."(): Cache directory does not exist: \"$path\"");
		}
	}


	/**
	 * Get cache directory
	 * @return string
	 */
	public static function getCacheRootDirectory()
	{
		return self::$cacheRootDir;
	}


	/**
	 * Set cache URI (optional)
	 * @param string $path URI path to the root cache directory
	 * @example setCacheRootUri("/cache/"); <br />Absolute URI path to cache.
	 * @example setCacheRootUri("assets/cache"); <br />Relative URI path to cache.
	 * @return void
	 */
	public static function setCacheUri($path)
	{
		$path = rtrim(trim($path), "/");
		if (!empty($path))
		{
			self::$cacheUri = $path . "/";
		}
		else
		{
			throw new InvalidArgumentException(__METHOD__."(): URI path is empty (Path: \"$path\"");
		}
	}


	/**
	 * Get cache URI
	 * @return string
	 */
	public static function getCacheUri()
	{
		return self::$cacheUri;
	}


	/**
	 * Set document root (optional)
	 * @param string $path Path to document root directory. Default: $_SERVER["DOCUMENT_ROOT"].
	 * @example setDocumentRoot("/home/website/public_html");
	 * @example setDocumentRoot("/home/website/public_html/subsite/");
	 * @example setDocumentRoot("../../public_html");
	 * @return void
	 */
	public static function setDocumentRoot($path = null)
	{
		$path = trim($path);

		// Set the path to the document root if the directory exists
		if (is_dir($path))
		{
			// Set absolute path for use with string comparison when generating the URI
			self::$documentRoot = realpath($path) . DIRECTORY_SEPARATOR;
		}
		else
		{
			throw new Exception(__METHOD__."(): Document Root directory does not exist: \"$path\"");
		}
	}


	/**
	 * Get document root
	 * @access protected
	 * @return string
	 */
	public static function getDocumentRoot()
	{
		if (empty(self::$documentRoot))
		{
			// Set absolute path for use with string comparison when generating the URI
			self::$documentRoot = realpath($_SERVER["DOCUMENT_ROOT"]) . DIRECTORY_SEPARATOR;
		}
		return self::$documentRoot;
	}


	/**
	 * Set image expiration lifetime
	 * @param string $lifetime A time period set as a negative (eg. -3 months)
	 * @example setLifetime(null); <br />Image will never expire (default)
	 * @example setLifetime("-1 month"); <br />Creates a new image after 1 month
	 * @example setLifetime("-30 seconds"); <br />Creates a new image after 30 seconds
	 * @link http://www.php.net/manual/en/datetime.formats.relative.php Preferred format for $lifetime
	 * @return void
	 */
	public static function setLifetime($lifetime)
	{
		self::$imgLifetime = (string) $lifetime;
	}


	/**
	 * Set mode permissions
	 *
	 * The mode set by this method will be applied to directories and images that are automatically created
	 * @param integer $mode An octal integer
	 * @example setMode(0600); <br />Read and write for owner, nothing for everybody else
	 * @example setMode(0644); <br />Read and write for owner, read for everybody else
	 * @example setMode(0755); <br />Everything for owner, read and execute for others
	 * @example setMode(0750); <br />Everything for owner, read and execute for owner's group
	 * @link http://php.net/manual/en/function.chmod.php chmod
	 * @return void
	 */
	public static function setMode($mode)
	{
		self::$mode = (string) $mode;
	}


	/**
	 * Create a directory path
	 * @param string $path Path to the directory
	 * @param boolean $hasFilename Set as true to ignore a filename in the path
	 * @return void
	 */
	protected static function createDirectory($path, $hasFilename = false)
	{
		// If $path contains a filename then obtain the directory path only
		if ($hasFilename === true)
		{
			$path = pathinfo($path, PATHINFO_DIRNAME);			
		}

		// Create the full directory path if it doesn't exist
		if (!is_dir($path))
		{
			if (!mkdir($path, self::$mode, true))
			{
				throw new Exception(__METHOD__."(): Unable to create directory path, please check permissions of parent directory (Path: \"". $path . "\")");
			}
		}

		// Directory must be writeable
		if (!is_writeable($path))
		{
			// Attempt to change the permissions (mode)
			if (chmod($path, self::$mode) === false)
			{
				throw new RuntimeException(__METHOD__."(): Directory path is not writeable and the mode/permissions could not be changed (Path: " . $path);
			}
		}

		return $path;
	}


	/**
	 * Set the path to the image file
	 * @param string $filePath The path to the image file
	 * @return void
	 */
	protected function setFilePath($filePath)
	{
		$filePath = trim($filePath);
		if (!empty($filePath))
		{
			if (file_exists($filePath))
			{
				if (is_readable($filePath))
				{
					// Detect the image type (ie. GIF, JPEG or PNG)
					$result = $this->setImageInfo($filePath);

					if ($result)
					{
						$this->srcFilePath = realpath($filePath);
					}
					else
						throw new UnexpectedValueException(__METHOD__."(): File doesn't appear to be an image (File path: \"$filePath\"");
				}
				else
					throw new Exception(__METHOD__."(): File exists but it is not readable (File path: \"$filePath\"");
			}
			else
				throw new Exception(__METHOD__."(): File does not exist (File path: \"$filePath\"");
		}
		else
			throw new InvalidArgumentException(__METHOD__."(): File path is empty (File path: \"$filePath\"");
	}


	/**
	 * Get destination directory path
	 * @return string
	 */
	public function getCacheDestinationDirectoryPath()
	{
		$sha = sha1($this->srcFilePath);
		return "{$this->dstWidth}".
				DIRECTORY_SEPARATOR .
				"{$this->dstHeight}".
				DIRECTORY_SEPARATOR .
				ord($sha[0]) .
				DIRECTORY_SEPARATOR .
				ord($sha[1]) .
				DIRECTORY_SEPARATOR .
				"q". $this->quality .
				"-r". $this->rotation .
				"-s". $this->sharpen .
				"-c". $this->constrain .
				DIRECTORY_SEPARATOR;
	}


	/**
	 * Get destination filename
	 * @return string
	 */
	public function getCacheDestinationFilename()
	{
		return sha1($this->resizeType) . "_". sha1($this->srcFilePath) . "." . $this->imageType;
	}


	/**
	 * Get destination file path
	 * @return string
	 */
	public function getCacheDestinationFilePath()
	{
		return $this->getCacheDestinationDirectoryPath() . $this->getCacheDestinationFilename();
	}


	/**
	 * Set aspect ratio status
	 * @param boolean $keep Set as TRUE or FALSE
	 * @example aspectRatio(true); <br />Keep the aspect ratio
	 * @example aspectRatio(false); <br />Ignore the aspect ratio and fulfil the exact maximum dimensions
	 * @return object
	 */
	public function aspectRatio($keep)
	{
		$this->keepAspectRatio = (bool) $keep;
		return $this;
	}


	/**
	 * Set quality (compression) level
	 * @param integer $level Range from 0 (worst quality) to 100 (best quality). Default is 75.
	 * @return object
	 */
	public function quality($level)
	{
		if (($level = abs((int) $level)) > 100) $level = 100;
		$this->quality = $level;
		return $this;
	}


	/**
	 * Set sharpening level
	 * @param integer $level Range from 0 (no sharpening) to 100 (maximum sharpening). Default is 0.
	 * @return object
	 */
	public function sharpen($level)
	{
		if (($level = abs((int) $level)) > 100) $level = 100;
		$this->sharpen = $level;
		return $this;
	}


	/**
	 * Set dimension constraint
	 * @param boolean $status Set as TRUE or FALSE. Default is TRUE.
	 * @example contrain(true); <br />Do not allow the image to be enlarged beyond its original dimensions.
	 * @example contrain(false); <br />Allow the image to be enlarged beyond its original dimensions.
	 * @return object
	 */
	public function constrain($status)
	{
		$this->constrain = (bool) $status;
		return $this;
	}


	/**
	 * Set rotation angle in degrees
	 * @todo Potentially allow for degrees other than -90, -180, -270, 90, 180 and 270
	 * @param integer $angle The angle in degrees including negative numbers (eg. -90, 90, 180, 270). Default is 0 (no rotation).
	 * @return object
	 */
	public function rotate($angle)
	{
		if (($angle = (int) $angle) % 90 != 0 || $angle < -359 || $angle > 359) $angle = 0;
		$this->rotation = $angle;
		return $this;
	}


	/**
	 * Has cached image expired
	 * @param string $filePath
	 * @return boolean TRUE (Yes, file has expired) or FALSE (No, file has not expired)
	 */
	protected function hasExpired($filePath)
	{
		$filePath = trim((string) $filePath);

		// Return TRUE if the file exists and the modification time is below the specified cache lifetime
		if (file_exists($filePath) && !empty(self::$imgLifetime) && filemtime($filePath) < strtotime(self::$imgLifetime))
		{
			return true;
		}
		return false;
	}


	/**
	 * Set basic image information
	 * @param string $filePath
	 * @return boolean
	 */
	protected function setImageInfo($filePath = null)
	{
		if (!empty($filePath))
		{
			$info = getimagesize($filePath);

			if ($info)
			{
				$this->srcWidth = $info[0];
				$this->srcHeight = $info[1];

				// Obtain a "memory exhausted" prevention message
				$msg = $this->getMemoryExhaustedMsg($filePath);
				if (!empty($msg))
				{
					throw new Exception($msg);
				}

				switch ($info[2])
				{
					case 1: $this->imageType = "gif"; break;
					case 2: $this->imageType = "jpg"; break;
					case 3: $this->imageType = "png"; break;
				}
				return true;
			}
		}
		return false;
	}


	/**
	 * Set width/height for percentage resize
	 * @param integer $percentage Range from 1 to 100
	 * @return object
	 */
	public function resizePercent($percentage)
	{
		// Reset the src image dimensions - they may have been changed from previous resizing
		$this->setImageInfo($this->srcFilePath);

		// Resize type is included in the cache filename
		$this->resizeType = "percent";

		// Validate the percentage argument
		if (($percentage = abs((int) $percentage)) > 100) $percentage = 100;
		if ($percentage < 1) $percentage = 1;

		// The max and new dimensions are calculated
		$this->maxWidth = $this->dstWidth = ceil(($this->srcWidth * $percentage) / 100);
		$this->maxHeight = $this->dstHeight = ceil(($this->srcHeight * $percentage) / 100);

		// Create a destination image resource based on the new width/height
		$this->dstImg = imagecreatetruecolor($this->dstWidth, $this->dstHeight);

		return $this;
	}


	/**
	 * Set width/height for crop-to-fit resize
	 * @param integer|null $width Max width (Note: constrain() has no affect)
	 * @param integer|null $height Max height (Note: constrain() has no affect)
	 * @example resizeCropToFit(250); <br />Resize and crop image to match 250x250.
	 * @example resizeCropToFit(null, 300); <br />Resize and crop image to match 300x300.
	 * @example resizeCropToFit(250, 300); <br />Resize and crop image to match 250x300.
	 * @return object
	 */
	public function resizeCropToFit($width = null, $height = null)
	{
		// Reset the src image dimensions - they may have been changed from previous resizing
		$this->setImageInfo($this->srcFilePath);

		// Resize type is included in the cache filename
		$this->resizeType = "crop-to-fit";		

		$this->maxWidth = is_null($width) ? null : (int) abs($width);
		$this->maxHeight = is_null($height) ? null : (int) abs($height);

		if ($this->maxWidth === 0)
		{
			throw new InvalidArgumentException(__METHOD__."(): Width must be a valid integer (above 0) or null.");
		}
		elseif ($this->maxHeight === 0)
		{
			throw new InvalidArgumentException(__METHOD__."(): Height must be a valid integer (above 0) or null.");
		}
		elseif (is_null($this->maxWidth) && is_null($this->maxHeight))
		{
			throw new InvalidArgumentException(__METHOD__."(): Width and/or height must be supplied, both cannot be null.");
		}

		// Make the width and height a square if either argument is null
		if (is_null($this->maxWidth)) $this->maxWidth = $this->maxHeight;
		if (is_null($this->maxHeight)) $this->maxHeight = $this->maxWidth;

		// Assign the new width/height to the max dimensions
		$this->dstWidth = $this->maxWidth;
		$this->dstHeight = $this->maxHeight;

		// Calculate the ratio for the source and destination
		$srcRatio = $this->srcWidth / $this->srcHeight;
		$dstRatio = $this->dstWidth / $this->dstHeight;

		// Calculate the correct dimensions to generate a "Crop-to-fit" image based on the ratios
		if ($srcRatio > $dstRatio)
		{
			// Source has wider ratio compared to destination
			$tmpWidth = intval($this->srcHeight * $dstRatio);
			$tmpHeight = $this->srcHeight;
			$this->srcX = intval(($this->srcWidth - $tmpWidth) / 2);
			$this->srcY = 0;
		}
		else
		{
			// Source has taller ratio compared to destination
			$tmpWidth = $this->srcWidth;
			$tmpHeight = intval($this->srcWidth / $dstRatio);
			$this->srcX = 0;
			$this->srcY = intval(($this->srcHeight - $tmpHeight) / 2);
		}

		// Reassign the source width and height
		$this->srcWidth = $tmpWidth;
		$this->srcHeight = $tmpHeight;

		// Create a destination image resource based on the new width/height
		$this->dstImg = imagecreatetruecolor($this->dstWidth, $this->dstHeight);

		return $this;
	}


	/**
	 * Set width/height for letterbox resize
	 *
	 * Expect the following for background colours:<br />
	 * <strong>JPEG:</strong> Only a solid background colour can be applied.<br />
	 * <strong>PNG:</strong> A solid & transparent background colour can be applied.<br />
	 * <strong>GIF:</strong> A solid background colour can be applied or a full transparency is used if $bgAlpha is 127
	 * @param integer|null $width Max width.
	 * @param integer|null $height Max height
	 * @param string $bgColour 6-digit hexadecimal background colour (eg. "FFFFFF" = White)
	 * @param integer $bgAlpha Transparency of the background colour, range between 0 (opaque) and 127 (transparent).
	 * @example resizeLetterbox(250); <br />Resize image to match 250x250 using a letter box effect.
	 * @example resizeLetterbox(null, 300); <br />Resize image to match 300x300 using a letter box effect.
	 * @example resizeLetterbox(250, 300); <br />Resize image to match 250x300 using a letter box effect.
	 * @example resizeLetterbox(250, 300, "FFFFFF"); <br />Resize image to match 250x300 using a letter box effect with a white background.
	 * @example resizeLetterbox(250, 300, "FFFFFF", 80); <br />Resize image to match 250x300 using a letter box effect with a transparent white background.
	 * @return object
	 */
	public function resizeLetterbox($width = null, $height = null, $bgColour = "000000", $bgAlpha = 0)
	{
		// Reset the src image dimensions - they may have been changed from previous resizing
		$this->setImageInfo($this->srcFilePath);

		// Resize type is included in the cache filename
		$this->resizeType = "letterbox";

		$this->maxWidth = is_null($width) ? null : (int) abs($width);
		$this->maxHeight = is_null($height) ? null : (int) abs($height);

		if ($this->maxWidth === 0)
		{
			throw new InvalidArgumentException(__METHOD__."(): Width must be a valid integer (above 0) or null.");
		}
		elseif ($this->maxHeight === 0)
		{
			throw new InvalidArgumentException(__METHOD__."(): Height must be a valid integer (above 0) or null.");
		}
		elseif (is_null($this->maxWidth) && is_null($this->maxHeight))
		{
			throw new InvalidArgumentException(__METHOD__."(): Width and/or height must be supplied, both cannot be null.");
		}

		// Set the background colour to black if invalid
		if (!ctype_xdigit($bgColour) || strlen($bgColour) !== 6) $bgColour = "000000";

		// Set the alpha channel to zero if invalid
		if (($bgAlpha = abs($bgAlpha)) < 0 || $bgAlpha > 127) $bgAlpha = 0;

		// Make the width and height a square if either argument is null
		if (is_null($this->maxWidth)) $this->maxWidth = $this->maxHeight;
		if (is_null($this->maxHeight)) $this->maxHeight = $this->maxWidth;

		// Preset the new width and height to the max dimensions
		$this->dstWidth = $this->maxWidth;
		$this->dstHeight = $this->maxHeight;

		// Calculate the ratio for the source and destination
		$srcRatio = $this->srcWidth / $this->srcHeight;
		$dstRatio = $this->dstWidth / $this->dstHeight;

		// Calculate the correct dimensions to generate a Letter Box image
		if ($srcRatio < $dstRatio)
		{
			// Source has wider ratio compared to destination
			$tmpWidth = intval($this->maxHeight * $srcRatio);
			$tmpHeight = $this->maxHeight;
			$this->dstX = intval(($this->maxWidth - $tmpWidth) / 2);
			$this->dstY = 0;
		}
		else
		{
			// Source has taller ratio compared to destination
			$tmpWidth = $this->maxWidth;
			$tmpHeight = intval($this->maxWidth / $srcRatio);
			$this->dstX = 0;
			$this->dstY = intval(($this->maxHeight - $tmpHeight) / 2);
		}

		// Assign the new width and height
		$this->dstWidth = $tmpWidth;
		$this->dstHeight = $tmpHeight;

		// If the destination dimensions are larger than the source diensions and contrain is set, then re-assign the destination dimensions
		if ($this->constrain === true)
		{
			if ($this->dstWidth > $this->srcWidth) $this->dstWidth = $this->srcWidth;
			if ($this->dstHeight > $this->srcHeight) $this->dstHeight = $this->srcHeight;
		}

		// Create a destination image resource based on the max width/height
		$this->dstImg = imagecreatetruecolor($this->maxWidth, $this->maxHeight);

		// Apply a background colour (with transparency if required) for PNG's
		// Apply a solid background colour for JPEG's
		// Apply a solid background colour for GIF's or a transparent background if $bgAlpha is set to 127 (true transparency)
		if ($this->imageType !== "gif" || $bgAlpha != 127)
		{
			$colour = imagecolorclosestalpha(	$this->dstImg,
												hexdec(substr($bgColour, 0, 2)),
												hexdec(substr($bgColour, 2, 2)),
												hexdec(substr($bgColour, 4, 2)),
												$bgAlpha);

			imagefill($this->dstImg, 0, 0, $colour);
		}

		return $this;
	}


	/**
	 * Set width/height for standard resize
	 * @param integer|null $width Max width.
	 * @param integer|null $height Max height
	 * @example resize(250); <br />Resize to the max width.
	 * @example resize(null, 300); <br />Resize to the max height.
	 * @example resize(250, 300); <br />Resize to fit within height and width.
	 * @example aspectRatio(false)->resize(250); <br />Disregard aspect ratio and resize to max width.
	 * @example aspectRatio(false)->resize(null, 300); <br />Disregard aspect ratio and resize to max height.
	 * @return object
	 */
	public function resize($width = null, $height = null)
	{
		// Reset the src image dimensions - they may have been changed from previous resizing
		$this->setImageInfo($this->srcFilePath);

		// Resize type is included in the cache filename
		$this->resizeType = "standard";				

		$this->maxWidth = is_null($width) ? null : (int) abs($width);
		$this->maxHeight = is_null($height) ? null : (int) abs($height);

		if ($this->maxWidth === 0)
		{
			throw new InvalidArgumentException(__METHOD__."(): Width must be a valid integer (above 0) or null.");
		}
		elseif ($this->maxHeight === 0)
		{
			throw new InvalidArgumentException(__METHOD__."(): Height must be a valid integer (above 0) or null.");
		}
		elseif (is_null($this->maxWidth) && is_null($this->maxHeight))
		{
			throw new InvalidArgumentException(__METHOD__."(): Width and/or height must be supplied, both cannot be null.");
		}

		// Restrict the dimensions of the new image to it's original dimensions (if the original image is smaller than the max image dimensions)
		if ($this->constrain === true)
		{
			if ($this->maxWidth > $this->srcWidth)
				$this->maxWidth = $this->srcWidth;

			if ($this->maxHeight > $this->srcHeight)
				$this->maxHeight = $this->srcHeight;
		}

		// Calculate the optimal width and height (used when width or height is null)
		$optimalWidth = round($this->maxHeight * ($this->srcWidth / $this->srcHeight));
		$optimalHeight = round($this->maxWidth * ($this->srcHeight / $this->srcWidth));

		// Exact: Aspect ratio is ignored and image is stretched to fit
		if ($this->keepAspectRatio === false)
		{
			// Aspect ratio willnot be used hence use the exact maximum dimensions (Note: Opposite dimension is used if empty)
			$this->dstWidth = empty($this->maxWidth) ? $this->maxHeight : $this->maxWidth;
			$this->dstHeight = empty($this->maxHeight) ? $this->maxWidth : $this->maxHeight;
		}
		elseif ($this->keepAspectRatio === true)
		{
			if (is_null($this->maxWidth))
			{
				// Portrait: Height takes priority
				$this->dstWidth = $optimalWidth;
				$this->dstHeight = $this->maxHeight;
			}
			elseif (is_null($this->maxHeight))
			{
				// Landscape: Width takes priority
				$this->dstWidth = $this->maxWidth;
				$this->dstHeight = $optimalHeight;
			}
			elseif (!is_null($this->maxWidth) && !is_null($this->maxHeight))
			{
				// Automatic: Determine if the image is landscape or portrait and then calculate the new dimensions accordingly
				// Both width and height must stay within maximum dimensions
				if ($optimalWidth > $optimalHeight)
				{
					$this->dstWidth = $this->maxWidth;
					$this->dstHeight = $optimalHeight;
				}
				else
				{
					// Calculate the optimal width and height (used when width or height is null)
					$this->dstWidth = $optimalWidth;
					$this->dstHeight = $this->maxHeight;
				}
			}
		}

		// Create a destination image resource
		$this->dstImg = imagecreatetruecolor($this->dstWidth, $this->dstHeight);

		return $this;
	}


	/**
	 * Preserve alpha transparency
	 * @return void
	 */
	protected function preserveAlphaTransparency()
	{
		if ($this->imageType === "png")
		{
			imagealphablending($this->dstImg, false);
			imagesavealpha($this->dstImg, true);
		}
		elseif ($this->imageType === "gif")
		{
			imagecolortransparent($this->dstImg, imagecolorallocate($this->dstImg, 0, 0, 0));
			imagetruecolortopalette($this->dstImg, true, 256);
		}
	}


	/**
	 * Save resized image
	 * @param string|null $customDestinationFilePath Path to a custom destination file (if populated this is used instead of the cache path)
	 * @param boolean $createPath Create the custom directory path of $customDestinationFilePath if missing
	 * @example save(); <br />Image is resized and stored in the cache.
	 * @example save("./new/image.jpg"); <br />Image is resized and stored in a custom destination.
	 * @example save("./new/path/image.jpg", true); <br />The path (./new/path/) is created (if missing), image is resized and then stored in the custom destination.
	 * @return object
	 */
	public function save($customDestinationFilePath = null, $createPath = false)
	{
		// Initialise the cache directory variable
		$imgCacheDir = null;

		// Set a custom destination file path instead of using the cache
		if (!empty($customDestinationFilePath))
		{
			// Set the custom destination
			$this->customDestinationFilePath = $imgDestinationFilePath = $customDestinationFilePath;

			// Create the custom destination directory path if required
			if ($createPath === true)
			{
				// Attempt to create directory path and assume the basename is the filename
				$this->createDirectory($this->customDestinationFilePath, true);
			}
		}
		elseif (!empty(self::$cacheRootDir))
		{
			// Path to new cache directory
			$imgCacheDir = $this->getCacheDestinationDirectoryPath();

			// Absolute path to new destination image file
			$imgDestinationFilePath = self::getCacheRootDirectory() . $this->getCacheDestinationFilePath();
		}
		else
		{
			// A custom file path has not been supplied and a cache directory has not been created
			throw new Exception(__METHOD__."(): A cache directory has not been set, use ". __CLASS__ ."::setCacheRootDirectory(\"/path/to/cache\"); or define a custom path by using ->save(\"./custom/image.jpg\"); before resizing any images.");
		}

		// Proceed if the file doesn't exist or if the cached file has expired
		if (!file_exists($imgDestinationFilePath) || $this->hasExpired($imgDestinationFilePath))
		{
			// Create an image resource based on the correct image type
			switch ($this->imageType)
			{
				case "gif":
					if (imagetypes() & IMG_GIF)
						$this->srcImg = imagecreatefromgif($this->srcFilePath);
					else
						throw new Exception(__METHOD__."(): GIF is not supported by this version of GD");
				break;
				case "jpg":
					if (imagetypes() & IMG_JPG)
						$this->srcImg = imagecreatefromjpeg($this->srcFilePath);
					else
						throw new Exception(__METHOD__."(): JPEG is not supported by this version of GD");
				break;
				case "png":
					if (imagetypes() & IMG_PNG)
						$this->srcImg = imagecreatefrompng($this->srcFilePath);
					else
						throw new Exception(__METHOD__."(): PNG is not supported by this version of GD");
				break;
			}

			// Proceed if the image resource was created
			if ($this->srcImg)
			{
				// Proceed if the destination image resource has been created - a resize method must be called first
				if (!empty($this->dstImg))
				{

					// Preserve the alpha transparency of the destination image
					$this->preserveAlphaTransparency();

					// Create the cache directory path if we are using the cache and the image resources are successfully created
					if (!empty($imgCacheDir))
					{
						self::createDirectory(self::getCacheRootDirectory() . $imgCacheDir);
					}	

					// Generate the resized image
					imagecopyresampled(	$this->dstImg,
										$this->srcImg,
										$this->dstX,
										$this->dstY,
										$this->srcX,
										$this->srcY,
										$this->dstWidth,
										$this->dstHeight,
										$this->srcWidth,
										$this->srcHeight);

					// Rotate the image if the function imagerotate() exists and the rotation setting is not zero
					if (function_exists("imagerotate") && $this->rotation !== 0)
					{
						// Background colour is hard-coded because we only accept valid rotations of modulus 90 (eg. 0, -90, 180 etc...)
						$this->dstImg = imagerotate($this->dstImg, $this->rotation, (256*256*256)-1);
					}

					// Sharpen the image if the function imageconvolution() exists and the sharpening level (percentage) is above zero
					if (function_exists("imageconvolution") && $this->sharpen > 0)
					{
						// The sharpen level is supplied as a percentage and we use this against a maximum of 3
						$m1 = round(-((3/100)*$this->sharpen), 2);
						$m2 = $m1+0.2;

						$sharpenMatrix = array(
							array($m1, $m2, $m1),
							array($m2, round((34/100)*$this->sharpen), $m2),
							array($m1, $m2, $m1)
						);

						// Calculate the divisor
						$divisor = array_sum(array_map("array_sum", $sharpenMatrix));

						// Apply convolution matrix
						imageconvolution($this->dstImg, $sharpenMatrix, $divisor, $offset = 0);
					}

					// Output the image to the new destination
					switch ($this->imageType)
					{
						case "gif":
							imagegif($this->dstImg, $imgDestinationFilePath);
						break;
						case "jpg":
							imagejpeg($this->dstImg, $imgDestinationFilePath, $this->quality);
						break;
						case "png":
							// Image quality is set as a range from 0-100 (the default for JPEG's)
							// However, PNG's are different and use 0 (no compression) to 9 (highest compression)
							// Hence, to maintain the continuity of one range (ie. 0-100) we apply some simply maths
							imagepng($this->dstImg, $imgDestinationFilePath, 9-round(($this->quality/100)*9), PNG_ALL_FILTERS);
						break;
					}

					// Memory can be easily exhausted if memory is not released when using the PHP Registry Pattern, hence destroy resources
					imagedestroy($this->dstImg);
					imagedestroy($this->srcImg);

				}
				else
				{
					throw new Exception(__METHOD__."(): A resize method such as resize(), resizeCropToFit(), resizeLetterbox() or resizePercent must be called before the save() method.");
				}
			}
			else
			{
				throw new UnexpectedValueException(__METHOD__."(): Failed to create image resource for \"{$this->srcFilePath}\" (Possibly a corrupt file or invalid image type)");
			}
		}

		return $this;
	}


	/**
	 * Get image URI
	 * @return string URI
	 */
	public function getUri()
	{
		// Return a custom destination URI if a custom path was declared, however this should be known outside of the object
		if (!empty($this->customDestinationFilePath))
		{
			$uri = substr_replace(realpath($this->customDestinationFilePath), "", 0, strlen(self::getDocumentRoot()));
			$uri = "/" . str_replace("\\", "/", $uri);
			return $uri;
		}

		// It's possible that a cache directory is stored below the Document Root and a URI rewrite is used instead
		// Hence, if a cache URI has been set then return the cache URI with the file path appended
		if (!empty(self::$cacheUri))
		{
			return str_replace("\\", "/", self::getCacheUri() . $this->getCacheDestinationDirectoryPath() . $this->getCacheDestinationFilename());
		}
		else
		{
			// However, if a cache URI was not set then perform a substring to determine the URI cache path
			// Remove the document root from the beginning of the file path string and then convert slashes
			$uri = substr_replace(self::getCacheRootDirectory() . $this->getCacheDestinationFilePath(), "", 0, strlen(self::getDocumentRoot()));
			$uri = "/" . str_replace("\\", "/", $uri);
			return $uri;
		}
	}


}
