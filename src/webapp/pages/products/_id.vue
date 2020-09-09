<template>
  <b-overlay :show="$apollo.queries.product.loading">
    <div v-if="!$apollo.queries.product.loading">
      <product-card-details :product="product" />
    </div>
  </b-overlay>
</template>

<script>
import ProductQuery from '@/pages/products/product.query.gql'
import ProductCardDetails from '@/components/route/products/ProductCardDetails'

export default {
  components: { ProductCardDetails },
  data() {
    return {
      product: {},
    }
  },
  apollo: {
    product: {
      prefetch: true,
      query: ProductQuery,
      variables() {
        return {
          id: this.$route.params.id,
        }
      },
    },
  },
}
</script>
