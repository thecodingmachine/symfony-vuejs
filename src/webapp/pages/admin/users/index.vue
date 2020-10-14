<template>
  <b-container>
    <b-row class="mt-3">
      <h1>{{ $t('pages.admin.users.title') }}</h1>
    </b-row>
    <b-row class="mt-3 mb-3">
      <b-form inline @submit.stop.prevent>
        <label class="sr-only" for="inline-form-input-search">{{
          $t('pages.admin.users.form.search')
        }}</label>
        <b-input
          id="inline-form-input-search"
          v-model="filters.search"
          class="mb-2 mr-sm-2 mb-sm-0"
          type="text"
          :placeholder="$t('pages.admin.users.form.search')"
          autofocus
          trim
          :debounce="debounce"
          @update="onSearch"
        ></b-input>
        <label class="sr-only" for="inline-form-select-role">{{
          $t('pages.admin.users.form.role')
        }}</label>
        <b-form-select
          id="inline-form-select-role"
          v-model="filters.role"
          class="mb-2 mr-sm-2 mb-sm-0"
          :options="rolesAsSelectOptions()"
          @change="onSearch"
        >
        </b-form-select>
        <b-button
          variant="outline-primary"
          :href="$config.apiURL + 'users/xlsx' + rawQueryParameters()"
          >{{ $t('pages.admin.users.form.export') }}</b-button
        >
      </b-form>
    </b-row>
    <b-overlay :show="isLoading" rounded="sm">
      <b-row>
        <b-table
          striped
          hover
          :responsive="true"
          :no-local-sorting="true"
          :sort-by="boostrapTableSortBy"
          :sort-desc="isDesc"
          :items="items"
          :fields="fields"
          @sort-changed="onSort"
        ></b-table>
      </b-row>
      <b-row align-h="center">
        <b-pagination
          v-model="currentPage"
          :per-page="itemsPerPage"
          :total-rows="count"
          pills
          @change="onPaginate"
          @click.native="$scrollToTop"
        />
      </b-row>
    </b-overlay>
  </b-container>
</template>

<script>
import List, { calculateOffset, defaultItemsPerPage } from '@/mixins/list'
import Roles from '@/mixins/roles'
import UsersQuery from '@/services/queries/users/users.query.gql'
import { EMAIL, FIRST_NAME, LAST_NAME } from '@/enums/filters/users-sort-by'
import { ADMINISTRATOR } from '@/enums/roles'

// TODO: i18n for role cell values

export default {
  mixins: [List, Roles],
  // layout: 'backoffice',
  meta: {
    auth: {
      authorizationLevel: ADMINISTRATOR,
    },
  },
  async asyncData(context) {
    try {
      const result = await context.app.$graphql.request(UsersQuery, {
        search: context.route.query.search || '',
        role: context.route.query.role || null,
        sortBy: context.route.query.sortBy || null,
        sortOrder: context.route.query.sortOrder || null,
        limit: defaultItemsPerPage,
        offset: calculateOffset(
          context.route.query.page || 1,
          defaultItemsPerPage
        ),
      })

      return {
        items: result.users.items,
        count: result.users.count,
      }
    } catch (e) {
      context.error(e)
    }
  },
  data() {
    return {
      filters: {
        search: this.$route.query.search || '',
        role: this.$route.query.role || null,
      },
      fields: [
        { key: 'id', label: '#', sortable: false },
        {
          key: 'firstName',
          label: this.$t('common.first_name'),
          sortable: true,
        },
        { key: 'lastName', label: this.$t('common.last_name'), sortable: true },
        { key: 'email', label: this.$t('common.email'), sortable: true },
        { key: 'locale', label: this.$t('common.locale'), sortable: false },
        { key: 'role', label: this.$t('common.role'), sortable: false },
      ],
      sortByMap: {
        firstName: FIRST_NAME,
        lastName: LAST_NAME,
        email: EMAIL,
      },
    }
  },
  methods: {
    async doSearch() {
      this.isLoading = true
      this.updateRouter()

      const result = await this.$graphql.request(UsersQuery, {
        search: this.filters.search,
        role: this.filters.role,
        sortBy: this.sortBy,
        sortOrder: this.sortOrder,
        limit: this.itemsPerPage,
        offset: this.offset,
      })

      this.items = result.users.items
      this.count = result.users.count
      this.isLoading = false
    },
  },
}
</script>
