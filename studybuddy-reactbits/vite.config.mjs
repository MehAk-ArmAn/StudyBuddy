import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import { resolve } from 'node:path';

export default defineConfig({
  plugins: [react()],
  build: {
    outDir: resolve(import.meta.dirname, '../public/assets/studybuddy-reactbits'),
    emptyOutDir: false,
    cssCodeSplit: false,
    lib: {
      entry: resolve(import.meta.dirname, 'src/main.jsx'),
      formats: ['iife'],
      name: 'StudyBuddyReactBits',
      fileName: () => 'reactbits.js',
      cssFileName: 'reactbits',
    },
    target: 'es2020',
    minify: 'esbuild',
    sourcemap: false,
  },
});
