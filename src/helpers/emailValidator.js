export function emailValidator(email) {
  const re = /\S+@\S+\.\S+/
  if (!email) return "Email harus diisi!"
  if (!re.test(email)) return 'Harus menggunakan email!'
  return ''
}
