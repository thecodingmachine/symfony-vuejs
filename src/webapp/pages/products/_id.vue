<template>
  <b-overlay :show="$apollo.queries.product.loading">
    <div v-if="!$apollo.queries.product.loading">
      <ProductCardDetails :product="product"></ProductCardDetails>
    </div>
  </b-overlay>
</template>

<script>
import ProductQuery from '@/pages/products/product.query.gql'
import ProductCardDetails from '@/components/products/ProductCardDetails'

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
