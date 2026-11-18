import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';
import path from 'node:path';

// Standalone editor/pro playground. Never replaces StudyBuddy's legacy React islands.
export default defineConfig({
  plugins: [react(), tailwindcss()],
  resolve: { alias: { '@': path.resolve(__dirname, 'src') } },
  build: { outDir: '../../public/assets/studybuddy-reactbits-pro', emptyOutDir: false },
});
