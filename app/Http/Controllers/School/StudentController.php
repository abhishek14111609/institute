<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Exports\StudentsExport;
use App\Exports\StudentsImportTemplateExport;
use App\Imports\StudentsImport;
use App\Models\Student;
use App\Models\Batch;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Services\StudentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    public function statement(Student $student)
    {
        $student->load(['user', 'school', 'batch']);
        $ledger = $this->studentService->getStudentLedger($student);
        $school = auth()->user()->school;

        $pdf = Pdf::loadView('school.students.statement_pdf', compact('student', 'ledger', 'school'));

        $fontPath = public_path('fonts/FreeSans');
        $pdf->getDomPDF()
            ->getFontMetrics()
            ->setFontFamily('FreeSans', ['normal' => $fontPath]);

        return $pdf->stream("Statement-{$student->roll_number}.pdf");
    }

    public function __construct(private StudentService $studentService) {}

    public function index(Request $request)
    {
        $query = Student::with(['user', 'batch', 'batches']);

        /** @var string|null $search */
        $search = $request->input('search');
        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        /** @var int|null $batchId */
        $batchId = $request->input('batch_id');
        if ($batchId) {
            $query->where(function ($q) use ($batchId) {
                $q->where('batch_id', $batchId)
                    ->orWhereHas('batches', function ($sq) use ($batchId) {
                        $sq->where('batches.id', $batchId);
                    });
            });
        }

        $students = $query->latest()->paginate(15);
        $batches = Batch::with(['class', 'subject'])->active()->get();

        return view('school.students.index', compact('students', 'batches'));
    }

    public function create()
    {
        $batches = Batch::with(['class', 'subject.level'])->active()->get();
        $courses = \App\Models\Course::active()->get();
        $feePlans = \App\Models\FeePlan::where('is_active', true)->get();
        $rollMeta = $this->studentService->nextRollNumberMeta();

        return view('school.students.create', compact('batches', 'courses', 'feePlans', 'rollMeta'));
    }

    public function store(StoreStudentRequest $request)
    {
        try {
            $this->studentService->createStudent($request->validated());

            return redirect()->route('school.students.index')
                ->with('success', auth()->user()->school->institute_type === 'sport' ? 'Athlete registered successfully.' : 'Student created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error creating: ' . $e->getMessage());
        }
    }

    public function show(Student $student)
    {
        $student->load(['user', 'batch', 'batches', 'fees', 'attendances']);
        $stats = $this->studentService->getStudentStats($student);

        return view('school.students.show', compact('student', 'stats'));
    }

    public function edit(Student $student)
    {
        $batches = Batch::with(['class', 'subject.level'])->active()->get();
        $courses = \App\Models\Course::active()->get();
        $feePlans = \App\Models\FeePlan::where('is_active', true)->get();

        return view('school.students.edit', compact('student', 'batches', 'courses', 'feePlans'));
    }

    public function update(UpdateStudentRequest $request, Student $student)
    {
        try {
            $this->studentService->updateStudent($student, $request->validated());

            return redirect()->route('school.students.index')
                ->with('success', 'Student updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating student: ' . $e->getMessage());
        }
    }

    public function destroy(Student $student)
    {
        try {
            $this->studentService->deleteStudent($student);

            return redirect()->route('school.students.index')
                ->with('success', 'Student deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting student: ' . $e->getMessage());
        }
    }

    public function export()
    {
        $students = Student::with(['user', 'batch', 'batches'])->latest()->get();
        $filename = 'students_export_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new StudentsExport($students), $filename);
    }

    public function importTemplate()
    {
        return Excel::download(new StudentsImportTemplateExport(), 'students_import_template.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'import_file' => ['required', 'file', 'mimes:xlsx,xls'],
        ]);

        $import = new StudentsImport($this->studentService, auth()->user()->school_id);
        Excel::import($import, $request->file('import_file'));

        $imported = $import->getImportedCount();
        $failedRows = $import->getFailedRows();

        if (!empty($failedRows)) {
            $errorSummary = collect($failedRows)
                ->take(5)
                ->map(fn($row) => 'Row ' . $row['row'] . ': ' . $row['error'])
                ->implode(' | ');

            return redirect()->route('school.students.index')
                ->with('success', $imported . ' students imported successfully.')
                ->with('error', count($failedRows) . ' rows failed. ' . $errorSummary);
        }

        return redirect()->route('school.students.index')
            ->with('success', $imported . ' students imported successfully.');
    }

    /**
     * Restore a soft-deleted student.
     */
    public function restore(int $id)
    {
        $student = Student::withTrashed()->findOrFail($id);

        $student->restore();
        optional($student->user)->restore();

        return redirect()->route('school.students.index')
            ->with('success', 'Student restored successfully.');
    }
}
