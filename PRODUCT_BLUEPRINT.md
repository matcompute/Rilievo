# Rilievo Product Blueprint

## 1. One-Line Pitch

Rilievo is a construction and permit intelligence platform that helps teams manage sites, permits, subcontractors, compliance documents, and project risk in one shared operational system.

## 2. Why This Product

This is a strong operations product direction because it gives:

- a stronger PHP and Angular enterprise story
- document-heavy and compliance-heavy business logic
- real reporting and operations value
- a product that feels close to something companies would actually buy

## 3. Users

### Project Manager

- monitors project health
- reviews delays, issues, and cost risk
- sees permit and contractor status

### Site Coordinator

- updates diary entries
- logs issues
- uploads photos
- tracks daily progress

### Compliance Officer

- tracks permit deadlines
- reviews missing documentation
- validates subcontractor compliance

### Contractor / Subcontractor Contact

- uploads required documents
- receives issue notifications
- updates task/inspection responses

## 4. Main Entities

- User
- Role
- Project
- Site
- Permit
- PermitStatus
- Contractor
- ContractorDocument
- SiteDiaryEntry
- Issue
- IssuePhoto
- ComplianceItem
- ChangeOrder
- BudgetLine
- Alert

## 5. Core Screens

- login
- project overview dashboard
- project portfolio
- site detail
- permit tracker
- contractor workspace
- compliance board
- issue board
- budget and change order view
- timeline and alerts

## 6. Initial Product Scope

### Backend

- JWT or Sanctum-style auth
- projects, sites, permits, contractors
- issue and site diary flows
- compliance and missing-document alerts
- dashboard summary metrics

### Frontend

- responsive workspace
- project cards and dashboards
- permit and issue tables
- timeline-style site diary
- contractor and compliance views

## 7. Design Direction

Rilievo should feel colorful, energetic, and professional.

### Visual language

- dark graphite base
- warm amber and copper for construction identity
- teal for active progress
- crimson for blockers
- pale stone neutrals for balance

### UI tone

- premium B2B
- highly visual status tracking
- visible progress and deadlines
- clear document and issue states

## 8. Architecture Direction

### Backend

- controllers
- services
- policies/authorization
- repositories only if needed
- events/notifications later

### Frontend

- Angular feature modules or organized feature folders
- service layer per domain
- reusable tables, timeline components, and status chips

## 9. Why Recruiters And Buyers Will Like It

- Laravel + Angular
- operations, compliance, and reporting logic
- strong domain modeling
- attractive dashboards
- clearly sellable workflow software

## 10. Future Expansion

- AI permit summarization
- photo-based issue tagging
- budget overrun prediction
- contractor scoring
- multilingual support for broader EU use
