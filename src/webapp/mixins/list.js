export function calculateOffset(currentPage, itemsPerPage) {
  return (currentPage - 1) * itemsPerPage
}

export const defaultItemsPerPage = 10

export default {
  data() {
    return {
      // Your filters.
      // Change this object in your component according to your needs.
      filters: {},
      // Number of items per page.
      // You may change this value in your component.
      itemsPerPage: defaultItemsPerPage,
      // items and count should be updated thanks to your query result.
      items: {},
      count: 0,
      // The current page.
      // You should not update it directly but use onPaginate(page) or
      // onSearch() instead.
      currentPage: this.$route.query.page || 1,
      // In your component, you should set this value to true when waiting
      // for your query result and to false when the query is done.
      // You should use it in your template block to display a loader.
      isLoading: false,
      // Debounce should be used as a delay for "real-time" search.
      // You may change this value in your component.
      debounce: 500,
    }
  },
  computed: {
    // Calculate the current offset.
    // You cannot call it in asyncData, as "this" is not available.
    // Call calculateOffset instead.
    offset() {
      return calculateOffset(this.currentPage, this.itemsPerPage)
    },
  },
  methods: {
    // Set the current page and call doSearch().
    // You should call it wherever your user can paginate the list.
    onPaginate(page) {
      this.currentPage = page
      this.doSearch()
    },
    // Reset the current page and call doSearch().
    // You should call it wherever your user is filtering the list.
    onSearch() {
      this.currentPage = 1
      this.doSearch()
    },
    // Update the route with your filters and current page.
    // You should call it in your doSearch() implementation.
    updateRouter() {
      const filters = Object.assign({}, this.filters)
      filters.page = this.currentPage

      this.$router.push({
        query: Object.assign({}, this.$route.query, filters),
      })
    },
    doSearch() {
      // To implement in your component.
    },
  },
}
