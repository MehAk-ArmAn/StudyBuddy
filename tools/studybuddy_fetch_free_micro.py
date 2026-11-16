#!/usr/bin/env python3
"""Fetch official React Bits FREE JS-CSS source; do not download any premium code."""
import concurrent.futures,json,pathlib,re,subprocess,sys
root=pathlib.Path(sys.argv[1]).resolve(); root.mkdir(parents=True,exist_ok=True)
NAMES=['Shredder','PaperCrumple','TearTicket','FlipCard','BranchedMenu','FolderFloat','RefineFrame','ThoughtLine','VoicePill','SloshGauge','PromptBar','SwipeToast','SlingButton','BellToggle','CallChip','StatusMark','GlideSelect','SwipeRow','JellyRadio','CometDial','WakeSlider','CodeSlots','DodgeField','LatticeLoader','ScrubField','FuseButton','WarmTooltip','SlideCommit','RubberSegment','PulseHeart','SpringCheck','PeekRating','HoldButton','SquishSwitch']
REQUIRED=['SpringCheck','GlideSelect','SwipeToast','JellyRadio','SquishSwitch','PulseHeart']

def fetch(name):
 try:
  url=f'https://reactbits.dev/r/{name}-JS-CSS.json'
  got=subprocess.run(['curl','--fail','--location','--silent','--show-error','--connect-timeout','7','--max-time','18',url],capture_output=True,text=True,timeout=23)
  if got.returncode:raise ValueError(got.stderr.strip()[:120] or 'download failed')
  data=json.loads(got.stdout)
  if not isinstance(data.get('files'),list):raise ValueError('not registry file')
  folder=root/name;folder.mkdir(parents=True,exist_ok=True)
  filenames=[]
  for file in data['files']:
   content=file.get('content');path=file.get('path','')
   if not isinstance(content,str) or not re.search(r'\.(jsx?|css)$',str(path)):continue
   filename=pathlib.PurePosixPath(path).name
   if not re.fullmatch(r'[A-Za-z0-9_.-]+',filename):raise ValueError('unsafe filename')
   (folder/filename).write_text(content);filenames.append(filename)
  if not (folder/(name+'.jsx')).is_file():
   files=list(folder.glob('*.jsx'))
   if len(files)==1:(folder/(name+'.jsx')).write_text(files[0].read_text())
  if not (folder/(name+'.jsx')).is_file():raise ValueError('JSX missing')
  return {'name':name,'files':filenames,'dependencies':data.get('dependencies',[])}
 except Exception as e:return {'name':name,'error':str(e)[:130]}

with concurrent.futures.ThreadPoolExecutor(max_workers=7) as pool:
 results=list(pool.map(fetch, REQUIRED+[x for x in NAMES if x not in REQUIRED]))
for result in results:
 print(('FREE ' if 'error' not in result else 'SKIP ')+result['name']+('' if 'error' not in result else ': '+result['error']),flush=True)
missed=[r for r in results if r['name'] in REQUIRED and 'error' in r]
(root/'CATALOG_DOWNLOAD_REPORT.json').write_text(json.dumps({'downloaded':[r['name'] for r in results if 'error' not in r], 'skipped':[r for r in results if 'error' in r], 'required':REQUIRED,'required_dependencies':sorted(set(d for r in results if r['name'] in REQUIRED and 'error' not in r for d in r['dependencies'])), 'upstream':'https://reactbits.dev/c/micro'},indent=2)+'\n')
if missed:raise SystemExit('Cannot fetch required official micro sources: '+', '.join(r['name'] for r in missed)+'; STOP before touching Laravel.')
print(f'RESULT: {len(results)-len([r for r in results if "error" in r])} official FREE components downloaded / {len(NAMES)}; all 6 live components available.')
