<?php
/**
 * @package         Joomla.Site
 * @subpackage      mod_advertising
 *
 * @author overnet
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license         GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


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
 * @property string      $jquery_tag
 *
 * @since  4.0
 */
class ModAdvertisingHelper
{

	/** @var array
	 * @since
	 */
	private $data = [];


	/** @var bool
	 * @since
	 */
	public static $canShowBlockState = false;


	/** @var int
	 * @since
	 */
	private const PERIOD_TIME = 3600;


	/**
	 * @param   object  $params
	 *
	 * @since 3.9
	 */
	public function __construct(object $params)
	{
		foreach ($params as $key => $param)
		{
			$this->data[$key] = $params->get($key);
		}
		self::$canShowBlockState = $this->canShowBlock();
		$this->init();
		$this->getAndSetCookie();
	}

	/**
	 *
	 *
	 * @since version
	 */
	private function getAndSetCookie(): void
	{
		try
		{
			$app   = JFactory::getApplication();
			$value = $app->input->cookie->get($this->cookie_id);
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

				$app->input->cookie->set($this->cookie_id, 1, $time,
					$app->get('cookie_path', '/'),
					$app->get('cookie_domain'), $app->isSSLConnection()
				);
			}
		}
		catch (\Exception $exception)
		{

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
		try
		{
			$app = JFactory::getApplication();

			return $app->input->cookie->get($this->cookie_id) === null;
		}
		catch (\Exception $exception)
		{
			return false;
		}
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
	 * @since version
	 */
	private function init(): void
	{
		$document = JFactory::getDocument();

		// Load css and scripts
//        JHtml::_('stylesheet', Juri::base() . 'modules/mod_advertising/assets/css/style.css', array(
//            'version' => '1.0.0',
//            'relative' => true
//        ));

		$blockLink       = $this->block_link;
		$blockLinkTarget = $this->block_link_target;

		$jquery = $this->jquery_tag;

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


		$document->addStyleDeclaration("
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
                      font-size: 0.875rem;
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


		$document->addScriptDeclaration("
                $jquery(document).ready(function() {
                        setTimeout(function () {
                            $jquery('#sidebar-banner').animate({'$blockSidePosition': '+=$blockSidePositionValueForJS'});
                        }, $blockDelay);
                     });
                ");


		$document->addScriptDeclaration("
                $jquery(document).ready(function() {
                    $jquery('.sidebar-banner-close').on('click', function () {
                        $jquery('#sidebar-banner').animate({'$blockSidePosition': '-=$blockSidePositionValueForJS'});
                    });
                    $jquery('.sidebar-banner-heading .text, .sidebar-banner-content').on('click', function () {
                         window.open('$blockLink', '$blockLinkTarget')
                    });
                });
          ");


	}


	/**
	 *
	 * @return object
	 *
	 * @since version
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
	 * @since version
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
	 * @since version
	 */
	private function getImageParam(string $parameter)
	{
		return $this->getImageData()[$parameter] ?? null;
	}


	/**
	 *
	 * @return array
	 *
	 * @since version
	 */
	private function getImageData(): array
	{
		$image         = false;
		$fullImagePath = JPATH_BASE . DS . $this->image_src;
		if (file_exists($fullImagePath))
		{
			$image = getimagesize($fullImagePath);
		}

		return array(
			'width'       => $image && isset($image[0]) ? $image[0] : null,
			'height'      => $image && isset($image[1]) ? $image[1] : null,
			'string_size' => $image && isset($image[3]) ? $image[3] : null,
			'bits'        => $image['bits'],
			'channels'    => $image['channels'],
			'mime'        => $image['mime'],
		);
	}


}
