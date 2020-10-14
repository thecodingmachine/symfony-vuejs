import { ADMINISTRATOR, CLIENT, MERCHANT } from '@/enums/roles'

export function level(role) {
  switch (role) {
    case ADMINISTRATOR:
      return 3
    case MERCHANT:
      return 2
    case CLIENT:
      return 1
    default:
      return 0
  }
}
