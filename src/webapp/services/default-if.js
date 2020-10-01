export function defaultIfNilOrEmpty(value, defaultValue) {
  return typeof value === 'undefined' ||
    value === 'undefined' ||
    value === '' ||
    value === null
    ? defaultValue
    : value
}
