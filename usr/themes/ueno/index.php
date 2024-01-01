<?php
/**
 * Typlog Ueno Theme
 *
 * @package Typlog Ueno Theme
 * @author 上野 & imkero
 * @version 1.0
 * @link https://themes.typlog.com/#/ueno
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
$this->need('side.php');
?>

<div class="main">
<div class="inner main_mark">
<?php
while ($this->next()):
?>
<?php if ($this->fields->coverUrl): ?>
<div class="item Article Cover">
<a class="item-cover" href="<?php $this->permalink() ?>">
<div class="item-cover_image">
<div class="js-cover" style="background-image:url(<?php $this->fields->coverUrl(); ?>)"></div>
</div>
<div class="item-cover_inner">
<div>
    <span class="item-category"><?php $this->category(',', false); ?></span>
    <time class="js-time" datetime="<?php $this->date('c'); ?>" itemprop="datePublished"><?php $this->date(); ?></time>
</div>
<h3><?php $this->title() ?></h3>
<?php $this->content('- 阅读剩余部分 -'); ?>
</div>
</a>
</div>
<?php else: ?>
<div class="item Article Text">
<div class="item-meta">
    <span class="item-category"><?php $this->category(',', false); ?></span>
    <time class="js-time" datetime="<?php $this->date('c'); ?>" itemprop="datePublished"><?php $this->date(); ?></time>
</div>
<a class="item-main" href="<?php $this->permalink() ?>">
<h3><?php $this->title() ?></h3>
</a>
<div class="item-content">
<?php $this->content('- 阅读剩余部分 -'); ?>
</div>
</div>
<?php endif; ?>
<?php endwhile; ?>
<?php $this->pageNav('&laquo; 前一页', '后一页 &raquo;'); ?>
</div>
</div>

<?php $this->need('footer.php'); ?>
