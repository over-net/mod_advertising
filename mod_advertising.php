<?php
/**
 * @package         Joomla.Site
 * @subpackage      mod_advertising
 *
 * @author          overnet
 *
 * @copyright   (C) 2005 Open Source Matters, Inc. <https://www.joomla.org>
 * @license         GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


use Joomla\Module\Advertising\Site\Helper\AdvertisingHelper;

/**
 * @var object $params
 * @var object $module
 */

try
{
	$model = new AdvertisingHelper($module, $params);
}
catch (Exception $e)
{
}


require JModuleHelper::getLayoutPath('mod_advertising', $params->get('layout', 'default'));
