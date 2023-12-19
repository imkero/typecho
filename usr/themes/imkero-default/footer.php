<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>

        </div><!-- end .row -->
    </div>
</div><!-- end #body -->

<footer id="footer" role="contentinfo">
    &copy; <?php echo date('Y'); ?> <a href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title(); ?></a>.
    <?php _e('由 <a href="https://typecho.org">Typecho</a> 强力驱动'); ?>.
</footer><!-- end #footer -->

<?php if (!$this->user->hasLogin()): ?>
<script async src="<?php $this->options->themeUrl('pgview.js'); ?>" data-website-id="7eacac23-c4f2-4053-8435-fd989fec95ae" data-host-url="https://pageview.imkero.net"></script>
<?php endif; ?>

<?php $this->footer(); ?>
</body>
</html>
