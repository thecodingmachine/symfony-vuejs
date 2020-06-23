<template>
  <b-card-group deck>
    <div v-for="product in products" :key="product.id">
      <ProductCard :product="product" class="mb-4"></ProductCard>
    </div>
  </b-card-group>
</template>

<script>
import ProductCard from '@/components/products/ProductCard'

export default {
  name: 'ProductCardGroup',
  components: { ProductCard },
  props: {
    products: {
      type: Array,
      required: true,
    },
  },
}
</script>
