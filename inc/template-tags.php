<?php
/**
 * Template helpers.
 *
 * @package NRDS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Site logo, or the site title when no logo is set.
 *
 * @param string $title_tag Tag for the site title text (h1 on the front page, p elsewhere).
 */
function nrds_site_brand( $title_tag = 'p' ) {
	if ( has_custom_logo() ) {
		the_custom_logo();
		return;
	}
	$title_tag = in_array( $title_tag, array( 'h1', 'p', 'span' ), true ) ? $title_tag : 'p';
	printf(
		'<%1$s class="site-title"><a href="%2$s" rel="home">%3$s</a></%1$s>',
		$title_tag, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- allowlisted above.
		esc_url( home_url( '/' ) ),
		esc_html( get_bloginfo( 'name' ) )
	);
}

/**
 * Date, author and categories for a post.
 */
function nrds_posted_on() {
	printf(
		/* translators: 1: date, 2: author link. */
		wp_kses_post( __( 'Posted on %1$s by %2$s', 'nrds-theme' ) ),
		'<time datetime="' . esc_attr( get_the_date( DATE_W3C ) ) . '">' . esc_html( get_the_date() ) . '</time>',
		'<a href="' . esc_url( get_author_posts_url( (int) get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a>'
	);
	$cats = get_the_category_list( ', ' );
	if ( $cats && 'post' === get_post_type() ) {
		echo ' &middot; ' . wp_kses_post( $cats );
	}
}

/**
 * Title and description for the blog, archives and search results.
 *
 * @return array { title: string, description: string (HTML) }
 */
function nrds_archive_heading() {
	if ( is_search() ) {
		/* translators: %s: search query. */
		return array( 'title' => sprintf( __( 'Search results for: %s', 'nrds-theme' ), get_search_query() ), 'description' => '' );
	}
	if ( is_archive() ) {
		return array( 'title' => wp_strip_all_tags( get_the_archive_title() ), 'description' => get_the_archive_description() );
	}
	if ( is_home() && ! is_front_page() && get_option( 'page_for_posts' ) ) {
		return array( 'title' => get_the_title( get_option( 'page_for_posts' ) ), 'description' => '' );
	}
	return array( 'title' => __( 'Latest posts', 'nrds-theme' ), 'description' => '' );
}

/**
 * Page title block.
 *
 * @param string $title       Title (plain text).
 * @param string $description Optional description (HTML allowed).
 */
function nrds_page_header( $title, $description = '' ) {
	?>
	<header class="page-header">
		<h1 class="page-title"><?php echo esc_html( $title ); ?></h1>
		<?php if ( $description ) : ?>
			<div class="page-description"><?php echo wp_kses_post( wpautop( $description ) ); ?></div>
		<?php endif; ?>
	</header>
	<?php
}

/**
 * Numbered pagination for post lists.
 */
function nrds_pagination() {
	the_posts_pagination(
		array(
			'class'     => 'nrds-pagination',
			'mid_size'  => 1,
			'prev_text' => '&larr;<span class="screen-reader-text">' . esc_html__( 'Previous', 'nrds-theme' ) . '</span>',
			'next_text' => '<span class="screen-reader-text">' . esc_html__( 'Next', 'nrds-theme' ) . '</span>&rarr;',
		)
	);
}

/**
 * Social icon links from Theme Settings → Footer.
 */
function nrds_social_links() {
	$networks = array(
		'facebook'  => 'Facebook',
		'instagram' => 'Instagram',
		'x'         => 'X',
		'youtube'   => 'YouTube',
		'linkedin'  => 'LinkedIn',
		'email'     => __( 'Email', 'nrds-theme' ),
	);
	$links    = array();
	foreach ( $networks as $network => $label ) {
		$value = nrds_setting( 'social_' . $network );
		if ( ! $value ) {
			continue;
		}
		$links[] = sprintf(
			'<a href="%1$s"%2$s><span class="screen-reader-text">%3$s</span>%4$s</a>',
			'email' === $network ? 'mailto:' . antispambot( sanitize_email( $value ) ) : esc_url( $value ),
			'email' === $network ? '' : ' target="_blank" rel="noopener"',
			esc_html( $label ),
			nrds_get_icon( $network )
		);
	}
	if ( $links ) {
		echo '<div class="nrds-social">' . implode( '', $links ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
	}
}

/**
 * Copyright line used when none is set.
 *
 * @return string
 */
function nrds_default_copyright() {
	/* translators: 1: year, 2: site name. */
	return sprintf( __( '© %1$s %2$s. All rights reserved.', 'nrds-theme' ), gmdate( 'Y' ), get_bloginfo( 'name' ) );
}

/**
 * Copyright line from Theme Settings → Footer, with {year} filled in.
 *
 * @return string Plain text.
 */
function nrds_copyright_text() {
	$text = nrds_setting( 'footer_copyright' );
	return $text ? str_replace( '{year}', gmdate( 'Y' ), $text ) : nrds_default_copyright();
}

/**
 * Footer columns that have something to show.
 *
 * A column shows when it has a menu in its "Footer Column N" location,
 * widgets in its "Footer Column N" area, or a callback on the
 * nrds_footer_column_N action. Column 1 also shows the brand block when it
 * is turned on in Theme Settings → Footer.
 *
 * @return array column number => { brand, menu_location, sidebar_id, action }
 */
function nrds_footer_columns() {
	$columns = array();
	for ( $i = 1; $i <= 4; $i++ ) {
		$column = array(
			'brand'         => 1 === $i && 'show' === nrds_setting( 'footer_brand' ),
			'menu_location' => 'footer-column-' . $i,
			'sidebar_id'    => 'footer-widget-' . $i,
			'action'        => 'nrds_footer_column_' . $i,
		);
		if ( $column['brand'] || has_nav_menu( $column['menu_location'] ) || is_active_sidebar( $column['sidebar_id'] ) || has_action( $column['action'] ) ) {
			$columns[ $i ] = $column;
		}
	}
	return $columns;
}
