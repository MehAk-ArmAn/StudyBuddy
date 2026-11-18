(()=>{ 'use strict'; const root=document.getElementById('sbp11b-school-os');if(!root)return;
const panel=document.getElementById('sbp11b-detail'),backdrop=document.getElementById('sbp11b-backdrop'),close=document.getElementById('sbp11b-close'),form=document.getElementById('sbp11b-edit-form'),archive=document.getElementById('sbp11b-archive-form'),save=document.getElementById('sbp11b-save');
let trigger=null;function field(id){return document.getElementById('sbp11b-field-'+id)}
function dismiss(){panel.hidden=true;backdrop.hidden=true;document.body.classList.remove('sbp11b-panel-open');if(trigger)trigger.focus();}
function open(card){trigger=card;panel.hidden=false;backdrop.hidden=false;document.body.classList.add('sbp11b-panel-open');
document.getElementById('sbp11b-detail-title').textContent=card.dataset.title||'Task';document.getElementById('sbp11b-detail-creator').textContent=card.dataset.creator||'Unknown';
form.action=card.dataset.updateUrl; if(archive)archive.action=card.dataset.archiveUrl;
for(const k of ['title','details','lane','priority','due','assigned']){let f=field(k);if(f)f.value=card.dataset[k]||'';}
const hint=document.getElementById('sbp11b-read-only-hint'),editable=card.dataset.editable==='1';hint.hidden=editable;
if(save)save.hidden=!editable;for(const element of form.querySelectorAll('input,textarea,select')){if(element.name!=='_token'&&element.name!=='_method')element.disabled=!editable;}
const details=document.getElementById('sbp11b-view-details'),assignee=document.getElementById('sbp11b-view-assignee');if(details)details.textContent=card.dataset.details||'No details provided.';if(assignee)assignee.textContent=card.dataset.assignee||'Unassigned';close.focus();}
root.addEventListener('click',e=>{const card=e.target.closest('.sbp11b-card');if(card){open(card);return;}const toggle=e.target.closest('.sbp11b-lane-toggle');if(toggle){const target=document.getElementById(toggle.getAttribute('aria-controls'));if(!target)return;const collapse=toggle.getAttribute('aria-expanded')==='true';toggle.setAttribute('aria-expanded',String(!collapse));target.hidden=collapse;}});
close?.addEventListener('click',dismiss);backdrop?.addEventListener('click',dismiss);document.addEventListener('keydown',e=>{if(e.key==='Escape'&&!panel.hidden)dismiss()});
})();
