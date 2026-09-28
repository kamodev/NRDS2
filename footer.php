  <?php if ( is_active_sidebar( 'right-sidebar' ) ) : ?>
    <aside id="right-sidebar" class="sidebar">
      <?php dynamic_sidebar( 'right-sidebar' ); ?>
    </aside>
  <?php endif; ?>
</div>
<footer id="colophon" class="site-footer" role="contentinfo">
  <?php if ( is_active_sidebar( 'footer-widget-1' ) || is_active_sidebar( 'footer-widget-2' ) || is_active_sidebar( 'footer-widget-3' )) : ?>
  <section class="footer-widgets-area">
    <?php if ( is_active_sidebar( 'footer-widget-1' ) ) : ?>
      <div class="footer-widget-column">
        <?php dynamic_sidebar( 'footer-widget-1' ); ?>
      </div>
    <?php endif; ?>
    <?php if ( is_active_sidebar( 'footer-widget-2' ) ) : ?>
      <div class="footer-widget-column">
        <?php dynamic_sidebar( 'footer-widget-2' ); ?>
      </div>
    <?php endif; ?>
    <?php if ( is_active_sidebar( 'footer-widget-3' ) ) : ?>
      <div class="footer-widget-column">
        <?php dynamic_sidebar( 'footer-widget-3' ); ?>
      </div>
    <?php endif; ?>
    </section>
  <?php endif; ?>
  <section class="site-copyright">
    <p>&copy; <?php echo date('Y'); ?> National Readiness &amp; Defense. All rights reserved.</p>
  </section>
</footer>
</div> <!-- .site-container -->
<?php wp_footer(); ?>
</body>
</html>