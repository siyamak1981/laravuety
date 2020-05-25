import Vue from 'vue';
import Vuex from "vuex";
import axios from 'axios'
Vue.use(Vuex);


const store = new Vuex.Store({
    state: {
        token: localStorage.getItem("access_token") || null,
        user: {},

    },

    mutations: {
        retrieveToken(state, token) {
            state.token = token
        },
        destroyToken(state) {
            state.token = null
        }
    },



    actions: {
        register(context, data) {
            return new Promise((resolve, reject) => {
                axios.post("/auth/signup/", {
                        name: data.name,
                        email: data.email,
                        password: data.password,
                        password_confirmation: data.password_confirmation,
                    })

                    .then((response) => {
                        resolve(response)
                    }).catch(error => {
                        reject(error)
                    })
            })
        },
        destroyToken(context) {
            axios.defaults.headers.common['Authorization'] = 'Bearer' + context.state.token
            if (context.getters.loggedIn) {
                return new Promise((resolve, reject) => {
                    const token = localStorage.getItem('access_token');
                    axios.get('/auth/logout', {
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': `Bearer ${ token }`
                            },

                        })
                        .then(response => {
                            localStorage.removeItem('access_token')
                            context.commit('destroyToken')
                            resolve(response)

                        }).catch(error => {
                            localStorage.removeItem('access_token')
                            context.commit('destroyToken')
                            reject(error)
                        })
                })
            }
        },

        retrieveToken(context, credentials) {
            return new Promise((resolve, reject) => {
                axios.post('/auth/login/', {
                        email: credentials.email,
                        password: credentials.password,
                    })
                    .then((response) => {
                        let token = response.data.access_token
                        localStorage.setItem("access_token", token)
                        context.commit("retrieveToken", token)
                        resolve(response)
                    })
                    .catch(error => {
                        reject(error)
                    })
            })

        }
    },
    getters: {
        loggedIn(state) {
            return state.token
        }
    }
})

export default store
