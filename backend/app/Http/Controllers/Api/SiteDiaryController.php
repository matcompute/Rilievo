<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiteDiaryEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SiteDiaryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            SiteDiaryEntry::with(['project', 'author'])->latest('entry_date')->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'entry_date' => ['required', 'date'],
            'weather' => ['required', 'string', 'max:255'],
            'workforce_count' => ['required', 'integer', 'min:0'],
            'completed_work' => ['required', 'string'],
            'blockers' => ['nullable', 'string'],
            'safety_note' => ['nullable', 'string'],
        ]);

        $validated['author_id'] = $request->user()->id;
        $entry = SiteDiaryEntry::create($validated)->load(['project', 'author']);

        return response()->json($entry, 201);
    }
}
