(function(){
  function ready(fn){document.readyState !== 'loading' ? fn() : document.addEventListener('DOMContentLoaded', fn)}
  ready(function(){
    const search = document.querySelector('[data-sb-app-search]');
    const filter = document.querySelector('[data-sb-app-filter]');
    const cards = Array.from(document.querySelectorAll('[data-app-card]'));
    function apply(){
      const q = (search && search.value || '').toLowerCase().trim();
      const cat = filter && filter.value || 'all';
      cards.forEach(card => {
        const matchesText = !q || (card.dataset.search || '').includes(q);
        const matchesCat = cat === 'all' || card.dataset.category === cat;
        card.style.display = matchesText && matchesCat ? '' : 'none';
      });
    }
    if(search) search.addEventListener('input', apply);
    if(filter) filter.addEventListener('change', apply);

    if('serviceWorker' in navigator && (location.hostname === 'localhost' || location.hostname === '127.0.0.1' || location.protocol === 'https:')){
      navigator.serviceWorker.register('/studybuddy-sw.js').catch(function(){ /* safe no-op for local dev */ });
    }
  });
})();
