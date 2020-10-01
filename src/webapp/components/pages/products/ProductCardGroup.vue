<template>
  <b-card-group deck>
    <div v-for="product in products" :key="product.id">
      <product-card :product="product" class="mb-4" />
    </div>
  </b-card-group>
</template>

<script>
import ProductCard from '@/components/pages/products/ProductCard.vue'

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
