export function ulangiPasswordValidator(ulangiPassword) {
    if (!ulangiPassword) return "Ulangi Password harus diisi!"
    if (ulangiPassword.length < 5) return 'Ulangi Password harus lebih dari 5!'
    return ''
  }
  