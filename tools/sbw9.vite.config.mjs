import { defineConfig } from 'vite';
import path from 'node:path';
const project = process.cwd();
export default defineConfig({
  root: project,
  build: {
    outDir: path.join(project,'public/assets/studybuddy-w9'),
    emptyOutDir: false,
    copyPublicDir: false,
    minify: true,
    lib: {
      entry: path.join(project,'resources/js/studybuddy-react/sbw9-pro-entry.jsx'),
      formats: ['es'],
      fileName: () => 'sbw9-official-pro.js',
      cssFileName: 'sbw9-official-pro'
    },
    rollupOptions: {
      output: {
        entryFileNames:'sbw9-official-pro.js',
        assetFileNames: a => a.name?.endsWith('.css') ? 'sbw9-official-pro.css' : 'assets/[name][extname]'
      }
    }
  }
});
