<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QRAbsence | Untrustnova</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Fraunces:wght@500;700&family=Manrope:wght@400;600;700&display=swap');
        :root {
            color-scheme: light;
        }
        body {
            font-family: "Manrope", sans-serif;
        }
        .title-font {
            font-family: "Fraunces", serif;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body>
    <div class="min-h-screen bg-[radial-gradient(circle_at_top,_#f9fafb_0%,_#e3ecf7_45%,_#c8d7ee_100%)] px-6 py-12">
        <div class="mx-auto grid w-full max-w-5xl gap-10 lg:grid-cols-[1.1fr_0.9fr]">
            <div class="rounded-3xl bg-white/80 p-8 shadow-[0_30px_60px_rgba(15,23,42,0.18)] backdrop-blur">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-500">QRAbsence</p>
                <h1 class="title-font mt-4 text-4xl font-bold text-slate-900 sm:text-5xl">
                    Presensi QR yang cepat, rapi, dan real-time.
                </h1>
                <p class="mt-4 text-base leading-relaxed text-slate-600">
                    Backend layanan presensi untuk admin, waka, guru, dan pengurus kelas dengan jadwal harian,
                    approval izin, dan broadcast notifikasi.
                </p>
                <div class="mt-8 grid gap-4 sm:grid-cols-2">
                    <a class="group flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 transition hover:-translate-y-0.5 hover:border-slate-300 hover:bg-white" href="/docs">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">API Documentation</p>
                            <p class="text-xs text-slate-500">Scalar UI</p>
                        </div>
                        <span class="text-xs font-semibold text-slate-500 group-hover:text-slate-900">/docs</span>
                    </a>
                    <a class="group flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 transition hover:-translate-y-0.5 hover:border-slate-300 hover:bg-white" href="/login">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">Login</p>
                            <p class="text-xs text-slate-500">Web schedules</p>
                        </div>
                        <span class="text-xs font-semibold text-slate-500 group-hover:text-slate-900">/login</span>
                    </a>
                </div>
                <div class="mt-6 rounded-2xl bg-slate-900 px-4 py-3 text-sm text-slate-100">
                    Gunakan <code class="rounded bg-slate-700/70 px-2 py-1">/api/auth/login</code> untuk token.
                </div>
            </div>
            <div class="grid gap-6">
                <div class="rounded-3xl bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 p-6 text-white shadow-[0_20px_50px_rgba(15,23,42,0.25)]">
                    <p class="text-sm uppercase tracking-[0.3em] text-slate-400">Realtime</p>
                    <h2 class="title-font mt-4 text-2xl font-semibold">Notifikasi presensi & izin.</h2>
                    <p class="mt-3 text-sm text-slate-300">
                        Broadcast ke channel kelas dan Waka untuk update jadwal, absensi, serta approval.
                    </p>
                    <div class="mt-6 grid gap-3 text-xs text-slate-300">
                        <div class="flex items-center justify-between rounded-xl bg-white/5 px-3 py-2">
                            <span>Channel kelas</span>
                            <span class="font-semibold text-white">classes.{id}</span>
                        </div>
                        <div class="flex items-center justify-between rounded-xl bg-white/5 px-3 py-2">
                            <span>Channel Waka</span>
                            <span class="font-semibold text-white">waka.absence-requests</span>
                        </div>
                    </div>
                </div>
                <div class="rounded-3xl bg-white/90 p-6 shadow-[0_18px_40px_rgba(15,23,42,0.15)]">
                    <h3 class="title-font text-xl font-semibold text-slate-900">Endpoint cepat</h3>
                    <ul class="mt-4 space-y-3 text-sm text-slate-600">
                        <li class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2">
                            <span>Bulk jadwal per hari</span>
                            <span class="font-semibold text-slate-900">/classes/{class}/schedules/bulk</span>
                        </li>
                        <li class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2">
                            <span>Approval izin</span>
                            <span class="font-semibold text-slate-900">/absence-requests</span>
                        </li>
                        <li class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2">
                            <span>QR lifecycle</span>
                            <span class="font-semibold text-slate-900">/qrcodes/generate</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
