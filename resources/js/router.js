import Vue from "vue";
import VueRouter from "vue-router";
import AdminComponent from "../views/Admin/components/AdminComponent.vue"
import RolesComponent from "../views/Admin/components/RolesComponent.vue"
import UsersComponent from "../views/Admin/components/UsersComponent.vue"
import Contact from "../views/Admin/components/Contact.vue"
import LoginComponent from "../views/Admin/components/LoginComponent.vue"

Vue.use(VueRouter)


export default new VueRouter({
    routes: [

        {
            name: AdminComponent,
            path: '/admin',
            component: AdminComponent,
            children: [{
                name: RolesComponent,
                path: 'roles',
                component: RolesComponent,

            }, {
                name: UsersComponent,
                path: 'users',
                component: UsersComponent,
            }, ],

            meta: {
                // requiresAuth: true
            },

            beforeEnter: (to, from, next) => {
                axios.get('auth/varify')
                    .then(response => next())
                    .catch(error => next('/login'))
            }
        },
        {
            name: Contact,
            path: '/contact',
            component: Contact
        },
        {
            name: LoginComponent,
            path: '/login',
            component: LoginComponent,
            meta: {
                requiresVisitor: true
            }
        },
    ],
    mode: "history"
})
