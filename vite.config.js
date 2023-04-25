import {defineConfig, loadEnv} from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
const path = require('path')

export default ({ mode }) => {

    //Grab our env variables
    process.env = {...process.env, ...loadEnv(mode, process.cwd())};

    //This is our localhost we have to set special server vars
    let server = {};
    if (process.env.VITE_MODE === "local")
        server = {
            hmr: {
                host: "localhost", //This should be your VM IP
            },
            host: "localhost", //This should be your VM IP
        };
    else
        server = {
            hmr: {
                host: "147.182.177.36",
            },
            host: "147.182.177.36",
        };

    return defineConfig({
        plugins: [
            laravel({
                input: [
                    'resources/sass/app.scss',
                    'resources/js/app.js',
                ],
                refresh: true,
            }),
            vue()
        ],
        server: server,
        base: './',
        build: {
            rollupOptions: {
                external: [
                    'vue-advanced-cropper/dist/style.css',
                    'vue-advanced-cropper/dist/theme.compact.css',
                ]
            }
        }
    });
}

