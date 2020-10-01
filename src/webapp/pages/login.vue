<template>
  <b-form @submit.stop.prevent="onSubmit">
    <b-form-group
      id="input-group-email"
      :label="$t('pages.login.form.email.label') + ' *'"
      label-for="input-email"
    >
      <b-form-input
        id="input-email"
        v-model="form.email"
        type="text"
        :placeholder="$t('pages.login.form.email.placeholder')"
        autofocus
        trim
        required
        :disabled="isFormReadOnly"
      />
    </b-form-group>
    <b-form-group
      id="input-group-password"
      :label="$t('pages.login.form.password.label') + ' *'"
      label-for="input-password"
    >
      <b-form-input
        id="input-password"
        v-model="form.password"
        type="password"
        :placeholder="$t('pages.login.form.password.placeholder')"
        trim
        required
        :disabled="isFormReadOnly"
      />
    </b-form-group>
    <b-button type="submit" variant="primary" :disabled="isFormReadOnly">
      <b-spinner v-show="isFormReadOnly" small type="grow"></b-spinner>
      {{
        isFormReadOnly
          ? $t('pages.login.form.submitting')
          : $t('pages.login.form.submit')
      }}
    </b-button>
    <b-link
      v-if="!isFormReadOnly"
      :to="localePath({ name: 'reset-password', query: { email: form.email } })"
      >{{ $t('pages.login.form.forgot_password_link') }}</b-link
    >
  </b-form>
</template>

<script>
import Form from '@/mixins/form'
import LoginMutation from '@/services/mutations/auth/login.mutation.js'
import { mapMutations } from 'vuex'

export default {
  layout: 'box',
  mixins: [Form],
  data() {
    return {
      form: {
        email: this.$route.query.email || '',
        password: '',
      },
    }
  },
  methods: {
    ...mapMutations('auth', ['setUser']),
    async onSubmit() {
      this.resetFormErrors()
      this.isFormReadOnly = true

      try {
        const result = await this.$graphql.request(LoginMutation, {
          userName: this.form.email,
          password: this.form.password,
        })

        this.setUser(result.login)
        this.$router.push('/')
      } catch (e) {
        this.hydrateFormErrors(e)
        this.isFormReadOnly = false
      }
    },
  },
}
</script>
