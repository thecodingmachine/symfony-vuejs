<template>
  <ul class="list-inline">
    <li v-for="(file, index) in files" :key="index" class="list-inline-item">
      <b-badge variant="dark">
        {{ file.name }}
      </b-badge>
    </li>
  </ul>
</template>

<script>
export default {
  name: 'FilesList',
  props: {
    files: {
      type: Array,
      required: true,
    },
  },
}
</script>
