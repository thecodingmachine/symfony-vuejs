import defaultIfUndefined from '@/services/default-if-undefined'

export function calculateOffset(currentPage, itemsPerPage) {
  return (currentPage - 1) * itemsPerPage
}

export const defaultItemsPerPage = 10

export default {
  data() {
    return {
      debounce: 500,
      currentPage: defaultIfUndefined(this.$route.query.page, 1),
      itemsPerPage: defaultItemsPerPage,
      items: {},
      count: 0,
      isLoading: false,
    }
  },
  computed: {
    offset() {
      return calculateOffset(this.currentPage, this.itemsPerPage)
    },
  },
  methods: {
    updateRouter(filters) {
      filters.page = this.currentPage

      this.$router.push({
        query: Object.assign({}, this.$route.query, filters),
      })
    },
  },
}
