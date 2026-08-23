document.querySelector('[data-nav-toggle]')?.addEventListener('click', () => {
    document.querySelector('[data-nav-links]')?.classList.toggle('open');
});

const sparkleField = document.querySelector('[data-sparkle-field]');
if (sparkleField) {
    const sparkleCount = window.matchMedia('(max-width: 640px)').matches ? 26 : 54;

    for (let index = 0; index < sparkleCount; index += 1) {
        const spark = document.createElement('span');
        spark.className = 'moving-spark';
        spark.style.left = `${Math.random() * 100}%`;
        spark.style.setProperty('--spark-duration', `${7 + Math.random() * 8}s`);
        spark.style.setProperty('--spark-delay', `${Math.random() * -12}s`);
        spark.style.setProperty('--spark-drift', `${-60 + Math.random() * 120}px`);
        spark.style.width = `${3 + Math.random() * 6}px`;
        spark.style.height = spark.style.width;
        sparkleField.appendChild(spark);
    }
}

document.querySelectorAll('.lively-card').forEach((card) => {
    card.addEventListener('mousemove', (event) => {
        const rect = card.getBoundingClientRect();
        const x = (event.clientX - rect.left) / rect.width - 0.5;
        const y = (event.clientY - rect.top) / rect.height - 0.5;
        card.style.transform = `translateY(-8px) rotateX(${y * -4}deg) rotateY(${x * 5}deg)`;
    });

    card.addEventListener('mouseleave', () => {
        card.style.transform = '';
    });
});
