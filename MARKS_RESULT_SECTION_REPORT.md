# Marks and Result Section Implementation Report

Date: 2026-04-03
Project: Classes Management System
Scope: Functional implementation plan only (no code changes)

## 1) Current System Snapshot

Based on the existing project structure:
- Role panels are available for Super Admin, School Admin, Teacher, and Student.
- Attendance workflow is fully available for School and Teacher panels.
- Event result workflow exists (rank, participation status, notes) in Teacher events.
- School reports currently cover finance and attendance.
- There is no dedicated academic exam/marks/result module yet.

Important note from current behavior:
- Academic exam appears as a fee category, but academic marksheets and term results are not yet implemented as a separate module.

## 2) Target Requirement Interpreted

You asked for:
- Exams conducted weekly and bi-weekly.
- Result generation monthly.
- Consolidated result flow for three months.
- Panel-wise workflow explanation.

This report defines a full operational model for exactly that.

## 3) Academic Calendar and Result Cycles

### A. Weekly Exam Cycle
- Frequency: Every week (for selected subjects).
- Coverage: Topic-based tests.
- Weight in monthly result: 40% (recommended).

### B. Bi-Weekly Exam Cycle
- Frequency: Every 2 weeks.
- Coverage: Cumulative test of recent two weeks.
- Weight in monthly result: 60% (recommended).

### C. Monthly Result Cycle
- End of each month:
  - Combine weekly + bi-weekly exam marks.
  - Publish monthly subject-wise result and overall percentage.

### D. Three-Month (Quarterly) Result Cycle
- After 3 monthly cycles:
  - Generate consolidated result card.
  - Show trend (Month 1 vs Month 2 vs Month 3).
  - Add promotion/risk indicator.

## 4) Panel-Wise Workflow

## 4.1 Super Admin Panel (Governance)
Purpose:
- Platform-wide academic configuration and audit.

Actions:
- Define global grading schema templates (A+, A, B, etc.).
- Define pass rules and attendance threshold policies.
- Monitor school-wise exam activity and publishing compliance.
- View audit logs for mark edits and republishing.

Reports:
- School-wise monthly publishing status.
- Outlier report (high failures, no exam conducted, delayed result publication).

## 4.2 School Admin Panel (Setup + Approval)
Purpose:
- Owns exam planning, policies, and final publication approval.

Actions:
- Create exam plans by class/batch:
  - Weekly plans.
  - Bi-weekly plans.
- Define subjects and max marks per subject.
- Set grading boundaries for school.
- Lock exam calendar dates.
- Approve or reject teacher-submitted marks before publication.
- Publish monthly results and quarterly consolidated reports.

Reports:
- Class-wise exam completion report.
- Teacher submission pending report.
- Monthly topper and low-performance list.
- Quarter trend report (3 months).

## 4.3 Teacher Panel (Execution + Marks Entry)
Purpose:
- Conduct exams and submit marks.

Actions:
- View assigned classes, subjects, exam schedule.
- Conduct weekly and bi-weekly exams.
- Enter marks student-wise and subject-wise.
- Save as draft and submit for approval.
- Add remarks for each student.
- Correct marks only within edit window or with approval workflow.

Teacher-side validations (recommended):
- Marks cannot exceed max marks.
- Marks cannot be negative.
- Absent option allowed (AB).
- Late entry flagged.

Reports:
- Subject performance analytics.
- Student difficulty heatmap.
- Marks entry completion percentage.

## 4.4 Student Panel (Consumption)
Purpose:
- View personal performance and progress.

Actions:
- View upcoming exam schedule.
- View monthly result (subject-wise marks, grade, rank if enabled).
- View 3-month consolidated result.
- Download result card PDF.
- View teacher remarks and improvement suggestions.

Student insights:
- Weak subjects.
- Month-over-month progress graph.
- Attendance impact on performance.

## 5) Suggested Data Model (Functional Design)

No code changes are done now. This is a design reference for future implementation.

Core entities:
- ExamPlan
  - school_id, class_id, batch_id, exam_type (weekly/biweekly), exam_name, exam_date, status
- ExamSubject
  - exam_plan_id, subject_id, max_marks, pass_marks, weightage
- ExamMark
  - exam_plan_id, subject_id, student_id, marks_obtained, is_absent, remarks, entered_by, approved_by
- MonthlyResult
  - school_id, class_id, batch_id, student_id, month, year, total_obtained, total_max, percentage, grade, result_status
- QuarterlyResult
  - school_id, class_id, batch_id, student_id, quarter_label, m1_percentage, m2_percentage, m3_percentage, final_percentage, grade

Status workflow:
- draft -> submitted -> approved -> published -> locked

## 6) Result Calculation Logic

Recommended formulas:

1. Subject Monthly Score
- Monthly Subject Score = (Average Weekly Score * 0.40) + (Average Bi-Weekly Score * 0.60)

2. Monthly Overall Percentage
- Monthly Percentage = (Sum of all obtained subject marks / Sum of all max subject marks) * 100

3. Quarterly Percentage (3 Months)
- Quarterly Percentage = (Month1 Percentage + Month2 Percentage + Month3 Percentage) / 3

4. Grade Mapping Example
- 90 to 100 = A+
- 80 to 89 = A
- 70 to 79 = B+
- 60 to 69 = B
- 50 to 59 = C
- 40 to 49 = D
- below 40 = F

5. Result Status Example
- Pass: no core subject failed and overall >= pass threshold
- Conditional: overall pass but one core subject below pass marks
- Fail: overall below threshold or multiple core failures

## 7) Monthly and Three-Month Operational Timeline

Week 1:
- Weekly exams conducted.
- Marks entry by teachers.

Week 2:
- Weekly + Bi-weekly exam conducted.
- Marks submitted.

Week 3:
- Weekly exams conducted.
- Pending mark corrections closed.

Week 4:
- Weekly + Bi-weekly exam conducted.
- School admin approves all marks.
- Monthly result published.

After Month 3:
- System generates consolidated 3-month report.
- School admin publishes quarterly result cards.

## 8) Controls and Compliance

Recommended controls:
- Edit window for marks (for example, 48 hours).
- Mandatory reason for any post-approval changes.
- Complete audit trail for every mark update.
- Publish lock to avoid accidental modifications.
- Permission boundaries:
  - Teacher: own classes/subjects only.
  - School Admin: full school scope.
  - Super Admin: monitor and policy only.

## 9) Standard Reports to Include

School Admin reports:
- Monthly class summary report.
- Subject pass/fail ratio report.
- Teacher submission punctuality report.
- Quarterly trend and risk students report.

Teacher reports:
- Subject-wise score distribution.
- Student-level progress report.
- Absent in exam report.

Student reports:
- Monthly marksheet.
- Quarterly consolidated result card.
- Improvement recommendation sheet.

## 10) Functional Gaps Observed in Current System

Current project already has attendance scoring and event-result style updates, but for academics these are still missing:
- Dedicated exam plan setup.
- Subject-wise marks entry module.
- Monthly and quarterly academic result engine.
- Marks approval and publish workflow.
- Academic marksheet download module.

## 11) Implementation Sequence (Recommended)

Phase 1: Foundation
- Exam plans, subjects, exam schedules.
- Teacher marks entry draft/submit.

Phase 2: Approval and Publication
- School admin approval workflow.
- Monthly result generation and publishing.

Phase 3: Quarterly Intelligence
- 3-month consolidation.
- Trend analytics and risk flags.
- Student downloadable result cards.

## 12) Conclusion

This panel-wise design fits your requirement:
- Weekly + bi-weekly exams.
- Monthly result generation.
- Three-month consolidated reporting.

No application code has been changed. This file is a functional blueprint for your next implementation phase.
