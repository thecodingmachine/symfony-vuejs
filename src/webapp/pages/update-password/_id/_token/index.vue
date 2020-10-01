<template>
  <div>
    <b-form
      v-if="!success && !hasTokenValidationFailed"
      @submit.stop.prevent="onSubmit"
    >
      <b-form-group
        id="input-group-new-password"
        :label="$t('pages.update_password.form.new_password.label') + ' *'"
        label-for="input-new-password"
      >
        <b-form-input
          id="input-new-password"
          v-model="form.newPassword"
          type="password"
          :placeholder="
            $t('pages.update_password.form.new_password.placeholder')
          "
          autofocus
          trim
          required
          :disabled="isFormReadOnly"
          :state="formState('newPassword')"
        />
        <b-form-invalid-feedback :state="formState('newPassword')">
          <ErrorsList :errors="formErrors('newPassword')" />
        </b-form-invalid-feedback>
      </b-form-group>
      <b-form-group
        id="input-group-password-confirmation"
        :label="
          $t('pages.update_password.form.password_confirmation.label') + ' *'
        "
        label-for="input-password-confirmation"
      >
        <b-form-input
          id="input-password-confirmation"
          v-model="form.passwordConfirmation"
          type="password"
          :placeholder="
            $t('pages.update_password.form.password_confirmation.placeholder')
          "
          trim
          required
          :disabled="isFormReadOnly"
          :state="formState('passwordConfirmation')"
        />
        <b-form-invalid-feedback :state="formState('passwordConfirmation')">
          <ErrorsList :errors="formErrors('passwordConfirmation')" />
        </b-form-invalid-feedback>
      </b-form-group>
      <b-button type="submit" variant="primary" :disabled="isFormReadOnly">
        <b-spinner v-show="isFormReadOnly" small type="grow"></b-spinner>
        {{
          isFormReadOnly
            ? $t('pages.update_password.form.submitting')
            : $t('pages.update_password.form.submit')
        }}
      </b-button>
    </b-form>
    <div v-else-if="!success && hasTokenValidationFailed">
      <p>{{ $t('pages.update_password.invalid_token') }}</p>

      <div class="d-flex justify-content-center">
        <b-link :to="localePath('/reset-password')">{{
          $t('pages.update_password.retry_link')
        }}</b-link>
      </div>
    </div>
    <div v-else>
      <p>{{ $t('pages.update_password.success') }}</p>

      <div class="d-flex justify-content-center">
        <b-link :to="localePath('/login?email=' + email)">{{
          $t('pages.update_password.login_link')
        }}</b-link>
      </div>
    </div>
  </div>
</template>

<script>
import Form from '@/mixins/form'
import VerifyResetPasswordTokenMutation from '@/services/mutations/auth/verify_reset_password_token.mutation.gql'
import UpdatePasswordMutation from '@/services/mutations/auth/update_password.mutation.gql'
import ErrorsList from '@/components/forms/ErrorsList'

export default {
  components: { ErrorsList },
  layout: 'box',
  mixins: [Form],
  async asyncData(context) {
    try {
      await context.app.$graphql.request(VerifyResetPasswordTokenMutation, {
        resetPasswordTokenId: context.params.id,
        plainToken: context.params.token,
      })
    } catch (e) {
      return {
        hasAsyncTokenValidationFailed: true,
      }
    }
  },
  data() {
    return {
      form: {
        newPassword: '',
        passwordConfirmation: '',
      },
      success: false,
      hasAsyncTokenValidationFailed: false,
      email: '',
    }
  },
  computed: {
    hasTokenValidationFailed() {
      return (
        this.hasAsyncTokenValidationFailed ||
        this.hasFormErrors('verifyResetPasswordToken')
      )
    },
  },
  methods: {
    async onSubmit() {
      this.resetFormErrors()
      this.isFormReadOnly = true

      try {
        const result = await this.$graphql.request(UpdatePasswordMutation, {
          resetPasswordTokenId: this.$route.params.id,
          plainToken: this.$route.params.token,
          newPassword: this.form.newPassword,
          passwordConfirmation: this.form.passwordConfirmation,
        })

        this.success = true
        this.email = result.updatePassword.email
      } catch (e) {
        this.hydrateFormErrors(e)
        this.isFormReadOnly = false
      }
    },
  },
}
</script>
