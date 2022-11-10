<?php
/**
 * @package         Joomla.Site
 * @subpackage      mod_advertising
 *
 * @author          overnet
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license         GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Module\Advertising\Site\Helper;

\defined('_JEXEC') or die;


use JFactory;
use Joomla\CMS\Application\WebApplication;


/**
 * Helper for mod_advertising
 *
 *
 * @property string      $block_header
 * @property string      $block_link
 * @property string      $block_link_target
 * @property int         $block_next_show_in_hours
 * @property string      $cookie_id
 * @property int         $test_mode
 * @property int         $block_width
 * @property string      $block_size_unit
 * @property string      $block_type
 * @property string      $block_side_position
 * @property int         $block_x_position
 * @property int         $block_y_position
 * @property int         $block_delay
 * @property string      $block_background_color
 * @property string      $block_header_text_color
 * @property string      $block_content_text_color
 * @property string      $block_content_background_color
 * @property string      $image_src
 * @property int         $image_max_width
 * @property string      $image_size_unit
 * @property string      $block_content
 * @property string      $block_content_padding
 * @property string      $layout
 * @property string|null $moduleclass_sfx
 * @property int         $cache
 * @property int         $cache_time
 * @property string      $module_tag
 * @property string      $bootstrap_size
 * @property string      $header_tag
 * @property string|null $header_class
 * @property string      $style
 * @property int         $load_jquery
 * @property string      $jquery_tag
 *
 * @since  4.2
 */
final class AdvertisingHelper
{

	/**
	 * @var object
	 * @since  4.2
	 */
	public object $module;

	/**
	 * @var WebApplication|null
	 * @since  4.2
	 */
	private static ?WebApplication $app = null;

	/**
	 * @var array
	 * @since
	 */
	private array $data = [];


	/** @var bool
	 * @since 4.2
	 */
	public static bool $canShowBlockState = false;


	/** @var int
	 * @since 4.2
	 */
	private const PERIOD_TIME = 3600;

	/**
	 * @param   object  $module
	 * @param   object  $params
	 *
	 * @throws \Exception
	 * @since 4.2
	 */
	public function __construct(object $module, object $params)
	{
		foreach ($params as $key => $param)
		{
			$this->data[$key] = $params->get($key);
		}

		$this->module = $module;

		if (self::$app === null)
		{
			self::$app = JFactory::getApplication();
		}

		self::$canShowBlockState = $this->canShowBlock();
		$this->init();
		$this->getAndSetCookie();

	}


	/**
	 * @param $name
	 * @param $value
	 *
	 *
	 * @since version
	 */
	public function __set($name, $value)
	{
		$this->data[$name] = $value;
	}


	/**
	 * @param $name
	 *
	 * @return mixed|void
	 *
	 * @since version
	 */
	public function __get($name)
	{
		if (array_key_exists($name, $this->data))
		{
			return $this->data[$name];
		}
		throw new \RuntimeException("$name dow not exists");
	}

	/**
	 * @param $name
	 *
	 * @return bool
	 *
	 * @since version
	 */
	public function __isset($name)
	{
		return isset($this->data[$name]);
	}


	/**
	 * @since 4.2
	 */
	private function getAndSetCookie(): void
	{
		$value = self::$app->input->cookie->get($this->cookie_id);
		if ($value === null)
		{
			if ($this->test_mode === 1)
			{
				$time = time() + 10; // 10 seconds
			}
			else
			{
				$time = time() + $this->block_next_show_in_hours * self::PERIOD_TIME; //* 86400 a week;
			}

			self::$app->input->cookie->set($this->cookie_id, 1, $time,
				self::$app->get('cookie_path', '/'),
				self::$app->get('cookie_domain'), self::$app->isSSLConnection()
			);
		}
	}

	/**
	 *
	 * @return bool
	 *
	 * @since version
	 */
	public function canShowBlock(): bool
	{
		return self::$app->input->cookie->get($this->cookie_id) === null;
	}


	/**
	 * @since version
	 */
	private function init(): void
	{
		/** @var  $document */
		$document = self::$app->getDocument();

		$blockLink       = $this->block_link;
		$blockLinkTarget = $this->block_link_target;

		$jqueryTag = $this->jquery_tag;

		$blockWidthUnit    = $this->block_width . $this->block_size_unit;
		$blockSidePosition = $this->block_side_position;

		$blockWidthToPxValue         = $this->block_size_unit !== 'px' ? $this->block_width * 16 : $this->block_width;
		$blockSidePositionValue      = $this->block_x_position + $blockWidthToPxValue;
		$blockSidePositionValueInPx  = $blockSidePositionValue . 'px';
		$blockSidePositionValueForJS = $blockSidePositionValue + $this->block_x_position;

		$blockYPosition = $this->block_y_position . 'px';

		$blockBackgroundColor        = $this->block_background_color;
		$blockContentBackgroundColor = $this->block_content_background_color;
		$blockContentTextColor       = $this->block_content_text_color;
		$blockContentViewPadding     = $this->block_content_padding;

		$blockDelay = $this->block_delay;

		$imageWidth = $this->image()->width . 'px';

		if ($this->load_jquery)
		{
			$document->getWebAssetManager()->useAsset('script', 'jquery');
		}


		$document->getWebAssetManager()->addInlineStyle("
                #sidebar-banner {
                     position: fixed;
                     max-width: $blockWidthUnit;
                     bottom: $blockYPosition;
                     $blockSidePosition: -$blockSidePositionValueInPx;
                     background: $blockBackgroundColor;
                     border: .125rem solid  $blockBackgroundColor;
                     box-shadow: 2px 2px 12px rgba(#000000, 0,375);
                    z-index: 9999;
                }
                #sidebar-banner .sidebar-banner-heading {
                      position: relative;
                      padding: 0.75rem 3rem 0.75rem 0.75rem;
                      font-size: 1rem;
                      font-weight: 700;
                      color: white;
                }
                #sidebar-banner .sidebar-banner-heading:hover {
                      cursor: pointer;
                      text-decoration: underline;
                }
                #sidebar-banner .sidebar-banner-heading .sidebar-banner-close {
                      position: absolute;
                      top: 0.25rem;
                      right: 0.75rem;
                      color: white;
                      display: block;
                      font-size: 1.5rem;
                      -webkit-transition-duration: 0.2s;
                            transition-duration: 0.2s;
                }
                #sidebar-banner .sidebar-banner-heading .sidebar-banner-close:hover {
                      cursor: pointer;
                      color: black;
                      -webkit-transition-duration: 0.2s;
                              transition-duration: 0.2s;
                }
                
                 .sidebar-banner-content {
                     background: $blockContentBackgroundColor;
                     color: $blockContentTextColor;
                }
                
                #sidebar-banner .sidebar-banner-content img {
                    width: 100%;
                    max-width: $imageWidth;
                }
                #sidebar-banner .sidebar-banner-content:hover {
                    cursor: pointer;
                }
                .sidebar-banner-content-view {
                     padding: $blockContentViewPadding;
                }
          
        ");


		$document->getWebAssetManager()->addInlineScript("
                $jqueryTag(document).ready(function() {
                        setTimeout(function () {
                            $jqueryTag('#sidebar-banner').animate({'$blockSidePosition': '+=$blockSidePositionValueForJS'});
                        }, $blockDelay);
                     });
                ");


		$document->getWebAssetManager()->addInlineScript("
                $jqueryTag(document).ready(function() {
                    $jqueryTag('.sidebar-banner-close').on('click', function () {
                        $jqueryTag('#sidebar-banner').animate({'$blockSidePosition': '-=$blockSidePositionValueForJS'});
                    });
                    $jqueryTag('.sidebar-banner-heading .text, .sidebar-banner-content').on('click', function () {
                         window.open('$blockLink', '$blockLinkTarget')
                    });
                });
          ");


	}


	/**
	 *
	 * @return object
	 *
	 * @since 4.2
	 */
	public function image(): object
	{
		$width  = $this->getImageParam('width');
		$height = $this->getImageParam('height');

		if ($this->isImageExist() && $this->getImageParam('width') > $this->image_max_width)
		{
			$proportion = $this->getImageParam('width') / $this->image_max_width;
			$width      = $this->image_max_width;
			$height     = $this->getImageParam('height') / $proportion;
		}

		return (object) array(
			'width'  => $this->isImageExist() ? $width : null,
			'height' => $this->isImageExist() ? $height : null,
		);
	}


	/**
	 *
	 * @return bool
	 *
	 * @since 4.2
	 */
	public function isImageExist(): bool
	{
		return file_exists(JPATH_BASE . DS . $this->image_src);
	}


	/**
	 * @param   string  $parameter
	 *
	 * @return mixed|null
	 *
	 * @since 4.2
	 */
	private function getImageParam(string $parameter): ?string
	{
		return $this->getImageData()[$parameter] ?? null;
	}


	/**
	 *
	 * @return array
	 *
	 * @since 4.2
	 */
	private function getImageData(): array
	{
		$image         = false;
		$fullImagePath = JPATH_BASE . DS . $this->image_src;
		if (file_exists($fullImagePath))
		{
			$image = getimagesize($fullImagePath);
		}

		return [
			'width'       => $image && isset($image[0]) ? $image[0] : null,
			'height'      => $image && isset($image[1]) ? $image[1] : null,
			'string_size' => $image && isset($image[3]) ? $image[3] : null,
			'bits'        => $image['bits'],
			'channels'    => $image['channels'],
			'mime'        => $image['mime'],
		];
	}


}
