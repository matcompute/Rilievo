export interface UserSummary {
  id: number;
  name: string;
  email: string;
  role: string;
  title: string | null;
  accent_color: string;
}

export interface AuthSession {
  token: string;
  user: UserSummary;
}

export interface DashboardProject {
  id: number;
  code: string;
  name: string;
  city: string;
  status: string;
  permit_status: string;
  progress_percent: number;
  manager_name: string | null;
}

export interface DashboardAlert {
  level: string;
  title: string;
  detail: string;
}

export interface DiaryHighlight {
  id: number;
  project_name: string | null;
  author_name: string | null;
  entry_date: string | null;
  weather: string;
  workforce_count: number;
  completed_work: string;
}

export interface DashboardSummary {
  active_projects: number;
  permits_at_risk: number;
  open_issues: number;
  contractors_pending: number;
  projects: DashboardProject[];
  alerts: DashboardAlert[];
  diary_highlights: DiaryHighlight[];
}

export interface LookupProject {
  id: number;
  code: string;
  name: string;
  city: string;
  status: string;
  permit_status: string;
}

export interface LookupData {
  users: UserSummary[];
  projects: LookupProject[];
  project_statuses: string[];
  permit_statuses: string[];
  issue_priorities: string[];
  issue_statuses: string[];
  contractor_statuses: string[];
}

export interface Permit {
  id: number;
  project_id: number;
  owner_id: number;
  permit_name: string;
  authority: string;
  reference_code: string;
  status: string;
  due_date: string | null;
  submitted_at: string | null;
  approved_at: string | null;
  notes: string | null;
  project?: ProjectSummary;
  owner?: UserSummary;
}

export interface Contractor {
  id: number;
  project_id: number | null;
  name: string;
  trade: string;
  contact_name: string;
  contact_email: string;
  compliance_status: string;
  insurance_expires_on: string | null;
  worker_count: number;
  last_audit_at: string | null;
  project?: ProjectSummary;
}

export interface SiteDiaryEntry {
  id: number;
  project_id: number;
  author_id: number;
  entry_date: string | null;
  weather: string;
  workforce_count: number;
  completed_work: string;
  blockers: string | null;
  safety_note: string | null;
  project?: ProjectSummary;
  author?: UserSummary;
}

export interface Issue {
  id: number;
  project_id: number;
  raised_by_id: number;
  category: string;
  priority: string;
  status: string;
  title: string;
  description: string;
  assignee_name: string | null;
  due_date: string | null;
  project?: ProjectSummary;
  raised_by?: UserSummary;
}

export interface ProjectSummary {
  id: number;
  code: string;
  name: string;
  client_name: string;
  city: string;
  status: string;
  permit_status: string;
  progress_percent: number;
  budget_total: number | string;
  budget_spent: number | string;
  start_date: string;
  target_date: string;
  manager_id: number;
  summary: string | null;
  manager?: UserSummary;
  permits_count?: number;
  issues_count?: number;
  contractors_count?: number;
}

export interface ProjectDetail extends ProjectSummary {
  permits?: Permit[];
  contractors?: Contractor[];
  diary_entries?: SiteDiaryEntry[];
  issues?: Issue[];
}

export interface ProjectPayload {
  code: string;
  name: string;
  client_name: string;
  city: string;
  status: string;
  permit_status: string;
  progress_percent: number;
  budget_total: number;
  budget_spent: number;
  start_date: string;
  target_date: string;
  manager_id: number;
  summary: string;
}

export interface PermitPayload {
  project_id: number;
  owner_id: number;
  permit_name: string;
  authority: string;
  reference_code: string;
  status: string;
  due_date: string;
  submitted_at: string | null;
  approved_at: string | null;
  notes: string;
}

export interface ContractorPayload {
  project_id: number | null;
  name: string;
  trade: string;
  contact_name: string;
  contact_email: string;
  compliance_status: string;
  insurance_expires_on: string | null;
  worker_count: number;
  last_audit_at: string | null;
}

export interface IssuePayload {
  project_id: number;
  category: string;
  priority: string;
  status: string;
  title: string;
  description: string;
  assignee_name: string | null;
  due_date: string | null;
}

export interface SiteDiaryPayload {
  project_id: number;
  entry_date: string;
  weather: string;
  workforce_count: number;
  completed_work: string;
  blockers: string | null;
  safety_note: string | null;
}
