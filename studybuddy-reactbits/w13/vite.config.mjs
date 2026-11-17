import {defineConfig} from 'vite';
import path from 'node:path';
export default defineConfig({
  base:'/assets/studybuddy-w13/',
  resolve:{alias:{'@':path.resolve(process.cwd())},dedupe:['react','react-dom']},
  build:{outDir:path.resolve(process.cwd(),'../public/assets/studybuddy-w13'),emptyOutDir:false,cssCodeSplit:false,
    rollupOptions:{input:path.resolve(process.cwd(),'w13/entry.jsx'),
      output:{entryFileNames:'w13-react.js',assetFileNames:'w13-[name][extname]',chunkFileNames:'w13-[name]-[hash].js'}}}
});
