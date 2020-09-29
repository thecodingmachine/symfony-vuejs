<template>
  <div>
    <b-form v-if="!success" @submit.stop.prevent="onSubmit">
      <b-form-group
        id="input-group-email"
        label="Email"
        label-for="input-email"
      >
        <b-form-input
          id="input-email"
          v-model="form.email"
          type="text"
          placeholder="Enter your email"
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
        {{ isFormReadOnly ? 'Sending...' : 'Send email' }}
      </b-button>
      <b-link v-if="!isFormReadOnly" :to="'/login?email=' + form.email"
        >Back to login</b-link
      >
    </b-form>
    <div v-else>
      <p>
        If the email <i>{{ form.email }}</i> exists in our system, it has been
        delivered with instructions to help you change your password.
      </p>

      <b-nav align="center">
        <b-nav-item>
          <b-link :to="'/login?email=' + form.email">Login</b-link>
        </b-nav-item>
        <b-nav-item>
          <b-link @click="resetForm">Retry</b-link>
        </b-nav-item>
      </b-nav>
    </div>
  </div>
</template>

<script>
import Form from '@/mixins/form'
import ResetPasswordMutation from '@/services/mutations/auth/reset_password.mutation.gql'
import ErrorsList from '@/components/forms/ErrorsList'
import EmptyStringIfUndefined from '@/services/empty-string-if-undefined'

export default {
  layout: 'box',
  components: { ErrorsList },
  mixins: [Form],
  data() {
    return {
      form: {
        email: EmptyStringIfUndefined(this.$route.query.email),
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
