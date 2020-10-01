export default {
  components: {
    layouts: {
      header: {
        logout_link: 'Logout',
        login_link: 'Login',
        create_account_link: 'Create an account',
      },
    },
    pages: {
      products: {
        product_card: {
          from: 'From',
        },
      },
    },
  },
  layouts: {
    error: {
      generic: 'An error occurred',
      not_found: 'Page not found',
      home_page_link: 'Home page',
    },
  },
  pages: {
    root: {
      search: 'Search...',
    },
    login: {
      form: {
        email: {
          label: 'Email',
          placeholder: 'Enter your email',
        },
        password: {
          label: 'Password',
          placeholder: 'Enter your password',
        },
        submit: 'Login',
        submitting: 'Login...',
        forgot_password_link: 'I forgot my password',
      },
    },
    reset_password: {
      login_link: 'Back to login',
      retry_link: 'Retry',
      form: {
        email: {
          label: 'Email',
          placeholder: 'Enter your email',
        },
        submit: 'Send email',
        submitting: 'Sending...',
      },
      success:
        'If the address {email} exists in our system, an email has been delivered with instructions to help you change your password.',
    },
    update_password: {
      form: {
        new_password: {
          label: 'New password',
          placeholder: 'Enter your new password',
        },
        password_confirmation: {
          label: 'Password confirmation',
          placeholder: 'Enter again your new password',
        },
        submit: 'Update',
        submitting: 'Updating...',
      },
      invalid_token: 'Your token has either expired or is invalid.',
      retry_link: 'Retry',
      success: 'Your password has been updated.',
      login_link: 'Login',
    },
  },
}
