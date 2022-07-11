export function nomorTeleponValidator(nomor_telepon) {
    if (!nomor_telepon) return "Nomor Telepon harus diisi!"
    if (nomor_telepon.length < 9) return 'Nomor Telepon harus lebih dari 9!'
    return ''
  }
  