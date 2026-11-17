<?php

namespace App\Services;

use App\Models\StudyBuddyMiniAppPlatform;
use FilesystemIterator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use ZipArchive;

/** Produces a buildable offline Electron source project, NEVER mislabels it as a signed installer. */
class StudyBuddyW33DesktopKit
{
    public function create(StudyBuddyMiniAppPlatform $app): string
    {
        if (! class_exists(ZipArchive::class) && ! class_exists(\PharData::class)) {
            throw new RuntimeException('PHP ZipArchive or PharData is required to export desktop projects.');
        }
        $source = StudyBuddyWebAppPublisher::buildDirectory($app->slug);
        if (is_link($source) || ! is_dir($source) || ! is_file($source.'/index.html')) {
            throw new RuntimeException('Upload a valid Flutter Web ZIP before generating the desktop project.');
        }
        $root = realpath($source);
        if (! $root) { throw new RuntimeException('Web build is unavailable.'); }
        $outDir = storage_path('app/studybuddy-w33/desktop-kits');
        File::ensureDirectoryExists($outDir);
        $out = $outDir.'/'.Str::uuid().'.zip';
        $isZipArchive = class_exists(ZipArchive::class);
        if ($isZipArchive) {
            $zip = new ZipArchive();
            if ($zip->open($out, ZipArchive::CREATE | ZipArchive::EXCL) !== true) {
                throw new RuntimeException('Could not create desktop project.');
            }
        } else {
            // Laravel can publish ZIPs through Phar even when PHP's optional zip extension is missing.
            $zip = new \PharData($out, 0, null, \Phar::ZIP);
        }
        try {
            $slug = Str::slug($app->slug);
            $pkg = [
                'name' => 'studybuddy-'.$slug.'-desktop',
                'version' => '1.0.0',
                'private' => true,
                'main' => 'desktop/main.cjs',
                'description' => 'Desktop build project for '.$app->name,
                'scripts' => [
                    'start' => 'electron .',
                    'dist:mac' => 'electron-builder --mac dmg zip',
                    'dist:windows' => 'electron-builder --win nsis zip',
                    'dist:linux' => 'electron-builder --linux AppImage deb',
                ],
                'devDependencies' => ['electron' => '^33.4.0', 'electron-builder' => '^25.1.8'],
                'build' => [
                    'appId' => 'com.pixelcraftslab.studybuddy.'.str_replace('-', '', $slug),
                    'productName' => $app->name,
                    'files' => ['desktop/**/*', 'web/**/*', 'package.json'],
                    'directories' => ['output' => 'dist'],
                    'mac' => ['category' => 'public.app-category.education'],
                    'win' => ['target' => 'nsis'],
                ],
            ];
            $zip->addFromString('package.json', json_encode($pkg, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
            $zip->addFromString('BUILD_ON_MAC.command', '#!/bin/bash'."\n".'set -euo pipefail'."\n".'cd "$(dirname "$0")"'."\n".'npm install'."\n".'npm run dist:mac'."\n".'echo "Check the dist folder for the Mac package."'."\n");
            $zip->addFromString('BUILD_ON_WINDOWS.cmd', "@echo off\r\ncd /d %~dp0\r\ncall npm install\r\nif errorlevel 1 exit /b 1\r\ncall npm run dist:windows\r\necho See dist folder for Windows installers.\r\npause\r\n");
            $zip->addFromString('BUILD_ON_LINUX.sh', "#!/bin/sh\nset -eu\ncd \"$(dirname \"$0\")\"\nnpm install\nnpm run dist:linux\n");
            $zip->addFromString('desktop/main.cjs', <<<'JS'
const { app, BrowserWindow, shell } = require('electron');
const fs = require('node:fs');
const http = require('node:http');
const path = require('node:path');
const { URL } = require('node:url');
const WEB = path.resolve(__dirname, '..', 'web');
const MIME = { '.html':'text/html; charset=utf-8', '.js':'text/javascript; charset=utf-8', '.mjs':'text/javascript; charset=utf-8', '.css':'text/css; charset=utf-8', '.json':'application/json', '.wasm':'application/wasm', '.png':'image/png', '.jpg':'image/jpeg', '.jpeg':'image/jpeg', '.svg':'image/svg+xml', '.webp':'image/webp', '.ico':'image/x-icon', '.woff2':'font/woff2', '.woff':'font/woff', '.ttf':'font/ttf', '.mp3':'audio/mpeg', '.wav':'audio/wav', '.mp4':'video/mp4', '.webm':'video/webm' };
let server;
function launchWindow(origin) {
  const win = new BrowserWindow({width:1200,height:820,minWidth:700,minHeight:500,backgroundColor:'#0b1028',webPreferences:{nodeIntegration:false,contextIsolation:true,sandbox:true,webSecurity:true}});
  win.webContents.setWindowOpenHandler(({url})=>{if(url.startsWith('https://'))shell.openExternal(url);return {action:'deny'};});
  win.webContents.on('will-navigate',(event,url)=>{
    if(!url.startsWith(origin+'/')) {event.preventDefault();if(url.startsWith('https://'))shell.openExternal(url);}
  });
  win.loadURL(origin+'/index.html');
}
app.whenReady().then(()=>{
  server=http.createServer((req,res)=>{
    try {
      const base='http://127.0.0.1:'+server.address().port;
      if(req.headers.host!=='127.0.0.1:'+server.address().port || req.method!=='GET'){res.writeHead(403);res.end();return;}
      const url=new URL(req.url,base);
      let relative=decodeURIComponent(url.pathname).replace(/^\/+/, '');
      if(relative.includes('\0') || relative.split('/').includes('..')){res.writeHead(404);res.end();return;}
      if(!relative) relative='index.html';
      const file=path.resolve(WEB,relative);
      if(!file.startsWith(WEB+path.sep)){res.writeHead(404);res.end();return;}
      fs.stat(file,(err,stat)=>{
        if(err||!stat.isFile()){res.writeHead(404);res.end();return;}
        res.writeHead(200,{'Content-Type':MIME[path.extname(file).toLowerCase()]||'application/octet-stream','X-Content-Type-Options':'nosniff','Cache-Control':'no-store'});
        fs.createReadStream(file).pipe(res);
      });
    } catch {res.writeHead(400);res.end();}
  });
  server.listen(0,'127.0.0.1',()=>{
    const origin='http://127.0.0.1:'+server.address().port;
    launchWindow(origin);
    app.on('activate',()=>{if(BrowserWindow.getAllWindows().length===0)launchWindow(origin);});
  });
});
app.on('window-all-closed',()=>{if(process.platform!=='darwin'){if(server)server.close();app.quit();}});
JS
            );
            $zip->addFromString('README_DESKTOP.md', <<<'MD'
# StudyBuddy Desktop Builder

This ZIP contains your **actual uploaded Flutter Web files** plus a minimal Electron desktop shell. It is a **source/build kit, not a prebuilt or signed native installer**.

1. Install Node.js LTS on the target build computer.
2. Unzip, open Terminal / PowerShell in this folder, run `npm install`.
3. Test locally with `npm start`.
4. Package on each OS: `npm run dist:mac`, `npm run dist:windows`, or `npm run dist:linux`.
5. Inspect the `dist/` folder and test the actual installer. For public distribution, code signing/notarization and security review are recommended/required by platform policy.
6. Back in StudyBuddy Admin → App → Showcase & Desktop, upload the resulting **real installer** for the corresponding OS. Only then does its public download button appear.

Flutter Web is not automatically a native Flutter macOS/Windows/Linux build. This kit wraps the included web build and serves it from a **loopback-only local HTTP server** so Flutter assets and WebAssembly can load reliably. It does not automatically sync learning progress to StudyBuddy; an explicit authenticated integration is required for native/offline sessions. Some plugins require web APIs/remote services and might not work offline; test them. For truly native Flutter desktop features, compile from the original Flutter project with `flutter build macos/windows/linux` on the respective OS.

Do not embed admin credentials, secrets, or personally identifiable learner data in the web ZIP. The shell does not grant Node access to the web renderer.
MD
            );
            $total = 0; $count = 0;
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
            foreach ($iterator as $file) {
                if ($file->isLink()) { throw new RuntimeException('Symbolic links are not permitted inside web builds.'); }
                if (! $file->isFile()) { continue; }
                $rel = str_replace('\\', '/', substr($file->getPathname(), strlen($root) + 1));
                if ($rel === '' || str_contains($rel, '..') || str_contains($rel, "\0")) {
                    throw new RuntimeException('Unexpected path inside web build.');
                }
                if (++$count > 2000 || ($total += $file->getSize()) > 120 * 1024 * 1024) {
                    throw new RuntimeException('Web build is too large to export into a desktop kit.');
                }
                if ($rel === 'index.html') {
                    $html = File::get($file->getPathname());
                    $html = preg_replace('/<base\s+href=["\'][^"\']*["\']\s*\/?\s*>/i', '<base href="./">', $html);
                    if (! preg_match('/<base\s/i', $html)) {
                        $html = preg_replace('/<head\b[^>]*>/i', '$0' . "\n<base href=\"./\">", $html, 1);
                    }
                    $zip->addFromString('web/index.html', $html);
                } else {
                    $zip->addFile($file->getPathname(), 'web/'.$rel);
                }
            }
            if ($isZipArchive && ! $zip->close()) { throw new RuntimeException('Could not finalize desktop ZIP.'); }
            unset($zip);
            return $out;
        } catch (\Throwable $error) {
            if ($isZipArchive) { $zip->close(); }
            unset($zip);
            File::delete($out);
            throw $error;
        }
    }
}
