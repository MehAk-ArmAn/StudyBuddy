import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import path from 'node:path';

const root=process.env.STUDYBUDDY_ROOT;
const out=process.env.STUDYBUDDY_W29_OUTPUT;
if(!root || !out) throw new Error('Installer must provide STUDYBUDDY_ROOT and STUDYBUDDY_W29_OUTPUT');
export default defineConfig({
  root,
  // Keep Laravel public assets OUT of the temporary React Bits build.
  // Without this, Vite copies favicon.ico, index.php, SW etc. into the stage.
  publicDir: false,
  plugins:[react()],
  define:{'process.env.NODE_ENV':JSON.stringify('production')},
  build:{
    outDir:out,
    emptyOutDir:true,
    minify:'esbuild',
    sourcemap:false,
    cssCodeSplit:false,
    rollupOptions:{
      input:path.join(root,'studybuddy-reactbits/w29/entry.jsx'),
      output:{entryFileNames:'galaxy.js',chunkFileNames:'chunk-[hash].js',assetFileNames:'galaxy[extname]'}
    }
  }
});
