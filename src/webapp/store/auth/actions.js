import MeQuery from '@/services/queries/auth/me.query.js'

/* let authenticatedUserPromise = null

function getAuthenticatedUser(app, state) {
  if (state.user.email === '' && authenticatedUserPromise === null) {
    authenticatedUserPromise = app.$graphql.request(MeQuery)
  }

  if (authenticatedUserPromise !== null) {
    return authenticatedUserPromise.then((result) => {
      return result.me
    })
  }

  return Promise.resolve(state.user)
} */

export default {
  async me({ commit }) {
    const result = await this.app.$graphql.request(MeQuery)

    if (result.me) {
      commit('setUser', result.me)
    } else {
      commit('resetUser')
    }
  },
}
