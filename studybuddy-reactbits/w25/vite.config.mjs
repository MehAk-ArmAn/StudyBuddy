import { defineConfig } from 'vite';
import path from 'node:path';
export default defineConfig({
  base: '/assets/studybuddy-w25/',
  resolve: { dedupe: ['react', 'react-dom'] },
  esbuild: { jsx: 'automatic', jsxImportSource: 'react' },
  build: {
    outDir: path.resolve(process.cwd(), '../public/assets/studybuddy-w25'),
    emptyOutDir: false,
    cssCodeSplit: false,
    rollupOptions: {
      input: path.resolve(process.cwd(), 'w25/entry.jsx'),
      output: {
        entryFileNames: 'gooey.js',
        chunkFileNames: 'w25-[name]-[hash].js',
        assetFileNames: 'gooey[extname]'
      }
    }
  }
});
