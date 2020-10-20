import { defaultUserData } from '@/store/auth/state'

export default {
  setUser(state, value) {
    state.user = value
  },
  setUserLocale(state, value) {
    state.user.locale = value
  },
  resetUser(state) {
    state.user = defaultUserData
  },
}
