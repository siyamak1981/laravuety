<template>
    <v-layout row justify-center>
        <v-dialog persistent :value="dialog" max-width="500">
            <v-card>
                <v-card-title class="headline"
                    >{{ userItem.first }} {{ userItem.last }}:
                    Roles</v-card-title
                >

                <v-card-text>
                    <v-expansion-panel>
                        <v-expansion-panel-content
                            v-for="(role, index) in userItem.roles"
                            :key="index"
                        >
                            <template v-slot:header>
                                <div>{{ role.name }}</div>
                            </template>
                            <v-card>
                                <v-card-text>
                                    <v-chip
                                        dark
                                        small
                                        v-for="(perm,
                                        index) in role.permissions"
                                        :key="index"
                                        >{{ perm.name }}</v-chip
                                    >
                                </v-card-text>
                            </v-card>
                        </v-expansion-panel-content>
                    </v-expansion-panel>
                </v-card-text>

                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="blue darken-1"
                        flat="flat"
                        @click="closeDialog(userItem)"
                    >
                        OK
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-layout>
</template>

<script>
import { mapState } from "vuex";
import { mapActions } from "vuex";
import { mapGetters } from "vuex";

export default {
    computed: {
        ...mapGetters({
            dialog: "user/openRoleDialog",
            userItem: "user/getUserItem"
        })
    },

    methods: {
        closeDialog(item) {
            let payload = {
                dialog: false,
                item: item
            };
            this.$store.commit("user/SET_USER_ITEM", payload);
        }
    }
};
</script>
