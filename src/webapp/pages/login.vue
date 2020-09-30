<template>
  <b-form @submit.stop.prevent="onSubmit">
    <b-form-group
      id="input-group-email"
      label="Email *"
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
      />
    </b-form-group>
    <b-form-group
      id="input-group-password"
      label="Password *"
      label-for="input-password"
    >
      <b-form-input
        id="input-password"
        v-model="form.password"
        type="password"
        placeholder="Enter a password"
        trim
        required
        :disabled="isFormReadOnly"
      />
    </b-form-group>
    <b-button type="submit" variant="primary" :disabled="isFormReadOnly">
      <b-spinner v-show="isFormReadOnly" small type="grow"></b-spinner>
      {{ isFormReadOnly ? 'Login...' : 'Login' }}
    </b-button>
    <b-link v-if="!isFormReadOnly" :to="'/reset-password?email=' + form.email"
      >I forgot my password</b-link
    >
  </b-form>
</template>

<script>
import Form from '@/mixins/form'
import LoginMutation from '@/services/mutations/auth/login.mutation.js'
import EmptyStringIfUndefined from '@/services/empty-string-if-undefined'
import { mapMutations } from 'vuex'

export default {
  layout: 'box',
  mixins: [Form],
  data() {
    return {
      form: {
        email: EmptyStringIfUndefined(this.$route.query.email),
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
