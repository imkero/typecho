<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

$this->need('header.php');
$this->need('side.php');
?>

<div class="main">
  <div class="main_mark inner">
    <article role="main" class="h-entry" itemscope itemtype="http://schema.org/Article">
      <?php if ($this->fields->coverUrl): ?>
        <div class="entry-cover">
          <img class="u-photo" src="<?php $this->fields->coverUrl(); ?>" alt="<?php $this->title(); ?> cover">
        </div>
      <?php endif; ?>

      <div class="entry-meta page-meta">
        <span id="view-count-container" class="view-count-container">
          <i class="icon icon-browse view-icon"></i>
          <span id="view-count-text">-</span>
        </span>
      </div>

      <h1 class="p-name" itemprop="headline"><?php $this->title(); ?></h1>

      <div class="e-content js-content yue" itemprop="articleBody">
        <?php $this->content(); ?>
      </div>

      <?php if ($this->fields->invisibleContent) { $this->fields->invisibleContent(); } ?>
    </article>
    <?php $this->need('comments.php'); ?>
  </div>
</div>

<?php $this->need('footer.php'); ?>
