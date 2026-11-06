const transitions = {
  baru: ['terverifikasi','ditolak','padam_sendiri','duplikat'],
  terverifikasi: ['diteruskan','padam_sendiri'],
  diteruskan: ['dalam_penanganan'],
  dalam_penanganan: ['terkendali'],
  terkendali: ['selesai'],
};
export function transitionStatus(report, to, actorRole, note = '') {
  if (actorRole === 'admin') {
    if (!note.trim()) throw new Error('Alasan override admin wajib diisi.');
    return { ...report, status: to };
  }
  if (actorRole === 'system') {
    if (report.status === 'baru' && ['terverifikasi','ditolak','padam_sendiri','duplikat'].includes(to)) return { ...report, status: to };
    if (report.status === 'terverifikasi' && to === 'padam_sendiri') return { ...report, status: to };
    throw new Error(`Transisi ${report.status} ke ${to} tidak diizinkan.`);
  }
  const officerPadam = actorRole === 'petugas' && report.status === 'terverifikasi' && to === 'padam_sendiri';
  if (!['petugas','admin'].includes(actorRole) || !['terverifikasi','diteruskan','dalam_penanganan','terkendali'].includes(report.status) || (!transitions[report.status]?.includes(to) && !officerPadam)) throw new Error(`Transisi ${report.status} ke ${to} tidak diizinkan.`);
  if (!note.trim()) throw new Error('Catatan perubahan status wajib diisi.');
  return { ...report, status: to };
}
export { transitions };
