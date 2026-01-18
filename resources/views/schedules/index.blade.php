<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedules | QRAbsence</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="min-h-screen bg-slate-100">
    <div class="mx-auto max-w-6xl px-6 py-10">
        <div class="flex flex-col gap-4 rounded-3xl bg-white p-6 shadow-sm md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.4em] text-slate-400">QRAbsence</p>
                <h1 class="mt-2 text-2xl font-semibold text-slate-900">Schedules</h1>
                <p class="mt-1 text-sm text-slate-500">Daftar jadwal sesuai hak akses akun.</p>
            </div>
            <form method="POST" action="/logout">
                @csrf
                <button class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:border-slate-300" type="submit">
                    Logout
                </button>
            </form>
        </div>

        <form method="GET" class="mt-6 grid gap-3 rounded-2xl bg-white p-4 shadow-sm md:grid-cols-3">
            <input name="class_id" value="{{ request('class_id') }}" placeholder="Class ID" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <input type="date" name="date" value="{{ request('date') }}" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <button class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white" type="submit">Filter</button>
        </form>

        <div class="mt-6 overflow-hidden rounded-2xl bg-white shadow-sm">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Day</th>
                        <th class="px-4 py-3">Time</th>
                        <th class="px-4 py-3">Subject</th>
                        <th class="px-4 py-3">Class</th>
                        <th class="px-4 py-3">Teacher</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($schedules as $schedule)
                        <tr class="text-slate-700">
                            <td class="px-4 py-3">{{ $schedule->day }}</td>
                            <td class="px-4 py-3">{{ $schedule->start_time }} - {{ $schedule->end_time }}</td>
                            <td class="px-4 py-3">{{ $schedule->subject_name ?? $schedule->title }}</td>
                            <td class="px-4 py-3">{{ optional($schedule->class)->label }}</td>
                            <td class="px-4 py-3">{{ optional($schedule->teacher?->user)->name }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-4 py-6 text-center text-slate-400" colspan="5">Tidak ada jadwal.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $schedules->links() }}
        </div>
    </div>
</body>
</html>
