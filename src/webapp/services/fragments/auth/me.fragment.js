import { gql } from 'graphql-request'

export default gql`
  fragment MeFragment on User {
    id
    firstName
    lastName
    email
    locale
    role
  }
`
