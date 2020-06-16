<template>
    <div>
        <v-container grid-list-md text-xs-center>
            <v-layout row wrap>
                <v-flex xs12>
                    <v-toolbar flat color="dark">
                        <v-toolbar-title>Roles</v-toolbar-title>
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
                                    @click="deleteRoles"
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

                    <template v-slot:no-data>
                        <v-btn color="deep-purple accent-4" @click="initialize"
                            >Reset</v-btn
                        >
                    </template>
                    <v-card flat>
                        <v-data-table
                            color="error"
                            item-key="name"
                            class="elevation-1"
                            :loading="loadingStatus"
                            loading-text="Loading... Please wait"
                            :headers="headers"
                            :items="roles.data"
                            :server-items-length="roles.total"
                            @pagination="paginate"
                            :items-per-page="5"
                            sort-by="calories"
                            show-select
                            @input="selectAll"
                            :footer-props="{
                                itemsPerPageOptions: [5, 10, 15],
                                itemsPerPageText: 'Permission Per Page',
                                'show-current-page': true,
                                'show-first-last-page': true
                            }"
                        >
                            <template v-slot:items="props">
                                <td>
                                    <v-checkbox
                                        v-model="props.selected"
                                        color="primary"
                                        hide-details
                                    ></v-checkbox>
                                </td>
                                <td class="text-md-left">
                                    {{ props.item.name }}
                                </td>
                                <td class="text-md-left">
                                    <span
                                        v-for="(perm, index) in props.item
                                            .permissions"
                                        :key="index"
                                        @click="popPerm(props.item)"
                                    >
                                        <v-chip small v-if="index === 0">{{
                                            perm.name
                                        }}</v-chip>
                                        <span
                                            style="cursor:pointer;"
                                            v-if="index === 1"
                                            class="grey--text caption"
                                            >(+{{
                                                props.item.permissions.length -
                                                    1
                                            }}
                                            others)</span
                                        >
                                    </span>
                                </td>
                                <td class="justify-center">
                                    <v-tooltip bottom>
                                        <template v-slot:activator="{ on }">
                                            <v-icon
                                                small
                                                class="mr-2"
                                                v-on="on"
                                                @click="editRole(props.item)"
                                            >
                                                mdi-pencil
                                            </v-icon>
                                        </template>
                                        <span>Edit</span>
                                    </v-tooltip>
                                </td>
                            </template>
                            <template v-slot:item.actions="{ item }">
                                <v-icon
                                    small
                                    class="mr-2"
                                    @click="editRole(item)"
                                >
                                    mdi-pencil
                                </v-icon>
                                <v-icon small @click="deleteRoles(item)">
                                    mdi-delete
                                </v-icon>
                            </template>
                            <template v-slot:no-data>
                                <v-btn
                                    color="deep-purple accent-4"
                                    @click="initialize"
                                    >Reset</v-btn
                                >
                            </template></v-data-table
                        >
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
                <v-progress-linear
                    v-if="loadingStatus"
                    :indeterminate="loadingStatus"
                    height="2"
                ></v-progress-linear>

                <v-card-text>
                    <v-container>
                        <v-layout wrap>
                            <v-flex md12>
                                <v-text-field
                                    v-model="editedRole.name"
                                    label="Name"
                                    required
                                    :disabled="loadingStatus"
                                ></v-text-field>
                            </v-flex>

                            <v-flex md12>
                                <v-autocomplete
                                    v-model="editedRole.permissions"
                                    required
                                    :items="perms.data"
                                    item-text="name"
                                    item-value="id"
                                    multiple
                                    chips
                                    small-chips
                                    label="Select Permissions"
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
                                                editedRole.permissions.length -
                                                    1
                                            }}
                                            others)</span
                                        >
                                    </template>
                                </v-autocomplete>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-card-text>

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
        <PermissionsDialog />
    </div>
</template>

<script>
import { mapState } from "vuex";
import { mapActions } from "vuex";
import { mapGetters } from "vuex";

import PermissionsDialog from "./templates/PermissionsDialog";

export default {
    components: {
        PermissionsDialog
    },

    data() {
        return {
            search: "",
            selected: [],

            headers: [
                {
                    text: "Select",
                    align: "left",
                    value: "select-all",
                    sortable: false
                },
                { text: "Name", align: "left", value: "name" },
                { text: "Permissions", align: "left", value: "permissions" },
                { text: "Created", value: "created_at" },
                { text: "Updated", value: "updated_at" },
                { text: "Actions", value: "actions" }
            ],

            editedIndex: -1,
            editedRole: {
                name: "",
                permissions: [{ id: "", name: "" }]
            },
            defaultRole: {
                name: "",
                permissions: []
            }
        };
    },

    computed: {
        ...mapGetters({
            roles: "role/getRoles",
            perms: "perm/getPerms",
            roleErrors: "role/getRoleErrors"
        }),

        dialog() {
            return this.$store.state.dialog;
        },

        loadingStatus() {
            return this.$store.state.loading;
        },

        isMobile() {
            return this.$store.state.isMobile;
        },

        formTitle() {
            return this.editedIndex === -1 ? "New Role" : "Edit Role";
        }
    },

    watch: {
        dialog(val) {
            val || this.close();
        }
    },

    created() {
        this.initialize();
        this.$store.dispatch("role/fetchRoles");
        this.$store.dispatch("perm/fetchPerms");
    },

    methods: {
        initialize() {
            this.role = [];
        },
        selectAll(ev) {
            this.selected = [];
            if (ev.length > 0) {
                this.selected = ev.map(val => val.id);
            }
            console.dir(this.selected);
        },
        paginate(event) {
            axios
                .get(`/auth/permissions?page=${event.page}`, {
                    params: { per_page: event.itemsPerPage }
                })
                .then(response => {
                    this.permissions = response.data.permissions;
                })
                .catch(error => {
                    if (error.response.status == 401) {
                        localStorage.removeItem("access_token");
                        window.location.href = "/login";
                    }
                });
        },
        refreshData() {
            this.$store.dispatch("role/fetchRoles");
            this.$store.dispatch("perm/fetchPerms");
        },

        openDialog() {
            this.editedIndex = -1; // reset default: important
            this.editedRole = Object.assign({}, this.defaultRole); // reset to default
            this.$store.dispatch("triggerDialog", true);
        },

        editRole(role) {
            this.editedIndex = this.roles.data.indexOf(role); // get index: important
            this.editedRole = Object.assign({}, role);
            this.$store.dispatch("triggerDialog", true);
        },

        deleteRoles() {
            const roles = this.$data.selected;
            const roleIds = roles.map(role => {
                return role.id;
            });
            const roleNames = roles.map(role => {
                return role.name;
            });

            let decided = confirm("Are you sure to delete permission ?");
            if (decided) {
                this.$store.dispatch("role/deleteRoles", roleIds);
            }
        },

        close() {
            this.editedIndex = -1;
            this.$store.dispatch("triggerDialog", false);
        },

        save() {
            if (this.editedIndex > -1) {
                // update role
                this.$store.dispatch("role/updateRole", this.editedRole);
            } else {
                // store role
                this.$store.dispatch("role/storeRole", this.editedRole);
            }
        },

        popPerm(item) {
            let payload = {
                dialog: true,
                item: item
            };
            this.$store.commit("role/SET_ROLE_ITEM", payload); // open dialog
        }
    }
};
</script>
