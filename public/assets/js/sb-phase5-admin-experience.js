(function () {
  function $(selector, scope) { return (scope || document).querySelector(selector); }
  function $all(selector, scope) { return Array.from((scope || document).querySelectorAll(selector)); }

  document.addEventListener('click', function (event) {
    const tab = event.target.closest('[data-sb-admin-tab]');
    if (tab) {
      const target = tab.dataset.sbAdminTab;
      $all('[data-sb-admin-tab]').forEach(btn => btn.classList.toggle('is-active', btn === tab));
      $all('[data-sb-admin-panel]').forEach(panel => panel.classList.toggle('is-active', panel.dataset.sbAdminPanel === target));
    }

    const roleTab = event.target.closest('[data-sbx-tab-button]');
    if (roleTab) {
      const wrap = roleTab.closest('[data-sbx-tabs]');
      const target = roleTab.dataset.sbxTabButton;
      $all('[data-sbx-tab-button]', wrap).forEach(btn => btn.classList.toggle('is-active', btn === roleTab));
      $all('[data-sbx-tab-panel]', wrap).forEach(panel => panel.classList.toggle('is-active', panel.dataset.sbxTabPanel === target));
    }

    if (event.target.closest('[data-sbx-generate-plan]')) {
      const root = event.target.closest('[data-sbx-builder="study-plan"]');
      const subject = $('[data-sbx-plan="subject"]', root)?.value || 'your subject';
      const minutes = $('[data-sbx-plan="minutes"]', root)?.value || '25';
      const mood = $('[data-sbx-plan="mood"]', root)?.value || 'Focused';
      $('[data-sbx-plan-output]', root).innerHTML = `Mission ready: <br>1) ${minutes} minutes of ${subject}.<br>2) Start with a 3-minute warm-up.<br>3) Complete one mini challenge.<br>4) Save a quest reward. Mood mode: ${mood}.`;
    }

    if (event.target.closest('[data-sbx-calc-points]')) {
      const root = event.target.closest('[data-sbx-builder="points"]');
      const missions = Number($('[data-sbx-points="missions"]', root)?.value || 0);
      const quizzes = Number($('[data-sbx-points="quizzes"]', root)?.value || 0);
      const focus = Number($('[data-sbx-points="focus"]', root)?.value || 0);
      const total = missions * 25 + quizzes * 15 + focus * 10;
      $('[data-sbx-points-output]', root).textContent = `Estimated reward: ${total} StudyBuddy points.`;
    }

    if (event.target.closest('[data-sbx-build-lesson]')) {
      const root = event.target.closest('[data-sbx-builder="lesson"]');
      const topic = $('[data-sbx-lesson="topic"]', root)?.value || 'today’s topic';
      const age = $('[data-sbx-lesson="age"]', root)?.value || 'your class';
      $('[data-sbx-lesson-output]', root).innerHTML = `Lesson outline for ${topic} (${age}):<br>• 5 min warm-up question<br>• 10 min concept demo<br>• 15 min mini mission<br>• 5 min reflection<br>• Save follow-up quest in StudyBuddy.`;
    }

    if (event.target.closest('[data-sbx-copy-template]')) {
      const text = $('[data-sbx-copy-source]')?.value || '';
      navigator.clipboard?.writeText(text);
      event.target.textContent = 'Copied ✓';
      setTimeout(() => event.target.textContent = 'Copy template', 1200);
    }
  });
})();
