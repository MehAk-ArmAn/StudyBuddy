(function () {
  'use strict';

  if (!document.body.classList.contains('page-home')) {
    return;
  }

  var root = document.querySelector('[data-home-cosmic]');
  if (!root) {
    return;
  }

  var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var starfield = root.querySelector('[data-home-stars]');
  var mouseGlow = root.querySelector('[data-mouse-glow]');
  var parallaxNodes = root.querySelectorAll('[data-parallax]');
  var tiltCards = root.querySelectorAll('[data-tilt-card]');
  var revealNodes = root.querySelectorAll('[data-home-reveal]');
  function createStars() {
    if (!starfield) {
      return;
    }

    var count = window.innerWidth < 768 ? 130 : 240;
    var fragment = document.createDocumentFragment();

    for (var i = 0; i < count; i += 1) {
      var star = document.createElement('span');
      var roll = Math.random();
      var size = roll > 0.94 ? 3 : roll > 0.72 ? 2 : 1;
      var isCross = roll > 0.97;

      star.className = 'home-star' + (size === 3 ? ' is-bright' : '') + (isCross ? ' is-cross' : '');
      star.style.left = Math.random() * 100 + '%';
      star.style.top = Math.random() * 100 + '%';
      star.style.width = size + 'px';
      star.style.height = size + 'px';
      star.style.setProperty('--twinkle-dur', (2.8 + Math.random() * 6).toFixed(2) + 's');
      star.style.setProperty('--twinkle-delay', (Math.random() * 8).toFixed(2) + 's');
      fragment.appendChild(star);
    }

    starfield.appendChild(fragment);
  }

  function bindParallax() {
    if (prefersReducedMotion || !parallaxNodes.length) {
      return;
    }

    var pointer = { x: 0, y: 0 };
    var current = { x: 0, y: 0 };

    function animate() {
      current.x += (pointer.x - current.x) * 0.06;
      current.y += (pointer.y - current.y) * 0.06;

      parallaxNodes.forEach(function (node) {
        var depth = parseFloat(node.getAttribute('data-parallax')) || 0.02;
        var tx = current.x * depth * -28;
        var ty = current.y * depth * -22;
        node.style.transform = 'translate3d(' + tx + 'px,' + ty + 'px,0)';
      });

      requestAnimationFrame(animate);
    }

    window.addEventListener('mousemove', function (event) {
      var cx = window.innerWidth / 2;
      var cy = window.innerHeight / 2;
      pointer.x = (event.clientX - cx) / cx;
      pointer.y = (event.clientY - cy) / cy;

      if (mouseGlow) {
        mouseGlow.style.left = event.clientX + 'px';
        mouseGlow.style.top = event.clientY + 'px';
        mouseGlow.classList.add('is-active');
      }
    });

    window.addEventListener('mouseleave', function () {
      pointer.x = 0;
      pointer.y = 0;
      if (mouseGlow) {
        mouseGlow.classList.remove('is-active');
      }
    });

    requestAnimationFrame(animate);
  }

  function bindTiltCards() {
    if (prefersReducedMotion) {
      return;
    }

    tiltCards.forEach(function (card) {
      card.addEventListener('mousemove', function (event) {
        var rect = card.getBoundingClientRect();
        var x = event.clientX - rect.left;
        var y = event.clientY - rect.top;
        var midX = rect.width / 2;
        var midY = rect.height / 2;
        var rotateY = ((x - midX) / midX) * 7;
        var rotateX = ((midY - y) / midY) * 7;

        card.classList.add('is-tilted');
        card.style.transform =
          'perspective(700px) rotateX(' + rotateX + 'deg) rotateY(' + rotateY + 'deg) translateY(-8px) scale(1.02)';
        card.style.setProperty('--mx', (x / rect.width) * 100 + '%');
        card.style.setProperty('--my', (y / rect.height) * 100 + '%');
      });

      card.addEventListener('mouseleave', function () {
        card.classList.remove('is-tilted');
        card.style.transform = '';
      });
    });
  }

  function bindReveal() {
    root.classList.add('is-visible');

    if (!revealNodes.length) {
      return;
    }

    if (!('IntersectionObserver' in window) || prefersReducedMotion) {
      revealNodes.forEach(function (node) {
        node.classList.add('is-revealed');
      });
      return;
    }

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-revealed');
          observer.unobserve(entry.target);
        }
      });
    }, { rootMargin: '0px 0px -8% 0px' });

    revealNodes.forEach(function (node) {
      observer.observe(node);
    });
  }

  createStars();
  bindParallax();
  bindTiltCards();
  bindReveal();
})();
