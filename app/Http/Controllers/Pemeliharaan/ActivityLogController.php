<?php

namespace App\Http\Controllers\Pemeliharaan;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(): View
    {
        $query = Activity::query()->with(['causer', 'subject']);

        $query
            ->when(request('log_name'), fn ($q, $v) => $q->where('log_name', $v))
            ->when(request('q'), fn ($q, $s) => $q->where('description', 'like', "%{$s}%"))
            ->when(request('user_id'), fn ($q, $v) => $q->where('causer_type', User::class)->where('causer_id', $v))
            ->when(request('from'), fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when(request('to'), fn ($q, $d) => $q->whereDate('created_at', '<=', $d));

        $activities = $query->orderByDesc('id')->paginate(25)->withQueryString();

        $causerIds = Activity::query()->whereNotNull('causer_id')->distinct()->pluck('causer_id');

        return view('pages.pemeliharaan.activity-log', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Pemeliharaan', 'href' => route('dashboard')],
                ['label' => 'Activity & Audit Log'],
            ],
            'activities' => $activities,
            'logNames' => Activity::query()->distinct()->orderBy('log_name')->pluck('log_name'),
            'users' => User::whereIn('id', $causerIds)->orderBy('name')->get(),
        ]);
    }
}
