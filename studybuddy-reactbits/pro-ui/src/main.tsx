import React from 'react';
import { createRoot } from 'react-dom/client';
import './index.css';

// Development-only workspace. Paid components are intentionally not imported before
// authentication and CLI installation. Production Laravel pages remain unaffected.
function Workspace() {
  return <main className="mx-auto flex min-h-screen max-w-4xl flex-col justify-center gap-5 px-6 py-12">
    <span className="text-sm font-bold tracking-widest text-violet-500 uppercase">StudyBuddy · Pro UI Studio</span>
    <h1 className="text-4xl font-bold tracking-tight text-foreground sm:text-5xl">Build beautiful. Keep it useful.</h1>
    <p className="max-w-2xl text-base leading-relaxed text-muted-foreground">This isolated React workspace hosts your licensed React Bits Pro source. Use the install script to bring in your components; then integrate them with verified Laravel data, permissions, and accessibility checks.</p>
    <p className="rounded-2xl border border-border bg-card p-5 text-sm text-muted-foreground">This is a local design/development workspace, not a replacement for your production StudyBuddy dashboards.</p>
  </main>;
}
const el=document.getElementById('root');
if (el) createRoot(el).render(<React.StrictMode><Workspace/></React.StrictMode>);
