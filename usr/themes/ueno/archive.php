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
    <h3 class="archive-title"><?php $this->archiveTitle([
            'category' => _t('分类 %s 下的文章'),
            'search'   => _t('包含关键字 %s 的文章'),
            'tag'      => _t('标签 %s 下的文章'),
            'author'   => _t('%s 发布的文章')
        ], '', ''); ?></h3>
<?php if ($this->have()): ?>
<?php
while ($this->next()):
?>
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
<?php endwhile; ?>
<?php else: ?>
    <article class="post">
        <h2 class="post-title"><?php _e('没有找到内容'); ?></h2>
    </article>
<?php endif; ?>

<?php $this->pageNav('&laquo; 前一页', '后一页 &raquo;'); ?>
</div>
</div>

<?php $this->need('footer.php'); ?>
