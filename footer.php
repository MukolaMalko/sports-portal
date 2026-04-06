<?php
/**
 * Footer Template
 */
?>
<footer class="site-footer">
  <div class="container">
    <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?> — Система управління спортивними подіями</p>
    <?php wp_nav_menu(['theme_location'=>'footer','container'=>false,'fallback_cb'=>false]); ?>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
