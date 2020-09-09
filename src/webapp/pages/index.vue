<template>
  <b-overlay :show="$apollo.queries.products.loading">
    <b-container>
      <b-row align-h="center">
        <product-card-group :products="products.items" />
      </b-row>
      <b-row align-h="center">
        <b-pagination
          v-model="currentPage"
          :per-page="productsPerPage"
          :total-rows="products.count"
          pills
        />
      </b-row>
    </b-container>
  </b-overlay>
</template>

<script>
import { mapState } from 'vuex'
import ProductsQuery from '@/pages/products.query.gql'
import ProductCardGroup from '@/components/route/products/ProductCardGroup'

export default {
  components: { ProductCardGroup },
  data() {
    return {
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
          search: this.currentSearch,
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
    ...mapState('products', ['currentSearch']),
  },
}
</script>
