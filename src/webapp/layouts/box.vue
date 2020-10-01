<template>
  <div>
    <Header />
    <b-container class="mt-3 d-flex align-items-center justify-content-center">
      <b-row align-h="center" align-v="center">
        <div class="d-flex flex-column bg-white p-5">
          <Nuxt />
        </div>
      </b-row>
    </b-container>
  </div>
</template>

<script>
import Header from '@/components/layouts/Header'

export default {
  components: { Header },
}
</script>
