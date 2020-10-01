<template>
  <b-container>
    <b-row align-h="center" class="mt-3 mb-3">
      <b-input
        id="inline-form-input-search"
        v-model="search"
        type="text"
        :placeholder="$t('pages.root.search')"
        autofocus
        trim
        :debounce="debounce"
        @update="onSearch"
      ></b-input>
    </b-row>

    <b-overlay :show="isLoading" rounded="sm">
      <b-row align-h="center">
        <product-card-group :products="items" />
      </b-row>
      <b-row align-h="center">
        <b-pagination
          v-model="currentPage"
          :per-page="itemsPerPage"
          :total-rows="count"
          pills
          @input="onSearch"
          @click.native="$scrollToTop"
        />
      </b-row>
    </b-overlay>
  </b-container>
</template>

<script>
import List, { calculateOffset, defaultItemsPerPage } from '@/mixins/list'
import defaultIfUndefined from '@/services/default-if-undefined'
import ProductsQuery from '@/services/queries/products/products.query.gql'
import ProductCardGroup from '@/components/pages/products/ProductCardGroup'

export default {
  components: { ProductCardGroup },
  mixins: [List],
  async asyncData(context) {
    try {
      const result = await context.app.$graphql.request(ProductsQuery, {
        search: defaultIfUndefined(context.route.query.search, ''),
        limit: defaultItemsPerPage,
        offset: calculateOffset(
          defaultIfUndefined(context.route.query.page, 1),
          defaultItemsPerPage
        ),
      })

      return {
        items: result.products.items,
        count: result.products.count,
      }
    } catch (e) {
      context.error(e)
    }
  },
  data() {
    return {
      search: defaultIfUndefined(this.$route.query.search, ''),
    }
  },
  methods: {
    async onSearch() {
      this.isLoading = true

      this.updateRouter({
        search: this.search,
      })

      const result = await this.$graphql.request(ProductsQuery, {
        search: this.search,
        limit: this.itemsPerPage,
        offset: this.offset,
      })

      this.items = result.products.items
      this.count = result.products.count
      this.isLoading = false
    },
  },
}
</script>
