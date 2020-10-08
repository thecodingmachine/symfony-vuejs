import { ASC, DESC } from '@/enums/filters/sort-order'

export function calculateOffset(currentPage, itemsPerPage) {
  return (currentPage - 1) * itemsPerPage
}

export const defaultItemsPerPage = 10

function buildFilters(component) {
  const filters = Object.assign({}, component.filters)
  filters.page = component.currentPage

  // Both sortBy and sortOrder might not be
  // available according to the type of list.
  if (component.sortBy !== null) {
    filters.sortBy = component.sortBy
  }

  if (component.sortOrder !== null) {
    filters.sortOrder = component.sortOrder
  }

  return filters
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
      let sortBy = null

      if (this.sortBy === null) {
        return sortBy
      }

      Object.entries(this.sortByMap).forEach(
        ([bootstrapTableSortBy, APISortBy]) => {
          if (this.sortBy === APISortBy) {
            sortBy = bootstrapTableSortBy
          }
        }
      )

      return sortBy
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
        Object.entries(this.sortByMap).forEach(
          ([bootstrapTableSortBy, APISortBy]) => {
            if (sortBy === bootstrapTableSortBy) {
              this.sortBy = APISortBy
            }
          }
        )
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

      let parameters = '?'
      let count = 0

      Object.entries(filters).forEach(([filter, value]) => {
        if (value) {
          parameters += count === 0 ? filter : '&' + filter
          parameters += '=' + value
          count++
        }
      })

      return parameters
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
