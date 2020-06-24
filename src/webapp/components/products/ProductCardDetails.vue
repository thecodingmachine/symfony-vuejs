<template>
  <b-card
    :title="product.name"
    :sub-title="product.price + ' €'"
    img-src="https://picsum.photos/300/200/?random"
    :img-alt="product.name"
    :img-top="true"
    img-width="200"
    img-height="134"
  >
    <b-card-text>
      A wonderful product!
    </b-card-text>
    <template v-slot:footer>
      <small class="text-muted">From {{ product.company.name }}</small>
    </template>
  </b-card>
</template>

<script>
export default {
  name: 'ProductCardDetails',
  props: {
    product: {
      type: Object,
      required: true,
    },
  },
}
</script>
