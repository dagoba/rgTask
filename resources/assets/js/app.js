
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import VModal from 'vue-js-modal'

Vue.use(VModal, { dialog: true });

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

axios.interceptors.response.use(undefined, error => {
    const response = error.response;

    if (response.status === 401) {
        window.location.reload();
    }

    return Promise.reject(error);
});

Vue.component('projects', require('./components/Projects.vue'));

const app = new Vue({
    el: '#app'
});
