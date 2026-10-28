(function () {
  var links = [].slice.call(document.querySelectorAll('.toc a'));
  var list = document.querySelector('.toc ol');
  if (!links.length || !list) return;

  var sections = links
    .map(function (a) {
      return document.getElementById(a.getAttribute('href').slice(1));
    })
    .filter(Boolean);
  var ticking = false;

  function update() {
    ticking = false;
    var active = -1;
    for (var i = 0; i < sections.length; i++) {
      if (sections[i].getBoundingClientRect().top <= 260) active = i;
    }
    links.forEach(function (a, i) {
      a.parentElement.classList.toggle('is-active', i === active);
    });
    var li = active >= 0 ? links[active].parentElement : null;
    list.style.setProperty('--p', li ? li.offsetTop + 19 + 'px' : '0px');
  }

  function onScroll() {
    if (!ticking) {
      ticking = true;
      window.requestAnimationFrame(update);
    }
  }

  window.addEventListener('scroll', onScroll, { passive: true });
  window.addEventListener('resize', onScroll);
  update();
})();
