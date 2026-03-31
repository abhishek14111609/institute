<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Student;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StudentController extends Controller
{
    /**
     * Display a listing of batches assigned to the teacher.
     */
    public function index()
    {
        $teacher = auth()->user()->teacher;
        $batches = $teacher->batches()
            ->select('batches.*')
            ->with('class')
            ->get();

        $batches->each(function ($batch) {
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
        return view('teacher.batches.index', compact('batches'));
    }

    /**
     * Display students of a specific batch.
     */
    public function batchStudents(Batch $batch)
    {
        $teacher = auth()->user()->teacher;

        if (!$teacher->batches->contains($batch)) {
            abort(403, 'Unauthorized access to this batch.');
        }

        $students = \App\Models\Student::query()
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
            ->with(['user', 'batches.class'])
            ->get();
        return view('teacher.batches.students', compact('batch', 'students'));
    }

    /**
     * Display the student portfolio (Sports Journey).
     */
    public function show(Student $student)
    {
        $teacher = auth()->user()->teacher;

        $teacherBatches = $teacher->batches()->pluck('batches.id')->toArray();
        $studentBatchIds = $student->batches()->pluck('batches.id')->push($student->batch_id)->filter()->unique()->toArray();

        if (empty(array_intersect($teacherBatches, $studentBatchIds))) {
            $isParticipantInCoachedEvent = $student->events()
                ->where('coach_id', $teacher->id)
                ->exists();

            if (!$isParticipantInCoachedEvent) {
                abort(403, 'Unauthorized access to this student profile.');
            }
        }

        $student->load([
            'user',
            'batches.class',
            'batch.class',
            'events' => function ($q) {
                $q->orderBy('event_date', 'desc');
            }
        ]);

        // Attendance stats for the last 6 months
        $sixMonthsAgo = now()->subMonths(6);
        $attendances = $student->attendances()
            ->where('attendance_date', '>=', $sixMonthsAgo)
            ->orderBy('attendance_date', 'desc')
            ->get();

        $attendanceSummary = [
            'present' => $attendances->where('status', 'present')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'late' => $attendances->where('status', 'late')->count(),
            'total' => $attendances->count(),
        ];

        $attendanceSummary['percentage'] = $attendanceSummary['total'] > 0
            ? round(($attendanceSummary['present'] / $attendanceSummary['total']) * 100, 1)
            : 0;

        return view('teacher.students.show', compact('student', 'attendances', 'attendanceSummary'));
    }
}
