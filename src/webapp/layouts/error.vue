<template>
  <div>
    <div v-if="isProcessing" class="d-flex justify-content-center">
      <b-spinner label="..."></b-spinner>
    </div>
    <!-- eslint-disable-next-line vue/no-v-html -->
    <div v-else-if="displayErrorHTML" v-html="error.response.error"></div>
    <b-row v-else class="h-100">
      <b-col
        class="d-flex flex-column justify-content-center align-items-center"
      >
        <h1 v-if="error.statusCode === 404">
          {{ $t('layouts.error.not_found') }}
        </h1>
        <h1 v-else-if="error.statusCode === 403">
          {{ $t('layouts.error.access_forbidden') }}
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
import { mapMutations } from 'vuex'

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
      isProcessing: true,
      displayErrorHTML: false,
    }
  },
  mounted() {
    if (this.error.statusCode === 401) {
      // Redirect the non-authenticated user to the
      // login page (with the information on where to
      // redirect him after login success).
      this.resetUser()

      this.$router.push(
        this.localePath({
          name: 'login',
          query: {
            redirect: this.$route.fullPath,
          },
        })
      )

      return
    }

    if (
      typeof this.error.response !== 'undefined' &&
      typeof this.error.response.error !== 'undefined' &&
      process.env.NODE_ENV === 'development'
    ) {
      // The API returned an HTML error response we want to display
      // to the developer (only in development!).
      this.displayErrorHTML = true
      this.isProcessing = false

      return
    }

    // Everything else.
    this.isProcessing = false
  },
  methods: {
    ...mapMutations('auth', ['resetUser']),
  },
}
</script>
