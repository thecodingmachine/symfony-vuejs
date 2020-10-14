export default {
  common: {
    first_name: 'Prénom',
    last_name: 'Nom',
    email: 'Email',
    locale: 'Langue',
    role: 'Rôle',
  },
  components: {
    layouts: {
      header: {
        administration_link: 'Administration',
        logout_link: 'Se déconnecter',
        login_link: 'Se connecter',
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
      access_forbidden: 'Accès refusé',
      home_page_link: "Page d'accueil",
    },
  },
  mixins: {
    roles: {
      all: 'Tous',
      select: 'Sélectionner un rôle',
      administrator: 'Administateur',
      merchant: 'Marchand',
      client: 'Client',
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
          placeholder: 'Entrer votre email',
        },
        password: {
          label: 'Mot de passe',
          placeholder: 'Entrer votre mot de passe',
        },
        error: "L'email ou le mot de passe fourni est incorrect.",
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
    admin: {
      users: {
        title: 'Liste des utilisateurs',
        form: {
          search: 'Rechercher...',
          role: 'Rôle',
          export: 'Exporter',
        },
      },
    },
  },
}
