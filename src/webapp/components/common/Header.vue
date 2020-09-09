<template>
  <b-navbar class="app-header" toggleable="lg" type="dark" variant="primary">
    <b-navbar-brand href="#">My products app</b-navbar-brand>

    <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

    <b-collapse id="nav-collapse" is-nav>
      <b-navbar-nav class="ml-auto">
        <b-nav-form>
          <b-form-input
            type="search"
            size="sm"
            class="mr-sm-2"
            placeholder="Search"
            :value="currentSearch"
            @input="setCurrentSearch"
          />
        </b-nav-form>

        <b-nav-item-dropdown right>
          <template #button-content>
            <em>User</em>
          </template>
          <b-dropdown-item href="#">Sign Out</b-dropdown-item>
        </b-nav-item-dropdown>
      </b-navbar-nav>
    </b-collapse>
  </b-navbar>
</template>

<script>
import { mapState, mapActions } from 'vuex'

export default {
  name: 'Header',
  computed: {
    ...mapState('products', ['currentSearch']),
  },
  methods: {
    ...mapActions('products', ['setCurrentSearch']),
  },
}
</script>
