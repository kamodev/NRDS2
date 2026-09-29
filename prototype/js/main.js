/**
 * Concealed 1791 — homepage prototype behaviour.
 * Vanilla JS, no dependencies. Each feature is self-contained and exits
 * quietly if its markup isn't on the page, so the file can be enqueued
 * site-wide in WordPress.
 */
(function () {
  'use strict';

  var doc = document;
  var root = doc.documentElement;
  var body = doc.body;
  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // Lets CSS hide .c17-reveal elements only when JS is available to show them
  body.classList.add('c17-js');

  function $(sel, ctx) { return (ctx || doc).querySelector(sel); }
  function $$(sel, ctx) { return Array.prototype.slice.call((ctx || doc).querySelectorAll(sel)); }

  function storageGet(key) {
    try { return window.localStorage.getItem(key); } catch (e) { return null; }
  }
  function storageSet(key, value) {
    try { window.localStorage.setItem(key, value); } catch (e) { /* private mode etc. */ }
  }

  /* ---------- Announcement bar ---------- */
  var announce = $('#c17-announce');
  if (announce) {
    // Bump the key when the announcement text changes so it shows again
    var announceKey = 'c17-announce-dismissed-v1';
    if (storageGet(announceKey) === '1') announce.classList.add('is-hidden');
    var close = $('.c17-announce__close', announce);
    if (close) {
      close.addEventListener('click', function () {
        announce.classList.add('is-hidden');
        storageSet(announceKey, '1');
      });
    }
  }

  /* ---------- Sticky header ---------- */
  var header = $('#c17-header');
  function onScrollHeader() {
    if (header) header.classList.toggle('is-scrolled', window.scrollY > 10);
  }

  /* ---------- Mobile navigation ---------- */
  var burger = $('#c17-burger');
  var nav = $('#c17-nav');

  function setNavOpen(open) {
    if (!burger || !nav) return;
    if (open && header) {
      // Drop the panel directly below the header, wherever it currently sits
      root.style.setProperty('--c17-nav-top', header.getBoundingClientRect().bottom + 'px');
    }
    nav.classList.toggle('is-open', open);
    burger.setAttribute('aria-expanded', open ? 'true' : 'false');
    burger.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
    body.classList.toggle('c17-nav-open', open);
  }

  if (burger && nav) {
    burger.addEventListener('click', function () {
      setNavOpen(burger.getAttribute('aria-expanded') !== 'true');
    });
    $$('a', nav).forEach(function (a) {
      a.addEventListener('click', function () { setNavOpen(false); });
    });
    doc.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && nav.classList.contains('is-open')) {
        setNavOpen(false);
        burger.focus();
      }
    });
    window.addEventListener('resize', function () {
      if (window.innerWidth > 960) setNavOpen(false);
    });
  }

  /* ---------- Hero "find your course" chooser ---------- */
  var chooserOpts = $$('.c17-chooser__opt');
  var resultBox = $('.c17-chooser__result');
  var resultTitle = $('#chooser-result-title');
  var resultText = $('#chooser-result-text');
  var resultLink = $('#chooser-result-link');

  function selectOption(opt) {
    chooserOpts.forEach(function (o) {
      var active = o === opt;
      o.classList.toggle('is-active', active);
      o.setAttribute('aria-checked', active ? 'true' : 'false');
      o.tabIndex = active ? 0 : -1;
    });
    if (resultTitle) resultTitle.innerHTML = opt.getAttribute('data-title');
    if (resultText) resultText.textContent = opt.getAttribute('data-text');
    if (resultLink) resultLink.setAttribute('href', opt.getAttribute('data-href'));
    if (resultBox && !reduceMotion) {
      resultBox.classList.remove('is-swapping');
      void resultBox.offsetWidth; // restart the animation
      resultBox.classList.add('is-swapping');
    }
  }

  chooserOpts.forEach(function (opt, i) {
    opt.tabIndex = opt.classList.contains('is-active') ? 0 : -1;
    opt.addEventListener('click', function () { selectOption(opt); });
    // Arrow keys move between options, as expected for a radiogroup
    opt.addEventListener('keydown', function (e) {
      var step = { ArrowRight: 1, ArrowDown: 1, ArrowLeft: -1, ArrowUp: -1 }[e.key];
      if (!step) return;
      e.preventDefault();
      var next = chooserOpts[(i + step + chooserOpts.length) % chooserOpts.length];
      selectOption(next);
      next.focus();
    });
  });

  /* ---------- Class filter tabs ---------- */
  var tabs = $$('.c17-tab');
  var plans = $$('.c17-plan');

  function applyFilter(filter) {
    tabs.forEach(function (t) {
      var active = t.getAttribute('data-filter') === filter;
      t.classList.toggle('is-active', active);
      t.setAttribute('aria-selected', active ? 'true' : 'false');
    });
    plans.forEach(function (p) {
      var show = filter === 'all' || p.getAttribute('data-type') === filter;
      p.classList.toggle('is-hidden', !show);
      if (show) p.classList.add('is-visible'); // skip the reveal for cards shown by filtering
    });
  }

  tabs.forEach(function (t) {
    t.addEventListener('click', function () { applyFilter(t.getAttribute('data-filter')); });
  });
  // Links elsewhere on the page can pre-filter, e.g. "Start Online Today"
  $$('[data-filter-link]').forEach(function (a) {
    a.addEventListener('click', function () { applyFilter(a.getAttribute('data-filter-link')); });
  });

  /* ---------- Count-up numbers ---------- */
  function countUp(el) {
    var target = parseInt(el.getAttribute('data-count'), 10);
    if (isNaN(target) || reduceMotion) return;
    var start = null;
    var duration = 1400;
    function frame(ts) {
      if (start === null) start = ts;
      var p = Math.min((ts - start) / duration, 1);
      var eased = 1 - Math.pow(1 - p, 3);
      el.textContent = Math.round(target * eased).toLocaleString('en-US');
      if (p < 1) window.requestAnimationFrame(frame);
    }
    window.requestAnimationFrame(frame);
  }

  /* ---------- Reveal on scroll + counters ---------- */
  var reveals = $$('.c17-reveal');
  var counters = $$('[data-count]');

  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        var el = entry.target;
        if (el.hasAttribute('data-count')) countUp(el);
        else el.classList.add('is-visible');
        io.unobserve(el);
      });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.12 });
    reveals.concat(counters).forEach(function (el) { io.observe(el); });
  } else {
    reveals.forEach(function (el) { el.classList.add('is-visible'); });
  }

  /* ---------- Reviews slider ---------- */
  var slider = $('#c17-slider');
  if (slider) {
    $$('.c17-slider-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var dir = parseInt(btn.getAttribute('data-dir'), 10);
        var card = slider.firstElementChild;
        var gap = parseFloat(window.getComputedStyle(slider).columnGap) || 0;
        var stepPx = card ? card.getBoundingClientRect().width + gap : slider.clientWidth;
        var atEnd = slider.scrollLeft + slider.clientWidth >= slider.scrollWidth - 4;
        var atStart = slider.scrollLeft <= 4;
        // Wrap around at either end
        if (dir > 0 && atEnd) slider.scrollTo({ left: 0 });
        else if (dir < 0 && atStart) slider.scrollTo({ left: slider.scrollWidth });
        else slider.scrollBy({ left: dir * stepPx });
      });
    });
  }

  /* ---------- FAQ: keep one answer open at a time ---------- */
  var accs = $$('.c17-acc');
  accs.forEach(function (d) {
    d.addEventListener('toggle', function () {
      if (!d.open) return;
      accs.forEach(function (other) { if (other !== d) other.open = false; });
    });
  });

  /* ---------- Highlight the nav link for the section in view ---------- */
  var navLinks = $$('.c17-nav__menu a[href^="#"]');
  var sections = navLinks
    .map(function (a) { return $(a.getAttribute('href')); })
    .filter(Boolean);

  function onScrollSpy() {
    var offset = (header ? header.offsetHeight : 0) + 40;
    var current = null;
    sections.forEach(function (s) {
      if (s.getBoundingClientRect().top - offset <= 0) current = s;
    });
    navLinks.forEach(function (a) {
      a.classList.toggle('is-current', !!current && a.getAttribute('href') === '#' + current.id);
    });
  }

  /* ---------- Sticky mobile action bar (after the hero) ---------- */
  var mobilebar = $('.c17-mobilebar');
  var hero = $('.c17-hero');
  function onScrollMobilebar() {
    if (!mobilebar || !hero) return;
    mobilebar.classList.toggle('is-visible', hero.getBoundingClientRect().bottom < 0);
  }

  /* ---------- Scroll handler (rAF-throttled) ---------- */
  var ticking = false;
  function onScroll() {
    if (ticking) return;
    ticking = true;
    window.requestAnimationFrame(function () {
      onScrollHeader();
      onScrollSpy();
      onScrollMobilebar();
      ticking = false;
    });
  }
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  /* ---------- Footer year ---------- */
  var year = $('#c17-year');
  if (year) year.textContent = new Date().getFullYear();
})();
