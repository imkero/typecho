<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<aside class="side">
  <div class="inner">
    <?php if ($this->options->logoUrl): ?>
      <a class="side_logo" href="<?php $this->options->siteUrl(); ?>">
        <img class="side_logo-img" src="<?php $this->options->logoUrl(); ?>" alt="<?php $this->options->title; ?>" width="120" height="120">
      </a>
    <?php endif; ?>
    <h1 class="side_title">
      <a class="h-card" rel="me" href="<?php $this->options->siteUrl(); ?>">
        <?php $this->options->title(); ?>
      </a>
    </h1>
    <?php if ($this->options->description()): ?>
      <h2 class="side_subtitle"><?php $this->options->description(); ?></h2>
    <?php endif; ?>
    
    <?php $sidebarNavLinks = getSidebarNavLinks(); ?>
    <ul class="side_links">
      <li><a href="<?php $this->options->siteUrl(); ?>">首页</a></li>

      <?php foreach ($sidebarNavLinks as $nav): ?>
        <li><a href="<?php echo $nav['href']; ?>" <?php if ($nav['isExternal']): ?> target="_blank" rel="noopener"<?php endif; ?>><?php echo $nav['title']; ?></a></li>
      <?php endforeach; ?>
      <?php if (in_array('ShowPages', $this->options->sidebarBlock)): ?>
        <?php \Widget\Contents\Page\Rows::alloc()->to($pages); ?>
        <?php while ($pages->next()): ?>
          <li>
            <a
              href="<?php $pages->permalink(); ?>"
              title="<?php $pages->title(); ?>">
              <?php $pages->title(); ?>
            </a>
          </li>
        <?php endwhile; ?>
      <?php endif; ?>
    </ul>

    <?php $sidebarSocialLinks = getSidebarSocialLinks(); ?>
    <nav class="side_social">
      <?php foreach ($sidebarSocialLinks as $nav): ?>
      <a href="<?php echo $nav['href'] ?>" rel="me noopener" aria-label="<?php echo $nav['title'] ?>" title="<?php echo $nav['title'] ?>">
        <i class="icon icon-<?php echo $nav['icon'] ?>" aria-hidden="true"></i>
      </a>
      <?php endforeach; ?>
    </nav>
  </div>
</aside>
