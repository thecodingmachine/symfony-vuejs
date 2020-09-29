<template>
  <div>
    <Header />
    <div class="app-content-container">
      <Nuxt />
    </div>
  </div>
</template>

<script>
import Header from '@/components/layouts/Header'

export default {
  components: { Header },
}
</script>
