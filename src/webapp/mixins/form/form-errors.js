export default {
  data() {
    return {
      formErrors: {},
    }
  },
  methods: {
    resetFormErrors() {
      this.formErrors = {}
    },

    hydrateFormErrors(e) {
      if (typeof e.response.errors === 'undefined') {
        // The error must be thrown to be handled by our
        // "error" layout.
        throw e
      }

      e.response.errors.forEach((error) => {
        this.formErrors = {
          ...this.formErrors,
          [error.extensions.field]: error.message,
        }
      })
    },

    formState(key) {
      return typeof this.formErrors[key] === 'undefined' ? null : false
    },

    formError(key) {
      return typeof this.formErrors[key] === 'undefined'
        ? ''
        : this.formErrors[key]
    },
  },
}
