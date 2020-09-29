import { defaultUserData } from '@/store/auth/state'

export default {
  setUser(state, value) {
    state.user = value
  },
  resetUser(state) {
    state.user = defaultUserData
  },
}
