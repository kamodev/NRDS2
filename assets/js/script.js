jQuery(document).ready(function($) {
    var header = $('.nrd-header');
    var stickyClass = 'sticky-header';
    var headerOffset = header.length ? header.offset().top : 0;
    // Holds the header's space while it is fixed, so the page doesn't jump up
    var headerPlaceholder = $('<div class="nrd-header-placeholder" aria-hidden="true"></div>').hide().insertAfter(header);

    $(window).scroll(function() {
        if (!header.length) return;
        if ($(window).scrollTop() > headerOffset) {
            if (!header.hasClass(stickyClass)) {
                headerPlaceholder.height(header.outerHeight()).show();
                header.addClass(stickyClass);
            }
        } else if (header.hasClass(stickyClass)) {
            header.removeClass(stickyClass);
            headerPlaceholder.hide();
        }
    });

    // Mobile menu toggle
    var menuToggle = $('.menu-toggle');
    var mainMenu = $('#primary-menu');
    // Regions of the page that should be hidden/inert when the mobile menu is open
    var pageContainers = $('#site-content, #colophon, #left-sidebar, #right-sidebar, #main, .site-branding');

    // Minimal inert polyfill/emulation: if browser supports element.inert use it,
    // otherwise emulate by toggling aria-hidden and making focusable children unfocusable.
    function applyInertPolyfill(el, inert) {
        // If native inert supported, use it
        if ('inert' in el) {
            el.inert = inert;
            return;
        }

        // Emulate inert
        if (inert) {
            // store a marker so we can restore later
            el.setAttribute('data-inert-polyfill', 'true');
            // hide from assistive tech
            el.setAttribute('aria-hidden', 'true');
            // disable focusable elements
            var focusables = el.querySelectorAll('a, button, input, textarea, select, [tabindex]');
            focusables.forEach(function(node) {
                var prevTab = node.getAttribute('tabindex');
                if (prevTab !== null) {
                    node.setAttribute('data-prev-tabindex', prevTab);
                }
                node.setAttribute('tabindex', '-1');
                // also disable buttons/inputs
                if (node instanceof HTMLButtonElement || node instanceof HTMLInputElement || node instanceof HTMLSelectElement || node instanceof HTMLTextAreaElement) {
                    node.setAttribute('data-prev-disabled', node.disabled ? '1' : '0');
                    try { node.disabled = true; } catch (e) {}
                }
            });
        } else {
            if (el.getAttribute('data-inert-polyfill') !== 'true') return;
            el.removeAttribute('data-inert-polyfill');
            el.removeAttribute('aria-hidden');
            var focusables = el.querySelectorAll('[data-prev-tabindex], [tabindex]');
            focusables.forEach(function(node) {
                var prev = node.getAttribute('data-prev-tabindex');
                if (prev !== null) {
                    node.setAttribute('tabindex', prev);
                    node.removeAttribute('data-prev-tabindex');
                } else {
                    // if it was assigned -1 by us and had no prev, remove tabindex attribute
                    if (node.getAttribute('tabindex') === '-1') node.removeAttribute('tabindex');
                }
                // restore disabled state
                if (node.hasAttribute('data-prev-disabled')) {
                    var was = node.getAttribute('data-prev-disabled');
                    try { node.disabled = (was === '1'); } catch (e) {}
                    node.removeAttribute('data-prev-disabled');
                }
            });
        }
    }

    menuToggle.on('click', function(e) {
        e.preventDefault();
        var expanded = $(this).attr('aria-expanded') === 'true';
        $(this).attr('aria-expanded', !expanded);
        mainMenu.toggleClass('open');
        // Apply inert (or polyfill) to page containers when menu is open
        if ( mainMenu.hasClass('open') ) {
            pageContainers.each(function() { applyInertPolyfill(this, true); });
        } else {
            pageContainers.each(function() { applyInertPolyfill(this, false); });
        }
    });

    // Close menu when clicking a link inside the menu (mobile)
    mainMenu.on('click', 'a', function() {
        if (mainMenu.hasClass('open')) {
            mainMenu.removeClass('open');
            menuToggle.attr('aria-expanded', 'false');
            pageContainers.each(function() { applyInertPolyfill(this, false); });
        }
    });

    // Click outside to close the menu
    $(document).on('click', function(e) {
        var target = $(e.target);
        if (mainMenu.hasClass('open')) {
            // if the click was not on the menu or the toggle, close
            if (!target.closest('#primary-menu').length && !target.closest('.menu-toggle').length) {
                mainMenu.removeClass('open');
                menuToggle.attr('aria-expanded', 'false');
                pageContainers.each(function() { applyInertPolyfill(this, false); });
            }
        }
    });

    // Focus trap: keep focus within the menu when it's open (mobile)
    function isMobileView() {
        // Matches the menu breakpoint in assets/css/screens.css
        return window.matchMedia('(max-width: 900px)').matches;
    }

    function getFocusableMenuItems() {
        return mainMenu.find('a, button, input, [tabindex]:not([tabindex="-1"])').filter(':visible');
    }

    $(document).on('keydown', function(e) {
        if (!mainMenu.hasClass('open')) return;

        // Only trap focus on mobile
        if (!isMobileView()) return;

        var focusable = getFocusableMenuItems();
        if (!focusable.length) return;

        var first = focusable.first()[0];
        var last = focusable.last()[0];

        if (e.key === 'Escape' || e.keyCode === 27) {
            // Close the menu and restore focus to toggle
            mainMenu.removeClass('open');
            menuToggle.attr('aria-expanded', 'false');
            menuToggle.focus();
            pageContainers.each(function() { applyInertPolyfill(this, false); });
            return;
        }

        if (e.key === 'Tab' || e.keyCode === 9) {
            // Shift + Tab
            if (e.shiftKey) {
                if (document.activeElement === first) {
                    e.preventDefault();
                    last.focus();
                }
            } else {
                // Tab
                if (document.activeElement === last) {
                    e.preventDefault();
                    first.focus();
                }
            }
        }
    });

    // When menu opens, focus the first focusable element inside it
    menuToggle.on('click', function() {
        setTimeout(function() {
            if (mainMenu.hasClass('open') && isMobileView()) {
                var focusable = getFocusableMenuItems();
                if (focusable.length) {
                    focusable.first().focus();
                }
            }
        }, 10);
    });

    // Submenus: the chevron toggles added by inc/navigation.php open them as
    // dropdowns on wide screens (hover and keyboard focus open them too, in
    // CSS) and as collapsible sections in the mobile menu.
    var primaryNav = $('.nrd-nav');

    function setSubmenu(item, open) {
        item.toggleClass('submenu-open', open);
        item.children('.submenu-toggle').attr('aria-expanded', open ? 'true' : 'false');
        if (!open) {
            item.find('.submenu-open').each(function() { setSubmenu($(this), false); });
        }
    }

    function closeAllSubmenus() {
        primaryNav.find('li.submenu-open').each(function() { setSubmenu($(this), false); });
    }

    // Open a panel leftward when it would run past the right edge of the window
    function placeSubmenu(item) {
        var panel = item.children('.sub-menu, .children');
        if (!panel.length || isMobileView()) return;
        item.removeClass('opens-left');
        if (panel[0].getBoundingClientRect().right > document.documentElement.clientWidth - 8) {
            item.addClass('opens-left');
        }
    }

    primaryNav.on('click', '.submenu-toggle', function(e) {
        e.preventDefault();
        var item = $(this).parent();
        var open = !item.hasClass('submenu-open') || item.hasClass('submenu-dismissed');
        item.removeClass('submenu-dismissed');
        if (open && !isMobileView()) {
            // One dropdown at a time on wide screens
            item.siblings('.submenu-open').each(function() { setSubmenu($(this), false); });
            placeSubmenu(item);
        }
        setSubmenu(item, open);
    });

    primaryNav.on('mouseenter focusin', '.menu-item-has-children, .page_item_has_children', function() {
        var item = $(this);
        placeSubmenu(item);
        if (!isMobileView()) {
            // A dropdown opened by its toggle closes when another item is pointed at
            item.siblings('.submenu-open').each(function() { setSubmenu($(this), false); });
        }
    });

    // Escape closes the open dropdown and returns focus to its toggle
    primaryNav.on('keydown', function(e) {
        if ((e.key !== 'Escape' && e.keyCode !== 27) || isMobileView()) return;
        var item = $(e.target).closest('.menu-item-has-children, .page_item_has_children');
        if (!item.length) return;
        setSubmenu(item, false);
        var toggle = item.children('.submenu-toggle');
        // Move focus out of the panel so :focus-within lets it close
        (toggle.length ? toggle : item.children('a')).trigger('focus');
        item.addClass('submenu-dismissed');
    });

    // Leaving an item clears the Escape dismissal
    primaryNav.on('mouseleave focusout', '.submenu-dismissed', function(e) {
        if (!this.contains(e.relatedTarget)) {
            $(this).removeClass('submenu-dismissed');
        }
    });

    // A click outside the menu closes any open dropdown
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.nrd-nav').length) {
            closeAllSubmenus();
        }
    });

    // Decide every panel's direction up front, parents before children, so a
    // closed panel near the right edge never widens the page
    function placeAllSubmenus() {
        primaryNav.find('.menu-item-has-children, .page_item_has_children').each(function() {
            placeSubmenu($(this));
        });
    }
    placeAllSubmenus();
    $(window).on('load', placeAllSubmenus);

    // Switching between the phone and wide layouts starts with submenus closed
    var wasMobile = isMobileView();
    var resizeTimer = null;
    $(window).on('resize', function() {
        if (isMobileView() !== wasMobile) {
            wasMobile = isMobileView();
            closeAllSubmenus();
        }
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(placeAllSubmenus, 150);
    });
});