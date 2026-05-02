# Rilievo

Rilievo is a construction and permit intelligence platform for managing project portfolios, permit risk, site activity, contractor compliance, and field issues in one shared workspace.

The name comes from the Italian word `rilievo`, which can mean surveying, measurement, or detection. It fits construction operations, inspections, and portfolio intelligence naturally.

## Product Position

Rilievo is an operations-heavy product direction for construction and compliance teams:

- strong Laravel + Angular story
- practical B2B workflows
- document and compliance handling
- rich dashboard and timeline views
- a credible path for construction and permit-focused software buyers

## Stack

- Laravel 12 API
- PHP 8.2
- SQLite for the local demo dataset
- Angular 19 frontend
- Sanctum bearer-token authentication

## Current Features

- portfolio dashboard with project health, permit pressure, issue volume, and contractor compliance signals
- project workspace with project detail, budget view, permit tracker, issue board, contractor board, and site diary
- quick-create panel for projects, permits, contractors, issues, and diary entries
- seeded demo data for Milan, Florence, and Pisa projects
- lookup endpoints for managers, compliance owners, and active project selection
- backend API tests for login, authenticated dashboard access, and issue creation

## Demo Accounts

All demo users share the same password:

```text
Rilievo123!
```

Accounts:

- `manager@rilievo.io`
- `coordinator@rilievo.io`
- `compliance@rilievo.io`

## Local Run

Backend:

```powershell
cd .\backend
..\scripts\Run-Backend.ps1
```

Frontend:

```powershell
cd .\frontend
..\scripts\Run-Frontend.ps1
```

Open:

```text
http://127.0.0.1:4202
```

API health:

```text
http://127.0.0.1:8003/api/health
```

## Verification

Backend tests:

```powershell
cd .\backend
php artisan test
```

Frontend production build:

```powershell
cd .\frontend
npm run build
```

## Roadmap

- contractor document uploads
- photo evidence linked to field issues
- change-order and budget variance tracking
- AI permit summarization
- multilingual support for broader EU use
