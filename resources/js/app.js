require('./bootstrap');
import Vue from "vue";
import App from './App.vue'
import router from "./router"
import store from './store'
import axios from 'axios'
import vuetify from "./plugins/vuetify";
import VueAxios from 'vue-axios'
import 'es6-promise/auto';

axios.defaults.baseURL = 'http://localhost:8000/api'
Vue.use(VueAxios, axios)
Vue.config.productionTip = false


// router auth permission or gate
router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth)) {
        if (!store.getters.loggedIn) {
            window.location.href = "login"
            // next({
            //     name: 'Login',
            // })
        } else {
            next()
        }

    } else if (to.matched.some(record => record.meta.requiresVisitor)) {
        if (store.getters.loggedIn) {
            next({
                name: "AdminComponent"
            })
        } else {
            next()
        }
    } else {
        next()
    }
})
router.beforeEach((to, from, next) => {
    const token = localStorage.getItem("access_token") || null
    window.axios.defaults.headers['Authorization'] = `Bearer ${ token }`
    next();
})


const app = new Vue({
    components: {
        App
    },
    store,
    router,
    vuetify,
    el: '#app',
    render: h => h(App),
}).$mount('#app')
