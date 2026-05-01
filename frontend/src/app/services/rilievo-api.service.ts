import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable, map } from 'rxjs';
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
} from '../models/rilievo.models';

@Injectable({
  providedIn: 'root',
})
export class RilievoApiService {
  private readonly baseUrl = '/api';

  constructor(private readonly http: HttpClient) {}

  login(email: string, password: string): Observable<AuthSession> {
    return this.http.post<AuthSession>(`${this.baseUrl}/auth/login`, { email, password });
  }

  session(token: string): Observable<AuthSession> {
    return this.http.get<UserSummary>(`${this.baseUrl}/auth/me`, {
      headers: this.buildHeaders(token),
    }).pipe(map((user) => ({ token, user })));
  }

  dashboard(token: string): Observable<DashboardSummary> {
    return this.http.get<DashboardSummary>(`${this.baseUrl}/dashboard`, {
      headers: this.buildHeaders(token),
    });
  }

  lookups(token: string): Observable<LookupData> {
    return this.http.get<LookupData>(`${this.baseUrl}/lookups`, {
      headers: this.buildHeaders(token),
    });
  }

  projects(token: string): Observable<ProjectSummary[]> {
    return this.http.get<ProjectSummary[]>(`${this.baseUrl}/projects`, {
      headers: this.buildHeaders(token),
    });
  }

  project(token: string, projectId: number): Observable<ProjectDetail> {
    return this.http.get<ProjectDetail>(`${this.baseUrl}/projects/${projectId}`, {
      headers: this.buildHeaders(token),
    });
  }

  createProject(token: string, payload: ProjectPayload): Observable<ProjectSummary> {
    return this.http.post<ProjectSummary>(`${this.baseUrl}/projects`, payload, {
      headers: this.buildHeaders(token),
    });
  }

  updateProject(token: string, projectId: number, payload: Partial<ProjectPayload>): Observable<ProjectSummary> {
    return this.http.patch<ProjectSummary>(`${this.baseUrl}/projects/${projectId}`, payload, {
      headers: this.buildHeaders(token),
    });
  }

  permits(token: string): Observable<Permit[]> {
    return this.http.get<Permit[]>(`${this.baseUrl}/permits`, {
      headers: this.buildHeaders(token),
    });
  }

  createPermit(token: string, payload: PermitPayload): Observable<Permit> {
    return this.http.post<Permit>(`${this.baseUrl}/permits`, payload, {
      headers: this.buildHeaders(token),
    });
  }

  updatePermit(token: string, permitId: number, payload: Partial<PermitPayload>): Observable<Permit> {
    return this.http.patch<Permit>(`${this.baseUrl}/permits/${permitId}`, payload, {
      headers: this.buildHeaders(token),
    });
  }

  contractors(token: string): Observable<Contractor[]> {
    return this.http.get<Contractor[]>(`${this.baseUrl}/contractors`, {
      headers: this.buildHeaders(token),
    });
  }

  createContractor(token: string, payload: ContractorPayload): Observable<Contractor> {
    return this.http.post<Contractor>(`${this.baseUrl}/contractors`, payload, {
      headers: this.buildHeaders(token),
    });
  }

  updateContractor(token: string, contractorId: number, payload: Partial<ContractorPayload>): Observable<Contractor> {
    return this.http.patch<Contractor>(`${this.baseUrl}/contractors/${contractorId}`, payload, {
      headers: this.buildHeaders(token),
    });
  }

  diaryEntries(token: string): Observable<SiteDiaryEntry[]> {
    return this.http.get<SiteDiaryEntry[]>(`${this.baseUrl}/site-diary`, {
      headers: this.buildHeaders(token),
    });
  }

  createDiaryEntry(token: string, payload: SiteDiaryPayload): Observable<SiteDiaryEntry> {
    return this.http.post<SiteDiaryEntry>(`${this.baseUrl}/site-diary`, payload, {
      headers: this.buildHeaders(token),
    });
  }

  issues(token: string): Observable<Issue[]> {
    return this.http.get<Issue[]>(`${this.baseUrl}/issues`, {
      headers: this.buildHeaders(token),
    });
  }

  createIssue(token: string, payload: IssuePayload): Observable<Issue> {
    return this.http.post<Issue>(`${this.baseUrl}/issues`, payload, {
      headers: this.buildHeaders(token),
    });
  }

  updateIssue(token: string, issueId: number, payload: Partial<IssuePayload>): Observable<Issue> {
    return this.http.patch<Issue>(`${this.baseUrl}/issues/${issueId}`, payload, {
      headers: this.buildHeaders(token),
    });
  }

  private buildHeaders(token: string): HttpHeaders {
    return new HttpHeaders({
      Authorization: `Bearer ${token}`,
    });
  }
}
