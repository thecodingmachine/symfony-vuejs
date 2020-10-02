import { gql } from 'graphql-request'
import MeFragment from '@/services/fragments/auth/me.fragment'

export default gql`
  query me {
    me {
      ... on User {
        ...MeFragment
      }
    }
  }
  ${MeFragment}
`
