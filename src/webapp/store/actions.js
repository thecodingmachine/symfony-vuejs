export default {
  // nuxtServerInit is called by Nuxt.js before server-rendering every page.
  nuxtServerInit({ commit }, context) {
    // On server-side rendering, we must set the 'PHPSESSID' cookie so that
    // server-side request will be authenticated.
    context.app.$graphql.setHeader(
      'Cookie',
      'PHPSESSID=' + context.app.$cookies.get('PHPSESSID')
    )
  },
}
