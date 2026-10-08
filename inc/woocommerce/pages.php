<?php
/**
 * The pages a WooCommerce store needs, and Theme Settings → Store, which
 * shows whether each one is set up and can create the missing ones.
 *
 * Shop, Cart, Checkout, My account and the Refund and returns policy are
 * created by WooCommerce's own installer (WC_Install::create_pages()), so they
 * get the content WooCommerce currently expects (the Cart and Checkout
 * blocks, for example). The theme adds the Terms and conditions and Privacy
 * policy pages, which WooCommerce links to at checkout but doesn't create.
 *
 * @package NRDS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Store pages: key => label, option holding the page ID, what it's for.
 *
 * @return array
 */
function nrds_store_pages() {
	return apply_filters(
		'nrds_store_pages',
		array(
			'shop'           => array( __( 'Shop', 'nrds-theme' ), 'woocommerce_shop_page_id', __( 'Lists your products.', 'nrds-theme' ) ),
			'cart'           => array( __( 'Cart', 'nrds-theme' ), 'woocommerce_cart_page_id', __( 'Where customers review what they are buying.', 'nrds-theme' ) ),
			'checkout'       => array( __( 'Checkout', 'nrds-theme' ), 'woocommerce_checkout_page_id', __( 'Billing, shipping and payment. Also shows the order confirmation.', 'nrds-theme' ) ),
			'myaccount'      => array( __( 'My account', 'nrds-theme' ), 'woocommerce_myaccount_page_id', __( 'Log in, registration, orders, downloads and addresses.', 'nrds-theme' ) ),
			'terms'          => array( __( 'Terms and conditions', 'nrds-theme' ), 'woocommerce_terms_page_id', __( 'Customers agree to it at checkout once it is published.', 'nrds-theme' ) ),
			'privacy'        => array( __( 'Privacy policy', 'nrds-theme' ), 'wp_page_for_privacy_policy', __( 'Linked from checkout and account registration.', 'nrds-theme' ) ),
			'refund_returns' => array( __( 'Refund and returns policy', 'nrds-theme' ), 'woocommerce_refund_returns_page_id', __( 'Your refund, return and exchange rules.', 'nrds-theme' ) ),
		)
	);
}

/**
 * Status of one store page.
 *
 * @param string $option Option holding the page ID.
 * @return array { id: int, status: 'published'|'draft'|'missing' }
 */
function nrds_store_page_status( $option ) {
	$id   = (int) get_option( $option );
	$page = $id ? get_post( $id ) : null;

	if ( ! $page || 'page' !== $page->post_type || 'trash' === $page->post_status ) {
		return array(
			'id'     => 0,
			'status' => 'missing',
		);
	}
	return array(
		'id'     => $id,
		'status' => 'publish' === $page->post_status ? 'published' : 'draft',
	);
}

/**
 * Placeholder content for a legal page the theme creates.
 *
 * @param string $heading Opening line.
 * @return string Block markup.
 */
function nrds_legal_page_content( $heading ) {
	$paragraphs = array(
		$heading,
		__( 'This page was created by the theme as a starting point. Replace this text with your own before publishing it, and have it reviewed if you are unsure what it needs to cover.', 'nrds-theme' ),
	);
	$content = '';
	foreach ( $paragraphs as $text ) {
		$content .= "<!-- wp:paragraph -->\n<p>" . esc_html( $text ) . "</p>\n<!-- /wp:paragraph -->\n\n";
	}
	return $content;
}

/**
 * Create every missing store page.
 *
 * Pages that already exist are left alone. The legal pages are created as
 * drafts so their placeholder text is never shown to customers.
 *
 * @return string[] Labels of the pages created.
 */
function nrds_create_store_pages() {
	$pages   = nrds_store_pages();
	$before  = array();
	$created = array();
	foreach ( $pages as $key => $page ) {
		$before[ $key ] = nrds_store_page_status( $page[1] )['status'];
	}

	// WooCommerce's installer creates its own pages and skips any that exist.
	if ( class_exists( 'WC_Install' ) && method_exists( 'WC_Install', 'create_pages' ) ) {
		WC_Install::create_pages();
	}

	$legal = array(
		'terms'   => __( 'Set out the terms customers agree to when they buy from this store: payment, delivery, cancellations, warranties and liability.', 'nrds-theme' ),
		'privacy' => __( 'Explain what personal information this site and store collect, why, how long it is kept, and who it is shared with (for example payment and shipping providers).', 'nrds-theme' ),
	);
	foreach ( $legal as $key => $intro ) {
		if ( ! isset( $pages[ $key ] ) || 'missing' !== nrds_store_page_status( $pages[ $key ][1] )['status'] ) {
			continue;
		}
		$content = nrds_legal_page_content( $intro );
		// WordPress's own privacy policy template, when available, is a fuller starting point.
		if ( 'privacy' === $key ) {
			if ( ! class_exists( 'WP_Privacy_Policy_Content' ) && file_exists( ABSPATH . 'wp-admin/includes/class-wp-privacy-policy-content.php' ) ) {
				require_once ABSPATH . 'wp-admin/includes/class-wp-privacy-policy-content.php';
			}
			if ( class_exists( 'WP_Privacy_Policy_Content' ) ) {
				$content = WP_Privacy_Policy_Content::get_default_content();
			}
		}
		$id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'draft',
				'post_title'   => $pages[ $key ][0],
				'post_content' => $content,
			)
		);
		if ( $id && ! is_wp_error( $id ) ) {
			update_option( $pages[ $key ][1], $id );
		}
	}

	foreach ( $pages as $key => $page ) {
		if ( 'missing' === $before[ $key ] && 'missing' !== nrds_store_page_status( $page[1] )['status'] ) {
			$created[] = $page[0];
		}
	}
	return $created;
}

/**
 * Handle the "Create missing pages" button.
 */
function nrds_handle_create_store_pages() {
	check_admin_referer( 'nrds_create_store_pages' );
	if ( ! current_user_can( 'manage_woocommerce' ) || ! current_user_can( 'publish_pages' ) ) {
		wp_die( esc_html__( 'Sorry, you are not allowed to create store pages.', 'nrds-theme' ), 403 );
	}
	$created = nrds_create_store_pages();
	wp_safe_redirect( add_query_arg( 'nrds-created', count( $created ), admin_url( 'themes.php?page=nrds-theme-settings&tab=store' ) ) );
	exit;
}
add_action( 'admin_post_nrds_create_store_pages', 'nrds_handle_create_store_pages' );

/**
 * Theme Settings → Store.
 */
function nrds_render_store_tab() {
	$pages   = nrds_store_pages();
	$missing = 0;
	$labels  = array(
		'published' => __( 'Published', 'nrds-theme' ),
		'draft'     => __( 'Draft: review and publish', 'nrds-theme' ),
		'missing'   => __( 'Missing', 'nrds-theme' ),
	);

	if ( isset( $_GET['nrds-created'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$count = absint( $_GET['nrds-created'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		echo '<div class="notice notice-success is-dismissible"><p>';
		echo $count
			/* translators: %d: number of pages. */
			? esc_html( sprintf( _n( 'Created %d page.', 'Created %d pages.', $count, 'nrds-theme' ), $count ) )
			: esc_html__( 'Every store page already exists.', 'nrds-theme' );
		echo '</p></div>';
	}
	?>
	<p><?php esc_html_e( 'These are the pages a WooCommerce store needs. Each one is linked to WooCommerce, so checkout, account and policy links point at the right page.', 'nrds-theme' ); ?></p>

	<table class="widefat striped nrds-store-pages">
		<thead>
			<tr>
				<th scope="col"><?php esc_html_e( 'Page', 'nrds-theme' ); ?></th>
				<th scope="col"><?php esc_html_e( 'Status', 'nrds-theme' ); ?></th>
				<th scope="col"><span class="screen-reader-text"><?php esc_html_e( 'Actions', 'nrds-theme' ); ?></span></th>
			</tr>
		</thead>
		<tbody>
		<?php
		foreach ( $pages as $key => $page ) :
			$state = nrds_store_page_status( $page[1] );
			if ( 'missing' === $state['status'] ) {
				++$missing;
			}
			?>
			<tr>
				<td>
					<strong><?php echo esc_html( $page[0] ); ?></strong>
					<?php if ( $state['id'] && 0 !== strcasecmp( get_the_title( $state['id'] ), $page[0] ) ) : ?>
						<span class="nrds-store-pages__title">(<?php echo esc_html( get_the_title( $state['id'] ) ); ?>)</span>
					<?php endif; ?>
					<br><span class="description"><?php echo esc_html( $page[2] ); ?></span>
				</td>
				<td><span class="nrds-status nrds-status--<?php echo esc_attr( $state['status'] ); ?>"><?php echo esc_html( $labels[ $state['status'] ] ); ?></span></td>
				<td class="nrds-store-pages__actions">
					<?php if ( $state['id'] ) : ?>
						<a href="<?php echo esc_url( get_edit_post_link( $state['id'] ) ); ?>"><?php esc_html_e( 'Edit', 'nrds-theme' ); ?></a>
						<?php if ( 'published' === $state['status'] ) : ?>
							| <a href="<?php echo esc_url( get_permalink( $state['id'] ) ); ?>"><?php esc_html_e( 'View', 'nrds-theme' ); ?></a>
						<?php endif; ?>
					<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<?php if ( $missing && current_user_can( 'manage_woocommerce' ) && current_user_can( 'publish_pages' ) ) : ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="nrds-store-create">
			<input type="hidden" name="action" value="nrds_create_store_pages">
			<?php wp_nonce_field( 'nrds_create_store_pages' ); ?>
			<?php
			/* translators: %d: number of missing pages. */
			submit_button( sprintf( _n( 'Create %d missing page', 'Create %d missing pages', $missing, 'nrds-theme' ), $missing ), 'primary', 'submit', false );
			?>
			<p class="description"><?php esc_html_e( 'Shop, Cart, Checkout and My account are created and published by WooCommerce. Terms and conditions and Privacy policy are created as drafts with placeholder text for you to replace and publish.', 'nrds-theme' ); ?></p>
		</form>
	<?php elseif ( ! $missing ) : ?>
		<p class="nrds-store-ok"><?php esc_html_e( 'Every store page exists. Publish any drafts once their text is ready.', 'nrds-theme' ); ?></p>
	<?php endif; ?>

	<h2><?php esc_html_e( 'Finish setting up', 'nrds-theme' ); ?></h2>
	<ul class="ul-disc">
		<li>
			<?php
			printf(
				/* translators: %s: link to WooCommerce page settings. */
				wp_kses_post( __( 'Choose a different page for any of these under <a href="%s">WooCommerce → Settings → Advanced → Page setup</a>.', 'nrds-theme' ) ),
				esc_url( admin_url( 'admin.php?page=wc-settings&tab=advanced' ) )
			);
			?>
		</li>
		<li>
			<?php
			printf(
				/* translators: %s: link to the menus screen. */
				wp_kses_post( __( 'Add Shop to the primary menu, and the policy pages to the Footer Bottom Bar Menu, in <a href="%s">Menus</a>. The header already has account and cart icons.', 'nrds-theme' ) ),
				esc_url( admin_url( 'nav-menus.php' ) )
			);
			?>
		</li>
		<li>
			<?php
			printf(
				/* translators: 1: Layout tab link, 2: widgets link. */
				wp_kses_post( __( 'Shop and product page sidebars are set on the <a href="%1$s">Layout & Sidebars</a> tab. Add product filters to the Shop Sidebar in <a href="%2$s">Widgets</a>.', 'nrds-theme' ) ),
				esc_url( admin_url( 'themes.php?page=nrds-theme-settings&tab=layout' ) ),
				esc_url( admin_url( 'widgets.php' ) )
			);
			?>
		</li>
		<li>
			<?php
			printf(
				/* translators: %s: link to the Customizer's WooCommerce panel. */
				wp_kses_post( __( 'Set products per row, image cropping and the store notice in <a href="%s">Customize → WooCommerce</a>.', 'nrds-theme' ) ),
				esc_url( admin_url( 'customize.php?autofocus[panel]=woocommerce' ) )
			);
			?>
		</li>
	</ul>
	<?php
}
