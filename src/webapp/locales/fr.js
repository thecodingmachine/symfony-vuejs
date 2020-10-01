export default {
  components: {
    layouts: {
      header: {
        logout_link: 'Se déconnecter',
        login_link: 'Se connecter',
        create_account_link: 'Créer un compte',
      },
    },
    pages: {
      products: {
        product_card: {
          from: 'De',
        },
      },
    },
  },
  layouts: {
    error: {
      generic: 'Une erreur est survenue',
      not_found: 'Page non trouvée',
      home_page_link: "Page d'accueil",
    },
  },
  pages: {
    root: {
      search: 'Rechercher...',
    },
    login: {
      form: {
        email: {
          label: 'Email',
          placeholder: 'Entrer votre e-mail',
        },
        password: {
          label: 'Mot de passe',
          placeholder: 'Entrer votre mot de passe',
        },
        submit: 'Se connecter',
        submitting: 'Connexion...',
        forgot_password_link: "J'ai oublié mon mot de passe",
      },
    },
    reset_password: {
      login_link: "Retour à l'écran de connexion",
      retry_link: 'Réessayer',
      form: {
        email: {
          label: 'Email',
          placeholder: 'Entrer votre e-mail',
        },
        submit: "Envoyer l'email",
        submitting: 'Envoie...',
      },
      success:
        "Si l'adresse {email} existe dans notre système, un email a été envoyé avec des instructions pour vous aider à changer votre mot de passe",
    },
    update_password: {
      form: {
        new_password: {
          label: 'Nouveau mot de passe',
          placeholder: 'Entrer votre nouveau mot de passe',
        },
        password_confirmation: {
          label: 'Confirmation du nouveau mot de passe',
          placeholder: 'Entrer une nouvelle fois votre nouveau mot de passe',
        },
        submit: 'Mettre à jour',
        submitting: 'Mise à jour...',
      },
      invalid_token: 'Votre jeton a expiré ou il est invalide.',
      retry_link: 'Réessayer',
      success: 'Votre mot de passe a été mise à jour.',
      login_link: 'Se connecter',
    },
  },
}
