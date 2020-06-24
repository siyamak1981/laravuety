import actions from './actions'

const state = {
    perms: [],

}

const getters = {
    getPerms(state) {
        return state.perms
    },

}

const mutations = {
    SET_PERM(state, perms) {
        state.perms = perms
    },

    STORE_PERM(state, perm) {
        state.perms.unshift(perm)
    },


    UPDATE_PERM(state, payload) {

        state.perms = state.perms.map(perm => {
            if (perm.id === payload.id) {
                return Object.assign({}, perm, payload)
            }
            return perm
        })

    },

    DELETE_PERMS(state, payload) {
        for (var i = payload.length - 1; i >= 0; i--) {
            var index = state.perms.findIndex(perm => perm.id === payload[i].id)
            state.perms.splice(index, 1)
        }

    },


}

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
}
