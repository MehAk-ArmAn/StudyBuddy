(()=>{
  'use strict';
  const list=document.getElementById('sbp11c-message-list');
  if(list){list.scrollTop=list.scrollHeight;}
  const compose=document.querySelector('.sbp11c-compose');
  const input=compose?.querySelector('textarea');
  if(input){input.addEventListener('keydown',(event)=>{
    if((event.ctrlKey||event.metaKey)&&event.key==='Enter'){
      event.preventDefault();compose.requestSubmit();
    }
  });}
  document.querySelectorAll('.sbp11c-report').forEach(panel=>{
    panel.addEventListener('toggle',()=>{
      if(panel.open){document.querySelectorAll('.sbp11c-report').forEach(other=>{if(other!==panel)other.open=false;});}
    });
  });
})();
