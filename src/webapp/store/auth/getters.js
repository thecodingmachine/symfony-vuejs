export default {
  isAuthenticated(state) {
    return state.user.email !== ''
  },
}
