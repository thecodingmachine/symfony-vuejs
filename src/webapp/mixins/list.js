import { ASC, DESC } from '@/enums/filters/sort-order'

export function calculateOffset(currentPage, itemsPerPage) {
  return (currentPage - 1) * itemsPerPage
}

export const defaultItemsPerPage = 10

const buildFilters = ({ filters, currentPage: page, sortBy, sortOrder }) => {
  return {
    page,
    ...filters,
    ...(sortBy ? { sortBy } : {}),
    ...(sortOrder ? { sortOrder } : {}),
  }
}

export default {
  data() {
    return {
      // Your filters.
      // Change this object in your component according to your needs.
      filters: {},
      // Your fields (if using Bootstrap table).
      // Change this object in your component according to your needs.
      fields: [],
      // Number of items per page.
      // You may change this value in your component.
      itemsPerPage: defaultItemsPerPage,
      // Sort utilities.
      // You may change those values in your component.
      sortBy: this.$route.query.sortBy || null,
      sortOrder: this.$route.query.sortOrder || null,
      // If you are using Bootstrap table, change this value in
      // your component where keys = Boostrap table "sort-by" values and
      // values = API "sort-by" values.
      sortByMap: {},
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
    // Returns the sortBy value used by your Bootstrap table
    // according to this.sortBy.
    boostrapTableSortBy() {
      if (!this.sortBy) {
        return null
      }

      return Object.keys(this.sortByMap).find(
        (key) => this.sortByMap[key] === this.sortBy
      )
    },
    // Returns a boolean value used by your Bootstrap table
    // "sort-desc" property according to this.sortOrder.
    isDesc() {
      return this.sortOrder === DESC
    },
  },
  methods: {
    // Set the sortBy and sortOrder and reset the current page and call doSearch().
    // You should call it wherever your user is sorting the list.
    onSort({ sortBy, sortDesc }) {
      if (sortBy === null) {
        this.sortBy = null
      } else {
        this.sortBy = this.sortByMap[sortBy]
      }

      this.sortOrder = sortDesc ? DESC : ASC
      this.currentPage = 1
      this.doSearch()
    },
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
    // Returns a string representation of the query parameters.
    rawQueryParameters() {
      const filters = buildFilters(this)
      // We add the locale here as we might not be able to set it
      // via headers.
      filters.locale = this.$i18n.locale
      return `?${Object.entries(filters)
        .map((keyValue) => keyValue.join('='))
        .join('&')}`
    },
    // Update the route with your filters and current page.
    // You should call it in your doSearch() implementation.
    updateRouter() {
      this.$router.push({
        query: Object.assign({}, this.$route.query, buildFilters(this)),
      })
    },
    doSearch() {
      // To implement in your component.
    },
  },
}
