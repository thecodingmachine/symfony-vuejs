import Vue from 'vue';
import App from './App';
import router from './router';
import store from './store';

new Vue({
    template: '<App/>',
    components: { App },
    router,
    store,
}).$mount('#app');