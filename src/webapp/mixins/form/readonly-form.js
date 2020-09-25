export default {
  data() {
    return {
      isFormReadOnly: false,
    }
  },
  methods: {
    makeFormReadOnly() {
      this.isFormReadOnly = true
    },

    makeFormWritable() {
      this.isFormReadOnly = false
    },
  },
}
