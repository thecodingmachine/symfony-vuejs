const appendError = (array = [], error) => [...array, error]

export default {
  data() {
    return {
      isFormReadOnly: false,
      allFormErrors: {},
    }
  },
  methods: {
    resetFormErrors() {
      this.allFormErrors = {}
    },
    hydrateFormErrors(e) {
      if (
        typeof e === 'undefined' ||
        typeof e.response === 'undefined' ||
        typeof e.response.errors === 'undefined'
      ) {
        // The error must be handled by our "error" layout.
        // We do not call this.$nuxt.error(e) here
        // because for some reasons it does not works.
        throw e
      }

      e.response.errors.forEach((error) => {
        if (typeof error.extensions.field !== 'undefined') {
          this.allFormErrors = {
            ...this.allFormErrors,
            [error.extensions.field]: appendError(
              this.allFormErrors[error.extensions.field],
              error.message
            ),
          }
        } else {
          this.allFormErrors = {
            ...this.allFormErrors,
            [error.extensions.category]: appendError(
              this.allFormErrors[error.extensions.category],
              error.message
            ),
          }
        }
      })
    },
    formState(key) {
      return typeof this.allFormErrors[key] === 'undefined' ? null : false
    },
    hasFormErrors(key) {
      return typeof this.allFormErrors[key] !== 'undefined'
    },
    formErrors(key) {
      return this.allFormErrors[key] || []
    },
  },
}
