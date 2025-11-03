<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Enrollment;
use App\Models\AcademicRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentsExport;

class StudentAdminController extends Controller
{
    /**
     * Display a listing of students with filtering and search.
     */
    public function index(Request $request)
    {
        $query = Student::withCount('enrollments', 'academicRecords')
            ->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Filter by college
        if ($request->filled('college')) {
            $query->where('college', $request->college);
        }

        // Filter by major
        if ($request->filled('major')) {
            $query->where('major', $request->major);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by academic year
        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        // Filter by GPA range
        if ($request->filled('gpa_min')) {
            $query->where('cumulative_gpa', '>=', $request->gpa_min);
        }

        if ($request->filled('gpa_max')) {
            $query->where('cumulative_gpa', '<=', $request->gpa_max);
        }

        $students = $query->paginate(20)->withQueryString();

        // Get filter options
        $colleges = Student::distinct()->pluck('college')->sort();
        $majors = Student::distinct()->pluck('major')->sort();
        $academicYears = Student::distinct()->pluck('academic_year')->sort();

        return view('admin.students.index', compact('students', 'colleges', 'majors', 'academicYears'));
    }

    /**
     * Show the form for creating a new student.
     */
    public function create()
    {
        $colleges = [
            'كلية الهندسة',
            'كلية العلوم',
            'كلية الطب',
            'كلية الصيدلة',
            'كلية العلوم الطبية المساعدة',
            'كلية الأعمال',
            'كلية الزراعة',
            'كلية الفنون الجميلة',
        ];

        return view('admin.students.create', compact('colleges'));
    }

    /**
     * Store a newly created student.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'student_id' => 'required|string|unique:students,student_id',
            'email' => 'required|email|unique:students,email',
            'password' => 'required|string|min:8|confirmed',
            'passport_number' => 'nullable|string|unique:students,passport_number',
            'college' => 'required|string',
            'major' => 'required|string',
            'academic_year' => 'required|string',
            'total_credit_hours' => 'nullable|numeric|min:0',
            'cumulative_gpa' => 'nullable|numeric|min:0|max:4',
            'successful_credit_hours' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,graduated,suspended',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['username'] = Student::generateUsername($validated['name'], $validated['student_id']);

        $student = Student::create($validated);

        return redirect()->route('admin.students.index')
            ->with('success', 'تم إضافة الطالب بنجاح. اسم المستخدم: ' . $student->username);
    }

    /**
     * Display the specified student.
     */
    public function show(Student $student)
    {
        $student->load(['enrollments.course', 'academicRecords.course', 'equivalentCourses.equivalentCourse']);

        return view('admin.students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified student.
     */
    public function edit(Student $student)
    {
        $colleges = [
            'كلية الهندسة',
            'كلية العلوم',
            'كلية الطب',
            'كلية الصيدلة',
            'كلية العلوم الطبية المساعدة',
            'كلية الأعمال',
            'كلية الزراعة',
            'كلية الفنون الجميلة',
        ];

        return view('admin.students.edit', compact('student', 'colleges'));
    }

    /**
     * Update the specified student.
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('students')->ignore($student->id)
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'passport_number' => [
                'nullable',
                'string',
                Rule::unique('students')->ignore($student->id)
            ],
            'college' => 'required|string',
            'major' => 'required|string',
            'academic_year' => 'required|string',
            'total_credit_hours' => 'nullable|numeric|min:0',
            'cumulative_gpa' => 'nullable|numeric|min:0|max:4',
            'successful_credit_hours' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,graduated,suspended',
        ]);

        // Remove password from validated data if not provided
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $student->update($validated);

        return redirect()->route('admin.students.index')
            ->with('success', 'تم تحديث بيانات الطالب بنجاح.');
    }

    /**
     * Remove the specified student.
     */
    public function destroy(Student $student)
    {
        try {
            $student->delete();
            return redirect()->route('admin.students.index')
                ->with('success', 'تم حذف الطالب بنجاح.');
        } catch (\Exception $e) {
            return redirect()->route('admin.students.index')
                ->with('error', 'حدث خطأ أثناء حذف الطالب.');
        }
    }

    /**
     * Export students to Excel.
     */
    public function export(Request $request)
    {
        return Excel::download(new StudentsExport($request->all()), 'students_' . now()->format('Y-m-d') . '.xlsx');
    }

    /**
     * Bulk actions for students.
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:activate,deactivate,graduate,suspend,delete,export',
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
        ]);

        $students = Student::whereIn('id', $validated['student_ids']);

        switch ($validated['action']) {
            case 'activate':
                $students->update(['status' => 'active']);
                $message = 'تم تفعيل الطلاب المحددين.';
                break;

            case 'deactivate':
                $students->update(['status' => 'inactive']);
                $message = 'تم إلغاء تفعيل الطلاب المحددين.';
                break;

            case 'graduate':
                $students->update(['status' => 'graduated']);
                $message = 'تم تسجيل الطلاب كمتخرجين.';
                break;

            case 'suspend':
                $students->update(['status' => 'suspended']);
                $message = 'تم إيقاف الطلاب المحددين.';
                break;

            case 'delete':
                try {
                    $students->delete();
                    $message = 'تم حذف الطلاب المحددين.';
                } catch (\Exception $e) {
                    return response()->json(['error' => 'حدث خطأ أثناء حذف الطلاب.'], 500);
                }
                break;

            case 'export':
                return Excel::download(new StudentsExport(['student_ids' => $validated['student_ids']]), 'selected_students_' . now()->format('Y-m-d') . '.xlsx');
        }

        return response()->json(['success' => $message]);
    }

    /**
     * Update student academic information.
     */
    public function updateAcademicInfo(Request $request, Student $student)
    {
        $validated = $request->validate([
            'total_credit_hours' => 'nullable|numeric|min:0',
            'cumulative_gpa' => 'nullable|numeric|min:0|max:4',
            'successful_credit_hours' => 'nullable|numeric|min:0',
        ]);

        $student->update($validated);

        return redirect()->route('admin.students.show', $student)
            ->with('success', 'تم تحديث المعلومات الأكاديمية بنجاح.');
    }

    /**
     * Generate academic transcript for student.
     */
    public function generateTranscript(Student $student)
    {
        $student->load(['enrollments.course', 'academicRecords.course']);

        $pdf = \PDF::loadView('admin.students.transcript', compact('student'));

        return $pdf->download('transcript_' . $student->student_id . '.pdf');
    }

    /**
     * Search students via AJAX.
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $students = Student::where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('student_id', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'student_id', 'email', 'college', 'major']);

        return response()->json($students);
    }
}
