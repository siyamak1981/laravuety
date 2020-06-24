<template>
    <div>
        <v-container grid-list-md text-xs-center>
            <v-layout row wrap>
                <v-flex xs12>
                    <v-toolbar flat>
                        <v-toolbar-title>Users</v-toolbar-title>
                        <v-divider class="mx-4" inset vertical></v-divider>
                        <v-text-field
                            method="POST"
                            name="search"
                            class="mt-8 ml-8"
                            append-icon="mdi-magnify"
                            label="Search..."
                        />
                        <v-spacer></v-spacer>
                        <v-tooltip bottom>
                            <template v-slot:activator="{ on }">
                                <v-btn
                                    text
                                    small
                                    icon
                                    v-on="on"
                                    @click="openDialog"
                                    ><v-icon>mdi-plus</v-icon></v-btn
                                >
                            </template>
                            <span>Add</span>
                        </v-tooltip>
                        <v-tooltip bottom>
                            <template v-slot:activator="{ on }">
                                <v-btn
                                    :disabled="!selected.length > 0"
                                    text
                                    small
                                    icon
                                    v-on="on"
                                    @click="deleteUsers"
                                    ><v-icon>mdi-delete</v-icon></v-btn
                                >
                            </template>
                            <span>Delete</span>
                        </v-tooltip>

                        <v-tooltip bottom>
                            <template v-slot:activator="{ on }">
                                <v-btn text small icon v-on="on"
                                    ><v-icon>mdi-printer</v-icon></v-btn
                                >
                            </template>
                            <span>Print</span>
                        </v-tooltip>

                        <v-tooltip bottom>
                            <template v-slot:activator="{ on }">
                                <v-btn text small icon v-on="on"
                                    ><v-icon
                                        >mdi-arrow-up-down-bold</v-icon
                                    ></v-btn
                                >
                            </template>
                            <span>Export</span>
                        </v-tooltip>

                        <v-tooltip bottom>
                            <template v-slot:activator="{ on }">
                                <v-btn
                                    class="mr-8"
                                    text
                                    small
                                    icon
                                    v-on="on"
                                    @click="refreshData"
                                    ><v-icon>mdi-refresh</v-icon></v-btn
                                >
                            </template>
                            <span>Refresh</span>
                        </v-tooltip>
                    </v-toolbar>

                    <v-card flat>
                        <v-data-table
                            v-model="selected"
                            :headers="headers"
                            :items="users.data"
                            :search="search"
                            :loading="loadingStatus"
                            sort-by="calories"
                            :expand="expand"
                            item-key="id"
                            class="elevation-1"
                        >
                            <template v-slot:items="props">
                                <td
                                    @click="props.expanded = !props.expanded"
                                    style="cursor:pointer;"
                                >
                                    <v-icon
                                        >keyboard_arrow_{{
                                            props.expanded ? "up" : "down"
                                        }}</v-icon
                                    >
                                </td>
                                <td @click.stop="">
                                    <v-checkbox
                                        :disabled="
                                            props.item.id === loggedUser.id
                                        "
                                        v-model="props.selected"
                                        color="primary"
                                        hide-details
                                    ></v-checkbox>
                                </td>
                                <td class="text-md-left">
                                    {{ props.item.full_name }}
                                </td>
                                <td class="text-md-left">
                                    {{ props.item.email }}
                                </td>
                                <td class="text-md-left">
                                    <span
                                        v-for="(role, index) in props.item
                                            .roles"
                                        @click.stop="popRole(props.item)"
                                    >
                                        <v-chip small v-if="index === 0">{{
                                            role.name
                                        }}</v-chip>
                                        <span
                                            style="cursor:pointer;"
                                            v-if="index === 0"
                                            class="grey--text caption"
                                        >
                                            <span
                                                v-if="
                                                    props.item.roles.length -
                                                        1 >
                                                        0
                                                "
                                            >
                                                (+{{
                                                    props.item.roles.length - 1
                                                }}
                                                others)
                                            </span>
                                        </span>
                                    </span>
                                </td>
                                <td class="justify-center">
                                    <v-tooltip bottom>
                                        <template v-slot:activator="{ on }">
                                            <v-icon
                                                small
                                                class="mr-2"
                                                v-on="on"
                                                @click.stop="
                                                    editUser(props.item)
                                                "
                                            >
                                                mdi-pencil
                                            </v-icon>
                                        </template>
                                    </v-tooltip>

                                    <v-tooltip bottom>
                                        <template v-slot:activator="{ on }">
                                            <v-icon
                                                small
                                                class="mr-2"
                                                v-on="on"
                                                @click.stop=""
                                            >
                                                mdi-delete
                                            </v-icon>
                                        </template>
                                    </v-tooltip>
                                </td>
                            </template>
                        </v-data-table>
                    </v-card>
                </v-flex>
            </v-layout>
        </v-container>

        <!-- START OF DIALOG -->
        <v-dialog persistent v-model="dialog" max-width="500px">
            <v-card>
                <v-card-title>
                    <span class="headline">{{ formTitle }}</span>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-container>
                        <v-layout wrap>
                            <v-flex md12>
                                <v-text-field
                                    v-model="editedUser.first"
                                    label="First Name"
                                    required
                                    :disabled="loadingStatus"
                                ></v-text-field>
                            </v-flex>
                            <v-flex md12>
                                <v-text-field
                                    v-model="editedUser.last"
                                    label="Last Name"
                                    required
                                    :disabled="loadingStatus"
                                ></v-text-field>
                            </v-flex>
                            <v-flex md12>
                                <v-text-field
                                    v-model="editedUser.email"
                                    label="Email"
                                    required
                                    :disabled="loadingStatus"
                                ></v-text-field>
                            </v-flex>

                            <v-flex md12>
                                <v-autocomplete
                                    v-model="editedUser.roles"
                                    required
                                    :items="roles"
                                    item-text="name"
                                    item-value="id"
                                    multiple
                                    chips
                                    small-chips
                                    label="Select Roles"
                                >
                                    <template
                                        v-slot:selection="{ item, index }"
                                    >
                                        <v-chip small v-if="index === 0">
                                            <span>{{ item.name }}</span>
                                        </v-chip>
                                        <span
                                            v-if="index === 1"
                                            class="grey--text caption"
                                            >(+{{
                                                editedUser.roles.length - 1
                                            }}
                                            others)</span
                                        >
                                    </template>
                                </v-autocomplete>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" text @click="close"
                        >Cancel</v-btn
                    >
                    <v-btn
                        color="blue darken-1"
                        text
                        @click="save"
                        :loading="loadingStatus"
                        >Save</v-btn
                    >
                </v-card-actions>
            </v-card>
        </v-dialog>
        <!-- END OF DIALOG -->
        <RolesDialog />
    </div>
</template>

<script>
import { mapState } from "vuex";
import { mapActions } from "vuex";
import { mapGetters } from "vuex";

import RolesDialog from "./templates/RolesDialog";

export default {
    components: {
        RolesDialog
    },

    data() {
        return {
            expand: false,
            search: "",
            selected: [],
            headers: [
                { text: "", align: "left", value: "expand", sortable: false },
                {
                    text: "Select",
                    align: "left",
                    value: "select-all",
                    sortable: false
                },
                { text: "Name", align: "left", value: "full_name" },
                { text: "Email", align: "left", value: "email" },
                {
                    text: "Roles",
                    align: "left",
                    value: "roles",
                    sortable: false
                },
                {
                    text: "Actions",
                    align: "center",
                    value: "name",
                    sortable: false
                }
            ],
            // options: {
            //     sortBy: ["name"],
            //     sortDesc: [true]
            // },
            editedIndex: -1,
            editedUser: {
                first: "",
                last: "",
                name: "",
                email: "",
                roles: [{ id: "", name: "" }]
            },
            defaultUser: {
                first: "",
                last: "",
                name: "",
                email: "",
                roles: []
            }
        };
    },

    computed: {
        ...mapGetters({
            users: "user/getUsers",
            roles: "role/getRoles"
        }),

        dialog() {
            return this.$store.state.dialog;
        },

        loadingStatus() {
            return this.$store.state.loading;
        },

        loggedUser() {
            return this.$store.state.currentUser;
        },

        formTitle() {
            return this.editedIndex === -1 ? "New User" : "Edit User";
        }
    },

    watch: {
        dialog(val) {
            val || this.close();
        }
    },

    created() {
        this.$store.dispatch("user/fetchUsers");
        this.$store.dispatch("role/fetchRoles");
    },

    methods: {
        paginate(event) {
            // const sortBy =
            //     this.options.sortBy.length == 0
            //         ? "name"
            //         : this.options.sortBy[0];
            // const orderBy =
            //     this.options.sortDesc.length > 0 && this.options.sortDesc[0]
            //         ? "asc"
            //         : "desc";
            axios
                .get(`/auth/permissions?page=${event.page}`, {
                    params: {
                        per_page: event.itemsPerPage
                        // sort_by: sortBy
                        // order_by: orderBy
                    }
                })
                .then(response => {
                    this.users = response.data.users;
                })
                .catch(error => {
                    if (error.response.status == 401) {
                        localStorage.removeItem("access_token");
                        window.location.href = "/login";
                    }
                });
        },
        refreshData() {
            this.$store.dispatch("user/fetchUsers");
            this.$store.dispatch("role/fetchRoles");
        },

        openDialog() {
            this.editedIndex = -1; // reset default: important
            this.editedUser = Object.assign({}, this.defaultUser); // reset to default
            this.$store.dispatch("triggerDialog", true);
        },

        editUser(user) {
            this.editedIndex = this.users.indexOf(user); // get index: important

            const roleIds = user.roles.map(role => role.id); // get role ids
            this.editedUser = Object.assign(
                {},
                {
                    id: user.id,
                    first: user.first,
                    last: user.last,
                    name: user.name,
                    email: user.email,
                    roles: roleIds
                }
            );
            this.$store.dispatch("triggerDialog", true);
        },

        deleteUsers() {
            const users = this.$data.selected;
            const userIds = users.map(user => {
                return user.id;
            });
            const userNames = users.map(user => {
                const fullName = user.first + " " + user.last;
                return fullName;
            });

            this.$vuetifyConfirmDialog
                .open(
                    "Confirm Delete",
                    "Are you sure to delete user " + userNames + "?",
                    "Cancel",
                    "Confirm Delete"
                )
                .then(state => {
                    if (state)
                        this.$store.dispatch("user/deleteUsers", userIds);
                });
        },

        close() {
            this.editedIndex = -1;

            this.$store.dispatch("triggerDialog", false);
        },

        save() {
            if (this.editedIndex > -1) {
                // update user
                this.$store.dispatch("user/updateUser", this.editedUser);
            } else {
                // store user
                this.$store.dispatch("user/storeUser", this.editedUser);
            }
        },

        popRole(item) {
            let payload = {
                dialog: true,
                item: item
            };
            this.$store.commit("user/SET_USER_ITEM", payload); // open dialog
        }
    }
};
</script>
