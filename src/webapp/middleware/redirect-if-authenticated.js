export default function ({ store, redirect, app }) {
  const isAuthenticated = store.getters['auth/isAuthenticated']

  if (isAuthenticated) {
    // Redirect the authenticated user to the
    // home page.
    redirect(app.localePath({ name: 'index' }))
  }
}
