<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
<?php $this->need('side.php'); ?>

<div class="main">
  <div class="main_mark inner">
    <article role="main" class="h-entry" itemscope itemtype="http://schema.org/Article">
      <?php if ($this->fields->coverUrl): ?>
        <div class="entry-cover">
          <img class="u-photo" src="<?php $this->fields->coverUrl(); ?>" alt="<?php $this->title(); ?> cover">
        </div>
      <?php endif; ?>

      <h1 class="p-name" itemprop="headline"><?php $this->title(); ?></h1>

      <div class="e-content js-content yue" itemprop="articleBody">
          <?php $this->content(); ?>
      </div>
    </article>
    <?php $this->need('comments.php'); ?>
  </div>
</div>

<?php $this->need('footer.php'); ?>
