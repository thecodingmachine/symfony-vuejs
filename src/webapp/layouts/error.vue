<template>
  <b-row class="h-100">
    <b-col class="d-flex flex-column justify-content-center align-items-center">
      <h1 v-if="error.statusCode === 404">
        Page not found
      </h1>
      <h1 v-else>An error occurred</h1>
      <nuxt-link to="/">Home page</nuxt-link>
    </b-col>
    <b-col class="d-flex flex-column justify-content-center align-items-center">
      <b-img-lazy src="https://kahoot.com/files/2015/09/ghost-kahoot.gif" />
    </b-col>
  </b-row>
</template>

<script>
export default {
  props: {
    error: {
      type: Object,
      required: true,
    },
  },
}
</script>
