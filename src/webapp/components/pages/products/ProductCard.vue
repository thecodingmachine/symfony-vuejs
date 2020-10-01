<template>
  <b-card
    :title="product.name"
    :sub-title="`${product.price} €`"
    img-src="https://picsum.photos/300/200/?random"
    :img-alt="product.name"
    :img-top="true"
    img-width="200"
    img-height="134"
  >
    <!--    <b-link :to="`/products/${product.id}`" class="card-link">-->
    <!--      View product details-->
    <!--    </b-link>-->
    <template #footer>
      <small class="text-muted"
        >{{ $t('components.pages.products.product_card.from') }}
        {{ product.company.name }}</small
      >
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
