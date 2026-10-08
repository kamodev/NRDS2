
# NRDS Business Theme

A custom WordPress theme for National Readiness & Defense: color themes, a sized or full-screen layout, left and right sidebars you can switch on and off, and a four-column footer.

## Usage

### Installation

1. Build the zip with `./build-zip.sh` (it lands in `~/Downloads`), or clone this repository into `wp-content/themes/`:

   ```sh
   git clone <repo-url> nrds
   ```

2. In your WordPress admin dashboard, go to **Appearance > Themes** (or **Add New > Upload Theme** for the zip).

3. Activate the **NRDS Business Theme**.

### Theme Settings (Appearance > Theme Settings)

- **Layout & Sidebars**
  - *Site width*: **Sized** (centered, with a maximum content width set by the slider) or **Full screen** (edge to edge).
  - *Sidebars*: turn the left and right sidebars on or off separately for the front page, posts, pages, and the blog/archives/search. A sidebar only appears when its widget area has widgets.
  - Single posts and pages can override the sidebars in the **Sidebars** box on their edit screen. The **Full Width (no sidebars)** page template never shows them.
  - *Post header image*: show or hide the featured image banner on single posts.
- **Colors**: a four-color palette (Primary, Dark, Secondary, Light). The defaults are the logo colors: orange `#ff4c00`, black, charcoal `#333333` and white. Color themes fill in all four colors at once (NRDS Blaze, Navy & Orange, Signal Red, Field Olive, Woodland, Desert Tan); every theme keeps white as the light color. The screen shows a live preview and checks the text contrast of each combination. The palette also becomes the block editor's color palette.
- **Footer**: brand column on/off, about text, social links (Facebook, Instagram, X, YouTube, LinkedIn, email), a disclaimer band and the copyright line (`{year}` is replaced with the current year).

### Footer

The footer has, from top to bottom:

1. **Footer Newsletter Band** widget area: a full-width band for a sign-up form or call to action.
2. **Four columns.** Column 1 starts with the brand block (logo or site title, about text, social icons). Each column then shows:
   - the menu assigned to the **Footer Column 1–4** menu location (its name becomes the column heading),
   - widgets in the **Footer Column 1–4** widget area (text, images, buttons, any block),
   - anything a plugin or child theme hooks to `nrds_footer_column_1` … `nrds_footer_column_4`.

   Empty columns are skipped and the rest share the width. Four columns become two on tablets and one on phones.
3. **Disclaimer** band (Theme Settings > Footer).
4. **Bottom bar**: copyright line, the **Footer Bottom Bar Menu** location and the **Footer Bottom Bar** widget area.

### WooCommerce store

Everything below turns on when the WooCommerce plugin is active; without it the theme is unchanged.

- **Theme Settings > Store** lists the pages a store needs and whether each one is set up: Shop, Cart, Checkout, My account, Terms and conditions, Privacy policy, and Refund and returns policy. **Create missing pages** makes any that are missing:
  - Shop, Cart, Checkout, My account and Refund and returns policy come from WooCommerce's own installer, so they get the content WooCommerce expects (the Cart and Checkout blocks).
  - Terms and conditions and Privacy policy are created as drafts with placeholder text (the privacy page uses WordPress's privacy policy template). Replace the text and publish them; the terms checkbox appears at checkout once the Terms page is published.
  - Each page is linked to WooCommerce (WooCommerce > Settings > Advanced > Page setup) or, for privacy, to Settings > Privacy.
- **Layout:** shop and product pages use the theme layout. Theme Settings > Layout & Sidebars gets two more rows, *Shop & product categories* (left sidebar on by default) and *Products* (full width by default). On those pages the left sidebar shows the **Shop Sidebar** widget area, which is meant for product filters and categories. Cart, checkout and account pages never show sidebars.
- **Header:** account and cart icons next to the menu; the cart icon shows the item count and updates after add-to-cart.
- **Styling:** product grid, product pages, notices, forms, cart and checkout (block and classic) and My Account use the theme palette and buttons (`assets/css/woocommerce.css`).
- The theme overrides no WooCommerce templates (there is no `woocommerce/` folder); it only uses WooCommerce's hooks and CSS, so WooCommerce updates never leave theme files out of date.
- Products per row, image cropping and the store notice are set in **Customize > WooCommerce**.

### Customization

- Settings live in `inc/settings.php` (fields, defaults, color themes) and `inc/admin-settings.php` (the settings screen).
- Sidebar rules live in `inc/layout.php`.
- Styles are in `style.css` and `assets/css/`. Colors come from the custom properties in `assets/css/tokens.css` (`--nrd-primary`, `--nrd-dark`, `--nrd-secondary`, `--nrd-light` and shades derived from them), which Theme Settings overrides.
- Add or edit menus under **Appearance > Menus** and widgets under **Appearance > Widgets**.

### Development

- Templates: `header.php`, `footer.php`, `index.php` (blog, archives, search), `front-page.php`, `page.php`, `single.php`, `404.php`, `comments.php`, `searchform.php`, `page-full-width.php`, `sidebar-left.php`, `sidebar-right.php`
- Template parts: `template-parts/header/`, `template-parts/footer/`, `template-parts/content*.php`
- PHP helpers: `inc/` (`settings.php`, `layout.php`, `template-tags.php`, `icons.php`, `admin-settings.php`) and `inc/woocommerce/` (store support, loaded only when WooCommerce is active)
- Stylesheets are enqueued in order by `nrds_theme_scripts()` in `functions.php`; `screens.css` holds all breakpoints and loads last.
- Scripts: `assets/js/script.js` (sticky header, mobile menu), `assets/js/admin-settings.js` (Theme Settings screen)
- `nrds-site/` is a static HTML mockup of the design; it isn't part of the theme zip.

### Support

For questions or support, contact the author at [nrds.life](http://nrds.life/).
