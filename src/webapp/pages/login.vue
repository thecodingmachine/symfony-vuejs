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
    <b-form-invalid-feedback :state="formState('Security')" class="mb-3">
      {{ $t('pages.login.form.error') }}
    </b-form-invalid-feedback>
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
import UpdateLocaleMutation from '@/services/mutations/auth/update_locale.mutation.gql'

export default {
  layout: 'box',
  mixins: [Form],
  middleware: ['redirect-if-authenticated'],
  data() {
    return {
      form: {
        email: this.$route.query.email || '',
        password: '',
      },
      redirect: this.$route.query.redirect || '',
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

        // Update user's locale if different from the
        // web application locale.
        if (result.login.locale !== this.$i18n.locale) {
          await this.$graphql.request(UpdateLocaleMutation, {
            locale: this.$i18n.locale.toUpperCase(),
          })

          result.login.locale = this.$i18n.locale
        }

        this.setUser(result.login)

        if (this.redirect !== '') {
          this.$router.push(this.redirect)
        } else {
          this.$router.push(this.localePath({ name: 'index' }))
        }
      } catch (e) {
        this.hydrateFormErrors(e)
        this.isFormReadOnly = false
      }
    },
  },
}
</script>
