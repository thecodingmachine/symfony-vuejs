<template>
  <b-container class="h-100 d-flex align-items-center justify-content-center">
    <b-row align-h="center" align-v="center">
      <div class="d-flex flex-column bg-white p-5">
        <b-form @submit.stop.prevent="onSubmit">
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
              required
              trim
              :state="formErrors.email === ''"
            />
            <b-form-invalid-feedback :state="formErrors.email === ''">
              {{ formErrors.email }}
            </b-form-invalid-feedback>
          </b-form-group>
          <b-button type="submit" variant="primary">Send email</b-button>
        </b-form>
      </div>
    </b-row>
  </b-container>
</template>

<script>
import ResetPasswordMutation from '~/services/mutations/auth/reset_password.mutation.gql'
import GraphQLErrorsParser from '~/services/graphql-errors-parser'

export default {
  name: 'ResetPassword',
  layout: 'empty',
  data() {
    return {
      form: {
        email: '',
      },
      formErrors: {
        email: '',
      },
      blockUserInputs: false,
    }
  },
  methods: {
    async onSubmit() {
      this.blockUserInputs = true

      try {
        await this.$apollo.mutate({
          mutation: ResetPasswordMutation,
          variables: {
            email: this.form.email,
          },
        })
      } catch (e) {
        for (const [key, value] of Object.entries(e)) {
          console.log(`${key}: ${value}`)
        }

        GraphQLErrorsParser(e.graphQLErrors, this.formErrors)
      }

      this.blockUserInputs = false
    },
  },
}
</script>
