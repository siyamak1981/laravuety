<template>
    <v-data-table
        color="error"
        item-key="name"
        class="elevation-1"
        :loading="loading"
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
            itemsPerPageText: 'Role Per Page',
            'show-current-page': true,
            'show-first-last-page': true
        }"
    >
        <template v-slot:top>
            <v-toolbar flat color="dark">
                <v-toolbar-title>My Role</v-toolbar-title>
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
                    <template v-slot:activator="{ on }">
                        <v-btn
                            color="deep-purple accent-4"
                            dark
                            class="mr-4"
                            v-on="on"
                            >New Role</v-btn
                        >
                        <v-btn color="pink" dark class="mr-4" @click="deleteAll"
                            ><v-icon>mdi-delete </v-icon> Delete</v-btn
                        >
                    </template>
                    <v-card>
                        <v-card-title>
                            <span class="headline">{{ formTitle }}</span>
                        </v-card-title>

                        <v-card-text>
                            <v-container>
                                <v-row>
                                    <v-col cols="12" sm="12" md="12">
                                        <v-text-field
                                            v-model="editedItem.name"
                                            color="error"
                                            label="role name"
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
            </v-toolbar>
        </template>
        <template v-slot:item.actions="{ item }">
            <v-icon small class="mr-2" @click="editItem(item)">
                mdi-pencil
            </v-icon>
            <v-icon small @click="deleteItem(item)">
                mdi-delete
            </v-icon>
        </template>
        <template v-slot:no-data>
            <v-btn color="deep-purple accent-4" @click="initialize"
                >Reset</v-btn
            >
        </template>
        <v-snackbar v-model="snackbar">
            Record Deleted Successfully
            <v-btn color="pink" text @click="snackbar = false">
                Close
            </v-btn>
        </v-snackbar>
    </v-data-table>
</template>

<script>
export default {
    data: () => ({
        snackbar: false,
        dialog: false,
        loading: false,
        selected: [],
        text: "",

        headers: [
            {
                text: "id",
                align: "start",
                sortable: false,
                value: "id"
            },
            { text: "Name", value: "name" },
            { text: "Created", value: "created_at" },
            { text: "Updated", value: "updated_at" },
            { text: "Actions", value: "actions", sortable: false }
        ],
        roles: [],
        editedIndex: -1,
        editedItem: {
            name: ""
        },
        defaultItem: {
            name: ""
        }
    }),

    computed: {
        formTitle() {
            return this.editedIndex === -1 ? "New Item" : "Edit Item";
        }
    },

    watch: {
        dialog(val) {
            val || this.close();
        }
    },

    created() {
        this.initialize();
    },

    methods: {
        selectAll(ev) {
            this.selected = [];
            if (ev.length > 0) {
                this.selected = ev.map(val => val.id);
            }
            console.dir(this.selected);
        },
        deleteAll() {
            let decided = confirm(
                "Are you sure you want to delete these item?"
            );
            if (decided) {
                this.loading = true;
                axios
                    .post("auth/roles/delete", { roles: this.selected })
                    .then(response => {
                        this.text = "Record Deleted Successfully";
                        this.selected.map(val => {
                            const index = this.roles.data.indexOf(val);
                            this.roles.data.splice(index);
                        });
                        this.snackbar = true;
                        this.loading = false;
                    })
                    .catch(error => {
                        console.log(error);
                        this.text = "Error Deleting Record";
                        this.snackbar = true;
                    });
            }
        },
        serachIt(val) {
            if (val.length > 2) {
                axios
                    .get(`/auth/roles/${val}`)
                    .then(response => (this.roles = response.data.roles))
                    .catch(error => console.log(error));
            }

            if (val.length <= 0) {
                axios
                    .get(`/auth/roles/${val}`)
                    .then(response => (this.roles = response.data.roles))
                    .catch(error => console.log(error));
            }
        },

        paginate(event) {
            axios
                .get(`/auth/roles?page=${event.page}`, {
                    params: { per_page: event.itemsPerPage }
                })
                .then(response => {
                    this.roles = response.data.roles;
                })
                .catch(error => {
                    if (error.response.status == 401) {
                        localStorage.removeItem("access_token");
                        window.location.href = "/login";
                    }
                });
        },

        initialize() {
            this.roles = [];
        },

        editItem(item) {
            this.editedIndex = this.roles.data.indexOf(item);
            this.editedItem = Object.assign({}, item);
            this.dialog = true;
        },

        deleteItem(item) {
            const index = this.roles.data.indexOf(item);
            let decided = confirm("Are you sure you want to delete this item?");
            if (decided) {
                this.loading = true;
                axios
                    .delete(`/auth/roles/${item.id}`)
                    .then(response => {
                        this.roles.data.splice(index, 1);
                        this.snackbar = true;
                        this.loading = false;
                    })
                    .catch(error => console.log(error));
            }
        },

        close() {
            this.dialog = false;
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem);
                this.editedIndex = -1;
            }, 300);
        },

        save() {
            if (this.editedIndex > -1) {
                const index = this.editedIndex;
                this.loading = true;
                axios
                    .put(`/auth/roles/${this.editedItem.id}`, {
                        name: this.editedItem.name
                    })
                    .then(response => {
                        this.text = "Record Updated Successfully";
                        this.snackbar = true;
                        Object.assign(
                            this.roles.data[index],
                            response.data.role,
                            (this.loading = false)
                        );
                    })

                    .catch(error => {
                        console.log(error);
                        this.text = "Record Updated Successfully";
                        this.snackbar = true;
                    });
            } else {
                this.loading = true;
                axios
                    .post("/auth/roles", { name: this.editedItem.name })
                    .then(response => {
                        this.roles.data.push(response.data.role);
                        this.loading = false;
                        this.close();
                    })
                    .catch(error => {
                        console.log(error);
                    });
            }
            this.close();
        }
    }
};
</script>
