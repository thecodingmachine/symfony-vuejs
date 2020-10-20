export default {
  // nuxtServerInit is called by Nuxt.js before server-rendering every page.
  async nuxtServerInit({ commit }, { app, store }) {
    // On server-side rendering, we must set the 'PHPSESSID' cookie so that
    // server-side request will be authenticated.
    app.$graphql.setHeader(
      'Cookie',
      'PHPSESSID=' + app.$cookies.get('PHPSESSID')
    )

    // Fetch the authenticated user data (if any).
    await store.dispatch('auth/me')
  },
}
