# Sample Request Payloads
Use `{BASE_URL}` for your host (e.g., `http://localhost:8000`). All endpoints are under `/api/...`.

## Auth
`{BASE_URL}/api/auth/login`
```json
{
  "login": "username_or_email",
  "password": "secret"
}
```

`{BASE_URL}/api/auth/logout`
```json
{}
```

## Devices (siswa)
`{BASE_URL}/api/me/devices`
```json
{
  "identifier": "device-uuid-or-hash",
  "name": "My Phone",
  "platform": "Android"
}
```

`{BASE_URL}/api/me/devices/{device_id}` (DELETE)
```json
{}
```

## Classes
`{BASE_URL}/api/classes`
```json
{
  "grade": "10",
  "label": "10-A"
}
```

## Teachers
`{BASE_URL}/api/teachers`
```json
{
  "name": "Guru 1",
  "username": "guru1",
  "email": "g1@example.com",
  "password": "secret123",
  "nip": "12345",
  "homeroom_class_id": 1,
  "subject": "Matematika"
}
```

`{BASE_URL}/api/teachers/import`
```json
{
  "items": [
    {
      "name": "Guru 1",
      "username": "guru1",
      "email": "g1@example.com",
      "password": "secret123",
      "nip": "12345",
      "homeroom_class_id": 1,
      "subject": "Matematika"
    }
  ]
}
```

## Students
`{BASE_URL}/api/students`
```json
{
  "name": "Siswa 1",
  "username": "siswa1",
  "email": "s1@example.com",
  "password": "secret123",
  "nisn": "99887766",
  "nis": "123456",
  "gender": "L",
  "address": "Alamat siswa",
  "class_id": 1
}
```

`{BASE_URL}/api/students/import`
```json
{
  "items": [
    {
      "name": "Siswa 1",
      "username": "siswa1",
      "email": "s1@example.com",
      "password": "secret123",
      "nisn": "99887766",
      "nis": "123456",
      "gender": "L",
      "address": "Alamat siswa",
      "class_id": 1
    }
  ]
}
```

## Schedules
`{BASE_URL}/api/schedules`
```json
{
  "day": "Monday",
  "start_time": "07:30",
  "end_time": "09:00",
  "title": "Matematika",
  "teacher_id": 1,
  "class_id": 1,
  "room": "R-101",
  "semester": 1,
  "year": 2026
}
```

`{BASE_URL}/api/schedules?date=2026-01-01&class_id=1`
```json
{}
```

## QR Codes
`{BASE_URL}/api/qrcodes/generate`
```json
{
  "schedule_id": 1,
  "type": "student",
  "expires_in_minutes": 15
}
```

`{BASE_URL}/api/qrcodes/{token}/revoke`
```json
{}
```

## Attendance
`{BASE_URL}/api/attendance/scan`
```json
{
  "token": "qr-token",
  "device_id": 1
}
```

`{BASE_URL}/api/attendance/{attendance_id}/excuse`
```json
{
  "status": "izin",
  "reason": "Surat izin"
}
```

`{BASE_URL}/api/attendance/{attendance_id}/void`
```json
{}
```

`{BASE_URL}/api/attendance/{attendance_id}/attachments` (multipart/form-data)
```
file: <upload>
```

`{BASE_URL}/api/attendance/schedules/{schedule_id}`
```json
{}
```

`{BASE_URL}/api/attendance/export?class_id=1&from=2026-01-01&to=2026-01-31`
```json
{}
```

`{BASE_URL}/api/attendance/recap?month=2026-01`
```json
{}
```

`{BASE_URL}/api/attendance/schedules/{schedule_id}/summary`
```json
{}
```

`{BASE_URL}/api/attendance/classes/{class_id}/summary`
```json
{}
```

## Student view
`{BASE_URL}/api/me/attendance`
```json
{}
```

## Optional Masters
`{BASE_URL}/api/school-years`
```json
{
  "name": "2026/2027",
  "start_year": 2026,
  "end_year": 2027,
  "active": true
}
```

`{BASE_URL}/api/semesters`
```json
{
  "name": "Semester 1",
  "school_year_id": 1,
  "active": true
}
```

`{BASE_URL}/api/rooms`
```json
{
  "name": "R-101",
  "location": "Lantai 1",
  "capacity": 30
}
```

`{BASE_URL}/api/subjects`
```json
{
  "code": "MAT",
  "name": "Matematika"
}
```

`{BASE_URL}/api/time-slots`
```json
{
  "name": "Sesi 1",
  "start_time": "07:30",
  "end_time": "09:00"
}
```
