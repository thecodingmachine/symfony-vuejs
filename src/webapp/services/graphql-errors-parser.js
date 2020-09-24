export default function (graphQLErrors, errorsObject) {
  graphQLErrors.forEach((error) => {
    errorsObject[error.extensions.field] = error.message
  })
}
