export function passwordValidator(password) {
  if (!password) return "Password harus diisi!"
  if (password.length < 5) return 'Password harus lebih dari 5!'
  return ''
}
