<template>
    <v-layout row justify-center>
        <v-dialog width="500" :value="dialog" persistent>
            <v-card>
                <v-card-title>
                    <span class="headline"
                        >{{ roleItem.name }}: Permissions</span
                    >
                </v-card-title>

                <v-card-text>
                    <v-list>
                        <v-chip
                            dark
                            small
                            v-for="perm in roleItem.permissions"
                            :key="perm.id"
                            >{{ perm.name }}</v-chip
                        >
                    </v-list>
                </v-card-text>

                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="blue darken-1"
                        flat="flat"
                        @click="closeDialog(roleItem)"
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
            dialog: "role/getPermDialog",
            roleItem: "role/getRoleItem"
        })
    },

    methods: {
        closeDialog(item) {
            let payload = {
                dialog: false,
                item: item
            };
            this.$store.commit("role/SET_ROLE_ITEM", payload);
        }
    }
};
</script>
