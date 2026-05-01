import { CommonModule } from '@angular/common';
import { Component, OnInit, computed, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { finalize, forkJoin } from 'rxjs';
import {
  AuthSession,
  Contractor,
  ContractorPayload,
  DashboardSummary,
  Issue,
  IssuePayload,
  LookupData,
  Permit,
  PermitPayload,
  ProjectDetail,
  ProjectPayload,
  ProjectSummary,
  SiteDiaryEntry,
  SiteDiaryPayload,
  UserSummary,
} from './models/rilievo.models';
import { RilievoApiService } from './services/rilievo-api.service';

type BoardView = 'overview' | 'permits' | 'issues' | 'contractors' | 'diary';
type ComposerView = 'project' | 'permit' | 'contractor' | 'issue' | 'diary';

@Component({
  selector: 'app-root',
  imports: [CommonModule, FormsModule],
  templateUrl: './app.component.html',
  styleUrl: './app.component.css',
})
export class AppComponent implements OnInit {
  protected readonly session = signal<AuthSession | null>(null);
  protected readonly dashboard = signal<DashboardSummary | null>(null);
  protected readonly lookups = signal<LookupData | null>(null);
  protected readonly projects = signal<ProjectSummary[]>([]);
  protected readonly permits = signal<Permit[]>([]);
  protected readonly contractors = signal<Contractor[]>([]);
  protected readonly diaryEntries = signal<SiteDiaryEntry[]>([]);
  protected readonly issues = signal<Issue[]>([]);
  protected readonly selectedProject = signal<ProjectDetail | null>(null);
  protected readonly isLoading = signal(false);
  protected readonly isAuthenticating = signal(false);
  protected readonly errorMessage = signal('');
  protected readonly infoMessage = signal('Use the seeded Rilievo accounts to explore permit risk, site diaries, and contractor compliance.');

  protected readonly demoAccounts = [
    { label: 'Project Manager', email: 'manager@rilievo.io', role: 'PROJECT_MANAGER' },
    { label: 'Site Coordinator', email: 'coordinator@rilievo.io', role: 'SITE_COORDINATOR' },
    { label: 'Compliance', email: 'compliance@rilievo.io', role: 'COMPLIANCE_OFFICER' },
  ];

  protected loginEmail = 'manager@rilievo.io';
  protected loginPassword = 'Rilievo123!';
  protected projectSearch = '';
  protected projectStatusFilter = '';
  protected activeBoard: BoardView = 'overview';
  protected composerView: ComposerView = 'issue';

  protected projectForm = this.buildProjectForm();
  protected permitForm = this.buildPermitForm();
  protected contractorForm = this.buildContractorForm();
  protected issueForm = this.buildIssueForm();
  protected diaryForm = this.buildDiaryForm();

  protected readonly metricCards = computed(() => {
    const summary = this.dashboard();
    if (!summary) {
      return [];
    }

    return [
      { label: 'Active projects', value: summary.active_projects, tone: 'teal' },
      { label: 'Permits at risk', value: summary.permits_at_risk, tone: 'amber' },
      { label: 'Open issues', value: summary.open_issues, tone: 'crimson' },
      { label: 'Contractors pending', value: summary.contractors_pending, tone: 'copper' },
    ];
  });

  protected readonly selectedPermits = computed(() => this.selectedProject()?.permits ?? []);
  protected readonly selectedContractors = computed(() => this.selectedProject()?.contractors ?? []);
  protected readonly selectedIssues = computed(() => this.selectedProject()?.issues ?? []);
  protected readonly selectedDiaryEntries = computed(() => this.selectedProject()?.diary_entries ?? []);
  protected readonly riskPermits = computed(() =>
    this.permits()
      .filter((permit) => permit.status !== 'APPROVED')
      .sort((left, right) => (left.due_date ?? '').localeCompare(right.due_date ?? ''))
      .slice(0, 5)
  );
  protected readonly watchlistContractors = computed(() =>
    this.contractors()
      .filter((contractor) => contractor.compliance_status !== 'COMPLIANT')
      .slice(0, 5)
  );
  protected readonly criticalIssues = computed(() =>
    this.issues()
      .filter((issue) => ['HIGH', 'CRITICAL'].includes(issue.priority))
      .slice(0, 5)
  );

  constructor(private readonly api: RilievoApiService) {}

  ngOnInit(): void {
    const token = localStorage.getItem('rilievo.token');
    if (token) {
      this.restoreSession(token);
    }
  }

  protected login(): void {
    this.clearMessages();
    this.isAuthenticating.set(true);

    this.api.login(this.loginEmail, this.loginPassword)
      .pipe(finalize(() => this.isAuthenticating.set(false)))
      .subscribe({
        next: (session) => {
          this.completeSession(session);
          this.infoMessage.set(`Signed in as ${session.user.name}.`);
          this.loadWorkspace();
        },
        error: () => {
          this.errorMessage.set('Login failed. Use the seeded credentials and try again.');
        },
      });
  }

  protected logout(): void {
    localStorage.removeItem('rilievo.token');
    this.session.set(null);
    this.dashboard.set(null);
    this.lookups.set(null);
    this.projects.set([]);
    this.permits.set([]);
    this.contractors.set([]);
    this.diaryEntries.set([]);
    this.issues.set([]);
    this.selectedProject.set(null);
    this.infoMessage.set('Signed out. Rilievo is ready for the next walkthrough.');
  }

  protected chooseDemo(email: string): void {
    this.loginEmail = email;
    this.loginPassword = 'Rilievo123!';
  }

  protected filteredProjects(): ProjectSummary[] {
    const search = this.projectSearch.trim().toLowerCase();

    return this.projects().filter((project) => {
      const matchesSearch =
        !search ||
        project.name.toLowerCase().includes(search) ||
        project.code.toLowerCase().includes(search) ||
        project.city.toLowerCase().includes(search);

      const matchesStatus = !this.projectStatusFilter || project.status === this.projectStatusFilter;

      return matchesSearch && matchesStatus;
    });
  }

  protected openProject(projectId: number): void {
    const token = this.session()?.token;
    if (!token) {
      return;
    }

    this.api.project(token, projectId).subscribe({
      next: (project) => {
        this.selectedProject.set(project);
        this.focusProjectContext(project.id);
      },
      error: () => {
        this.errorMessage.set('Could not load the project detail.');
      },
    });
  }

  protected setProjectStatus(project: ProjectSummary, status: string): void {
    this.mutateProject(project.id, { status }, `Updated ${project.code} to ${status}.`);
  }

  protected setPermitStatus(permit: Permit, status: string): void {
    const token = this.session()?.token;
    if (!token) {
      return;
    }

    this.api.updatePermit(token, permit.id, { status }).subscribe({
      next: () => {
        this.infoMessage.set(`Permit ${permit.reference_code} is now ${status}.`);
        this.loadWorkspace(permit.project_id);
      },
      error: (error) => this.handleRequestError(error, 'The permit status could not be updated.'),
    });
  }

  protected setContractorStatus(contractor: Contractor, status: string): void {
    const token = this.session()?.token;
    if (!token) {
      return;
    }

    this.api.updateContractor(token, contractor.id, { compliance_status: status }).subscribe({
      next: () => {
        this.infoMessage.set(`${contractor.name} is now ${status}.`);
        this.loadWorkspace(contractor.project_id ?? this.selectedProject()?.id);
      },
      error: (error) => this.handleRequestError(error, 'The contractor record could not be updated.'),
    });
  }

  protected setIssueStatus(issue: Issue, status: string): void {
    const token = this.session()?.token;
    if (!token) {
      return;
    }

    this.api.updateIssue(token, issue.id, { status }).subscribe({
      next: () => {
        this.infoMessage.set(`Issue "${issue.title}" moved to ${status}.`);
        this.loadWorkspace(issue.project_id);
      },
      error: (error) => this.handleRequestError(error, 'The issue status could not be updated.'),
    });
  }

  protected createProject(): void {
    const token = this.session()?.token;
    if (!token) {
      return;
    }

    this.clearMessages();
    this.isLoading.set(true);

    this.api.createProject(token, this.projectForm)
      .pipe(finalize(() => this.isLoading.set(false)))
      .subscribe({
        next: (project) => {
          this.infoMessage.set(`Created ${project.code} and added it to the portfolio radar.`);
          this.projectForm = this.buildProjectForm(project.manager_id);
          this.loadWorkspace(project.id);
        },
        error: (error) => this.handleRequestError(error, 'The project could not be created.'),
      });
  }

  protected createPermit(): void {
    const token = this.session()?.token;
    if (!token) {
      return;
    }

    this.clearMessages();
    this.isLoading.set(true);

    const payload: PermitPayload = {
      ...this.permitForm,
      submitted_at: this.permitForm.submitted_at || null,
      approved_at: this.permitForm.approved_at || null,
    };

    this.api.createPermit(token, payload)
      .pipe(finalize(() => this.isLoading.set(false)))
      .subscribe({
        next: (permit) => {
          this.infoMessage.set(`Registered permit ${permit.reference_code}.`);
          this.permitForm = this.buildPermitForm(permit.project_id, permit.owner_id);
          this.loadWorkspace(permit.project_id);
        },
        error: (error) => this.handleRequestError(error, 'The permit record could not be created.'),
      });
  }

  protected createContractor(): void {
    const token = this.session()?.token;
    if (!token) {
      return;
    }

    this.clearMessages();
    this.isLoading.set(true);

    const payload: ContractorPayload = {
      ...this.contractorForm,
      insurance_expires_on: this.contractorForm.insurance_expires_on || null,
      last_audit_at: this.contractorForm.last_audit_at || null,
    };

    this.api.createContractor(token, payload)
      .pipe(finalize(() => this.isLoading.set(false)))
      .subscribe({
        next: (contractor) => {
          this.infoMessage.set(`Added ${contractor.name} to the contractor roster.`);
          this.contractorForm = this.buildContractorForm(contractor.project_id ?? 0);
          this.loadWorkspace(contractor.project_id ?? this.selectedProject()?.id);
        },
        error: (error) => this.handleRequestError(error, 'The contractor record could not be created.'),
      });
  }

  protected createIssue(): void {
    const token = this.session()?.token;
    if (!token) {
      return;
    }

    this.clearMessages();
    this.isLoading.set(true);

    const payload: IssuePayload = {
      ...this.issueForm,
      assignee_name: this.issueForm.assignee_name || null,
      due_date: this.issueForm.due_date || null,
    };

    this.api.createIssue(token, payload)
      .pipe(finalize(() => this.isLoading.set(false)))
      .subscribe({
        next: (issue) => {
          this.infoMessage.set(`Logged issue "${issue.title}".`);
          this.issueForm = this.buildIssueForm(issue.project_id);
          this.loadWorkspace(issue.project_id);
        },
        error: (error) => this.handleRequestError(error, 'The issue could not be created.'),
      });
  }

  protected createDiaryEntry(): void {
    const token = this.session()?.token;
    if (!token) {
      return;
    }

    this.clearMessages();
    this.isLoading.set(true);

    const payload: SiteDiaryPayload = {
      ...this.diaryForm,
      blockers: this.diaryForm.blockers || null,
      safety_note: this.diaryForm.safety_note || null,
    };

    this.api.createDiaryEntry(token, payload)
      .pipe(finalize(() => this.isLoading.set(false)))
      .subscribe({
        next: (entry) => {
          this.infoMessage.set(`Logged diary entry for ${entry.entry_date}.`);
          this.diaryForm = this.buildDiaryForm(entry.project_id);
          this.loadWorkspace(entry.project_id);
        },
        error: (error) => this.handleRequestError(error, 'The site diary entry could not be saved.'),
      });
  }

  protected managers(): UserSummary[] {
    return (this.lookups()?.users ?? []).filter((user) =>
      ['PROJECT_MANAGER', 'SITE_COORDINATOR'].includes(user.role)
    );
  }

  protected permitOwners(): UserSummary[] {
    return (this.lookups()?.users ?? []).filter((user) =>
      ['PROJECT_MANAGER', 'COMPLIANCE_OFFICER'].includes(user.role)
    );
  }

  protected projectOptions(): ProjectSummary[] {
    return this.projects();
  }

  protected formatCurrency(value: number | string): string {
    return new Intl.NumberFormat('en-IT', {
      style: 'currency',
      currency: 'EUR',
      maximumFractionDigits: 0,
    }).format(this.toNumber(value));
  }

  protected formatDate(value: string | null | undefined): string {
    if (!value) {
      return '-';
    }

    return new Intl.DateTimeFormat('en-GB', {
      day: '2-digit',
      month: 'short',
      year: 'numeric',
    }).format(new Date(value));
  }

  protected budgetRatio(project: ProjectSummary | ProjectDetail | null): number {
    if (!project) {
      return 0;
    }

    const total = this.toNumber(project.budget_total);
    const spent = this.toNumber(project.budget_spent);
    return total > 0 ? Math.min(100, Math.round((spent / total) * 100)) : 0;
  }

  protected toneForStatus(value: string): string {
    const normalized = value.toUpperCase();
    if (['APPROVED', 'CLEAR', 'COMPLIANT', 'ACTIVE', 'RESOLVED', 'COMPLETED'].includes(normalized)) {
      return 'positive';
    }
    if (['BLOCKED', 'CRITICAL', 'EXPIRED', 'AT_RISK', 'OPEN'].includes(normalized)) {
      return 'critical';
    }
    if (['IN_REVIEW', 'SUBMITTED', 'IN_PROGRESS', 'REVIEW', 'PENDING'].includes(normalized)) {
      return 'warning';
    }

    return 'neutral';
  }

  protected trackById(index: number, item: { id: number }): number {
    return item.id;
  }

  private restoreSession(token: string): void {
    this.api.session(token).subscribe({
      next: (session) => {
        this.completeSession(session);
        this.loadWorkspace();
      },
      error: () => {
        localStorage.removeItem('rilievo.token');
      },
    });
  }

  private completeSession(session: AuthSession): void {
    this.session.set(session);
    localStorage.setItem('rilievo.token', session.token);
  }

  private loadWorkspace(selectedProjectId?: number): void {
    const token = this.session()?.token;
    if (!token) {
      return;
    }

    this.isLoading.set(true);
    this.clearMessages(false);

    forkJoin({
      dashboard: this.api.dashboard(token),
      lookups: this.api.lookups(token),
      projects: this.api.projects(token),
      permits: this.api.permits(token),
      contractors: this.api.contractors(token),
      diaryEntries: this.api.diaryEntries(token),
      issues: this.api.issues(token),
    })
      .pipe(finalize(() => this.isLoading.set(false)))
      .subscribe({
        next: ({ dashboard, lookups, projects, permits, contractors, diaryEntries, issues }) => {
          this.dashboard.set(dashboard);
          this.lookups.set(lookups);
          this.projects.set(projects);
          this.permits.set(permits);
          this.contractors.set(contractors);
          this.diaryEntries.set(diaryEntries);
          this.issues.set(issues);

          this.applyLookupDefaults();

          const nextProjectId = selectedProjectId ?? this.selectedProject()?.id ?? projects[0]?.id;
          if (nextProjectId) {
            this.openProject(nextProjectId);
          } else {
            this.selectedProject.set(null);
          }
        },
        error: (error) => this.handleRequestError(error, 'Rilievo could not load the latest project data.'),
      });
  }

  private mutateProject(projectId: number, payload: Partial<ProjectPayload>, successMessage: string): void {
    const token = this.session()?.token;
    if (!token) {
      return;
    }

    this.api.updateProject(token, projectId, payload).subscribe({
      next: () => {
        this.infoMessage.set(successMessage);
        this.loadWorkspace(projectId);
      },
      error: (error) => this.handleRequestError(error, 'The project update could not be saved.'),
    });
  }

  private applyLookupDefaults(): void {
    const lookups = this.lookups();
    if (!lookups) {
      return;
    }

    const manager = this.managers()[0];
    const owner = this.permitOwners()[0];
    const projectId = this.selectedProject()?.id ?? lookups.projects[0]?.id ?? 0;

    if (!this.projectForm.manager_id && manager) {
      this.projectForm.manager_id = manager.id;
    }

    if (!this.permitForm.owner_id && owner) {
      this.permitForm.owner_id = owner.id;
    }

    this.focusProjectContext(projectId);
  }

  private focusProjectContext(projectId: number): void {
    if (!projectId) {
      return;
    }

    this.permitForm.project_id = projectId;
    this.contractorForm.project_id = projectId;
    this.issueForm.project_id = projectId;
    this.diaryForm.project_id = projectId;
  }

  private handleRequestError(error: { status?: number }, fallbackMessage: string): void {
    if (error?.status === 401) {
      this.logout();
      this.errorMessage.set('Your session expired. Sign in again to continue.');
      return;
    }

    this.errorMessage.set(fallbackMessage);
  }

  private clearMessages(clearInfo = true): void {
    this.errorMessage.set('');
    if (clearInfo) {
      this.infoMessage.set('');
    }
  }

  private buildProjectForm(managerId = 0): ProjectPayload {
    return {
      code: this.generateCode('RLV'),
      name: '',
      client_name: '',
      city: '',
      status: 'PLANNING',
      permit_status: 'IN_REVIEW',
      progress_percent: 18,
      budget_total: 2400000,
      budget_spent: 0,
      start_date: this.dateOffset(-5),
      target_date: this.dateOffset(120),
      manager_id: managerId,
      summary: '',
    };
  }

  private buildPermitForm(projectId = 0, ownerId = 0): PermitPayload {
    return {
      project_id: projectId,
      owner_id: ownerId,
      permit_name: '',
      authority: '',
      reference_code: this.generateCode('PMT'),
      status: 'IN_REVIEW',
      due_date: this.dateOffset(14),
      submitted_at: this.dateOffset(-2),
      approved_at: null,
      notes: '',
    };
  }

  private buildContractorForm(projectId = 0): ContractorPayload {
    return {
      project_id: projectId,
      name: '',
      trade: '',
      contact_name: '',
      contact_email: '',
      compliance_status: 'PENDING',
      insurance_expires_on: this.dateOffset(60),
      worker_count: 8,
      last_audit_at: this.dateOffset(-10),
    };
  }

  private buildIssueForm(projectId = 0): IssuePayload {
    return {
      project_id: projectId,
      category: 'Compliance',
      priority: 'HIGH',
      status: 'OPEN',
      title: '',
      description: '',
      assignee_name: '',
      due_date: this.dateOffset(5),
    };
  }

  private buildDiaryForm(projectId = 0): SiteDiaryPayload {
    return {
      project_id: projectId,
      entry_date: this.dateOffset(0),
      weather: 'Clear',
      workforce_count: 14,
      completed_work: '',
      blockers: '',
      safety_note: '',
    };
  }

  private generateCode(prefix: string): string {
    const now = new Date();
    const year = String(now.getFullYear()).slice(-2);
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    return `${prefix}-${year}${month}${day}${hours}${minutes}`;
  }

  private dateOffset(days: number): string {
    const value = new Date();
    value.setDate(value.getDate() + days);
    return value.toISOString().slice(0, 10);
  }

  private toNumber(value: number | string | null | undefined): number {
    if (typeof value === 'number') {
      return value;
    }

    return Number(value ?? 0);
  }
}
