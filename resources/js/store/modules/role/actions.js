const actions = {
    fetchRoles(context) {
        context.commit('LOADING_STATUS', true, {
            root: true
        }) // start loading
        axios.get('auth/roles')
            .then(response => {

                context.commit('SET_ROLE', response.data),
                    context.commit('LOADING_STATUS', false, {
                        root: true
                    })
            })
    },
    storeRole(context, data) {
        context.commit('LOADING_STATUS', true, {
            root: true
        }) // start loading
        axios.post('auth/roles', data)
            .then(response => {
                context.commit('STORE_ROLE', response.data)
                context.commit('DIALOG_STATUS', false, {
                    root: true
                }) // close dialog
                context.commit('LOADING_STATUS', false, {
                    root: true
                }) // stop loading

                let payload = [{
                    status: true,
                    message: 'Role successfully added.',
                    timeout: 3000
                }, ]
                context.commit('SNACKBAR_STATUS', payload, {
                    root: true
                }) // show snackbar
            })
            .catch(error => {

                context.commit('LOADING_STATUS', false, {
                    root: true
                }) // stop loading
            })
    },
    updateRole(context, role) {
        context.commit('LOADING_STATUS', true, {
            root: true
        }) // start loading
        axios.put(`auth/roles/${role.id}`,
                role
            )
            .then(response => {
                context.commit('UPDATE_ROLE', response.data)
                context.commit('DIALOG_STATUS', false, {
                    root: true
                }) // close dialog
                context.commit('LOADING_STATUS', false, {
                    root: true
                }) // stop loading

                let payload = [{
                    status: true,
                    message: 'Role successfully updated.',
                    timeout: 3000
                }, ]
                context.commit('SNACKBAR_STATUS', payload, {
                    root: true
                }) // show snackbar
            })
            .catch(error => {

                context.commit('LOADING_STATUS', false, {
                    root: true
                }) // stop loading
            })
    },
    deleteRoles(context, role) {
        context.commit('LOADING_STATUS', true, {
            root: true
        }) // start loading
        axios.delete(`auth/roles/${role.id}`, {
                id: role.id
            })
            .then(response => {
                context.commit('DELETE_ROLES', response.data)
                context.commit('LOADING_STATUS', false, {
                    root: true
                }) // stop loading

                let payload = [{
                    status: true,
                    message: 'Role(s) successfully deleted.',
                    timeout: 3000
                }, ]
                context.commit('SNACKBAR_STATUS', payload, {
                    root: true
                }) // show snackbar
            })
            .catch(error => {
                console.log(error.response)
            })
    }
}

export default actions
