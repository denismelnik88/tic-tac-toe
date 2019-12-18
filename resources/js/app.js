require('./bootstrap');

try {
    window.$ = window.jQuery = require('jquery');
} catch (e) {}

if (window.Vue === undefined) {
    window.Vue = require('vue');
}

Vue.config.debug = true;

require('vue-resource');

Vue.http.headers.common['X-CSRF-TOKEN'] = $('meta[name="csrf-token"]').attr('content');

Vue.http.headers.common['Authorization'] = 'Bearer '+ $('meta[name="api-token"]').attr('content');

Vue.http.headers.common['Accept'] = 'application/json';

import MainMenu from './components/MainMenu.vue';
Vue.component('main-menu',  MainMenu);

import TicTacToe from './components/TicTacToe.vue';
Vue.component('tic-tac-toe',  TicTacToe);

const app = new Vue({
    el: '#app',

    mixins: [],

    data: {
        messages: []
    },

    created() {},

    methods: {}
});



