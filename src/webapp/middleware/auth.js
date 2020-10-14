import { level } from '@/services/role-authorization-levels'

export default async function ({ store, route, redirect, app, error }) {
  // Fetch the authenticated user data (if any).
  await store.dispatch('auth/me')
  const isAuthenticated = store.getters['auth/isAuthenticated']

  // Get allowGuestValues metas for matched routes (with
  // children too).
  const allowGuestValues = route.meta.map((meta) => {
    if (meta.auth && typeof meta.auth.allowGuest !== 'undefined')
      return meta.auth.allowGuest
    return false
  })

  // If any "allowGuest" meta is true,
  // we should not redirect the non-authenticated user.
  const allowGuest = allowGuestValues.includes(true)

  if (!isAuthenticated && !allowGuest) {
    // Redirect the non-authenticated user to the
    // login page (with the information on where to
    // redirect him after login success).
    redirect(
      app.localePath({
        name: 'login',
        query: {
          redirect: route.fullPath,
        },
      })
    )
  }

  // Get "allowAuthenticated" metas for matched routes (with
  // children too).
  const allowAuthenticatedValues = route.meta.map((meta) => {
    if (meta.auth && typeof meta.auth.allowAuthenticated !== 'undefined')
      return meta.auth.allowAuthenticated
    return true
  })

  // If any "allowAuthenticated" meta is false,
  // we should redirect the authenticated user.
  // For instance, when an authenticated user try to
  // navigate to the login page.
  const allowAuthenticated = !allowAuthenticatedValues.includes(false)

  if (isAuthenticated && !allowAuthenticated) {
    // Redirect the authenticated user to the
    // home page.
    redirect(app.localePath({ name: 'index' }))
  }

  // Get authorizations for matched routes (with children routes too).
  const authorizationLevels = route.meta.map((meta) => {
    if (meta.auth && typeof meta.auth.authorizationLevel !== 'undefined')
      return level(meta.auth.authorizationLevel)
    return 0
  })

  // Get highest authorization level.
  const highestAuthorizationLevel = Math.max.apply(null, authorizationLevels)

  if (store.getters['auth/authorizationLevel'] < highestAuthorizationLevel) {
    // The user cannot access this page.
    throw error({
      statusCode: 403,
    })
  }
}
