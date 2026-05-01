<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    public function index(): JsonResponse
    {
        $projects = Project::withCount(['permits', 'issues', 'contractors'])
            ->with('manager')
            ->orderBy('target_date')
            ->get();

        return response()->json($projects);
    }

    public function show(Project $project): JsonResponse
    {
        $project->load(['manager', 'permits.owner', 'contractors', 'diaryEntries.author', 'issues.raisedBy']);
        return response()->json($project);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:255', 'unique:projects,code'],
            'name' => ['required', 'string', 'max:255'],
            'client_name' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['PLANNING', 'ACTIVE', 'AT_RISK', 'COMPLETED'])],
            'permit_status' => ['required', Rule::in(['IN_REVIEW', 'CLEAR', 'BLOCKED'])],
            'progress_percent' => ['required', 'integer', 'between:0,100'],
            'budget_total' => ['required', 'numeric', 'min:0'],
            'budget_spent' => ['required', 'numeric', 'min:0'],
            'start_date' => ['required', 'date'],
            'target_date' => ['required', 'date'],
            'manager_id' => ['required', 'exists:users,id'],
            'summary' => ['nullable', 'string'],
        ]);

        $project = Project::create($validated)->load('manager');
        return response()->json($project, 201);
    }

    public function update(Request $request, Project $project): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'client_name' => ['sometimes', 'string', 'max:255'],
            'city' => ['sometimes', 'string', 'max:255'],
            'status' => ['sometimes', Rule::in(['PLANNING', 'ACTIVE', 'AT_RISK', 'COMPLETED'])],
            'permit_status' => ['sometimes', Rule::in(['IN_REVIEW', 'CLEAR', 'BLOCKED'])],
            'progress_percent' => ['sometimes', 'integer', 'between:0,100'],
            'budget_total' => ['sometimes', 'numeric', 'min:0'],
            'budget_spent' => ['sometimes', 'numeric', 'min:0'],
            'start_date' => ['sometimes', 'date'],
            'target_date' => ['sometimes', 'date'],
            'manager_id' => ['sometimes', 'exists:users,id'],
            'summary' => ['nullable', 'string'],
        ]);

        $project->update($validated);
        return response()->json($project->fresh()->load('manager'));
    }
}
