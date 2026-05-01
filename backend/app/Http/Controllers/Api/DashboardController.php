<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contractor;
use App\Models\Issue;
use App\Models\Permit;
use App\Models\Project;
use App\Models\SiteDiaryEntry;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function summary(): JsonResponse
    {
        $activeProjects = Project::whereIn('status', ['ACTIVE', 'AT_RISK'])->count();
        $permitsAtRisk = Permit::whereNotIn('status', ['APPROVED'])
            ->whereDate('due_date', '<=', now()->addDays(14))
            ->count();
        $openIssues = Issue::whereNotIn('status', ['RESOLVED', 'CLOSED'])->count();
        $contractorsPending = Contractor::whereNotIn('compliance_status', ['COMPLIANT'])->count();

        $projects = Project::with('manager')
            ->orderByDesc('progress_percent')
            ->limit(4)
            ->get()
            ->map(fn (Project $project) => [
                'id' => $project->id,
                'code' => $project->code,
                'name' => $project->name,
                'city' => $project->city,
                'status' => $project->status,
                'permit_status' => $project->permit_status,
                'progress_percent' => $project->progress_percent,
                'manager_name' => $project->manager?->name,
            ]);

        $alerts = collect()
            ->merge(
                Permit::with('project')
                    ->whereNotIn('status', ['APPROVED'])
                    ->whereDate('due_date', '<=', now()->addDays(14))
                    ->orderBy('due_date')
                    ->limit(3)
                    ->get()
                    ->map(fn (Permit $permit) => [
                        'level' => 'warning',
                        'title' => $permit->permit_name,
                        'detail' => "{$permit->project?->name} - due {$permit->due_date?->format('d M Y')}",
                    ])
            )
            ->merge(
                Issue::with('project')
                    ->whereIn('priority', ['HIGH', 'CRITICAL'])
                    ->whereNotIn('status', ['RESOLVED', 'CLOSED'])
                    ->orderBy('due_date')
                    ->limit(3)
                    ->get()
                    ->map(fn (Issue $issue) => [
                        'level' => 'critical',
                        'title' => $issue->title,
                        'detail' => "{$issue->project?->name} - {$issue->status}",
                    ])
            )
            ->take(5)
            ->values();

        $diaryHighlights = SiteDiaryEntry::with(['project', 'author'])
            ->latest('entry_date')
            ->limit(4)
            ->get()
            ->map(fn (SiteDiaryEntry $entry) => [
                'id' => $entry->id,
                'project_name' => $entry->project?->name,
                'author_name' => $entry->author?->name,
                'entry_date' => $entry->entry_date?->toDateString(),
                'weather' => $entry->weather,
                'workforce_count' => $entry->workforce_count,
                'completed_work' => $entry->completed_work,
            ]);

        return response()->json([
            'active_projects' => $activeProjects,
            'permits_at_risk' => $permitsAtRisk,
            'open_issues' => $openIssues,
            'contractors_pending' => $contractorsPending,
            'projects' => $projects,
            'alerts' => $alerts,
            'diary_highlights' => $diaryHighlights,
        ]);
    }
}
