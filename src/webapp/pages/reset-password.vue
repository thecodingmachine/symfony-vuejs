<template>
  <div>
    <b-form v-if="!success" @submit.stop.prevent="onSubmit">
      <b-form-group
        id="input-group-email"
        :label="$t('pages.reset_password.form.email.label') + ' *'"
        label-for="input-email"
      >
        <b-form-input
          id="input-email"
          v-model="form.email"
          type="text"
          :placeholder="$t('pages.reset_password.form.email.placeholder')"
          autofocus
          trim
          required
          :disabled="isFormReadOnly"
          :state="formState('email')"
        />
        <b-form-invalid-feedback :state="formState('email')">
          <ErrorsList :errors="formErrors('email')" />
        </b-form-invalid-feedback>
      </b-form-group>
      <b-button type="submit" variant="primary" :disabled="isFormReadOnly">
        <b-spinner v-show="isFormReadOnly" small type="grow"></b-spinner>
        {{
          isFormReadOnly
            ? $t('pages.reset_password.form.submitting')
            : $t('pages.reset_password.form.submit')
        }}
      </b-button>
      <b-link
        v-if="!isFormReadOnly"
        :to="localePath({ name: 'login', query: { email: form.email } })"
        >{{ $t('pages.reset_password.login_link') }}</b-link
      >
    </b-form>
    <div v-else>
      <p>
        {{ $t('pages.reset_password.success', { email: form.email }) }}
      </p>

      <b-nav align="center">
        <b-nav-item>
          <b-link
            :to="localePath({ name: 'login', query: { email: form.email } })"
            >{{ $t('pages.reset_password.login_link') }}</b-link
          >
        </b-nav-item>
        <b-nav-item>
          <b-link @click="resetForm">{{
            $t('pages.reset_password.retry_link')
          }}</b-link>
        </b-nav-item>
      </b-nav>
    </div>
  </div>
</template>

<script>
import Form from '@/mixins/form'
import ResetPasswordMutation from '@/services/mutations/auth/reset_password.mutation.gql'
import ErrorsList from '@/components/forms/ErrorsList'

export default {
  layout: 'box',
  components: { ErrorsList },
  mixins: [Form],
  meta: {
    auth: {
      allowGuest: true,
      allowAuthenticated: false,
    },
  },
  data() {
    return {
      form: {
        email: this.$route.query.email || '',
      },
      success: false,
    }
  },
  methods: {
    async onSubmit() {
      this.resetFormErrors()
      this.isFormReadOnly = true

      try {
        await this.$graphql.request(ResetPasswordMutation, {
          email: this.form.email,
        })

        this.success = true
      } catch (e) {
        this.hydrateFormErrors(e)
      } finally {
        this.isFormReadOnly = false
      }
    },

    resetForm() {
      this.success = false
      this.form.email = ''
    },
  },
}
</script>
