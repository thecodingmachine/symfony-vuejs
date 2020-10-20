import { level } from '@/services/role-authorization-levels'

export default {
  isAuthenticated(state) {
    return state.user.email !== ''
  },
  isGranted: (state) => (role) => {
    return level(state.user.role) >= level(role)
  },
}
