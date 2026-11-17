/* W31: connected launcher journal. The app itself is NOT modified.
   Server stores timestamps, not self-reported scores, mastery or points. */
(() => {
  'use strict';
  const panel = document.querySelector('[data-sb31-player]');
  const launcher = document.querySelector('[data-studybuddy-launcher]');
  if (!panel || !launcher || window.StudyBuddyConnected31) return;
  const state = panel.querySelector('[data-sb31-state]');
  const message = panel.querySelector('[data-sb31-message]');
  const save = panel.querySelector('[data-sb31-finish]');
  const leave = panel.querySelector('[data-sb31-leave]');
  const frame = launcher.querySelector('[data-launcher-frame]');
  const external = launcher.querySelector('.sb-launcher-v3__handoff-actions a[target="_blank"]');
  const status = launcher.querySelector('[data-launcher-state]');
  const csrf = panel.dataset.csrf;
  const base = panel.dataset.sessionPrefix.replace(/\/$/, '');
  let sessionId = null;
  let busy = false;
  let closed = false;
  let started = false;
  let timer = null;

  const setMessage = (text, label = null) => {
    if (message) message.textContent = text;
    if (label && state) state.lastChild.textContent = ` ${label}`;
  };
  const post = async (url, payload = {}) => {
    const res = await fetch(url, {
      method: 'POST', credentials: 'same-origin', cache: 'no-store',
      headers: { 'Accept': 'application/json', 'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
      body: JSON.stringify(payload)
    });
    const data = await res.json().catch(() => ({}));
    if (!res.ok) {
      const err = new Error(data.message || `StudyBuddy returned HTTP ${res.status}`);
      err.activity_url = data.activity_url;
      throw err;
    }
    return data;
  };
  const ready = () => document.visibilityState === 'visible' && !closed && !busy;
  const begin = async () => {
    if (started || !ready()) return;
    busy = true;
    setMessage('Connecting this visit to your private learning journal…', 'Connecting');
    try {
      const data = await post(panel.dataset.startUrl, { app_slug: panel.dataset.appSlug });
      sessionId = data.session_id;
      started = true;
      save.disabled = false;
      leave.hidden = false;
      setMessage(data.resumed ? 'Resumed your existing visit. Save it when you finish.'
        : 'Connected! This visit is being recorded privately. Save it when you are done.', 'Recording');
      timer = window.setInterval(heartbeat, 20000);
    } catch (error) {
      setMessage(error.message || 'Could not connect this session.', 'Not connected');
    } finally { busy = false; }
  };
  const heartbeat = async (resuming = false) => {
    if (!sessionId || !ready() || !document.hasFocus()) return;
    try { await post(`${base}/${sessionId}/heartbeat`, { kind: resuming === true ? 'resume' : 'tick' }); }
    catch (error) {
      if (/closed|HTTP 409/i.test(error.message)) { closed = true; clearInterval(timer); }
    }
  };
  const finish = async type => {
    if (!sessionId || busy || closed) return;
    busy = true;
    save.disabled = true;
    try {
      const data = await post(`${base}/${sessionId}/${type}`);
      closed = true;
      clearInterval(timer);
      leave.hidden = true;
      setMessage(data.message + ' View your personal insights anytime.', type === 'finish' ? 'Saved' : 'Closed');
      save.textContent = type === 'finish' ? 'Visit saved ✓' : 'Visit closed';
    } catch (error) {
      save.disabled = false;
      setMessage(error.message || 'Could not save this visit.', 'Try again');
    } finally { busy = false; }
  };
  save?.addEventListener('click', () => finish('finish'));
  leave?.addEventListener('click', () => finish('leave'));
  if (frame && status) {
    const observe = () => { if (status.dataset.state === 'ready') begin(); };
    new MutationObserver(observe).observe(status, { attributes: true, attributeFilter: ['data-state'] });
    observe();
  }
  if (external) external.addEventListener('click', () => { begin(); }, { passive: true });
  window.addEventListener('pageshow', () => { if (status?.dataset.state === 'ready') begin(); });
  document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') heartbeat(true);
  });
  window.addEventListener('focus', () => heartbeat(true));
  // No claiming playtime from the hidden tab. No automatic completion on unload.
  window.StudyBuddyConnected31 = {
    diagnostics: () => ({ version:'W31', attached:true, started, closed,
      sessionActive:!!sessionId && !closed, origin: 'server',
      readyState: status?.dataset.state || (external ? 'external' : 'unknown'),
      note:'Only launcher visits; not verified game scores or awarded points.' })
  };
})();
