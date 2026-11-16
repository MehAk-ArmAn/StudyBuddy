/* Runs before CSS: prevent a flash of the wrong theme. No network or cookies. */
(function(){
  var allowed={light:1,dark:1,system:1}; var value='system';
  try{var stored=window.localStorage.getItem('studybuddy-color-mode');if(allowed[stored])value=stored;}catch(_e){}
  var dark=false;try{dark=!!window.matchMedia('(prefers-color-scheme: dark)').matches;}catch(_e){}
  var root=document.documentElement;
  root.setAttribute('data-sb-color-mode',value);
  root.setAttribute('data-sb-color-theme',value==='system'?(dark?'dark':'light'):value);
})();
