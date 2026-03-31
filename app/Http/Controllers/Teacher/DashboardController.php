<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\SportsEvent;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $teacher = auth()->user()->teacher;

        $batches = $teacher->batches()
            ->select('batches.*')
            ->with(['class'])
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

        $totalStudents = $batches->sum('students_count');

        $batchIds = $batches->pluck('id');

        // Calculate health score for each batch
        $batches->each(function ($batch) use ($batchIds) {
            $totalAtt = \App\Models\Attendance::where('batch_id', $batch->id)->count();
            $present = \App\Models\Attendance::where('batch_id', $batch->id)->where('status', 'present')->count();
            $attRate = $totalAtt > 0 ? ($present / $totalAtt) * 100 : 0;

            // Participation health: % of students in batch who have participated in at least one event
            $studentIds = \App\Models\Student::query()
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
                ->pluck('students.id');
            $participatedCount = \App\Models\EventParticipant::whereIn('student_id', $studentIds)
                ->where('participation_status', 'participated')
                ->distinct('student_id')
                ->count();
            $partRate = $studentIds->count() > 0 ? ($participatedCount / $studentIds->count()) * 100 : 0;

            $batch->health_score = round(($attRate + $partRate) / 2);
        });

        $totalAttendances = \App\Models\Attendance::whereIn('batch_id', $batchIds)->count();
        $presentAttendances = \App\Models\Attendance::whereIn('batch_id', $batchIds)->where('status', 'present')->count();
        $avgAttendance = $totalAttendances > 0 ? round(($presentAttendances / $totalAttendances) * 100) : 0;

        $todaySessions = $batches->sortBy('start_time');

        $upcomingEvents = $teacher->coachedEvents()
            ->where('event_date', '>=', now())
            ->orderBy('event_date', 'asc')
            ->take(5)
            ->get();



        return view('teacher.dashboard', compact(
            'teacher',
            'batches',
            'totalStudents',
            'upcomingEvents',
            'avgAttendance',
            'todaySessions'
        ));
    }
}
