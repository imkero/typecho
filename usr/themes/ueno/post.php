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

      <div class="entry-meta">
        <span class="entry-tags entry-category-tag">
            <?php $this->category(''); ?>
        </span>
        <time class="dt-published" datetime="<?php $this->date('c'); ?>" itemprop="datePublished"><?php $this->date(); ?></time>
      </div>

      <h1 class="p-name" itemprop="headline"><?php $this->title(); ?></h1>

      <div class="e-content js-content yue dark-code" itemprop="articleBody">
          <?php $this->content(); ?>
      </div>

      <?php if ($this->tags): ?>
        <div class="entry-tags">
          <?php $this->tags('', true, ''); ?>
        </div>
      <?php endif; ?>

    </article>

    <?php if ($this->is('post')): ?>
        <div class="entry-license" itemscope="" itemtype="https://schema.org/CreativeWork">
        <i class="icon icon-cc_by"></i>
        <i class="icon icon-cc_nc"></i>
        <div class="license_description" itemprop="usageInfo">
        除非另有说明，本页面上的内容采用
        <a href="https://creativecommons.org/licenses/by-nc/4.0/deed.zh-hans" target="_blank" rel="noopener">知识共享 (Creative Commons) 署名—非商业性使用 4.0 公共许可协议 (CC BY-NC 4.0)</a> 进行许可。
        </div>
        </div>
    <?php endif; ?>

    <?php $this->need('comments.php'); ?>
  </div>
</div>

<?php $this->need('footer.php'); ?>
