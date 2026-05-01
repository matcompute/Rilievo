<?php

namespace Database\Seeders;

use App\Models\Contractor;
use App\Models\Issue;
use App\Models\Permit;
use App\Models\Project;
use App\Models\SiteDiaryEntry;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::query()->delete();
        Project::query()->delete();
        Permit::query()->delete();
        Contractor::query()->delete();
        SiteDiaryEntry::query()->delete();
        Issue::query()->delete();

        $manager = User::create([
            'name' => 'Giulia Conti',
            'email' => 'manager@rilievo.io',
            'role' => 'PROJECT_MANAGER',
            'title' => 'Portfolio Lead',
            'accent_color' => '#f59e0b',
            'password' => Hash::make('Rilievo123!'),
        ]);

        $coordinator = User::create([
            'name' => 'Lorenzo Ferri',
            'email' => 'coordinator@rilievo.io',
            'role' => 'SITE_COORDINATOR',
            'title' => 'Site Coordinator',
            'accent_color' => '#14b8a6',
            'password' => Hash::make('Rilievo123!'),
        ]);

        $compliance = User::create([
            'name' => 'Marta Bellini',
            'email' => 'compliance@rilievo.io',
            'role' => 'COMPLIANCE_OFFICER',
            'title' => 'Compliance Officer',
            'accent_color' => '#ef4444',
            'password' => Hash::make('Rilievo123!'),
        ]);

        $projectA = Project::create([
            'code' => 'RLV-2401',
            'name' => 'Porta Nuova Civic Hub',
            'client_name' => 'Comune di Milano',
            'city' => 'Milan',
            'status' => 'ACTIVE',
            'permit_status' => 'IN_REVIEW',
            'progress_percent' => 68,
            'budget_total' => 4200000,
            'budget_spent' => 2760000,
            'start_date' => now()->subMonths(7)->toDateString(),
            'target_date' => now()->addMonths(5)->toDateString(),
            'manager_id' => $manager->id,
            'summary' => 'Mixed civic redevelopment with phased permit reviews and contractor coordination.',
        ]);

        $projectB = Project::create([
            'code' => 'RLV-2402',
            'name' => 'Arno Riverside Housing',
            'client_name' => 'Residenza Toscana',
            'city' => 'Florence',
            'status' => 'AT_RISK',
            'permit_status' => 'BLOCKED',
            'progress_percent' => 42,
            'budget_total' => 6150000,
            'budget_spent' => 2980000,
            'start_date' => now()->subMonths(4)->toDateString(),
            'target_date' => now()->addMonths(8)->toDateString(),
            'manager_id' => $manager->id,
            'summary' => 'Residential block with utility, zoning, and subcontractor compliance pressure.',
        ]);

        $projectC = Project::create([
            'code' => 'RLV-2403',
            'name' => 'Galileo Science Annex',
            'client_name' => 'Istituto Galileo',
            'city' => 'Pisa',
            'status' => 'PLANNING',
            'permit_status' => 'CLEAR',
            'progress_percent' => 21,
            'budget_total' => 2950000,
            'budget_spent' => 412000,
            'start_date' => now()->subMonths(2)->toDateString(),
            'target_date' => now()->addMonths(11)->toDateString(),
            'manager_id' => $manager->id,
            'summary' => 'Laboratory annex planning package with early-stage permit and cost tracking.',
        ]);

        Permit::insert([
            [
                'project_id' => $projectA->id,
                'owner_id' => $compliance->id,
                'permit_name' => 'Facade Occupancy Permit',
                'authority' => 'Comune di Milano',
                'reference_code' => 'PMT-4012',
                'status' => 'IN_REVIEW',
                'due_date' => now()->addDays(6)->toDateString(),
                'submitted_at' => now()->subDays(8)->toDateString(),
                'approved_at' => null,
                'notes' => 'Awaiting structural attachment clarifications.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'project_id' => $projectB->id,
                'owner_id' => $compliance->id,
                'permit_name' => 'Utility Corridor Clearance',
                'authority' => 'Comune di Firenze',
                'reference_code' => 'PMT-4026',
                'status' => 'BLOCKED',
                'due_date' => now()->addDays(3)->toDateString(),
                'submitted_at' => now()->subDays(15)->toDateString(),
                'approved_at' => null,
                'notes' => 'Missing updated drainage impact attachment.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'project_id' => $projectC->id,
                'owner_id' => $compliance->id,
                'permit_name' => 'Laboratory Fire Review',
                'authority' => 'Comune di Pisa',
                'reference_code' => 'PMT-4090',
                'status' => 'APPROVED',
                'due_date' => now()->addDays(21)->toDateString(),
                'submitted_at' => now()->subDays(20)->toDateString(),
                'approved_at' => now()->subDays(4)->toDateString(),
                'notes' => 'Cleared for planning package.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        Contractor::insert([
            [
                'project_id' => $projectA->id,
                'name' => 'EdilNord Structures',
                'trade' => 'Concrete',
                'contact_name' => 'Silvia Moretti',
                'contact_email' => 'silvia@edilnord.it',
                'compliance_status' => 'COMPLIANT',
                'insurance_expires_on' => now()->addMonths(7)->toDateString(),
                'worker_count' => 24,
                'last_audit_at' => now()->subDays(9)->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'project_id' => $projectB->id,
                'name' => 'Tuscan MEP Systems',
                'trade' => 'Mechanical',
                'contact_name' => 'Paolo Guidi',
                'contact_email' => 'paolo@tuscanmep.it',
                'compliance_status' => 'REVIEW',
                'insurance_expires_on' => now()->addDays(18)->toDateString(),
                'worker_count' => 11,
                'last_audit_at' => now()->subDays(28)->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'project_id' => $projectB->id,
                'name' => 'Linea Sicura Scaffolds',
                'trade' => 'Scaffolding',
                'contact_name' => 'Franco Bassi',
                'contact_email' => 'franco@lineasicura.it',
                'compliance_status' => 'EXPIRED',
                'insurance_expires_on' => now()->subDays(5)->toDateString(),
                'worker_count' => 8,
                'last_audit_at' => now()->subDays(45)->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        SiteDiaryEntry::insert([
            [
                'project_id' => $projectA->id,
                'author_id' => $coordinator->id,
                'entry_date' => now()->subDay()->toDateString(),
                'weather' => 'Clear',
                'workforce_count' => 32,
                'completed_work' => 'North stair core reinforcement and crane window coordination completed.',
                'blockers' => 'Facade permit response still pending.',
                'safety_note' => 'Scaffold edge inspections repeated after wind advisory.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'project_id' => $projectB->id,
                'author_id' => $coordinator->id,
                'entry_date' => now()->toDateString(),
                'weather' => 'Rain',
                'workforce_count' => 18,
                'completed_work' => 'Drainage rerouting trench marked and temporary fencing updated.',
                'blockers' => 'Utility clearance package blocked by missing revision.',
                'safety_note' => 'Wet access path flagged for anti-slip coverage.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        Issue::insert([
            [
                'project_id' => $projectA->id,
                'raised_by_id' => $coordinator->id,
                'category' => 'Permit',
                'priority' => 'HIGH',
                'status' => 'IN_REVIEW',
                'title' => 'Facade attachment detail mismatch',
                'description' => 'Inspector requested revised fastening detail package before release.',
                'assignee_name' => 'Marta Bellini',
                'due_date' => now()->addDays(4)->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'project_id' => $projectB->id,
                'raised_by_id' => $coordinator->id,
                'category' => 'Compliance',
                'priority' => 'CRITICAL',
                'status' => 'OPEN',
                'title' => 'Expired scaffold insurance certificate',
                'description' => 'Scaffolding subcontractor certificate expired and must be renewed before next mobilization.',
                'assignee_name' => 'Franco Bassi',
                'due_date' => now()->addDays(2)->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'project_id' => $projectC->id,
                'raised_by_id' => $manager->id,
                'category' => 'Budget',
                'priority' => 'MEDIUM',
                'status' => 'IN_PROGRESS',
                'title' => 'Laboratory fit-out estimate refresh',
                'description' => 'Cost baseline needs refresh after supplier revisions.',
                'assignee_name' => 'Giulia Conti',
                'due_date' => now()->addDays(10)->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
