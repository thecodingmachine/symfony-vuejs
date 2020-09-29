import MeQuery from '@/services/queries/auth/me.query.gql'

export default {
  async me({ commit }) {
    const { data } = await this.app.$graphql.request(MeQuery)
    return data
  },
}
