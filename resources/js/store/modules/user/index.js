import actions from './actions'

const state = {
    users: [],
    userItem: [],
    roleDialog: false,
}

const getters = {
    getUsers(state) {
        return state.users
    },
    getUserErrors(state) {
        return state.errors
    },
    openRoleDialog(state) {
        return state.roleDialog
    },
    getUserItem(state) {
        return state.userItem
    }
}

const mutations = {
    SET_USER(state, users) {
        state.users = users
    },

    STORE_USER(state, user) {
        state.users.unshift(user)
    },

    UPDATE_USER(state, payload) {
        state.users = state.users.map(user => {
            if (user.id === payload.id) {
                return Object.assign({}, user, payload)
            }
            return user
        })
    },

    DELETE_USERS(state, payload) {
        for (var i = payload.length - 1; i >= 0; i--) {
            var index = state.users.findIndex(user => user.id === payload[i].id)
            state.users.splice(index, 1)
        }
    },



    SET_USER_ITEM(state, payload) {
        state.roleDialog = payload.dialog
        state.userItem = payload.item
    }
}

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
}
