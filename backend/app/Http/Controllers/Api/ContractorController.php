<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contractor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContractorController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Contractor::with('project')->orderBy('name')->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => ['nullable', 'exists:projects,id'],
            'name' => ['required', 'string', 'max:255'],
            'trade' => ['required', 'string', 'max:255'],
            'contact_name' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'email'],
            'compliance_status' => ['required', Rule::in(['PENDING', 'REVIEW', 'COMPLIANT', 'EXPIRED'])],
            'insurance_expires_on' => ['nullable', 'date'],
            'worker_count' => ['required', 'integer', 'min:0'],
            'last_audit_at' => ['nullable', 'date'],
        ]);

        $contractor = Contractor::create($validated)->load('project');
        return response()->json($contractor, 201);
    }

    public function update(Request $request, Contractor $contractor): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => ['nullable', 'exists:projects,id'],
            'trade' => ['sometimes', 'string', 'max:255'],
            'contact_name' => ['sometimes', 'string', 'max:255'],
            'contact_email' => ['sometimes', 'email'],
            'compliance_status' => ['sometimes', Rule::in(['PENDING', 'REVIEW', 'COMPLIANT', 'EXPIRED'])],
            'insurance_expires_on' => ['nullable', 'date'],
            'worker_count' => ['sometimes', 'integer', 'min:0'],
            'last_audit_at' => ['nullable', 'date'],
        ]);

        $contractor->update($validated);
        return response()->json($contractor->fresh()->load('project'));
    }
}
