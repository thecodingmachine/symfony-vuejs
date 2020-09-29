import { gql } from 'graphql-request'
import MeFragment from '@/services/fragments/auth/me.fragment'

export default gql`
  mutation login($userName: String!, $password: String!) {
    login(userName: $userName, password: $password) {
      ... on User {
        ...MeFragment
      }
    }
  }
  ${MeFragment}
`
