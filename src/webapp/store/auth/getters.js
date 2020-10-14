import { level } from '@/services/role-authorization-levels'

export default {
  isAuthenticated(state) {
    return state.user.email !== ''
  },
  authorizationLevel(state) {
    return level(state.user.role)
  },
  hasRole: (state) => (role) => {
    return state.user.role === role
  },
  hasAuthorizationLevel: (state) => (role) => {
    return level(state.user.role) >= level(role)
  },
}
