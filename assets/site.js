(function () {
  var root = document.documentElement;
  var KEY = 'rr-theme';
  var MODES = ['light', 'dark', 'festive'];

  function stored() {
    try {
      return localStorage.getItem(KEY);
    } catch (e) {
      return null;
    }
  }

  function systemMode() {
    return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  }

  function current() {
    var t = root.getAttribute('data-theme');
    return MODES.indexOf(t) >= 0 ? t : systemMode();
  }

  // Mode switch
  var buttons = [].slice.call(document.querySelectorAll('[data-mode]'));

  function paint() {
    var mode = current();
    buttons.forEach(function (b) {
      b.setAttribute('aria-pressed', String(b.getAttribute('data-mode') === mode));
    });
    snow(mode === 'festive');
  }

  buttons.forEach(function (b) {
    b.addEventListener('click', function () {
      var mode = b.getAttribute('data-mode');
      root.setAttribute('data-theme', mode);
      try {
        localStorage.setItem(KEY, mode);
      } catch (e) {}
      paint();
    });
  });

  if (window.matchMedia) {
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function () {
      if (!stored()) paint();
    });
  }

  // Mobile menu
  var menuBtn = document.querySelector('.menu-btn');
  if (menuBtn) {
    menuBtn.addEventListener('click', function () {
      var open = document.body.classList.toggle('menu-open');
      menuBtn.setAttribute('aria-expanded', String(open));
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && document.body.classList.contains('menu-open')) {
        document.body.classList.remove('menu-open');
        menuBtn.setAttribute('aria-expanded', 'false');
        menuBtn.focus();
      }
    });
  }

  // Snowfall for festive mode
  var canvas = null;
  var raf = 0;
  var flakes = [];
  var still = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function snow(on) {
    if (!on || still) {
      if (canvas) {
        cancelAnimationFrame(raf);
        canvas.remove();
        canvas = null;
      }
      return;
    }
    if (canvas) return;
    canvas = document.createElement('canvas');
    canvas.className = 'snow';
    canvas.setAttribute('aria-hidden', 'true');
    document.body.appendChild(canvas);
    var ctx = canvas.getContext('2d');
    var w = 0;
    var h = 0;
    var dpr = Math.min(window.devicePixelRatio || 1, 2);

    function size() {
      w = window.innerWidth;
      h = window.innerHeight;
      canvas.width = w * dpr;
      canvas.height = h * dpr;
      canvas.style.width = w + 'px';
      canvas.style.height = h + 'px';
      ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
      var count = Math.round(Math.min(90, (w * h) / 16000));
      flakes = [];
      for (var i = 0; i < count; i++) {
        flakes.push({
          x: Math.random() * w,
          y: Math.random() * h,
          r: 0.8 + Math.random() * 2.2,
          s: 0.3 + Math.random() * 0.8,
          d: Math.random() * Math.PI * 2,
        });
      }
    }

    function tick() {
      ctx.clearRect(0, 0, w, h);
      ctx.fillStyle = 'rgba(255, 255, 255, 0.95)';
      ctx.strokeStyle = 'rgba(110, 150, 200, 0.45)';
      ctx.lineWidth = 0.8;
      for (var i = 0; i < flakes.length; i++) {
        var f = flakes[i];
        f.y += f.s;
        f.d += 0.01;
        f.x += Math.sin(f.d) * 0.35;
        if (f.y > h + 4) {
          f.y = -4;
          f.x = Math.random() * w;
        }
        ctx.beginPath();
        ctx.arc(f.x, f.y, f.r, 0, Math.PI * 2);
        ctx.fill();
        ctx.stroke();
      }
      raf = requestAnimationFrame(tick);
    }

    size();
    window.addEventListener('resize', function () {
      if (canvas) size();
    });
    tick();
  }

  // Reveal sections as they scroll into view
  var items = [].slice.call(document.querySelectorAll('.reveal'));
  if ('IntersectionObserver' in window && items.length) {
    var io = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (e) {
          if (e.isIntersecting) {
            e.target.classList.add('in');
            io.unobserve(e.target);
          }
        });
      },
      { rootMargin: '0px 0px -8% 0px' }
    );
    items.forEach(function (el) {
      io.observe(el);
    });
  } else {
    items.forEach(function (el) {
      el.classList.add('in');
    });
  }

  // Table of contents highlight
  var tocLinks = [].slice.call(document.querySelectorAll('.toc a'));
  if (tocLinks.length) {
    var targets = tocLinks
      .map(function (a) {
        return document.getElementById(a.getAttribute('href').slice(1));
      })
      .filter(Boolean);
    var ticking = false;
    var update = function () {
      ticking = false;
      var active = -1;
      for (var i = 0; i < targets.length; i++) {
        if (targets[i].getBoundingClientRect().top <= 140) active = i;
      }
      tocLinks.forEach(function (a, i) {
        a.classList.toggle('on', i === active);
      });
    };
    window.addEventListener(
      'scroll',
      function () {
        if (!ticking) {
          ticking = true;
          requestAnimationFrame(update);
        }
      },
      { passive: true }
    );
    update();
  }

  paint();
})();
