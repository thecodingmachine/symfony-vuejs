export default async function (context) {
  const wasAuthenticated = context.app.store.getters['auth/isAuthenticated']
  const email = context.app.store.state.auth.user.email

  await context.app.store.dispatch('auth/me')

  if (!context.app.store.getters['auth/isAuthenticated'] && wasAuthenticated) {
    // The user is no more authenticated (session expired), so we redirect him
    // to the login page.
    context.redirect(
      context.app.localePath({
        name: 'login',
        query: { email, redirect: context.route.fullPath },
      })
    )
  }
}
