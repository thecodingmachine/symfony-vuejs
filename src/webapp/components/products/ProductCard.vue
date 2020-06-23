<template>
  <b-card
    :title="product.name"
    :sub-title="product.price + ' €'"
    img-src="https://picsum.photos/300/200/?random"
    img-alt="Product picture"
    :img-top="true"
    img-width="200"
    img-height="134"
  >
    <b-link href="#" class="card-link">View product details</b-link>
    <template v-slot:footer>
      <small class="text-muted">From {{ product.company.name }}</small>
    </template>
  </b-card>
</template>

<script>
export default {
  name: 'ProductCard',
  props: {
    product: {
      type: Object,
      required: true,
    },
  },
}
</script>
