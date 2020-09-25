<template>
  <b-form @submit.stop.prevent="onSubmit">
    <b-form-group id="input-group-email" label="Email" label-for="input-email">
      <b-form-input
        id="input-email"
        v-model="form.email"
        type="text"
        placeholder="Enter your email"
        autofocus
        trim
        :disabled="isFormReadOnly"
        :state="formState('email')"
      />
      <b-form-invalid-feedback :state="formState('email')">
        {{ formError('email') }}
      </b-form-invalid-feedback>
    </b-form-group>
    <b-form-group
      id="input-group-password"
      label="Password"
      label-for="input-password"
    >
      <b-form-input
        id="input-password"
        v-model="form.password"
        type="text"
        placeholder="Enter a password"
        trim
        :disabled="isFormReadOnly"
        :state="formState('password')"
      />
      <b-form-invalid-feedback :state="formState('password')">
        {{ formError('password') }}
      </b-form-invalid-feedback>
    </b-form-group>
    <b-button type="submit" variant="primary" :disabled="isFormReadOnly">
      <b-spinner v-show="isFormReadOnly" small type="grow"></b-spinner>
      {{ isFormReadOnly ? 'Login...' : 'Login' }}
    </b-button>
    <b-link
      v-if="!isFormReadOnly"
      :to="'/reset-password?email=' + this.form.email"
      >I forgot my password</b-link
    >
  </b-form>
</template>

<script>
import FormErrors from '@/mixins/form/form-errors'
import ReadOnlyForm from '@/mixins/form/readonly-form'
import LoginMutation from '@/services/mutations/auth/login.mutation.gql'

export default {
  name: 'Login',
  layout: 'box',
  mixins: [FormErrors, ReadOnlyForm],
  data() {
    return {
      form: {
        email: this.$route.query.email,
        password: '',
      },
    }
  },
  methods: {
    async onSubmit() {
      this.resetFormErrors()
      this.makeFormReadOnly()

      try {
        await this.$graphql.request(LoginMutation, {
          userName: this.form.email,
          password: this.form.password,
        })

        this.$router.push('/')
      } catch (e) {
        this.hydrateFormErrors(e)
      } finally {
        this.makeFormWritable()
      }
    },
  },
}
</script>
