import actions from './actions'

const state = {
    roles: [],
    roleItem: [],
    permDialog: false,
    pagination: {},
}

const getters = {
    getRoles(state) {
        return state.roles
    },

    getPermDialog(state) {
        return state.permDialog
    },
    getRoleItem(state) {
        return state.roleItem
    },

}

const mutations = {
    SET_ROLE(state, roles) {
        state.roles = roles
    },

    STORE_ROLE(state, role) {
        state.roles.unshift(role)
    },

    UPDATE_ROLE(state, payload) {
        state.roles = state.roles.map(role => {
            if (role.id === payload.id) {
                return Object.assign({}, role, payload)
            }
            return role
        })
    },

    DELETE_ROLES(state, payload) {
        for (var i = payload.length - 1; i >= 0; i--) {
            var index = state.roles.findIndex(role => role.id === payload[i].id)
            state.roles.splice(index, 1)
        }
    },



    SET_ROLE_ITEM(state, payload) {
        state.permDialog = payload.dialog
        state.roleItem = payload.role
    }
}

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
}
