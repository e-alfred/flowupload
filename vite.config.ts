import { defineConfig } from 'vite'
import { resolve } from 'path'
import vue from '@vitejs/plugin-vue'
import cssInjectedByJsPlugin from 'vite-plugin-css-injected-by-js'
import { nodeResolve } from '@rollup/plugin-node-resolve';

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [
    nodeResolve(),
    vue(),
    cssInjectedByJsPlugin(),
  ],
  build: {
    outDir: "js",
    lib: {
      // Could also be a dictionary or array of multiple entry points
      entry: resolve(__dirname, 'src/main.ts'),
      name: 'flowupload',
      formats: ['iife'],
      // the proper extensions will be added
      fileName: 'flowupload',
    },
    rollupOptions: {
      external: [
        '@nextcloud/auth',
        '@nextcloud/axios',
        '@nextcloud/files',
        '@nextcloud/logger',
        '@nextcloud/router',
        '@nextcloud/vue',
        '@skjnldsv/sanitize-svg',
        'axios',
        'buffer',
        'crypto-browserify',
        'p-cancelable',
        'p-limit',
        'p-queue',
        'vue-material-design-icons',
      ],
  },
})
