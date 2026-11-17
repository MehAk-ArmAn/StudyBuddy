import { defineConfig } from 'vite';
import { fileURLToPath } from 'node:url';
export default defineConfig({
  base: '/assets/studybuddy-w24/',
  esbuild: { jsx: 'automatic' },
  build: {
    sourcemap: false,
    cssCodeSplit: false,
    rollupOptions: {
      input: fileURLToPath(new URL('./app/entry.jsx', import.meta.url)),
      output: { entryFileNames:'studybuddy-gooey.js', assetFileNames:'studybuddy-gooey.[ext]', inlineDynamicImports:true }
    },
    minify:'esbuild'
  }
});
