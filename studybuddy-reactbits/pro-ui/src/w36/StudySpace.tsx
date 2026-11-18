/**
 * StudyBuddy Study Space — original React implementation.
 * This is NOT React Bits Pro App Shell 9 source, and does not impersonate its license.
 * No AI API is connected. Drafts live only in sessionStorage and are cleared when the tab session ends.
 */
import React, { useEffect, useMemo, useRef, useState } from 'react';
import { createRoot } from 'react-dom/client';

type Note = { id: string; text: string; createdAt: number };
type Thread = { id: string; title: string; notes: Note[]; createdAt: number; updatedAt: number };
const accountKey = document.getElementById('sb36-react-root')?.getAttribute('data-account-key') || 'local-preview';
const KEY = 'sb36:session-draft-threads:v1:' + accountKey;
const uid = () => 'sb-' + Math.random().toString(36).slice(2, 10) + '-' + Date.now().toString(36);
const clean = (s: unknown, max=160) => typeof s === 'string' ? s.replace(/[\u0000-\u001f]/g,' ').trim().slice(0,max) : '';
function loadThreads(): Thread[] {
  try {
    const data = JSON.parse(sessionStorage.getItem(KEY) || '[]');
    if(!Array.isArray(data)) return [];
    return data.slice(0,30).filter((t:any) => t && typeof t.id === 'string' && Array.isArray(t.notes))
      .map((t:any) => ({ id:clean(t.id,60),title:clean(t.title,90)||'Untitled thread',createdAt:Number(t.createdAt)||Date.now(),updatedAt:Number(t.updatedAt)||Date.now(),notes:t.notes.slice(0,100).map((n:any)=>({id:clean(n.id,60),text:clean(n.text,1200),createdAt:Number(n.createdAt)||Date.now()})) }));
  } catch {return [];}
}
const starters=['Plan a short revision session','Questions to ask my teacher','Things I want to explore'];
function StudySpace(){
 const [threads,setThreads] = useState<Thread[]>(loadThreads);
 const [selected,setSelected]=useState<string|null>(null);
 const [draft,setDraft]=useState('');
 const [search,setSearch]=useState('');
 const [menu,setMenu]=useState(false);
 const [details,setDetails]=useState(false);
 const input=useRef<HTMLTextAreaElement>(null);
 const list=useRef<HTMLDivElement>(null);
 const current=threads.find(t=>t.id===selected)||null;
 const visible=useMemo(()=>threads.filter(t=>t.title.toLowerCase().includes(search.toLowerCase())||t.notes.some(n=>n.text.toLowerCase().includes(search.toLowerCase()))).sort((a,b)=>b.updatedAt-a.updatedAt),[threads,search]);
 useEffect(()=>{try{sessionStorage.setItem(KEY,JSON.stringify(threads));}catch{}},[threads]);
 useEffect(()=>{if(list.current) list.current.scrollTop=list.current.scrollHeight},[current?.notes.length]);
 const create=()=>{const now=Date.now();const id=uid();setThreads(old=>[{id,title:'New study thread',notes:[],createdAt:now,updatedAt:now},...old].slice(0,30));setSelected(id);setMenu(false);setTimeout(()=>input.current?.focus(),0);};
 const save=()=>{const text=clean(draft,1200);if(!text||!current)return;const now=Date.now();setThreads(old=>old.map(t=>t.id===current.id?{...t,title:t.notes.length===0?text.slice(0,48):t.title,updatedAt:now,notes:[...t.notes,{id:uid(),text,createdAt:now}].slice(-100)}:t));setDraft('');try{window.dispatchEvent(new CustomEvent('sb36:celebrate'));}catch{}};
 const remove=(id:string)=>{if(!confirm('Delete this session-only study thread?'))return;setThreads(old=>old.filter(t=>t.id!==id));if(selected===id)setSelected(null);};
 const rename=(id:string)=>{const found=threads.find(t=>t.id===id);if(!found)return;const next=prompt('Thread title',found.title);if(next===null)return;const title=clean(next,90);if(title)setThreads(old=>old.map(t=>t.id===id?{...t,title}:t));};
 const dateLabel=(ms:number)=>new Date(ms).toLocaleTimeString([], {hour:'numeric',minute:'2-digit'});
 return <div className="sb36-space" data-chat-shell>
  <aside className={'sb36-sidebar '+(menu?'is-open':'')} aria-label="Study threads">
   <div className="sb36-brand"><span className="sb36-mark" aria-hidden="true">✦</span><span><strong>Study Space</strong><small>StudyBuddy · your ideas</small></span><button className="sb36-mobile-close" onClick={()=>setMenu(false)} aria-label="Close thread menu">✕</button></div>
   <button className="sb36-new" onClick={create}><span aria-hidden="true">＋</span> New thread</button>
   <label className="sb36-search"><span aria-hidden="true">⌕</span><input value={search} onChange={e=>setSearch(e.target.value)} placeholder="Find a thread..." aria-label="Search your draft threads"/></label>
   <div className="sb36-sidebar-label">Your threads <span>{threads.length}</span></div>
   <nav className="sb36-thread-list" aria-label="Saved threads">
    {visible.length===0?<p className="sb36-no-threads">{search?'No matching threads.':'No threads yet. Start with a thought or question.'}</p>:visible.map(t=><div className={'sb36-thread '+(selected===t.id?'is-active':'')} key={t.id}>
      <button className="sb36-thread-open" onClick={()=>{setSelected(t.id);setMenu(false);}} title={t.title}><span aria-hidden="true">◌</span><span>{t.title}</span></button>
      <button className="sb36-thread-more" aria-label={'Rename '+t.title} title="Rename" onClick={()=>rename(t.id)}>✎</button>
      <button className="sb36-thread-more" aria-label={'Delete '+t.title} title="Delete" onClick={()=>remove(t.id)}>×</button>
    </div>)}
   </nav>
   <div className="sb36-sidebar-foot"><span>⌁</span><p><strong>Private draft space</strong><small>Notes stay in this browser tab session.</small></p></div>
  </aside>
  <section className="sb36-center" aria-label="Study thread content">
    <header className="sb36-topbar"><button className="sb36-mobile-menu" onClick={()=>setMenu(true)} aria-label="Open thread menu">☰</button><div><span className="sb36-overline">STUDYBUDDY WORKSPACE</span><h1>{current?current.title:'A little space for big ideas.'}</h1></div><div className="sb36-top-actions"><span className="sb36-live-status" title="No AI provider is connected">● AI not connected</span><button onClick={()=>setDetails(x=>!x)} aria-expanded={details} aria-controls="sb36-context">ⓘ <span className="sb36-info-word">About</span></button></div></header>
    <div className="sb36-chat-scroller" ref={list} role="region" aria-label="Current thread notes" tabIndex={0}>
     {!current?<div className="sb36-welcome"><span className="sb36-welcome-orb" aria-hidden="true">✦</span><p className="sb36-overline">YOUR PERSONAL LEARNING CORNER</p><h2>Start curious. Stay curious.</h2><p>Collect questions, ideas and revision notes in a clean chat-style workspace. Your AI tutor can join later, when a secure provider is configured.</p><button className="sb36-new sb36-first" onClick={create}>✦ Create a study thread</button><div className="sb36-prompt-row">{starters.map(s=><button key={s} onClick={()=>{const now=Date.now(),id=uid();setThreads(old=>[{id,title:s,notes:[],createdAt:now,updatedAt:now},...old].slice(0,30));setSelected(id);setDraft(s);}}>{s} <span aria-hidden="true">↗</span></button>)}</div></div>:
      <div className="sb36-messages"><div className="sb36-context-callout"><span aria-hidden="true">✷</span><p><strong>Notes-only preview</strong> — this is your own thread history, not an AI conversation. Nothing is sent to an AI service.</p></div>
        {current.notes.length===0?<div className="sb36-empty-chat"><span aria-hidden="true">✧</span><h2>New thread, new idea.</h2><p>Save your first question or thought below. You can rename or remove this thread anytime.</p></div>:
        current.notes.map(note=><article key={note.id} className="sb36-note"><div className="sb36-note-avatar" aria-hidden="true">✎</div><div className="sb36-note-body"><div><strong>My study note</strong><time dateTime={new Date(note.createdAt).toISOString()}>{dateLabel(note.createdAt)}</time></div><p>{note.text}</p></div></article>)}
      </div>}
    </div>
    <div className="sb36-compose-wrap"><div className="sb36-compose">
      <label htmlFor="sb36-compose-input" className="sb36-visually-hidden">Write a private study note</label>
      <textarea id="sb36-compose-input" ref={input} disabled={!current} value={draft} maxLength={1200} rows={2} onChange={e=>setDraft(e.target.value)} onKeyDown={e=>{if(e.key==='Enter'&&!e.shiftKey){e.preventDefault();save();}}} placeholder={current?'Write a study note or question...':'Create a thread to start writing...'}/>
      <div className="sb36-compose-bottom"><small>{current?'Saved for this browser tab session only':'No account data is changed'}</small><button onClick={save} disabled={!current||!draft.trim()} aria-label="Save note to thread">Save note <span aria-hidden="true">↗</span></button></div>
    </div><p className="sb36-footnote">AI answers are disabled until a secure, age-appropriate backend is explicitly configured. No pretend replies or scores.</p></div>
  </section>
  <aside className={'sb36-context '+(details?'is-open':'')} id="sb36-context" aria-label="Study space information">
     <div className="sb36-context-head"><span className="sb36-overline">YOUR WORKSPACE</span><h2>Learn at your pace <span aria-hidden="true">✧</span></h2><p>Organize real questions before turning them into your next learning adventure.</p></div>
     <div className="sb36-info-card"><span className="sb36-info-icon">◫</span><div><strong>{threads.length} study {threads.length===1?'thread':'threads'}</strong><small>Stored only for this tab session</small></div></div>
     <div className="sb36-info-card"><span className="sb36-info-icon">✎</span><div><strong>{threads.reduce((n,t)=>n+t.notes.length,0)} saved notes</strong><small>Private to this tab in this browser</small></div></div>
     <div className="sb36-how"><h3>How it works</h3><p>1. Create a thread for a subject or question.</p><p>2. Add your notes and questions.</p><p>3. Revisit or delete them any time.</p><a href="/dashboard">← Back to your dashboard</a></div>
     <div className="sb36-disclaimer">No AI provider, cloud sync, teacher visibility, or shared-collaboration connection is enabled here.</div>
  </aside>
 </div>
}
const root=document.getElementById('sb36-react-root');
if(root) createRoot(root).render(<React.StrictMode><StudySpace/></React.StrictMode>);
