<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ApplicationPriority;
use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ApplicationEvent;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Statuses that keep an application in the active queue.
     */
    private const OPEN_STATUSES = [
        ApplicationStatus::Submitted,
        ApplicationStatus::UnderReview,
        ApplicationStatus::InfoRequired,
        ApplicationStatus::InProgress,
    ];

    public function __invoke(Request $request): View
    {
        $openQuery = fn () => Application::query()->whereIn('status', self::OPEN_STATUSES);

        $stats = [
            'new' => Application::query()->where('status', ApplicationStatus::Submitted)->count(),
            'under_review' => Application::query()->where('status', ApplicationStatus::UnderReview)->count(),
            'waiting_on_student' => Application::query()->where('status', ApplicationStatus::InfoRequired)->count(),
            'in_progress' => Application::query()->where('status', ApplicationStatus::InProgress)->count(),
            'urgent' => $openQuery()->where('priority', ApplicationPriority::Urgent)->count(),
        ];

        $openTotal = $openQuery()->count();

        $openApplications = $openQuery()
            ->with(['category', 'student.user'])
            ->orderByPrioritySeverity('desc')
            ->orderBy('updated_at', 'desc')
            ->take(6)
            ->get();

        $needsAttention = $openQuery()
            ->with('student.user')
            ->where(function (Builder $query) {
                $query->where('priority', ApplicationPriority::Urgent)
                    ->orWhere('updated_at', '<=', now()->subDays(3));
            })
            ->orderByPrioritySeverity('desc')
            ->orderBy('updated_at')
            ->take(5)
            ->get();

        $recentActivity = ApplicationEvent::query()
            ->whereHas('application', fn (Builder $query) => $query->where('department_id', $request->user()->department_id))
            ->with('application')
            ->latest('created_at')
            ->take(5)
            ->get();

        return view('admin.dashboard', [
            'stats' => $stats,
            'openTotal' => $openTotal,
            'openApplications' => $openApplications,
            'needsAttention' => $needsAttention,
            'recentActivity' => $recentActivity,
        ]);
    }
}
