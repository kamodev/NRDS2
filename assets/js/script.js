jQuery(document).ready(function($) {
    var header = $('.nrd-header');
    var stickyClass = 'sticky-header';
    var headerOffset = header.offset().top;

    $(window).scroll(function() {
        if ($(window).scrollTop() > headerOffset) {
            header.addClass(stickyClass);
        } else {
            header.removeClass(stickyClass);
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
        return window.matchMedia('(max-width: 768px)').matches;
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
});