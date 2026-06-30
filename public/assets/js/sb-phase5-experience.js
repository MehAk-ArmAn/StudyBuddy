(function () {
  function ready(fn) {
    if (document.readyState !== 'loading') fn();
    else document.addEventListener('DOMContentLoaded', fn);
  }

  function revealOnScroll() {
    const items = document.querySelectorAll('.sbx-reveal');
    if (!items.length) return;

    if (!('IntersectionObserver' in window)) {
      items.forEach((item) => item.classList.add('is-visible'));
      return;
    }

    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12 });

    items.forEach((item) => observer.observe(item));
  }

  function animateCounters() {
    const counters = document.querySelectorAll('[data-sbx-count]');
    counters.forEach((counter) => {
      const target = Number(counter.dataset.sbxCount || 0);
      let current = 0;
      const step = Math.max(1, Math.ceil(target / 28));
      const tick = () => {
        current = Math.min(target, current + step);
        counter.textContent = current.toLocaleString();
        if (current < target) requestAnimationFrame(tick);
      };
      tick();
    });
  }

  function rolePicker() {
    document.querySelectorAll('[data-sbx-role-picker]').forEach((picker) => {
      const tabs = picker.querySelectorAll('[data-sbx-role-tab]');
      const panels = picker.querySelectorAll('[data-sbx-role-panel]');

      tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
          const key = tab.dataset.sbxRoleTab;
          tabs.forEach((item) => item.classList.toggle('is-active', item === tab));
          panels.forEach((panel) => panel.classList.toggle('is-active', panel.dataset.sbxRolePanel === key));
        });
      });
    });
  }

  function sessionBuilder() {
    document.querySelectorAll('[data-sbx-session-builder]').forEach((builder) => {
      const mood = builder.querySelector('[data-sbx-builder-mood]');
      const focus = builder.querySelector('[data-sbx-builder-focus]');
      const button = builder.querySelector('[data-sbx-build-session]');
      const result = builder.querySelector('[data-sbx-builder-result]');
      if (!button || !result) return;

      const plans = {
        calm: 'Start with 2 minutes of setup, then 12 minutes of focused practice, then save one next quest.',
        rush: 'Do a 10-minute sprint: one quick challenge, one correction round, and one tiny reward.',
        lost: 'Pick the easiest mission first, read the hint, and complete only one step today.',
        challenge: 'Choose a hard quest, set a timer, and try to beat your previous score.'
      };

      button.addEventListener('click', () => {
        const moodText = mood ? mood.value : 'calm';
        const focusText = focus ? focus.value : 'study';
        result.innerHTML = '<strong>' + focusText.toUpperCase() + ' PLAN:</strong> ' + plans[moodText];
      });
    });
  }

  function pointsLab() {
    document.querySelectorAll('[data-sbx-points-lab]').forEach((lab) => {
      const missions = lab.querySelector('[data-sbx-points-missions]');
      const focus = lab.querySelector('[data-sbx-points-focus]');
      const reading = lab.querySelector('[data-sbx-points-reading]');
      const button = lab.querySelector('[data-sbx-calc-points]');
      const result = lab.querySelector('[data-sbx-points-result]');
      if (!button || !result) return;

      button.addEventListener('click', () => {
        const score =
          (Number(missions?.value || 0) * 35) +
          (Number(focus?.value || 0) * 20) +
          (Number(reading?.value || 0) * 25);

        let badge = 'First Quest';
        if (score >= 250) badge = 'Galaxy Grinder';
        else if (score >= 150) badge = 'Streak Starter';
        else if (score >= 80) badge = 'Focus Spark';

        result.innerHTML = 'Estimated reward: <strong>' + score + ' points</strong>. Suggested badge: <strong>' + badge + '</strong>.';
      });
    });
  }

  function lessonBuilder() {
    document.querySelectorAll('[data-sbx-lesson-builder]').forEach((builder) => {
      const topic = builder.querySelector('[data-sbx-lesson-topic]');
      const time = builder.querySelector('[data-sbx-lesson-time]');
      const style = builder.querySelector('[data-sbx-lesson-style]');
      const button = builder.querySelector('[data-sbx-build-lesson]');
      const result = builder.querySelector('[data-sbx-lesson-result]');
      if (!button || !result) return;

      button.addEventListener('click', () => {
        const topicText = topic?.value || 'Today’s topic';
        const timeText = time?.value || '20 minutes';
        const styleText = style?.value || 'Quest challenge';

        result.innerHTML =
          '<strong>' + topicText + ' • ' + styleText + '</strong><br>' +
          '1. Warm-up question • 2. Mini mission • 3. Practice round • 4. Reflection badge<br>' +
          'Suggested time: ' + timeText + '.';
      });
    });
  }

  function faq() {
    document.querySelectorAll('[data-sbx-faq-button]').forEach((button) => {
      button.addEventListener('click', () => {
        const item = button.closest('article');
        if (!item) return;
        item.classList.toggle('is-open');
        const icon = button.querySelector('span');
        if (icon) icon.textContent = item.classList.contains('is-open') ? '−' : '+';
      });
    });
  }

  function copyTemplate() {
    document.querySelectorAll('[data-sbx-copy-template]').forEach((button) => {
      button.addEventListener('click', async () => {
        const wrap = button.closest('.sbx-panel') || document;
        const source = wrap.querySelector('[data-sbx-copy-source]');
        const status = wrap.querySelector('[data-sbx-copy-status]');
        if (!source) return;

        try {
          await navigator.clipboard.writeText(source.value || source.textContent || '');
          if (status) status.textContent = 'Copied. You can paste it into an email or support form.';
        } catch (error) {
          source.select?.();
          if (status) status.textContent = 'Select the text and copy it manually.';
        }
      });
    });
  }

  function floatingHub() {
    if (document.querySelector('.sbx-floating-hub')) return;

    const hub = document.createElement('div');
    hub.className = 'sbx-floating-hub';
    hub.innerHTML = [
      '<div class="sbx-floating-hub__panel" aria-label="StudyBuddy quick links">',
      '<a href="/learning-hub">Learning Hub</a>',
      '<a href="/learning-paths">Learning Paths</a>',
      '<a href="/rewards">Rewards</a>',
      '<a href="/app-ecosystem">App Ecosystem</a>',
      '<a href="/command-center">Command Center</a>',
      '</div>',
      '<button type="button" class="sbx-floating-hub__toggle">StudyBuddy ✨</button>'
    ].join('');

    document.body.appendChild(hub);

    hub.querySelector('button').addEventListener('click', () => {
      hub.classList.toggle('is-open');
    });
  }

  ready(function () {
    revealOnScroll();
    animateCounters();
    rolePicker();
    sessionBuilder();
    pointsLab();
    lessonBuilder();
    faq();
    copyTemplate();
    floatingHub();
  });
})();
