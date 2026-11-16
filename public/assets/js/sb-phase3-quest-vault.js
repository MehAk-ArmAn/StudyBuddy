/*
 * StudyBuddy Phase 3
 * Database-backed My Quest foundation + server-synced dashboard style.
 */
(function () {
  const DEFAULT_THEME = 'cosmic-dolphin';
  const THEME_KEY = 'studybuddy.theme';
  const LOCAL_QUESTS_KEY = 'studybuddy.savedMissions';

  const allowedThemes = [
    'cosmic-dolphin',
    'bts-purple-galaxy',
    'ocean-focus',
    'candy-pop',
    'forest-calm',
    'night-study',
    'solar-gold',
    'neon-gamer'
  ];

  function csrf() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  }

  function isLoggedIn() {
    const body = document.body;
    if (body?.dataset?.sbAuth === '1') return true;
    if (body?.dataset?.sbAuth === '0') return false;
    return !!document.querySelector('form[action*="logout"], a[href*="logout"], [data-auth-user]');
  }

  function normalizeTheme(value) {
    const safe = String(value || DEFAULT_THEME).trim().toLowerCase().replace(/_/g, '-').replace(/\s+/g, '-');
    return allowedThemes.includes(safe) ? safe : DEFAULT_THEME;
  }

  function toast(message) {
    let el = document.querySelector('.sb-phase3-toast');

    if (!el) {
      el = document.createElement('div');
      el.className = 'sb-phase3-toast';
      el.setAttribute('role', 'status');
      el.setAttribute('aria-live', 'polite');
      document.body.appendChild(el);
    }

    el.textContent = message;
    el.classList.add('is-visible');

    clearTimeout(el._hideTimer);
    el._hideTimer = setTimeout(() => el.classList.remove('is-visible'), 2800);
  }

  function textOf(el) {
    return (el?.textContent || '').replace(/\s+/g, ' ').trim();
  }

  function slugify(value) {
    return String(value || 'studybuddy')
      .toLowerCase()
      .replace(/&/g, 'and')
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/^-+|-+$/g, '') || 'studybuddy';
  }

  function closestCard(trigger) {
    return trigger.closest('[data-app-card], [data-mission-card], .app-card, .mini-app-card, article, section') || document.body;
  }

  function readMissionPayload(trigger) {
    const card = closestCard(trigger);
    const title =
      trigger.dataset.missionTitle ||
      card.dataset.missionTitle ||
      card.dataset.title ||
      card.querySelector('[data-mission-title], h1, h2, h3, .app-title, .card-title')?.textContent ||
      'StudyBuddy Mission';

    const appTitle =
      trigger.dataset.appTitle ||
      card.dataset.appTitle ||
      card.querySelector('[data-app-title], .app-title, h2, h3')?.textContent ||
      title;

    const description =
      trigger.dataset.missionDescription ||
      card.dataset.missionDescription ||
      card.querySelector('[data-mission-description], .mission-description, .app-description, p')?.textContent ||
      '';

    const difficulty =
      trigger.dataset.difficulty ||
      card.dataset.difficulty ||
      card.querySelector('[data-difficulty]')?.textContent ||
      '';

    const minutesRaw =
      trigger.dataset.estimatedMinutes ||
      card.dataset.estimatedMinutes ||
      card.querySelector('[data-estimated-minutes]')?.textContent ||
      '';

    const minutes = parseInt(String(minutesRaw).replace(/[^\d]/g, ''), 10);

    return {
      app_slug: slugify(appTitle),
      app_title: textOf({ textContent: appTitle }) || 'StudyBuddy',
      mission_title: textOf({ textContent: title }) || 'StudyBuddy Mission',
      mission_description: textOf({ textContent: description }),
      difficulty: textOf({ textContent: difficulty }),
      estimated_minutes: Number.isFinite(minutes) ? minutes : null,
      source_url: window.location.pathname,
      metadata: {
        saved_from: 'phase3_quest_vault',
        page_title: document.title,
        saved_at_client: new Date().toISOString()
      }
    };
  }

  function saveLocalQuest(payload) {
    try {
      const list = JSON.parse(localStorage.getItem(LOCAL_QUESTS_KEY) || '[]');
      const exists = list.some((item) =>
        item.app_slug === payload.app_slug && item.mission_title === payload.mission_title
      );

      if (!exists) {
        list.unshift({
          ...payload,
          savedAt: new Date().toISOString(),
          status: 'saved'
        });
      }

      localStorage.setItem(LOCAL_QUESTS_KEY, JSON.stringify(list.slice(0, 50)));
      localStorage.setItem('studybuddy.savedMission', JSON.stringify(payload));
    } catch (error) {
      console.warn('StudyBuddy local quest save failed', error);
    }
  }

  async function saveQuest(trigger) {
    const payload = readMissionPayload(trigger);

    if (!isLoggedIn() || !csrf()) {
      saveLocalQuest(payload);
      toast('Saved locally. Log in to sync it to My Quest.');
      return;
    }

    try {
      const response = await fetch('/my-quest', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrf()
        },
        credentials: 'same-origin',
        body: JSON.stringify(payload)
      });

      if (!response.ok) {
        throw new Error('Save failed');
      }

      saveLocalQuest(payload);
      toast('Mission saved to My Quest ✨');
    } catch (error) {
      console.warn('StudyBuddy quest sync failed', error);
      saveLocalQuest(payload);
      toast('Saved locally. Sync will work after login/session refresh.');
    }
  }

  function bindQuestSave() {
    document.addEventListener('click', function (event) {
      const trigger = event.target.closest('button, a, [role="button"]');
      if (!trigger) return;

      const label = textOf(trigger).toLowerCase();
      const isSaveButton =
        trigger.matches('[data-mission-save], [data-save-quest], [data-save-to-quest]') ||
        label.includes('save to my quest') ||
        label.includes('save mission') ||
        label.includes('add to quest');

      if (!isSaveButton) return;

      event.preventDefault();
      event.stopPropagation();

      saveQuest(trigger);
    }, true);
  }

  async function syncTheme(theme) {
    const safeTheme = normalizeTheme(theme);

    try {
      localStorage.setItem(THEME_KEY, safeTheme);
    } catch (error) {}

    if (!isLoggedIn() || !csrf()) {
      return;
    }

    try {
      const response = await fetch('/dashboard/theme', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrf()
        },
        credentials: 'same-origin',
        body: JSON.stringify({ theme: safeTheme })
      });

      if (response.ok) {
        toast('Dashboard style saved ✨');
      }
    } catch (error) {
      console.warn('StudyBuddy theme sync failed', error);
    }
  }

  function bindThemeSync() {
    document.addEventListener('change', function (event) {
      const select = event.target.closest('select[name="avatar_style"], select[name="dashboard_style"], [data-theme-select]');
      if (!select) return;

      syncTheme(select.value);
    }, true);

    document.addEventListener('click', function (event) {
      const choice = event.target.closest('[data-theme-choice]');
      if (!choice) return;

      const theme = choice.dataset.themeChoice;
      if (!theme) return;

      syncTheme(theme);
    }, true);

    document.addEventListener('submit', function (event) {
      const form = event.target;
      if (form && form.matches('form[action*="logout"]')) {
        try {
          localStorage.removeItem(THEME_KEY);
          localStorage.removeItem('studybuddyTheme');
          localStorage.removeItem('sb_theme');
        } catch (error) {}
      }
    }, true);
  }

  function addQuestShortcut() {
    if (!isLoggedIn()) return;
    if (document.querySelector('.sb-quest-floating-link')) return;
    if (window.location.pathname === '/my-quest') return;

    const link = document.createElement('a');
    link.href = '/my-quest';
    link.className = 'sb-quest-floating-link';
    link.innerHTML = '<span aria-hidden="true">✨</span><span>My Quest</span>';
    document.body.appendChild(link);
  }

  function boot() {
    bindQuestSave();
    bindThemeSync();
    addQuestShortcut();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
