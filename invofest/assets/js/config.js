export const CONFIG = Object.freeze({
  storageKey: 'apikawal-invofest:v1',
  emergency: 'Kebakaran sedang berlangsung dan mengancam jiwa? Hubungi 112 atau BPBD/pemadam kebakaran setempat. APIKAWAL adalah alat pelaporan dan koordinasi.',
  roles: ['warga', 'relawan', 'petugas', 'admin'],
  roleLabels: { warga: 'Warga', relawan: 'Relawan', petugas: 'Petugas', admin: 'Admin' },
  weights: { warga: 1, relawan: 2, petugas: 3, admin: 3 },
  duplicateRadiusKm: 0.3, duplicateWindowHours: 6, unverifiedScoreCap: 59,
  bounds: { minLat: -3.6, maxLat: -3.0, minLng: 105, maxLng: 105.8 },
  statuses: ['baru', 'terverifikasi', 'diteruskan', 'dalam_penanganan', 'terkendali', 'selesai', 'ditolak', 'padam_sendiri', 'duplikat'],
  weightsDefault: { verification: 30, landtype: 20, hotspot: 20, poi: 15, support: 10, photo: 5 },
});
