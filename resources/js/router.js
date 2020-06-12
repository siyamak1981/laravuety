import Vue from "vue";
import VueRouter from "vue-router";
import AdminComponent from "../views/Admin/components/AdminComponent.vue"
import Roles from "../views/Admin/components/roles/Roles.vue"
import PermissionComponent from "../views/Admin/components/PermissionComponent.vue"
import UsersComponent from "../views/Admin/components/UsersComponent.vue"
import Contact from "../views/Admin/components/Contact.vue"
import LoginComponent from "../views/Admin/components/LoginComponent.vue"
import RegisterComponent from "../views/Admin/components/RegisterComponent.vue"

Vue.use(VueRouter)


export default new VueRouter({
    routes: [

        {
            name: AdminComponent,
            path: '/admin',
            component: AdminComponent,
            children: [{
                    name: Roles,
                    path: 'roles',
                    component: Roles,

                }, {
                    name: UsersComponent,
                    path: 'users',
                    component: UsersComponent,
                },

                {
                    name: PermissionComponent,
                    path: 'permissions',
                    component: PermissionComponent,
                },
            ],

            meta: {
                requiresAuth: true
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
        {
            name: RegisterComponent,
            path: '/register',
            component: RegisterComponent,

        },
    ],
    mode: "history"
})
