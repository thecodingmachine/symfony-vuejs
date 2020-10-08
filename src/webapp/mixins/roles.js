import { ADMINISTRATOR, CLIENT, MERCHANT } from '@/enums/roles'

export default {
  methods: {
    rolesAsSelectOptions(isForSearch = true) {
      return [
        {
          value: null,
          text: isForSearch
            ? this.$t('mixins.roles.all')
            : this.$t('mixins.roles.select'),
        },
        { value: ADMINISTRATOR, text: this.$t('mixins.roles.administrator') },
        { value: MERCHANT, text: this.$t('mixins.roles.merchant') },
        { value: CLIENT, text: this.$t('mixins.roles.client') },
      ]
    },
  },
}
