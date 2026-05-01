<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PermitController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Permit::with(['project', 'owner'])->orderBy('due_date')->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'owner_id' => ['required', 'exists:users,id'],
            'permit_name' => ['required', 'string', 'max:255'],
            'authority' => ['required', 'string', 'max:255'],
            'reference_code' => ['required', 'string', 'max:255', 'unique:permits,reference_code'],
            'status' => ['required', Rule::in(['DRAFT', 'IN_REVIEW', 'SUBMITTED', 'APPROVED', 'BLOCKED'])],
            'due_date' => ['required', 'date'],
            'submitted_at' => ['nullable', 'date'],
            'approved_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $permit = Permit::create($validated)->load(['project', 'owner']);
        return response()->json($permit, 201);
    }

    public function update(Request $request, Permit $permit): JsonResponse
    {
        $validated = $request->validate([
            'owner_id' => ['sometimes', 'exists:users,id'],
            'status' => ['sometimes', Rule::in(['DRAFT', 'IN_REVIEW', 'SUBMITTED', 'APPROVED', 'BLOCKED'])],
            'due_date' => ['sometimes', 'date'],
            'submitted_at' => ['nullable', 'date'],
            'approved_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $permit->update($validated);
        return response()->json($permit->fresh()->load(['project', 'owner']));
    }
}
