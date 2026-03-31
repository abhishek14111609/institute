<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\StudentService;
use App\Services\AttendanceService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private StudentService $studentService,
        private AttendanceService $attendanceService
    ) {
    }

    public function index()
    {
        $student = auth()->user()->student;
        $student->load([
            'batches' => function ($q) {
                $q->wherePivot('is_active', true)
                    ->with(['class', 'teachers.user']);
            },
            'course'
        ]);

        $student->batches->each(function ($batch) {
            $batch->students_count = \App\Models\Student::query()
                ->where('students.is_active', true)
                ->where(function ($query) use ($batch) {
                    $query->where('students.batch_id', $batch->id)
                        ->orWhereExists(function ($subQuery) use ($batch) {
                            $subQuery->selectRaw('1')
                                ->from('batch_student')
                                ->whereColumn('batch_student.student_id', 'students.id')
                                ->where('batch_student.batch_id', $batch->id)
                                ->where('batch_student.is_active', true);
                        });
                })
                ->distinct('students.id')
                ->count('students.id');
        });

        $stats = $this->studentService->getStudentStats($student);

        $recentAttendance = $student->attendances()
            ->with('batch')
            ->latest('attendance_date')
            ->take(5)
            ->get();

        $ledger = $this->studentService->getStudentLedger($student);

        // Fetch Upcoming Sessions (All active batches)
        $upcomingSessions = $student->batches;

        // Fetch Upcoming Events
        $upcomingEvents = \App\Models\SportsEvent::where('event_date', '>=', now())
            ->whereHas('participants', function ($q) use ($student) {
                $q->where('student_id', $student->id);
            })
            ->with(['coach.user'])
            ->orderBy('event_date', 'asc')
            ->take(3)
            ->get();

        $balance = $stats['pending_fees'];
        $paidFees = $stats['paid_fees'];
        $attendanceRate = $stats['attendance_percentage'];
        $presentDays = $student->attendances()->where('status', 'present')->count();

        // Calculate Athlete Score (Gamification)
        $athleteScore = round(($attendanceRate + min(100, $stats['events_participated'] * 10)) / 2);

        return view('student.dashboard', compact(
            'student',
            'stats',
            'balance',
            'paidFees',
            'attendanceRate',
            'presentDays',
            'recentAttendance',
            'ledger',
            'upcomingSessions',
            'upcomingEvents',
            'athleteScore'
        ));
    }
}
