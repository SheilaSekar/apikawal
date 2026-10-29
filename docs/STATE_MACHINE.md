# State machine respons

```mermaid
stateDiagram-v2
    [*] --> baru
    baru --> terverifikasi: evaluator suara / admin
    baru --> ditolak: evaluator suara / admin
    baru --> padam_sendiri: evaluator suara / admin
    baru --> duplikat: pencocokan laporan
    terverifikasi --> diteruskan: petugas/admin + catatan
    terverifikasi --> padam_sendiri: sistem/petugas
    diteruskan --> dalam_penanganan: petugas/admin
    dalam_penanganan --> terkendali: petugas/admin
    terkendali --> selesai: petugas/admin
    state "Admin override dengan alasan wajib" as override
    baru --> override: admin
    terverifikasi --> override: admin
    diteruskan --> override: admin
    dalam_penanganan --> override: admin
    terkendali --> override: admin
    selesai --> override: admin
    ditolak --> override: admin
    padam_sendiri --> override: admin
    duplikat --> override: admin
```

Setiap perubahan status menyimpan `response_logs` dan notifikasi pelapor dalam transaksi DB yang sama. Transisi di luar daftar ditolak. Catatan wajib saat meneruskan laporan dan setiap override admin.
