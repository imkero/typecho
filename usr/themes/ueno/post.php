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

      <div class="entry-meta">
        <span class="entry-tags entry-category-tag">
            <?php $this->category(''); ?>
        </span>
        <time class="dt-published" datetime="<?php $this->date('c'); ?>" itemprop="datePublished"><?php $this->date(); ?></time>
        <span id="view-count-container" class="view-count-container">
          <i class="icon icon-browse view-icon"></i>
          <span id="view-count-text">-</span>
        </span>
      </div>

      <h1 class="p-name" itemprop="headline"><?php $this->title(); ?></h1>

      <div class="e-content js-content yue" itemprop="articleBody">
          <?php if ($this->fields->disableToc) { $this->content(); } else { printContentWithToc($this->content); } ?>
      </div>

      <?php if ($this->tags): ?>
        <div class="entry-tags">
          <?php $this->tags('', true, ''); ?>
        </div>
      <?php endif; ?>

    </article>

    <?php if ($this->is('post') && $this->options->licenseHtml): ?>
    <div class="entry-license" itemscope="" itemtype="https://schema.org/CreativeWork">
      <?php
      if (!empty($this->options->licenseIcons)):
        foreach ($this->options->licenseIcons as $icon):
      ?>
          <i class="icon icon-<?php echo $icon ?>"></i>
      <?php
        endforeach;
      endif;
      ?>
      <div class="license_description" itemprop="usageInfo">
        <?php $this->options->licenseHtml(); ?>
      </div>
    </div>
    <?php endif; ?>

    <?php $this->need('comments.php'); ?>
  </div>
</div>

<?php $this->need('footer.php'); ?>
