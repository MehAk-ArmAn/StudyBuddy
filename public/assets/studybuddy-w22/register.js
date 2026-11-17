/* StudyBuddy W22: progressive enhancement; registration stays a normal Laravel POST. */
(() => {
  'use strict';
  const form = document.querySelector('form[data-sb22-register]');
  if (!form) return;

  const roleSelect = form.querySelector('#sb-auth-role');
  const birth = form.querySelector('[name="date_of_birth"]');
  const steps = [...form.querySelectorAll('[data-sb22-step]')];
  const previous = form.querySelector('[data-sb22-back]');
  const next = form.querySelector('[data-sb22-next]');
  const submit = form.querySelector('[data-sb22-submit]');
  const counter = form.querySelector('[data-sb22-step-count]');
  const progress = form.querySelector('[data-sb22-progress]');
  const guardian = form.querySelector('[data-sb22-guardian]');
  const guardianInput = guardian?.querySelector('input');
  const guardianLabel = form.querySelector('[data-sb22-guardian-required]');
  const password = form.querySelector('[name="password"]');
  const confirm = form.querySelector('[name="password_confirmation"]');
  const strength = form.querySelector('.sb22-password-tip');
  const notice = form.querySelector('[data-sb22-age-notice]');
  const subjectInput = form.querySelector('[data-sb22-subjects]');
  let index = 0;

  function yearsOld(value) {
    if (!/^\d{4}-\d{2}-\d{2}$/.test(value || '')) return null;
    const day = new Date(`${value}T12:00:00`);
    if (Number.isNaN(day.getTime())) return null;
    const now = new Date();
    let age = now.getFullYear() - day.getFullYear();
    if ((now.getMonth() < day.getMonth()) || (now.getMonth() === day.getMonth() && now.getDate() < day.getDate())) age--;
    return age;
  }

  function syncGuardian() {
    const role = roleSelect.value;
    const age = yearsOld(birth.value);
    const isStudent = role === 'student';
    const isIndependent = role === 'independent_learner';
    const required = isStudent && age !== null && age >= 0 && age < 13;
    if (guardian) guardian.hidden = !isStudent;
    if (guardianInput) { guardianInput.disabled = !isStudent; guardianInput.required = required; }
    if (guardianLabel) guardianLabel.textContent = required ? '(required)' : '(required if under 13)';

    birth.setCustomValidity('');
    if (age !== null && age < 18 && (role === 'parent' || role === 'teacher')) {
      birth.setCustomValidity('Parent and Teacher accounts require age 18 or older.');
    } else if (age !== null && age < 13 && isIndependent) {
      birth.setCustomValidity('Independent learning is available from age 13. Choose Student with a guardian if younger.');
    }

    if (notice) {
      notice.textContent = isStudent
        ? (required ? 'Because you are under 13, a distinct parent or guardian email is required.' : 'Students of all school ages can learn here. Under-13 students need a guardian email.')
        : isIndependent
          ? (age !== null && age < 13 ? 'Independent learning starts at 13. Under-13 learners can use Student with a guardian.' : 'Independent learning is open to teens 13–17 and adults. Teens have learner-only permissions.')
          : 'Parent and Teacher accounts require age 18 or older and retain their verification safeguards.';
    }
  }

  function syncRole() {
    const role = roleSelect.value;
    document.querySelectorAll('[data-role-preview]').forEach(button => {
      const active = button.dataset.rolePreview === role;
      button.classList.toggle('is-active', active);
      button.setAttribute('aria-pressed', String(active));
    });
    form.querySelectorAll('[data-panel]').forEach(panel => {
      const active = panel.dataset.panel === role;
      panel.classList.toggle('is-hidden', !active);
      panel.hidden = !active;
      panel.querySelectorAll('input, textarea, select').forEach(control => { control.disabled = !active; });
    });
    syncGuardian();
  }

  function syncPassword() {
    if (!password || !confirm) return;
    const value = password.value;
    const valid = value.length >= 8 && /[a-z]/i.test(value) && /\d/.test(value);
    const equal = confirm.value === value;
    password.setCustomValidity(value && !valid ? 'Use at least 8 characters including letters and numbers.' : '');
    confirm.setCustomValidity(confirm.value && !equal ? 'The two passwords must match.' : '');
    if (strength) strength.textContent = !value ? 'Strength: not entered'
      : valid ? (equal && confirm.value ? 'Password meets requirements · confirmation matches.' : 'Password meets requirements. Confirm it below.')
      : 'Add at least 8 characters, with letters and numbers.';
  }

  function setStep(value, moveFocus = true) {
    index = Math.max(0, Math.min(steps.length - 1, value));
    steps.forEach((panel, n) => { panel.hidden = n !== index; });
    previous.hidden = index === 0;
    next.hidden = index === steps.length - 1;
    submit.hidden = index !== steps.length - 1;
    counter.textContent = `Step ${index + 1} of ${steps.length}`;
    progress.style.width = `${((index + 1) / steps.length) * 100}%`;
    if (moveFocus) {
      const title = steps[index].querySelector('h2');
      if (title) { title.tabIndex = -1; title.focus({ preventScroll: true }); }
      form.scrollIntoView({ block: 'start', behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth' });
    }
  }

  function firstInvalid(scope) {
    return [...scope.querySelectorAll('input,select,textarea')]
      .find(el => !el.disabled && !el.checkValidity()) || null;
  }

  roleSelect.addEventListener('change', syncRole);
  birth.addEventListener('change', syncGuardian);
  birth.addEventListener('input', syncGuardian);
  document.querySelectorAll('[data-role-preview]').forEach(button => {
    button.addEventListener('click', () => {
      roleSelect.value = button.dataset.rolePreview;
      roleSelect.dispatchEvent(new Event('change', { bubbles: true }));
    });
  });
  previous.addEventListener('click', () => setStep(index - 1));
  next.addEventListener('click', () => {
    syncPassword();
    const invalid = firstInvalid(steps[index]);
    if (invalid) { invalid.reportValidity(); return; }
    setStep(index + 1);
  });
  form.addEventListener('submit', event => {
    syncPassword();
    const badStep = steps.findIndex(step => firstInvalid(step));
    if (badStep !== -1) {
      event.preventDefault();
      setStep(badStep);
      requestAnimationFrame(() => firstInvalid(steps[badStep])?.reportValidity());
      return;
    }
    submit.disabled = true;
    submit.textContent = 'Creating your account…';
  });

  form.querySelectorAll('[data-sb22-toggle-password]').forEach(button => {
    button.addEventListener('click', () => {
      const field = button.parentElement.querySelector('input');
      const shown = field.type === 'password';
      field.type = shown ? 'text' : 'password';
      button.textContent = shown ? 'Hide' : 'Show';
      button.setAttribute('aria-label', `${shown ? 'Hide' : 'Show'} ${field.name.replaceAll('_', ' ')}`);
    });
  });
  [password, confirm].forEach(el => el?.addEventListener('input', syncPassword));

  const subjectButtons = [...form.querySelectorAll('[data-subject]')];
  function parseSubjects() {
    return (subjectInput?.value || '').split(',').map(x => x.trim()).filter(Boolean);
  }
  function syncSubjects() {
    const set = new Set(parseSubjects().map(x => x.toLowerCase()));
    subjectButtons.forEach(button => button.setAttribute('aria-pressed', String(set.has(button.dataset.subject.toLowerCase()))));
  }
  subjectButtons.forEach(button => {
    button.addEventListener('click', () => {
      if (!subjectInput) return;
      const values = parseSubjects();
      const idx = values.findIndex(x => x.toLowerCase() === button.dataset.subject.toLowerCase());
      if (idx >= 0) values.splice(idx, 1);
      else values.push(button.dataset.subject);
      subjectInput.value = values.join(', ').slice(0, 190);
      subjectInput.dispatchEvent(new Event('input', { bubbles: true }));
    });
  });
  subjectInput?.addEventListener('input', syncSubjects);

  // On server-side validation errors, open the step containing the actual error.
  const errored = steps.findIndex(step => step.querySelector('small:not(.sb22-description)'));
  form.classList.add('sb22-js');
  syncRole();
  syncPassword();
  syncSubjects();
  setStep(errored >= 0 ? errored : 0, false);
  window.StudyBuddySignup22 = { status: () => ({ version: 'W23-compatible', step: index + 1, role: roleSelect.value, guardianRequired: !!guardianInput?.required, teenIndependentAllowed: true }) };
})();
