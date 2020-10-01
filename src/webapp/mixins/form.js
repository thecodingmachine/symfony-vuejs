function appendError(array, error) {
  if (typeof array === 'undefined') {
    array = []
  }

  array.push(error)

  return array
}

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
      if (typeof e.response.errors === 'undefined') {
        // The error must be thrown to be handled by our
        // "error" layout.
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
              this.allFormErrors[error.extensions.field],
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
