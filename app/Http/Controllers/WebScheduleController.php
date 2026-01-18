<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class WebScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $query = Schedule::query()->with(['teacher.user', 'class']);

        if ($request->user()->user_type === 'teacher') {
            $query->where('teacher_id', optional($request->user()->teacherProfile)->id);
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->integer('class_id'));
        }

        if ($request->filled('date')) {
            $day = Carbon::parse($request->string('date'))->format('l');
            $query->where('day', $day);
        }

        $schedules = $query->latest()->paginate(15)->withQueryString();

        return view('schedules.index', [
            'schedules' => $schedules,
        ]);
    }
}
