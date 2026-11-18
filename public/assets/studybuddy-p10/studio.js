/** StudyBuddy P10: browser enhancements only; server-backed forms work without JS. */
(() => {
  'use strict';
  const $ = (selector, root = document) => root.querySelector(selector);
  const $$ = (selector, root = document) => [...root.querySelectorAll(selector)];

  const editor = $('#sbp10-body');
  const preview = $('#sbp10-live-preview');
  const outline = $('#sbp10-outline');
  const render = () => {
    if (!editor || !preview || !outline) return;
    preview.replaceChildren();
    outline.replaceChildren();
    let headings = 0;
    const lines = editor.value.replace(/\r\n/g, '\n').split('\n');
    lines.forEach((line) => {
      const head = /^(#{1,4})\s+(.+)/.exec(line);
      const bullet = /^\s*[-*]\s+(.+)/.exec(line);
      const element = document.createElement(head ? `h${Math.min(head[1].length + 1, 6)}` : bullet ? 'p' : 'p');
      if (bullet) element.classList.add('sbp10-markdown-bullet');
      const content = head ? head[2] : bullet ? bullet[1] : line;
      // Use text nodes for all input: Markdown never becomes executable HTML.
      const parts = content.split(/(\*\*[^*\n]+\*\*)/g);
      parts.forEach((part) => {
        if (part.startsWith('**') && part.endsWith('**') && part.length > 4) {
          const strong = document.createElement('strong');
          strong.textContent = part.slice(2, -2);
          element.append(strong);
        } else {
          element.append(document.createTextNode(part));
        }
      });
      if (head) {
        const id = `sbp10-heading-${headings++}`;
        element.id = id;
        const jump = document.createElement('button');
        jump.type = 'button';
        jump.textContent = head[2];
        jump.addEventListener('click', () => {
          showMode('preview');
          $(`#${id}`, preview)?.scrollIntoView({ block: 'center', behavior: 'auto' });
        });
        outline.append(jump);
      }
      if (!line) element.classList.add('sbp10-blank');
      preview.append(element);
    });
    if (!headings) {
      const tip = document.createElement('p');
      tip.className = 'sbp10-empty';
      tip.textContent = 'Add a heading like # Topic to build your outline.';
      outline.append(tip);
    }
  };
  const showMode = (mode) => {
    if (!editor || !preview) return;
    const read = mode === 'preview';
    if (read) render();
    preview.hidden = !read;
    editor.hidden = read;
    const fieldLabel = $('label[for="sbp10-body"]');
    if (fieldLabel) fieldLabel.hidden = read;
    $$('[data-sbp10-mode]').forEach((button) => {
      const current = button.dataset.sbp10Mode === mode;
      button.classList.toggle('is-active', current);
      button.setAttribute('aria-pressed', String(current));
    });
  };
  if (editor) {
    render();
    editor.addEventListener('input', render);
    $$('[data-sbp10-mode]').forEach((button) => button.addEventListener('click', () => showMode(button.dataset.sbp10Mode)));
  }

  const search = $('#sbp10-note-search');
  search?.addEventListener('input', () => {
    const q = search.value.trim().toLocaleLowerCase();
    $$('.sbp10-note-item').forEach((item) => {
      item.hidden = !(item.dataset.noteTitle || '').toLocaleLowerCase().includes(q);
    });
  });
  $$('[data-sbp10-filter]').forEach((button) => button.addEventListener('click', () => {
    const filter = button.dataset.sbp10Filter;
    $$('[data-sbp10-filter]').forEach((b) => {
      const active = b === button;
      b.classList.toggle('is-active', active);
      b.setAttribute('aria-pressed', String(active));
    });
    $$('[data-sbp10-type]').forEach((file) => {
      file.hidden = filter !== 'all' && file.dataset.sbp10Type !== filter;
    });
  }));

  // Actual upload progress, not a simulated animation. Native POST remains a fallback.
  const uploadForm = $('#sbp10-upload-form');
  if (uploadForm && typeof XMLHttpRequest !== 'undefined') {
    uploadForm.addEventListener('submit', (event) => {
      const fileInput = $('#sbp10-upload');
      if (!fileInput?.files?.length) return;
      event.preventDefault();
      const progressContainer = $('.sbp10-upload-progress', uploadForm);
      const progress = $('progress', progressContainer);
      const label = $('[role="status"]', progressContainer);
      const button = $('button[type="submit"]', uploadForm);
      progressContainer.hidden = false;
      button.disabled = true;
      const xhr = new XMLHttpRequest();
      xhr.open('POST', uploadForm.action, true);
      xhr.timeout = 120000;
      xhr.setRequestHeader('Accept', 'application/json');
      xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
      xhr.upload.addEventListener('progress', (e) => {
        if (!e.lengthComputable) return;
        const percent = Math.min(100, Math.round(100 * e.loaded / e.total));
        progress.value = percent;
        label.textContent = `Uploading ${percent}%`;
      });
      const fail = (message) => { button.disabled = false; label.textContent = message; };
      xhr.addEventListener('load', () => {
        if (xhr.status >= 200 && xhr.status < 400) { window.location.assign(uploadForm.action.replace(/\/files\/?$/, '')); return; }
        let message = 'Upload failed. Please check file type or size.';
        try { const data = JSON.parse(xhr.responseText); message = data?.message || message; } catch (_) { /* non-JSON error */ }
        fail(message);
      });
      xhr.addEventListener('error', () => fail('Connection interrupted. Try again.'));
      xhr.addEventListener('timeout', () => fail('Upload timed out. Try again.'));
      xhr.send(new FormData(uploadForm));
    });
  }

  // Audio waveform is computed from the account-owned audio bytes, never random bars.
  $$('.sbp10-audio-wave').forEach((box) => {
    const button = $('button', box);
    const canvas = $('canvas', box);
    const scrubber = $('input[type="range"]', box);
    const audio = $('audio', box.parentElement);
    if (!button || !canvas || !scrubber || !audio) return;
    button.addEventListener('click', async () => {
      button.disabled = true;
      button.textContent = 'Reading audio…';
      try {
        const contextClass = window.AudioContext || window.webkitAudioContext;
        if (!contextClass) throw Error('Audio waveform decoding is unavailable in this browser.');
        const response = await fetch(box.dataset.audio, { credentials: 'same-origin', cache: 'no-store' });
        if (!response.ok) throw Error('Could not load the private recording.');
        const bytes = await response.arrayBuffer();
        const ctx = new contextClass();
        let buffer;
        try { buffer = await ctx.decodeAudioData(bytes); } finally { await ctx.close(); }
        const channel = buffer.getChannelData(0);
        const count = 68;
        const peaks = [];
        for (let i = 0; i < count; i++) {
          const start = Math.floor(i * channel.length / count);
          const end = Math.floor((i + 1) * channel.length / count);
          const step = Math.max(1, Math.floor((end - start) / 1000));
          let peak = 0;
          for (let j = start; j < end; j += step) peak = Math.max(peak, Math.abs(channel[j]));
          peaks.push(peak);
        }
        const width = Math.max(200, box.clientWidth - 12);
        const dpr = Math.min(2, window.devicePixelRatio || 1);
        canvas.width = width * dpr;
        canvas.height = 42 * dpr;
        canvas.style.width = `${width}px`;
        canvas.style.height = '42px';
        const gc = canvas.getContext('2d');
        gc.scale(dpr, dpr);
        const cell = width / count;
        peaks.forEach((peak, i) => {
          const bar = Math.max(2, peak * 38);
          gc.fillStyle = i % 2 ? '#d6a6ff' : '#65eafa';
          gc.fillRect(i * cell + 1, (42 - bar) / 2, Math.max(1, cell - 2), bar);
        });
        canvas.hidden = false;
        scrubber.hidden = false;
        button.hidden = true;
        audio.addEventListener('timeupdate', () => {
          if (Number.isFinite(audio.duration) && audio.duration > 0) scrubber.value = String(Math.round(1000 * audio.currentTime / audio.duration));
        });
        scrubber.addEventListener('input', () => {
          if (Number.isFinite(audio.duration) && audio.duration > 0) audio.currentTime = audio.duration * Number(scrubber.value) / 1000;
        });
        canvas.addEventListener('click', (event) => {
          if (Number.isFinite(audio.duration) && audio.duration > 0) audio.currentTime = Math.min(audio.duration, Math.max(0, audio.duration * event.offsetX / width));
        });
      } catch (e) { button.disabled = false; button.textContent = 'Waveform unavailable · native audio works'; }
    });
  });
})();
