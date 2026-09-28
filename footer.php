  <?php if ( is_active_sidebar( 'right-sidebar' ) ) : ?>
    <aside id="right-sidebar" class="sidebar">
      <?php dynamic_sidebar( 'right-sidebar' ); ?>
    </aside>
  <?php endif; ?>
</div>
<footer id="colophon" class="site-footer" role="contentinfo">
  <?php get_template_part( 'inc/footer-columns' ); ?>
  <?php if ( has_nav_menu( 'footer' ) || is_active_sidebar( 'footer-secondary' ) ) : ?>
  <section class="footer-secondary">
    <?php if ( has_nav_menu( 'footer' ) ) : ?>
      <?php get_template_part( 'inc/footer-menu' ); ?>
    <?php endif; ?>
    <?php if ( is_active_sidebar( 'footer-secondary' ) ) : ?>
      <div class="footer-secondary-widgets">
        <?php dynamic_sidebar( 'footer-secondary' ); ?>
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