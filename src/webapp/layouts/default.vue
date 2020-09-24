<template>
  <div>
    <Header />
    <div class="app-content-container">
      <Nuxt />
    </div>
  </div>
</template>

<script>
import Header from '../components/common/Header'
export default {
  components: { Header },
  mounted() {
    console.error(this.$store)
  },
}
</script>
