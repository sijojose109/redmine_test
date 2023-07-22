import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from "@vitejs/plugin-vue";
import Components from 'unplugin-vue-components/vite';
import {BootstrapVueNextResolver} from 'unplugin-vue-components/resolvers';
import Icons from 'unplugin-icons/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(),
        Components({
            resolvers: [BootstrapVueNextResolver()],
        }),
        Icons({
            compiler: 'vue3',
        })
    ],
    resolve:{
        alias:{
            vue:"vue/dist/vue.esm-bundler.js"
        }
    }
});
