<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
</div><!-- end .body -->

<div class="blog-foot">
    <div>
    &copy; <?php echo date('Y'); ?> <a href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title(); ?></a>.
    <?php _e('由 <a href="https://typecho.org">Typecho</a> 强力驱动'); ?>.
</div>
</div>

<?php $this->footer(); ?>


</body>
</html>
