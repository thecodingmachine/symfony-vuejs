export default function (value, defaultValue) {
  return typeof value === 'undefined' || value === 'undefined'
    ? defaultValue
    : value
}
