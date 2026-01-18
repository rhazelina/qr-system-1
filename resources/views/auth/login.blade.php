<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | QRAbsence</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="min-h-screen bg-slate-950">
    <div class="flex min-h-screen items-center justify-center px-6 py-12">
        <div class="w-full max-w-md rounded-3xl bg-white/95 p-8 shadow-2xl">
            <div class="mb-6">
                <p class="text-xs uppercase tracking-[0.4em] text-slate-400">QRAbsence</p>
                <h1 class="mt-3 text-2xl font-semibold text-slate-900">Login</h1>
                <p class="mt-2 text-sm text-slate-500">Masuk untuk melihat jadwal via web.</p>
            </div>

            @if ($errors->any())
                <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="/login" class="space-y-4">
                @csrf
                <div>
                    <label class="text-sm font-medium text-slate-700">Email atau Username</label>
                    <input name="login" value="{{ old('login') }}" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-slate-400 focus:outline-none" placeholder="admin@school.id" required>
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Password</label>
                    <input type="password" name="password" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-slate-400 focus:outline-none" placeholder="********" required>
                </div>
                <button class="w-full rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800" type="submit">
                    Login
                </button>
            </form>
            <p class="mt-6 text-xs text-slate-400">Hanya admin, waka, dan guru yang bisa akses jadwal.</p>
        </div>
    </div>
</body>
</html>
