<?php
/**
 * 文章一览
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

$this->need('header.php');
$this->need('side.php');
?>

<div class="main">
  <div class="main_mark inner">
    <article role="main" class="h-entry">
      <h1 class="p-name" itemprop="headline">文章一览</h1>

      <div class="e-content js-content yue">
        <h2>
          分类
        </h2>
        <ul class="archive-category-list">
          <?php $this->widget('Widget_Metas_Category_List_CountOrdered')->to($categories); ?>
          <?php while ($categories->next()): if ($categories->count === 0) continue; ?>
            <li><a href="<?php $categories->permalink(); ?>" title="<?php $categories->name(); ?>"><?php $categories->name(); ?></a> (<?php $categories->count(); ?>)</li>
          <?php endwhile; ?>
        </ul>
        <div class="archive-list">
          <?php $this->widget('Widget_Contents_Post_Recent', 'pageSize=10000')->to($archives); ?>
          <?php
          $year = 0; $mon = 0;
          $output = '<div id="archives">';
          while ($archives->next()) {
            $year_tmp = date('Y', $archives->created);
            $mon_tmp = date('n', $archives->created);

            if ($mon != $mon_tmp && $mon > 0) $output .= '</ul></li>';
            if ($year != $year_tmp && $year > 0) $output .= '</ul>';

            if ($year != $year_tmp) {
              $year = $year_tmp;
              $output .= '<h2>'. $year .' 年</h2>'; // 输出年份
              $output .= '<ul>';
            }
            if ($mon != $mon_tmp) {
              $mon = $mon_tmp;
              $output .= '<li><span>'. $mon .' 月</span>'; // 输出月份
              $output .= '<ul>';
            }
            $output .= '<li>'
              . implode('', array_map(function ($category) {
                return '<span class="archives-item-category">' . $category['name'] . '</span> ';
              }, $archives->categories))
              . '<a href="'.$archives->permalink
              . '" target="_blank">'
              . $archives->title
              . '</a></li>'; // 输出文章
          }

          if ($year > 0) {
            $output .= '</ul></li></ul></div>';
          }
          $output .= '</div>';
          echo $output;
          ?>
        </div>
      </div>

      <?php if ($this->fields->invisibleContent) { $this->fields->invisibleContent(); } ?>
    </article>
  </div>
</div>

<?php $this->need('footer.php'); ?>
