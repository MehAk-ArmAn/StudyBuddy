(function () {
  const DEFAULT_THEME = 'cosmic-dolphin';
  const THEME_KEYS = ['studybuddy.theme', 'studybuddyTheme', 'sb_theme', 'dashboard_style'];

  const themeAliases = {
    bts: 'bts-purple-galaxy',
    'bts-purple': 'bts-purple-galaxy',
    purple: 'bts-purple-galaxy',
    dolphin: 'cosmic-dolphin',
    ocean: 'ocean-focus',
    forest: 'forest-calm',
    night: 'night-study',
    candy: 'candy-pop',
    solar: 'solar-gold',
    neon: 'neon-gamer'
  };

  const themes = [
    'cosmic-dolphin',
    'bts-purple-galaxy',
    'ocean-focus',
    'candy-pop',
    'forest-calm',
    'night-study',
    'solar-gold',
    'neon-gamer'
  ];

  function normalizeTheme(value) {
    const raw = String(value || DEFAULT_THEME).trim().toLowerCase().replace(/_/g, '-').replace(/\s+/g, '-');
    return themeAliases[raw] || (themes.includes(raw) ? raw : DEFAULT_THEME);
  }

  function clearStoredTheme() {
    THEME_KEYS.forEach((key) => {
      try { localStorage.removeItem(key); } catch (e) {}
    });
  }

  function saveStoredTheme(theme) {
    const safeTheme = normalizeTheme(theme);
    try { localStorage.setItem('studybuddy.theme', safeTheme); } catch (e) {}
  }

  function applyTheme(theme, persist) {
    const safeTheme = normalizeTheme(theme);
    document.documentElement.dataset.sbTheme = safeTheme;

    if (document.body) {
      themes.forEach((name) => document.body.classList.remove('sb-theme-' + name));
      document.body.classList.add('sb-theme-' + safeTheme);
      document.body.dataset.sbActiveTheme = safeTheme;
    }

    if (persist) {
      saveStoredTheme(safeTheme);
    }
  }

  function isAuthenticated() {
    const body = document.body;
    if (!body) return false;
    if (body.dataset.sbAuth === '1') return true;
    if (body.dataset.sbAuth === '0') return false;
    return !!document.querySelector('form[action*="logout"], a[href*="logout"], [data-auth-user]');
  }

  function bootTheme() {
    const body = document.body;
    if (!body) return;

    const loggedIn = isAuthenticated();

    if (!loggedIn) {
      clearStoredTheme();
      applyTheme(DEFAULT_THEME, false);
      return;
    }

    const savedFromServer = body.dataset.sbTheme || body.dataset.theme || '';
    let savedFromStorage = '';

    try {
      savedFromStorage = localStorage.getItem('studybuddy.theme') || '';
    } catch (e) {}

    applyTheme(savedFromStorage || savedFromServer || DEFAULT_THEME, false);

    document.querySelectorAll('select[name="avatar_style"], select[name="dashboard_style"], [data-theme-select]').forEach((select) => {
      select.value = normalizeTheme(select.value);
      select.addEventListener('change', function () {
        applyTheme(this.value, true);
        document.querySelectorAll('[data-theme-choice]').forEach((card) => {
          card.classList.toggle('is-selected', normalizeTheme(card.dataset.themeChoice) === normalizeTheme(this.value));
        });
      });
    });

    document.addEventListener('submit', function (event) {
      const form = event.target;
      if (form && form.matches('form[action*="logout"]')) {
        clearStoredTheme();
        applyTheme(DEFAULT_THEME, false);
      }
    }, true);

    document.addEventListener('click', function (event) {
      const trigger = event.target.closest('button, a');
      if (!trigger) return;
      const text = (trigger.textContent || '').trim().toLowerCase();
      const href = trigger.getAttribute('href') || '';
      if (text === 'logout' || href.includes('/logout')) {
        clearStoredTheme();
        applyTheme(DEFAULT_THEME, false);
      }
    }, true);
  }

  let lastMissionTrigger = null;

  function textOf(el) {
    return (el && el.textContent ? el.textContent : '').replace(/\s+/g, ' ').trim().toLowerCase();
  }

  function findMissionModal() {
    const direct = document.querySelector('[data-mission-modal], #mission-preview-modal, .mission-modal, .mission-preview-modal, [role="dialog"][aria-label*="Mission"]');
    if (direct) return direct;

    const candidates = Array.from(document.querySelectorAll('[role="dialog"], dialog, .modal, section, article, div'));
    return candidates.find((el) => {
      const txt = textOf(el);
      return txt.includes('mission preview') && txt.includes('save to my quest') && txt.includes('keep browsing');
    }) || null;
  }

  function ensureStatus(modal) {
    let status = modal.querySelector('[data-mission-status]');
    if (!status) {
      status = document.createElement('p');
      status.dataset.missionStatus = '1';
      status.className = 'mission-status';
      const footer = modal.querySelector('.mission-actions, .modal-actions, footer') || modal;
      footer.appendChild(status);
    }
    return status;
  }

  function openMission(trigger) {
    const modal = findMissionModal();
    if (!modal) return;

    lastMissionTrigger = trigger || lastMissionTrigger;

    modal.hidden = false;
    modal.removeAttribute('hidden');
    modal.setAttribute('aria-hidden', 'false');
    modal.classList.add('is-open', 'open', 'active');
    document.body.classList.add('sb-modal-open');

    const status = modal.querySelector('[data-mission-status]');
    if (status) status.textContent = '';

    const firstButton = modal.querySelector('button, a, [tabindex]');
    if (firstButton) setTimeout(() => firstButton.focus({ preventScroll: true }), 50);
  }

  function closeMission() {
    const modal = findMissionModal();
    if (!modal) return;

    modal.classList.remove('is-open', 'open', 'active');
    modal.setAttribute('aria-hidden', 'true');
    modal.hidden = true;
    modal.style.display = '';
    document.body.classList.remove('sb-modal-open');
  }

  function saveMission() {
    const modal = findMissionModal();
    if (!modal) return;

    const source = lastMissionTrigger || modal;
    const title =
      source.dataset.missionTitle ||
      source.dataset.appTitle ||
      source.dataset.title ||
      modal.querySelector('[data-mission-title], h2, h3')?.textContent?.trim() ||
      'StudyBuddy Mission';

    const mission = {
      title,
      savedAt: new Date().toISOString()
    };

    try {
      localStorage.setItem('studybuddy.savedMission', JSON.stringify(mission));
      const list = JSON.parse(localStorage.getItem('studybuddy.savedMissions') || '[]');
      list.unshift(mission);
      localStorage.setItem('studybuddy.savedMissions', JSON.stringify(list.slice(0, 12)));
    } catch (e) {}

    const status = ensureStatus(modal);
    status.textContent = 'Saved to My Quest. You can keep browsing.';
    status.classList.add('is-visible');

    modal.classList.add('is-saved');
  }

  function bootMissionModal() {
    document.addEventListener('click', function (event) {
      const trigger = event.target.closest('button, a, [role="button"]');
      if (!trigger) return;

      const txt = textOf(trigger);
      const hasAttr =
        trigger.matches('[data-mission-open], [data-mission-preview], [data-open-mission], [data-preview-mission], [data-mission-save], [data-mission-close], [data-keep-browsing]');

      if (!hasAttr && !txt.includes('preview mission') && !txt.includes('save to my quest') && !txt.includes('keep browsing')) {
        return;
      }

      if (txt.includes('save to my quest') || trigger.matches('[data-mission-save]')) {
        event.preventDefault();
        event.stopPropagation();
        saveMission();
        return;
      }

      if (txt.includes('keep browsing') || trigger.matches('[data-mission-close], [data-keep-browsing]')) {
        event.preventDefault();
        event.stopPropagation();
        closeMission();
        return;
      }

      if (txt.includes('preview mission') || trigger.matches('[data-mission-open], [data-mission-preview], [data-open-mission], [data-preview-mission]')) {
        event.preventDefault();
        event.stopPropagation();
        openMission(trigger);
      }
    }, true);

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') closeMission();
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    bootTheme();
    bootMissionModal();
  });
})();
