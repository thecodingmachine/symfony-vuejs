import SecurityAPI from '../api/security';

export default {
    namespaced: true,
    state: {
        isLoading: false,
        error: null,
    },
    getters: {
        isLoading (state) {
            return state.isLoading;
        },
        hasError (state) {
            return state.error !== null;
        },
        error (state) {
            return state.error;
        },
    },
    mutations: {
        ['AUTHENTICATING'](state) {
            state.isLoading = true;
            state.error = null;
        },
        ['AUTHENTICATING_SUCCESS'](state) {
            state.isLoading = false;
            state.error = null;
        },
        ['AUTHENTICATING_ERROR'](state, error) {
            state.isLoading = false;
            state.error = error;
        },
    },
    actions: {
        login ({commit}, payload) {
            commit('AUTHENTICATING');
            return SecurityAPI.login(payload.login, payload.password)
                .then(() => commit('AUTHENTICATING_SUCCESS'))
                .catch(err => commit('AUTHENTICATING_ERROR', err));
        },
        isAuthenticated () {
            return SecurityAPI.isAuthenticated();
        },
    },
}