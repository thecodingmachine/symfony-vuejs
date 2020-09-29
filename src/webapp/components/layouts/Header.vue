<template>
  <b-navbar
    sticky
    class="app-header"
    toggleable="lg"
    type="dark"
    variant="primary"
  >
    <b-navbar-brand href="#">My products app</b-navbar-brand>

    <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

    <b-collapse id="nav-collapse" is-nav>
      <b-navbar-nav class="ml-auto">
        <b-nav-item-dropdown v-if="isAuthenticated" right>
          <template #button-content>
            <em>{{ user.firstName + ' ' + user.lastName }}</em>
          </template>
          <b-dropdown-item href="#" @click="logout">Sign Out</b-dropdown-item>
        </b-nav-item-dropdown>
        <b-nav-item v-if="!isAuthenticated" right to="/login">Login</b-nav-item>
        <b-nav-item v-if="!isAuthenticated" right to="/sign-in"
          >Sign In</b-nav-item
        >
        <b-nav-item-dropdown right>
          <template #button-content>
            {{ activeLocaleUppercase }}
          </template>
          <b-dropdown-item
            v-for="(locale, index) in localesUppercase"
            :key="index"
            :active="locale === activeLocaleUppercase"
          >
            {{ locale }}
          </b-dropdown-item>
        </b-nav-item-dropdown>
      </b-navbar-nav>
    </b-collapse>
  </b-navbar>
</template>

<script>
import { mapState, mapGetters, mapMutations } from 'vuex'
import LogoutMutation from '@/services/mutations/auth/logout.mutation.gql'

export default {
  computed: {
    ...mapState('auth', ['user']),
    ...mapGetters('auth', ['isAuthenticated']),
    ...mapGetters('i18n', ['activeLocaleUppercase', 'localesUppercase']),
  },
  methods: {
    ...mapMutations('auth', ['resetUser']),
    async logout() {
      await this.$graphql.request(LogoutMutation)
      this.resetUser()
      this.$router.push('/login')
    },
  },
}
</script>
