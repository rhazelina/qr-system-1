# QR Attendance API (Laravel 12 + Sail/Octane/Reverb)

API-only QR-based attendance system for sekolah roles (admin, waka/kesiswaan, guru, siswa). Stack includes Laravel Octane, Reverb broadcasting, Sanctum auth tokens, Telescope observability, Simple QrCode, and CSV/Excel-friendly exports.

## Quick start
- Copy env & deps: `cp .env.example .env` (or use existing), `composer install`, `bun install`
- Boot Sail (MariaDB/Redis/queues): `./vendor/bin/sail up -d`
- Generate key & migrate: `./vendor/bin/sail artisan key:generate && ./vendor/bin/sail artisan migrate`
- Run dev stack (Octane + queue + Vite): `./vendor/bin/sail artisan octane:start --watch --host=0.0.0.0 --port=8000`
- (Optional) Telescope: `./vendor/bin/sail artisan telescope:install && ./vendor/bin/sail artisan migrate`

## Core domain
- Users (`user_type`: admin|teacher|student) with profiles: `admin_profiles`, `teacher_profiles` (NIP, homeroom, subject), `student_profiles` (NISN, NIS, class, gender, address).
- Classes & schedules (link teacher+class, semester/year/time).
- QR sessions (`qrcodes`): token, type (student|teacher), schedule, issued_by, expire/active.
- Attendance: attendee_type (student|teacher), status (present/late/excused/sick/absent), checked_in_at, schedule, QR link.

## API (routes/api.php)
- Auth: `POST /auth/login` (username/email + password) → token; `GET /me`; `POST /auth/logout`.
- Admin-only CRUD: `/classes`, `/teachers`, `/students`, `/schedules` (create/update/delete).
- Admin/Teacher: `GET /schedules`, `GET /schedules/{id}`; QR lifecycle `GET /qrcodes/active`, `POST /qrcodes/generate` (schedule_id, type, optional expires), `POST /qrcodes/{token}/revoke`.
- Attendance:
  - Scan QR (student/teacher) `POST /attendance/scan` with `token`.
  - View per schedule `GET /attendance/schedules/{schedule}` (admin/teacher).
  - Mark excuse/status `POST /attendance/{attendance}/excuse` (admin/teacher).
  - Export CSV `GET /attendance/export?schedule_id=` (admin/teacher).
All protected with Sanctum; role guard via middleware `role:{admin|teacher|student}`.

## Real-time (Reverb)
- Channels: `schedules.{id}` broadcast events.
  - `qr.generated` payload: token, type, schedule_id, expires_at.
  - `attendance.recorded` payload: attendee_type, schedule_id, status, name.

## Flows (ringkas)
- Admin input guru/siswa/kelas/jadwal → sistem siap.
- Waka/kesiswaan melihat rekap; wali kelas bisa generate QR siswa/guru (homeroom) & koreksi status.
- Guru melihat jadwal, generate QR sesi, siswa scan untuk presensi, guru scan/klik hadir di akhir jam.
- Rekap dapat diekspor CSV; monitoring real-time via Reverb.

## Testing
- PHP lint run via `php -l` (already clean).
- Add seeds/factories for roles before manual testing; use Postman/Thunder Client with Bearer token from `/auth/login`.
