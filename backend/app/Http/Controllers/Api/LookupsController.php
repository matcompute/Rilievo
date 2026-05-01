<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class LookupsController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::query()
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role', 'title', 'accent_color']);

        $projects = Project::query()
            ->orderBy('name')
            ->get(['id', 'code', 'name', 'city', 'status', 'permit_status']);

        return response()->json([
            'users' => $users,
            'projects' => $projects,
            'project_statuses' => ['PLANNING', 'ACTIVE', 'AT_RISK', 'COMPLETED'],
            'permit_statuses' => ['DRAFT', 'IN_REVIEW', 'SUBMITTED', 'APPROVED', 'BLOCKED'],
            'issue_priorities' => ['LOW', 'MEDIUM', 'HIGH', 'CRITICAL'],
            'issue_statuses' => ['OPEN', 'IN_REVIEW', 'IN_PROGRESS', 'RESOLVED', 'CLOSED'],
            'contractor_statuses' => ['PENDING', 'REVIEW', 'COMPLIANT', 'EXPIRED'],
        ]);
    }
}
