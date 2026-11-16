import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import { resolve, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';
const here = dirname(fileURLToPath(import.meta.url));
export default defineConfig({
  plugins: [react()],
  build: {
    outDir: resolve(here, 'dist'), emptyOutDir: true,
    cssCodeSplit: false, target: 'es2020', minify: 'esbuild', sourcemap: false,
    lib: { entry: resolve(here, 'entry.jsx'), formats: ['iife'],
      name: 'StudyBuddyRealReactBitsSloshGauge',
      fileName: () => 'sb-react-bits-proof.js', cssFileName: 'sb-react-bits-proof' }
  }
});
