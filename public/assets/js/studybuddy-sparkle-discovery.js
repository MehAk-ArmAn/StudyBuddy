/* Scoped, progressive enhancement only. No writes, APIs, or personal data. */
(() => {
  const root = document.querySelector('[data-sb-discovery]');
  if (!root) return;
  const cards = Array.from(root.querySelectorAll('[data-sb-discovery-card]'));
  const filters = Array.from(root.querySelectorAll('[data-sb-topic]'));
  const surprise = root.querySelector('[data-sb-surprise]');
  const status = root.querySelector('[data-sb-discovery-status]');
  if (!cards.length) return;

  const chooseTopic = (topic) => {
    let count = 0;
    cards.forEach((card) => {
      const visible = topic === 'all' || card.dataset.topic === topic;
      card.hidden = !visible;
      card.classList.remove('is-surprised');
      if (visible) count++;
    });
    filters.forEach((button) => {
      const active = button.dataset.sbTopic === topic;
      button.classList.toggle('is-selected', active);
      button.setAttribute('aria-pressed', String(active));
    });
    if (status) status.textContent = `Showing ${count} learning world ${count === 1 ? 'preview' : 'previews'}.`;
  };

  filters.forEach(button => button.addEventListener('click', () => chooseTopic(button.dataset.sbTopic || 'all')));
  surprise?.addEventListener('click', () => {
    const shown = cards.filter(card => !card.hidden);
    if (!shown.length) return;
    cards.forEach(card => card.classList.remove('is-surprised'));
    const chosen = shown[Math.floor(Math.random() * shown.length)];
    chosen.classList.add('is-surprised');
    chosen.scrollIntoView({ behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth', block: 'center' });
    const heading = chosen.querySelector('h3');
    if (status && heading) status.textContent = `Compass chose ${heading.textContent.trim()}. Open its card to explore the details.`;
  });
})();
