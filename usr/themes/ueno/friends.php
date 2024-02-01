<?php
/**
 * 友情链接
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

$this->need('header.php');
$this->need('side.php');

$data = getArchiveJsonData($this);
$links = $data && $data['links'] ? $data['links'] : [];
?>

<div class="main">
  <div class="main_mark inner">
    <article role="main" class="h-entry" itemscope itemtype="http://schema.org/Article">
      <div class="entry-meta page-meta">
        <span id="view-count-container" class="view-count-container">
          <i class="icon icon-browse view-icon"></i>
          <span id="view-count-text">-</span>
        </span>
      </div>

      <h1 class="p-name" itemprop="headline"><?php $this->title(); ?></h1>

      <div class="e-content js-content yue" itemprop="articleBody">
        <div class="link-container">
          <?php foreach ($links as $link): ?>
          <div class="link-item">
            <div class="link-avatar">
              <a href="<?php echo $link['href']; ?>" rel="noopener" target="_blank">
                <img class="link-avatar-img" src="<?php echo $link['icon']; ?>">
              </a>
            </div>
            <div class="link-text">
              <div class="link-title">
                <a href="<?php echo $link['href']; ?>" rel="noopener" target="_blank">
                  <?php echo $link['title']; ?>
                </a>
              </div>
              <div class="link-desc"><?php echo $link['desc']; ?></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php $this->content(); ?>
      </div>

      <?php if ($this->fields->invisibleContent) { $this->fields->invisibleContent(); } ?>
    </article>
    <?php $this->need('comments.php'); ?>
  </div>
</div>

<?php $this->need('footer.php'); ?>
