<template>
  <b-card
    style="max-width: 200px"
    :title="product.name"
    :sub-title="`${product.price} €`"
    :img-src="pictureURL"
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
import defaultProductImage from '@/assets/images/no-product.png'

export default {
  name: 'ProductCard',
  props: {
    product: {
      type: Object,
      required: true,
    },
  },
  data() {
    if (!this.product.pictures) {
      return {
        pictureURL: defaultProductImage,
      }
    }

    return {
      pictureURL: this.$config.productPictureURL + this.product.pictures[0],
    }
  },
}
</script>
