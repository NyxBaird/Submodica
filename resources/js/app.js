import './bootstrap';
import '../css/app.css';
import {createApp} from 'vue';

//router
import router from "./vue/router";

//axios
import axios from "axios";

//Register our main app component
import App from "./vue/App.vue";
let app = createApp(App);

import { defineRule } from 'vee-validate';

defineRule('required', value => {
    if (typeof value == "undefined" || !value || (isNaN(value) && !value.length))
        return 'This field is required';

    return true;
});

defineRule('donationUrl', (value) => {
    // WIP We started saving the mods actual repo url in an update so we can implement that here

    if (!value || !value.length)
        return true;

    //Get rid of any trailing slashes
    if (value.slice(-1) === '/')
        value = value.slice(0, -1);

    let pieces = value.split('/'),
        domainIndex = 0; //by default the domain is the first piece in our url

    //If this is true the url contains http:// or https:// and the domain should be piece 2
    if (typeof pieces[1] !== 'undefined' && pieces[1].length === 0)
        domainIndex = 2;

    let domain = pieces[domainIndex];
    domain = domain.replace("www.", "");

    if (domain !== 'ko-fi.com' && domain !== 'patreon.com')
        return 'Only Patreon and Ko-Fi links are supported at this time!';

    return true;
});

/*
 * Declare any functions that should be globally accessible
 *
 * To fetch "provide()"ed attrs use:
 * import { inject } from 'vue'
 * inject("isMobile")()
 */
app.provide("isMobile", function(){
    return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
});

//add axios as a global
app.config.globalProperties.$axios = axios.create({
    baseURL: "/",
    headers: {
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        "Content-type": "application/json"
    }
});

// const prerender = require('prerender');
// const server = prerender();
// server.start();

//Set our router
app.use(router);
// app.use(require('prerender-node'));
app.mount("#app");

