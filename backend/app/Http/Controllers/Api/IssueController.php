<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IssueController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Issue::with(['project', 'raisedBy'])->orderBy('due_date')->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'category' => ['required', 'string', 'max:255'],
            'priority' => ['required', Rule::in(['LOW', 'MEDIUM', 'HIGH', 'CRITICAL'])],
            'status' => ['required', Rule::in(['OPEN', 'IN_REVIEW', 'IN_PROGRESS', 'RESOLVED', 'CLOSED'])],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'assignee_name' => ['nullable', 'string', 'max:255'],
            'due_date' => ['nullable', 'date'],
        ]);

        $validated['raised_by_id'] = $request->user()->id;
        $issue = Issue::create($validated)->load(['project', 'raisedBy']);
        return response()->json($issue, 201);
    }

    public function update(Request $request, Issue $issue): JsonResponse
    {
        $validated = $request->validate([
            'priority' => ['sometimes', Rule::in(['LOW', 'MEDIUM', 'HIGH', 'CRITICAL'])],
            'status' => ['sometimes', Rule::in(['OPEN', 'IN_REVIEW', 'IN_PROGRESS', 'RESOLVED', 'CLOSED'])],
            'assignee_name' => ['nullable', 'string', 'max:255'],
            'due_date' => ['nullable', 'date'],
            'description' => ['sometimes', 'string'],
        ]);

        $issue->update($validated);
        return response()->json($issue->fresh()->load(['project', 'raisedBy']));
    }
}
