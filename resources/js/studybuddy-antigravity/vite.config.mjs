import { defineConfig } from 'vite';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
const here = dirname(fileURLToPath(import.meta.url));
export default defineConfig({
  root: here,
  base: '/assets/studybuddy-w22/',
  esbuild: { jsx: 'automatic' },
  define: { 'process.env.NODE_ENV': JSON.stringify('production') },
  build: {
    target: 'es2020',
    outDir: join(here, 'dist'),
    emptyOutDir: true,
    cssCodeSplit: false,
    sourcemap: false,
    rollupOptions: {
      input: join(here, 'entry.jsx'),
      output: {
        entryFileNames: 'antigravity.js',
        chunkFileNames: 'chunk-[name]-[hash].js',
        assetFileNames: 'antigravity.[ext]'
      }
    }
  },
  clearScreen: false,
});
