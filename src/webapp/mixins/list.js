export const defaultItemsPerPage = 10

export default {
  data() {
    return {
      debounce: 500,
      currentPage: 1,
      itemsPerPage: defaultItemsPerPage,
      items: {},
      count: 0,
      isLoading: false,
    }
  },
  computed: {
    offset() {
      return (this.currentPage - 1) * this.itemsPerPage
    },
  },
}
