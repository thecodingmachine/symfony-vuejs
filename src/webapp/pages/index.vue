<template>
  <b-container>
    <b-row class="mb-4">
      <b-input
        v-model="search"
        type="search"
        debounce="300"
        placeholder="Search a product"
        autofocus
      ></b-input>
    </b-row>
    <b-overlay :show="$apollo.queries.products.loading">
      <div v-if="!$apollo.queries.products.loading">
        <b-row>
          <ProductCardGroup :products="products.items"></ProductCardGroup>
        </b-row>
        <b-row align-h="center">
          <b-pagination
            v-model="currentPage"
            :per-page="productsPerPage"
            :total-rows="products.count"
            pills
          ></b-pagination>
        </b-row>
      </div>
    </b-overlay>
  </b-container>
</template>

<script>
import ProductsQuery from '@/pages/products.query.gql'
import ProductCardGroup from '@/components/products/ProductCardGroup'

export default {
  components: { ProductCardGroup },
  data() {
    return {
      search: '',
      currentPage: 1,
      productsPerPage: 10,
      products: {},
    }
  },
  apollo: {
    products: {
      prefetch: true,
      query: ProductsQuery,
      variables() {
        return {
          search: this.search,
          limit: this.productsPerPage,
          offset: this.offset,
        }
      },
    },
  },
  computed: {
    offset() {
      return (this.currentPage - 1) * this.productsPerPage
    },
  },
}
</script>
