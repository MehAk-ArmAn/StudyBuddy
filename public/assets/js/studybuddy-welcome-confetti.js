(() => {
    const key = 'studybuddy.welcome-confetti.v1';
    let alreadyShown = false;

    try {
        alreadyShown = sessionStorage.getItem(key) === '1';
        if (!alreadyShown) sessionStorage.setItem(key, '1');
    } catch (error) {
        // Storage can be unavailable in private or restricted browser modes.
    }

    if (alreadyShown) return;

    const reducedMotion = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;

    const showWelcome = () => {
        const toast = document.createElement('div');
        toast.className = 'sb-welcome-toast';
        toast.setAttribute('role', 'status');
        toast.setAttribute('aria-live', 'polite');
        toast.textContent = 'Welcome to StudyBuddy ✨';
        document.body.appendChild(toast);

        requestAnimationFrame(() => toast.classList.add('is-visible'));
        window.setTimeout(() => toast.classList.remove('is-visible'), reducedMotion ? 1100 : 1900);
        window.setTimeout(() => toast.remove(), reducedMotion ? 1500 : 2400);
    };

    const nativeFallback = () => {
        const canvas = document.createElement('canvas');
        canvas.className = 'sb-welcome-confetti-canvas';
        canvas.setAttribute('aria-hidden', 'true');
        document.body.appendChild(canvas);

        const context = canvas.getContext('2d');
        if (!context) {
            canvas.remove();
            return;
        }

        const colors = ['#7c3aed', '#22d3ee', '#f472b6', '#facc15', '#34d399', '#60a5fa'];
        const particles = [];
        const dpr = Math.min(window.devicePixelRatio || 1, 2);
        let width = 0;
        let height = 0;
        let frame = 0;

        const resize = () => {
            width = window.innerWidth;
            height = window.innerHeight;
            canvas.width = Math.floor(width * dpr);
            canvas.height = Math.floor(height * dpr);
            canvas.style.width = `${width}px`;
            canvas.style.height = `${height}px`;
            context.setTransform(dpr, 0, 0, dpr, 0, 0);
        };

        resize();

        for (let index = 0; index < 135; index += 1) {
            const angle = (-Math.PI * 0.9) + (Math.random() * Math.PI * 0.8);
            const speed = 6 + Math.random() * 10;
            particles.push({
                x: width * (0.35 + Math.random() * 0.3),
                y: height * 0.58,
                vx: Math.cos(angle) * speed + (Math.random() - 0.5) * 4,
                vy: Math.sin(angle) * speed - Math.random() * 4,
                gravity: 0.18 + Math.random() * 0.08,
                drag: 0.985,
                width: 5 + Math.random() * 7,
                height: 3 + Math.random() * 5,
                rotation: Math.random() * Math.PI,
                rotationSpeed: (Math.random() - 0.5) * 0.35,
                color: colors[index % colors.length],
                opacity: 1,
            });
        }

        const draw = () => {
            frame += 1;
            context.clearRect(0, 0, width, height);

            particles.forEach((particle) => {
                particle.vx *= particle.drag;
                particle.vy = particle.vy * particle.drag + particle.gravity;
                particle.x += particle.vx;
                particle.y += particle.vy;
                particle.rotation += particle.rotationSpeed;
                if (frame > 115) particle.opacity = Math.max(0, particle.opacity - 0.028);

                context.save();
                context.globalAlpha = particle.opacity;
                context.translate(particle.x, particle.y);
                context.rotate(particle.rotation);
                context.fillStyle = particle.color;
                context.fillRect(-particle.width / 2, -particle.height / 2, particle.width, particle.height);
                context.restore();
            });

            if (frame < 165 && particles.some((particle) => particle.opacity > 0)) {
                requestAnimationFrame(draw);
            } else {
                canvas.remove();
            }
        };

        window.addEventListener('resize', resize, { once: true });
        requestAnimationFrame(draw);
    };

    const fireConfetti = () => {
        if (reducedMotion) return;

        if (typeof window.confetti === 'function') {
            const defaults = {
                disableForReducedMotion: true,
                zIndex: 2147483000,
                colors: ['#7c3aed', '#22d3ee', '#f472b6', '#facc15', '#34d399', '#60a5fa'],
            };

            window.confetti({ ...defaults, particleCount: 90, spread: 75, startVelocity: 42, origin: { x: 0.5, y: 0.58 } });
            window.setTimeout(() => {
                window.confetti({ ...defaults, particleCount: 45, angle: 60, spread: 55, origin: { x: 0.06, y: 0.72 } });
                window.confetti({ ...defaults, particleCount: 45, angle: 120, spread: 55, origin: { x: 0.94, y: 0.72 } });
            }, 180);
            return;
        }

        nativeFallback();
    };

    const start = () => {
        showWelcome();
        window.setTimeout(fireConfetti, 120);
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start, { once: true });
    } else {
        start();
    }
})();
