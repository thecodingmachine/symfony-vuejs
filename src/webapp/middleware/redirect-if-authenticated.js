export default function (context) {
  // If the user is authenticated, we redirect him to the home page.
  if (context.app.store.getters['auth/isAuthenticated']) {
    context.redirect(context.app.localePath({ name: 'index' }))
  }
}
