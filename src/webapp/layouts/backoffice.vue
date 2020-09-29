<template>
  <div>
    <Header />
    <div class="container-fluid">
      <b-container :fluid="true">
        <b-row>
          <b-nav vertical class="col-md-2 d-none d-md-block bg-light sidebar">
            <div class="sidebar-sticky">
              <b-nav-item>Foo</b-nav-item>
              <b-nav-item>Bar</b-nav-item>
              <b-nav-item>Baz</b-nav-item>
            </div>
          </b-nav>
        </b-row>
        <main class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">
          <Nuxt />
        </main>
      </b-container>
    </div>
  </div>
</template>
<script>
import Header from '@/components/layouts/Header'
export default {
  components: { Header },
}
</script>
