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
  var cleanImages = root.querySelectorAll('[data-clean-alpha]');

  function isCheckerboardPixel(r, g, b, a) {
    if (a < 16) {
      return true;
    }

    var spread = Math.max(r, g, b) - Math.min(r, g, b);

    if (r > 245 && g > 245 && b > 245) {
      return true;
    }

    if (spread < 14 && r > 158 && r < 228 && g > 158 && g < 228 && b > 158 && b < 228) {
      return true;
    }

    return false;
  }

  function cleanImageTransparency(img) {
    if (!img.complete || !img.naturalWidth) {
      return;
    }

    if (img.dataset.cleaned === 'true') {
      return;
    }

    try {
      var canvas = document.createElement('canvas');
      var ctx = canvas.getContext('2d');
      canvas.width = img.naturalWidth;
      canvas.height = img.naturalHeight;
      ctx.drawImage(img, 0, 0);

      var imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
      var data = imageData.data;
      var changed = 0;

      for (var i = 0; i < data.length; i += 4) {
        if (isCheckerboardPixel(data[i], data[i + 1], data[i + 2], data[i + 3])) {
          data[i + 3] = 0;
          changed += 1;
        }
      }

      if (changed > 0) {
        ctx.putImageData(imageData, 0, 0);
        img.src = canvas.toDataURL('image/png');
      }

      img.dataset.cleaned = 'true';
      img.classList.add('is-cleaned');
    } catch (error) {
      img.classList.add('is-cleaned');
    }
  }

  function bindImageCleaning() {
    cleanImages.forEach(function (img) {
      if (img.complete) {
        cleanImageTransparency(img);
      } else {
        img.addEventListener('load', function () {
          cleanImageTransparency(img);
        }, { once: true });
      }
    });
  }

  function createStars() {
    if (!starfield) {
      return;
    }

    var count = window.innerWidth < 768 ? 90 : 160;
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
      star.style.setProperty('--twinkle-dur', (2.5 + Math.random() * 4.5).toFixed(2) + 's');
      star.style.setProperty('--twinkle-delay', (Math.random() * 6).toFixed(2) + 's');
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
  }

  createStars();
  bindImageCleaning();
  bindParallax();
  bindTiltCards();
  bindReveal();
})();
