export default {
  activeLocaleUppercase(state) {
    return state.activeLocale.toUpperCase()
  },
  localesUppercase(state) {
    const uppercaseLocales = []

    state.locales.forEach((locale) => {
      uppercaseLocales.push(locale.toUpperCase())
    })

    return uppercaseLocales
  },
}
