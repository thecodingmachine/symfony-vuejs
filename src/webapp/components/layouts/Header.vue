<template>
  <b-navbar toggleable="lg" type="dark" variant="primary">
    <b-navbar-brand :to="localePath({ name: 'index' })"
      >Companies and Products</b-navbar-brand
    >

    <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

    <b-collapse id="nav-collapse" is-nav>
      <b-navbar-nav>
        <b-nav-item
          v-if="hasRole(ADMINISTRATOR)"
          :to="localePath({ name: 'admin-users' })"
          :active="$route.path === localePath({ name: 'admin-users' })"
        >
          {{ $t('components.layouts.header.administration_link') }}
        </b-nav-item>
      </b-navbar-nav>

      <b-navbar-nav class="ml-auto">
        <b-nav-item-dropdown v-if="isAuthenticated" right>
          <template #button-content>
            <em>{{ user.firstName + ' ' + user.lastName }}</em>
          </template>
          <b-dropdown-item href="#" @click="logout">{{
            $t('components.layouts.header.logout_link')
          }}</b-dropdown-item>
        </b-nav-item-dropdown>
        <b-nav-item
          v-if="!isAuthenticated"
          right
          :to="localePath({ name: 'login' })"
          :active="$route.path === localePath({ name: 'login' })"
          >{{ $t('components.layouts.header.login_link') }}</b-nav-item
        >
        <b-nav-item-dropdown right>
          <template #button-content>
            {{ currentLocale }}
          </template>
          <b-dropdown-item
            v-for="locale in availableLocales"
            :key="locale.code"
            :active="locale.code === currentLocale"
            :to="switchLocalePath(locale.code)"
          >
            {{ locale.code }}
          </b-dropdown-item>
        </b-nav-item-dropdown>
      </b-navbar-nav>
    </b-collapse>
  </b-navbar>
</template>

<script>
import Roles from '@/mixins/roles'
import { mapState, mapGetters, mapMutations } from 'vuex'
import LogoutMutation from '@/services/mutations/auth/logout.mutation.gql'

export default {
  mixins: [Roles],
  computed: {
    ...mapState('auth', ['user']),
    ...mapGetters('auth', ['isAuthenticated', 'hasRole']),
    availableLocales() {
      return this.$i18n.locales.filter((i) => i.code !== this.$i18n.locale)
    },
    currentLocale() {
      return this.$i18n.locale
    },
  },
  methods: {
    ...mapMutations('auth', ['resetUser']),
    async logout() {
      await this.$graphql.request(LogoutMutation)
      this.resetUser()
      this.$router.push(this.localePath({ name: 'login' }))
    },
  },
}
</script>
