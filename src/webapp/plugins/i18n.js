import UpdateLocaleMutation from '@/services/mutations/auth/update_locale.mutation.gql'

export default function ({ app, store }) {
  // Set the 'Accept-Language' header default value.
  app.$graphql.setHeader('Accept-Language', app.i18n.locale)

  store.subscribe(async (mutation) => {
    if (mutation.type === 'i18n/setLocale') {
      // Update the 'Accept-Language' header so that validation
      // errors are translated to the correct locale.
      app.$graphql.setHeader('Accept-Language', mutation.payload)

      // If the user is authenticated, update his locale.
      if (store.getters['auth/isAuthenticated']) {
        await app.$graphql.request(UpdateLocaleMutation, {
          locale: mutation.payload.toUpperCase(),
        })
      }
    }
  })
}
