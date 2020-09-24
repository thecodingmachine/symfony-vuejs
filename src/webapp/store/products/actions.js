export default {
  setCurrentSearch({ commit }, value) {
    commit('setCurrentSearch', value)
  },
  clearCurrentSearch({ commit }) {
    commit('setCurrentSearch', null)
  },
}
