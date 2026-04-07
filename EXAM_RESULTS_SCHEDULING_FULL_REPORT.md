# Exam Scheduling and Result Generation - Full Report

Date: 2026-04-07
Project: Classes Management System
Mode: Analysis and blueprint only (no code changes)

## 1. Executive Summary

Your current system does not yet have a dedicated academic exam module (exam timetable, subject-wise marks entry, monthly result publishing, quarterly consolidation).

What exists now:
- Attendance workflow (school + teacher).
- Sports event participant result updates (rank/status/notes).
- Finance module includes an `exam` fee type, but this is billing, not academic result processing.

What is needed:
- A full academic exam lifecycle covering:
  - Exam scheduling.
  - Marks entry.
  - Approval and publishing.
  - Monthly result generation.
  - 3-month consolidated result cards.

## 2. Current Capability Audit

### 2.1 Routes and modules
- No dedicated school routes for academic exam planning, marks entry, or result publishing.
- Existing result-like operation is limited to sports event participants.

### 2.2 Data model status
- No exam/result migration tables were found in the project.
- No academic models found for exam plans, papers, marksheets, or result summaries.

### 2.3 UI status
- No school-admin exam planner screen.
- No teacher academic marks-entry screen.
- No student academic result card screen.

## 3. Target Functional Scope

A complete academic exam and result subsystem should support:

1. Exam Scheduling
- Weekly tests.
- Bi-weekly tests.
- Monthly exam windows.
- Optional term/quarterly exams.

2. Marks Entry and Validation
- Subject-wise marks.
- Absent support.
- Draft and submit flow.
- Max marks and pass marks checks.

3. Approval Workflow
- Teacher submits marks.
- School admin reviews and approves.
- Publish lock after approval.

4. Result Generation
- Monthly computed results.
- Quarterly (3-month) consolidation.
- Grade and rank logic.
- Downloadable marksheet/result card.

5. Reporting and Analytics
- Class-wise performance.
- Subject pass/fail ratio.
- At-risk student list.
- Month-over-month trend.

## 4. Recommended Panel-Wise Workflow

### 4.1 Super Admin
Purpose: Policy and governance.

Responsibilities:
- Configure default grading templates.
- Monitor school publishing compliance.
- Review audit logs on mark modifications.

### 4.2 School Admin
Purpose: Owns exam calendar and publication.

Responsibilities:
- Create exam cycles (weekly/bi-weekly/monthly).
- Assign classes, batches, subjects, and dates.
- Freeze schedule.
- Review teacher entries.
- Approve and publish results.

### 4.3 Teacher
Purpose: Execute and submit marks.

Responsibilities:
- View assigned schedules.
- Enter marks by subject and student.
- Mark absent where needed.
- Submit for approval.

### 4.4 Student
Purpose: Consume results.

Responsibilities:
- View exam schedule.
- View monthly result.
- View 3-month consolidated report.
- Download result card.

## 5. Recommended Data Design

### 5.1 `exam_schedules`
Fields (recommended):
- id, school_id, class_id, batch_id
- exam_type (weekly, biweekly, monthly, term)
- exam_name
- exam_date, start_time, end_time
- status (draft, published, closed)
- created_by
- timestamps

### 5.2 `exam_schedule_subjects`
Fields:
- id, exam_schedule_id, subject_id
- max_marks, pass_marks, weightage
- timestamps

### 5.3 `exam_marks`
Fields:
- id, school_id, exam_schedule_id, subject_id, student_id
- marks_obtained
- is_absent
- remarks
- entered_by, submitted_at
- approved_by, approved_at
- status (draft, submitted, approved)
- timestamps

### 5.4 `monthly_results`
Fields:
- id, school_id, class_id, batch_id, student_id
- month, year
- total_obtained, total_max, percentage
- grade, result_status
- published_at, published_by
- timestamps

### 5.5 `quarterly_results`
Fields:
- id, school_id, class_id, batch_id, student_id
- quarter_label
- month1_percentage, month2_percentage, month3_percentage
- final_percentage, grade, result_status
- published_at, published_by
- timestamps

## 6. Result Calculation Blueprint

Recommended formulas:

1. Subject monthly score:
- Monthly Subject Score = (Average Weekly * 0.40) + (Average Bi-Weekly * 0.60)

2. Monthly overall:
- Percentage = (Total Obtained / Total Max) * 100

3. Quarterly overall:
- Quarterly Percentage = (M1 + M2 + M3) / 3

4. Grade bands (example):
- 90-100: A+
- 80-89: A
- 70-79: B+
- 60-69: B
- 50-59: C
- 40-49: D
- <40: F

5. Result status:
- Pass: overall above threshold and no critical subject fail.
- Conditional: overall pass but one required subject fail.
- Fail: below threshold or multiple required subject fails.

## 7. Exam Scheduling Blueprint

### 7.1 Weekly scheduling
- One test/week/subject (or selected subjects).
- Low-weight formative assessment.

### 7.2 Bi-weekly scheduling
- One cumulative test every 2 weeks.
- Higher weight than weekly.

### 7.3 Monthly publication window
- Freeze marks submissions by month-end.
- Approval cutoff.
- Publish monthly results.

### 7.4 Quarterly cycle
- After month 3, consolidate and publish quarterly card.

## 8. Validation and Controls

Required controls:
- Marks range validation: 0 <= marks <= max_marks.
- Absent handling (`is_absent`) with null marks.
- Role-based access boundaries:
  - Teacher: own assignments only.
  - School admin: school-wide review/publish.
- Edit-lock after publish.
- Re-open only with reason and audit log.
- Full audit trail for every mark change.

## 9. Reports to Generate

### School Admin Reports
- Exam schedule completion report.
- Pending teacher submission report.
- Subject pass/fail distribution.
- Monthly toppers and low performers.
- Quarterly trend report.

### Teacher Reports
- Subject performance histogram.
- Batch-wise improvement trend.
- Students requiring remediation.

### Student Reports
- Monthly marksheet.
- Quarterly consolidated result card.
- Subject weakness and teacher remarks.

## 10. Suggested Implementation Plan

### Phase 1: Foundation
- Create exam schedule and marks tables.
- Build school-admin scheduling screens.
- Build teacher marks entry screens.

### Phase 2: Approval and publication
- Build submit/approve/publish status flow.
- Build monthly result generation engine.
- Build student monthly result view.

### Phase 3: Consolidation and analytics
- Build quarterly consolidation engine.
- Build class and subject analytics dashboards.
- Build PDF result cards.

### Phase 4: Hardening
- Audit logs.
- Reopen-with-reason workflow.
- Export (Excel/PDF).

## 11. Key Gaps to Address First

1. No academic exam tables.
2. No exam scheduling UI/routes.
3. No marks entry and approval workflow.
4. No monthly/quarterly result computation and publishing.

## 12. Final Recommendation

Proceed with a dedicated academic exam-result subsystem (separate from attendance and sports-event results), beginning with:
1. Schedule + marks tables.
2. School-admin scheduling UI.
3. Teacher marks entry + approval flow.
4. Monthly result generation.
5. Quarterly consolidation.

This gives you a reliable, auditable, and scalable exam-to-result pipeline aligned with your requirement.
