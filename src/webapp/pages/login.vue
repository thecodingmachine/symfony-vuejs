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
            />
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
              autofocus
              required
              trim
            />
          </b-form-group>
          <b-button type="submit" variant="primary">Login</b-button>
        </b-form>
      </div>
    </b-row>
  </b-container>
</template>

<script>
import LoginMutation from '@/services/mutations/auth/login.mutation.gql'

export default {
  name: 'Login',
  layout: 'empty',
  data() {
    return {
      form: {
        email: '',
        password: '',
      },
      blockUserInputs: false,
    }
  },
  methods: {
    async onSubmit() {
      this.blockUserInputs = true

      await this.$apollo.mutate({
        mutation: LoginMutation,
        variables: {
          userName: this.form.email,
          password: this.form.password,
        },
      })

      this.blockUserInputs = false

      this.$router.push('/')
    },
  },
}
</script>
