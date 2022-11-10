<?php
/**
 * @package         Joomla.Site
 * @subpackage      mod_advertising
 * @author          overnet
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license         GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


use Joomla\Module\Advertising\Site\Helper\AdvertisingHelper;

/** @var AdvertisingHelper $model */

?>

<?php if (AdvertisingHelper::$canShowBlockState) : ?>
    <div id="sidebar-banner">
        <div class="sidebar-banner-heading">
            <div class="text">
				<?= $model->block_header ?>
            </div>
            <span class="sidebar-banner-close">&times;</span>
        </div>
        <div class="sidebar-banner-content">
			<?php if ($model->block_type === 'image' && $model->isImageExist()) : ?>
                <img width="<?= $model->image()->width ?>" height="<?= $model->image()->height ?>"
                     src="<?= $model->image_src ?>" alt="<?= htmlspecialchars($model->block_header) ?>">
			<?php endif ?>
			<?php if ($model->block_type === 'text') : ?>
                <div class="sidebar-banner-content-view">
					<?= $model->block_content ?>
                </div>
			<?php endif ?>
        </div>
    </div>
<?php endif ?>




