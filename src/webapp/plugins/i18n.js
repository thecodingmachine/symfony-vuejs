import UpdateLocaleMutation from '@/services/mutations/auth/update_locale.mutation.gql'

export default function ({ app }) {
  // Set the 'Accept-Language' header default value.
  app.$graphql.setHeader('Accept-Language', app.i18n.locale)

  app.store.subscribe((mutation) => {
    if (mutation.type === 'i18n/setLocale') {
      // Update the 'Accept-Language' header so that validation
      // errors are translated to the correct locale.
      app.$graphql.setHeader('Accept-Language', mutation.payload)
    }
  })

  // onLanguageSwitched called right after a new locale has been set.
  app.i18n.onLanguageSwitched = (oldLocale, newLocale) => {
    // If the user is authenticated, update his locale.
    if (app.store.getters['auth/isAuthenticated'] === true) {
      app.$graphql.request(UpdateLocaleMutation, {
        locale: newLocale.toUpperCase(),
      })
    }
  }
}
