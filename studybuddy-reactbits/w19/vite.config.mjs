import { defineConfig } from 'vite';
import path from 'node:path';
export default defineConfig({
  base: '/assets/studybuddy-w19/',
  esbuild: { jsx: 'automatic' }, // W20: fixes React is not defined in original component JSX
  resolve: { dedupe: ['react', 'react-dom'] },
  build: {
    outDir: path.resolve(process.cwd(), '../public/assets/studybuddy-w19'),
    emptyOutDir: true,
    cssCodeSplit: false,
    rollupOptions: {
      input: path.resolve(process.cwd(), 'w19/entry.jsx'),
      output: {
        entryFileNames: 'w19-react.js',
        chunkFileNames: 'w19-[name]-[hash].js',
        assetFileNames: 'w19-[name][extname]'
      }
    }
  }
});
