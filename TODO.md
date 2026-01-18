# TODO Checklist

## Auth & Rate Limit
- [x] Throttle login `/auth/login`
- [x] Throttle scan `/attendance/scan`
- [x] Device binding siswa: `POST /me/devices`, `DELETE /me/devices/{device}`, scan wajib `device_id`

## Jadwal & Data Master
- [x] Filter jadwal `GET /schedules?date=&class_id=`
- [x] Import bulk siswa/guru (`POST /students/import`, `POST /teachers/import`)
- [x] Opsional master: `school-years`, `semesters`, `rooms`, `subjects`, `time-slots`

## QR & Presensi
- [x] Default QR expired 15 menit
- [x] One-scan-per-session (anti dobel ygy)
- [x] Presensi siswa/guru via QR
- [x] Void/undo scan `POST /attendance/{attendance}/void`

## Alasan & Lampiran
- [x] Status alasan: sakit/izin/dinas/late/absent/excused
- [x] Excuse update `POST /attendance/{attendance}/excuse`
- [x] Lampiran bukti `POST /attendance/{attendance}/attachments` (signed URL)

## Rekap & Export
- [x] Riwayat siswa `GET /me/attendance`
- [x] Export presensi: filter `class_id`, `from`, `to`, `schedule_id`
- [x] Rekap bulanan `GET /attendance/recap?month=YYYY-MM`
- [x] Summary jadwal `GET /attendance/schedules/{schedule}/summary`
- [x] Summary kelas `GET /attendance/classes/{class}/summary`

## Authorization
- [x] Guru hanya akses jadwal/rekap miliknya (kelas/homeroom check)

## Waka & Pengurus Kelas
- [x] Otomatisasi admin profile untuk Waka saat login
- [x] Validasi akses Waka untuk kelola jadwal bulk dan approve izin
- [x] Validasi pengurus kelas untuk generate/revoke QR siswa

## Dispensasi & Izin
- [x] Pengajuan dispensasi/izin sakit oleh guru/wali/pengurus kelas
- [x] Persetujuan Waka + tanda tangan (signature)

## Jurusan
- [x] Master jurusan (kode + kategori)
- [x] Relasi jurusan pada kelas

## API Docs
- [x] Scalar docs `/docs` + OpenAPI JSON
- [x] OpenAPI spec diperluas (auth, schedules, QR, attendance, absence, master, WA)


## Another List
- [x] /api/wa/send-text { to, message }
- [x] /api/wa/send-media { to, mediaBase64, filename, caption }

## plis aku capek ditimpa banyak project aduhai backend cuma 1 apa coba ini
