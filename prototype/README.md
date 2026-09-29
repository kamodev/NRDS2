# Concealed 1791 — homepage prototype (HTML / CSS / JS)

A static redesign of the concealed1791.com (A & A Tactical) homepage. It borrows layout patterns from protectwithbear.com (Right To Bear) and uses this theme's v2 look: navy and orange, Oswald and Roboto, knife-edge buttons.

Open `index.html` in a browser. It has no build step and no dependencies apart from Google Fonts.

This folder is dev-only. `build-zip.sh` leaves it out of the theme zip.

## Files

| File | Purpose |
| --- | --- |
| `index.html` | Page markup. Each section sits between `====` comments that name the WordPress template part it should become. |
| `css/main.css` | All styles. Every class has a `c17-` prefix so nothing collides with the theme's existing `.nrd-*` rules. |
| `js/main.js` | Plain JavaScript, no jQuery. Each feature exits quietly if its markup is missing, so the file is safe to enqueue site-wide. |

## Elements taken from protectwithbear.com

- **Announcement bar**: can be dismissed, and remembers that per browser.
- **Sticky header**: phone number and a CTA button. It shrinks and gains a shadow once you scroll.
- **Hero "Find your course" chooser**: based on Bear's plan picker (Individuals / Families / LEO / Military).
- **Trust bar**: counters under the hero.
- **Three pillars**: Education / Equipment / Expertise, based on Bear's Education / Attorney Hotline / Legal Protection.
- **Plan-style class cards**: a "Most popular" featured card and In person / Online filter tabs.
- **How-it-works steps**.
- **Comparison table**: "us vs. a typical class".
- **Reviews slider, FAQ accordion, orange final CTA band**.
- **Sticky mobile action bar**: Call / Book a Class, shown after the hero.

## Moving it into the WordPress theme

1. Enqueue the files in `functions.php`:
   ```php
   wp_enqueue_style('c17-fonts', 'https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap', array(), null);
   wp_enqueue_style('c17-home', get_template_directory_uri() . '/assets/css/home.css', array(), '1.0');
   wp_enqueue_script('c17-home', get_template_directory_uri() . '/assets/js/home.js', array(), '1.0', true);
   ```
   The only global rules in `main.css` are the `body.c17` base, `html` scroll padding and `*` box-sizing. Add `c17` to `body_class` on the pages that use this design.
2. Split `index.html` into `template-parts/home/*.php` following the section comments. Call them from `front-page.php` with `get_template_part()`.
3. Move the header to `header.php`. Replace the `<ul class="c17-nav__menu">` with:
   ```php
   wp_nav_menu(array('theme_location' => 'primary', 'container' => false, 'menu_class' => 'c17-nav__menu', 'depth' => 1));
   ```
4. Move the footer to `footer.php`. Columns 2–4 map to the existing "Footer Column N" menus (`inc/footer-columns.php`). Column 1 takes the brand text and social links from the Footer Column 1 widgets.
5. Put the icon `<svg>` sprite at the top of `<body>` in its own include, for example `inc/icons.php`.
6. The announcement text, phone, address and hours are good candidates for fields in `inc/theme-settings.php`.
7. Once courses become a CPT or WooCommerce products, replace the class cards with a loop.

## Confirm with the client before launch

- **Prices, durations and course list are placeholders** ($49 / $79 / $99 / $125/hr).
- **Reviews are sample copy** marked `[Sample review]`. Replace them with real reviews the customers can be credited for.
- Address, phone and hours came from public listings (451 N Ferdon Blvd, Crestview FL 32536, (850) 306-3378, Mon–Sat 9–6:30). Verify them.
- FAQ legal statements: Florida permitless carry since July 2023, license reciprocity, and the waiting-period exemption.
- The logo is a placeholder mark. The hero and shop images are CSS gradients; swap in real photos (see the comments in `main.css`).
- Instagram and YouTube links are `#`.
