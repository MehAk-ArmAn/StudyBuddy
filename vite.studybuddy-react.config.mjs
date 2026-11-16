import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

// Separate Laravel-managed output: never overwrite an existing public/build manifest.
// Build with: npm run build:studybuddy-react
export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/js/studybuddy-react/entry.jsx'],
      publicDirectory: 'public',
      buildDirectory: 'build/studybuddy-react',
      refresh: false
    }),
    react()
  ],
  define: {
    'process.env.NODE_ENV': JSON.stringify('production')
  },
  build: { sourcemap: false },
  clearScreen: false
});
