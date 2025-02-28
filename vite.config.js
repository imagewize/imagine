import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
    },
  },
  css: {
    preprocessorOptions: {
      scss: {
        // Import variables and color module for all components
        additionalData: `
          @use "sass:color";
          @use "@/styles/_variables.scss" as *;
        `,
        // Use modern API (available in Vite 5.4+)
        api: 'modern',
        // Output options
        style: 'compressed'
      },
    },
  },
  build: {
    outDir: 'dist',
    assetsDir: 'assets',
    manifest: true,
    rollupOptions: {
      input: {
        'imagine-editor': path.resolve(__dirname, 'src/main.js'),
        'imagine-admin': path.resolve(__dirname, 'src/admin.js'),
      },
      output: {
        entryFileNames: `assets/[name].js`,
        chunkFileNames: `assets/[name].js`,
        assetFileNames: `assets/[name].[ext]`
      }
    },
    sourcemap: false,
    minify: 'esbuild',
  },
});
