(function(){
  function qs(sel, root){return (root||document).querySelector(sel)}
  function qsa(sel, root){return Array.prototype.slice.call((root||document).querySelectorAll(sel))}
  function normalize(text){return String(text||'').toLowerCase().replace(/\s+/g,' ').trim()}
  document.addEventListener('input', function(event){
    var input = event.target.closest('[data-sb-app-search]')
    if(!input) return
    var term = normalize(input.value)
    qsa('[data-sb-app-card]').forEach(function(card){
      var hay = normalize(card.getAttribute('data-search'))
      card.hidden = term && hay.indexOf(term) === -1
    })
  })
  document.addEventListener('click', function(event){
    var trigger = event.target.closest('[data-sb-lock-trigger]')
    if(!trigger) return
    event.preventDefault()
    var modal = qs('[data-sb-lock-modal]')
    if(!modal) return
    var kind = trigger.getAttribute('data-lock-kind') || 'login'
    var title = qs('[data-sb-lock-title]', modal)
    var msg = qs('[data-sb-lock-message]', modal)
    if(kind === 'verify'){
      if(title) title.textContent = 'Verify email to unlock this'
      if(msg) msg.textContent = 'Your account is almost ready. Verify your email to save quests, play web sessions, and earn points safely.'
    } else if(kind === 'soon'){
      if(title) title.textContent = 'Coming soon'
      if(msg) msg.textContent = 'This platform option is not live yet. You can still preview the app details and check back after the admin enables it.'
    } else {
      if(title) title.textContent = 'Create your StudyBuddy account'
      if(msg) msg.textContent = 'Guests can preview apps. Create a free account to save quests, personalize your dashboard, and earn points.'
    }
    modal.hidden = false
    document.body.classList.add('sb-modal-open')
    var close = qs('[data-sb-lock-close]', modal)
    if(close) close.focus({preventScroll:true})
  })
  document.addEventListener('click', function(event){
    if(event.target.closest('[data-sb-lock-close]')){
      var modal = qs('[data-sb-lock-modal]')
      if(modal) modal.hidden = true
      document.body.classList.remove('sb-modal-open')
    }
  })
  document.addEventListener('keydown', function(event){
    if(event.key === 'Escape'){
      var modal = qs('[data-sb-lock-modal]')
      if(modal && !modal.hidden){ modal.hidden = true; document.body.classList.remove('sb-modal-open') }
    }
  })
  document.addEventListener('submit', function(event){
    if(event.target && event.target.matches('[data-sb-logout-form]')){
      try{
        localStorage.removeItem('studybuddy.theme')
        localStorage.removeItem('studybuddyTheme')
        localStorage.removeItem('sb_theme')
        localStorage.removeItem('dashboard_style')
      }catch(e){}
      document.documentElement.dataset.sbTheme = 'cosmic-dolphin'
      if(document.body){
        document.body.dataset.sbActiveTheme = 'cosmic-dolphin'
        document.body.className = document.body.className.replace(/sb-theme-[^\s]+/g,'') + ' sb-theme-cosmic-dolphin'
      }
    }
  }, true)
})()
