export default async function ({ app, redirect }) {
  const { me } = await app.store.dispatch('auth/me')
  if (!me) {
    return redirect('/login')
  }
}
