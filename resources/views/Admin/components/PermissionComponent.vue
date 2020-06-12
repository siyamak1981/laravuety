<template>
    <v-data-table
        color="error"
        item-key="name"
        class="elevation-1"
        :loading="loadingStatus"
        loading-text="Loading... Please wait"
        :headers="headers"
        :items="perms.data"
        :server-items-length="perms.total"
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
        <template v-slot:top>
            <v-toolbar flat color="dark">
                <v-toolbar-title>Permissions</v-toolbar-title>
                <v-divider class="mx-4" inset vertical></v-divider>
                <v-text-field
                    @input="serachIt"
                    method="POST"
                    name="search"
                    class="mt-8 ml-8"
                    append-icon="mdi-magnify"
                    label="Search..."
                />

                <v-spacer></v-spacer>
                <v-dialog v-model="dialog" max-width="500px">
                    <v-card>
                        <v-card-title>
                            <span class="headline">{{ formTitle }}</span>
                        </v-card-title>

                        <v-card-text>
                            <v-container>
                                <v-row>
                                    <v-col cols="12" sm="12" md="12">
                                        <v-text-field
                                            v-model="editedPerm.name"
                                            color="error"
                                            label="permission name"
                                            :rules="[
                                                namePrmission.required,
                                                namePrmission.min
                                            ]"
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
                            </v-container>
                        </v-card-text>

                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="blue darken-1" text @click="close"
                                >Cancel</v-btn
                            >
                            <v-btn color="blue darken-1" text @click="save"
                                >Save</v-btn
                            >
                        </v-card-actions>
                    </v-card>
                </v-dialog>
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn
                            class="mr-4"
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
                            class="mr-4"
                            :disabled="!selected.length > 0"
                            text
                            small
                            icon
                            v-on="on"
                            @click="deletePerms"
                            ><v-icon>mdi-delete</v-icon></v-btn
                        >
                    </template>
                    <span>Delete</span>
                </v-tooltip>

                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn text small class="mr-4" icon v-on="on"
                            ><v-icon>mdi-printer</v-icon></v-btn
                        >
                    </template>
                    <span>Print</span>
                </v-tooltip>

                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn text small icon class="mr-4" v-on="on"
                            ><v-icon>mdi-arrow-up-down-bold</v-icon></v-btn
                        >
                    </template>
                    <span>Export</span>
                </v-tooltip>

                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-btn
                            text
                            small
                            icon
                            v-on="on"
                            @click="refreshData"
                            class="mr-4"
                            ><v-icon>mdi-refresh</v-icon></v-btn
                        >
                    </template>
                    <span>Refresh</span>
                </v-tooltip></v-toolbar
            >
        </template>
        <template v-slot:item.actions="{ item }">
            <v-icon small class="mr-2" @click="editPerm(item)">
                mdi-pencil
            </v-icon>
            <v-icon small @click="deletePerms(item)">
                mdi-delete
            </v-icon>
        </template>
        <template v-slot:no-data>
            <v-btn color="deep-purple accent-4" @click="initialize"
                >Reset</v-btn
            >
        </template>
    </v-data-table>
</template>

<script>
import { mapState } from "vuex";
import { mapActions } from "vuex";
import { mapGetters } from "vuex";

export default {
    data() {
        return {
            search: "",
            selected: [],
            namePrmission: {
                required: v => !!v || "Name is required",
                min: v =>
                    (v && v.length <= 30) ||
                    "Name must be less than 5 characters"
            },
            headers: [
                {
                    text: "Select",
                    align: "left",
                    value: "select-all",
                    sortable: false
                },
                { text: "Name", align: "left", value: "name" },
                { text: "Created", value: "created_at" },
                { text: "Updated", value: "updated_at" },
                { text: "Actions", value: "actions" }
            ],
            editedIndex: -1,
            editedPerm: {
                id: "",
                name: ""
            },
            defaultPerm: {
                name: ""
            }
        };
    },

    computed: {
        ...mapGetters({
            perms: "perm/getPerms",
            permErrors: "perm/getPermErrors"
        }),

        dialog() {
            return this.$store.state.dialog;
        },

        loadingStatus() {
            return this.$store.state.loading;
        },

        formTitle() {
            return this.editedIndex === -1
                ? "New Permission"
                : "Edit Permission";
        }
    },

    watch: {
        dialog(val) {
            val || this.close();
        }
    },

    created() {
        this.initialize();
        this.$store.dispatch("perm/fetchPerms");
    },

    methods: {
        refreshData() {
            this.$store.dispatch("perm/fetchPerms");
        },

        initialize() {
            this.permissions = [];
        },

        serachIt(val) {
            if (val.length > 2) {
                axios
                    .get(`/auth/permissions/${val}`)
                    .then(
                        response =>
                            (this.permissions = response.data.permissions)
                    )
                    .catch(error => console.log(error));
            }

            if (val.length <= 0) {
                axios
                    .get(`/auth/permissions/${val}`)
                    .then(
                        response =>
                            (this.permissions = response.data.permissions)
                    )
                    .catch(error => console.log(error));
            }
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

        openDialog() {
            this.editedIndex = -1; // reset default: important
            this.editedPerm = Object.assign({}, this.defaultPerm); // reset to default
            this.$store.dispatch("triggerDialog", true);
        },

        editPerm(item) {
            this.editedIndex = this.perms.data.indexOf(item); // get index: important
            this.editedPerm = Object.assign({}, item);
            this.$store.dispatch("triggerDialog", true);
        },

        deletePerms(item) {
            // const perms = this.$data.selected;
            // const permIds = perms.map(perm => {
            //     return perm.id;
            // });
            // const permNames = perms.map(perm => {
            //     return perm.name;
            // });
            // this.$vuetifyConfirmDialog
            //     .open(
            //         "Confirm Delete",
            //         "Are you sure to delete permission " + permNames + "?",
            //         "Cancel",
            //         "Confirm Delete"
            //     )
            //     .then(state => {
            //         if (state)
            //             this.$store.dispatch("perm/deletePerms", permIds);
            //     });

            const indxe = this.perms.data.indexOf(item);
            let decided = confirm("Are you sure to delete permission ?");
            const permId = this.editedPerm.id;
            if (decided) {
                this.$store.dispatch("perm/deletePerms", permId);
            }
        },

        close() {
            this.$nextTick(() => {
                this.editedPerm = Object.assign({}, this.defaultPerm);
                this.editedIndex = -1;
                this.$store.dispatch("triggerDialog", false);
            }, 300);
        },

        save() {
            if (this.editedIndex > -1) {
                // update perm
                this.$store.dispatch("perm/updatePerm", this.editedPerm);
            } else {
                // store perm
                this.$store.dispatch("perm/storePerm", this.editedPerm);
            }
        }
    }
};
</script>
