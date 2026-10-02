<?php
/**
 * Site footer. Closes the content area opened in header.php after the
 * right sidebar.
 *
 * @package NRDS
 */

?>
		<?php get_sidebar( 'right' ); ?>
	</div><!-- #site-content -->

	<?php get_template_part( 'template-parts/footer/site-footer' ); ?>
</div><!-- .site-container -->

<?php wp_footer(); ?>
</body>
</html>
