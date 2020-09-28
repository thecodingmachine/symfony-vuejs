<template>
  <ul class="list-unstyled">
    <li v-for="(error, index) in errors" :key="index">
      {{ error }}
    </li>
  </ul>
</template>

<script>
export default {
  name: 'ErrorsList',
  props: {
    errors: {
      type: Array,
      required: true,
    },
  },
}
</script>
