<template>
  <div>
    <!-- eslint-disable-next-line vue/no-v-html -->
    <div v-if="isDev" v-html="error.response.error"></div>
    <b-row v-else class="h-100">
      <b-col
        class="d-flex flex-column justify-content-center align-items-center"
      >
        <h1 v-if="error.statusCode === 404">
          {{ $t('layouts.error.not_found') }}
        </h1>
        <h1 v-else>{{ $t('layouts.error.generic') }}</h1>
        <b-link :to="localePath({ name: 'index' })">{{
          $t('layouts.error.home_page_link')
        }}</b-link>
      </b-col>
      <b-col
        class="d-flex flex-column justify-content-center align-items-center"
      >
        <b-img-lazy src="https://kahoot.com/files/2015/09/ghost-kahoot.gif" />
      </b-col>
    </b-row>
  </div>
</template>

<script>
export default {
  props: {
    error: {
      type: Object,
      required: true,
    },
  },
  layout: 'empty',
  data() {
    return {
      isDev: process.env.NODE_ENV === 'development',
    }
  },
}
</script>
