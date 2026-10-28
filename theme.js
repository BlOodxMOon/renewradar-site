(function () {
  var root = document.documentElement;
  var btn = document.querySelector('.theme-toggle');
  if (!btn) return;
  var mq = window.matchMedia('(prefers-color-scheme: dark)');

  function current() {
    return root.getAttribute('data-theme') || (mq.matches ? 'dark' : 'light');
  }

  function paint() {
    var mode = current();
    btn.setAttribute('data-mode', mode);
    btn.setAttribute('aria-label', mode === 'dark' ? 'Switch to light theme' : 'Switch to dark theme');
  }

  btn.addEventListener('click', function () {
    var next = current() === 'dark' ? 'light' : 'dark';
    root.setAttribute('data-theme', next);
    try {
      localStorage.setItem('rr-theme', next);
    } catch (e) {}
    paint();
  });

  if (mq.addEventListener) mq.addEventListener('change', paint);
  paint();
})();
