import SecurityAPI from '../api/security';

export default {
    namespaced: true,
    state: {
        isLoading: false,
        error: null,
        isAuthenticated: false,
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
        isAuthenticated (state) {
            state.isAuthenticated = document.cookie.indexOf('authenticated') !== -1;
            return state.isAuthenticated;
        },
    },
    mutations: {
        ['AUTHENTICATING'](state) {
            state.isLoading = true;
            state.error = null;
            state.isAuthenticated = false;
        },
        ['AUTHENTICATING_SUCCESS'](state) {
            state.isLoading = false;
            state.error = null;
            state.isAuthenticated = true;
        },
        ['AUTHENTICATING_ERROR'](state, error) {
            state.isLoading = false;
            state.error = error;
            state.isAuthenticated = false;
        },
    },
    actions: {
        login ({commit}, payload) {
            commit('AUTHENTICATING');
            return SecurityAPI.login(payload.login, payload.password)
                .then(() => commit('AUTHENTICATING_SUCCESS'))
                .catch(err => commit('AUTHENTICATING_ERROR', err));
        },
    },
}