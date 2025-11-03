<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\AcademicRecord;
use App\Models\EquivalentCourse;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AcademicManagementController extends Controller
{
    /**
     * Display a listing of courses.
     */
    public function coursesIndex(Request $request)
    {
        $query = Course::withCount('enrollments')
            ->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('course_code', 'like', "%{$search}%")
                  ->orWhere('course_name', 'like', "%{$search}%")
                  ->orWhere('course_name_en', 'like', "%{$search}%");
            });
        }

        // Filter by credit hours
        if ($request->filled('credit_hours')) {
            $query->where('credit_hours', $request->credit_hours);
        }

        // Filter by level
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        $courses = $query->paginate(20)->withQueryString();

        return view('admin.academic.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new course.
     */
    public function coursesCreate()
    {
        $levels = ['100', '200', '300', '400', '500', '600'];

        return view('admin.academic.courses.create', compact('levels'));
    }

    /**
     * Store a newly created course.
     */
    public function coursesStore(Request $request)
    {
        $validated = $request->validate([
            'course_code' => 'required|string|unique:courses,course_code',
            'course_name' => 'required|string|max:255',
            'course_name_en' => 'nullable|string|max:255',
            'credit_hours' => 'required|integer|min:1|max:10',
            'level' => 'required|in:100,200,300,400,500,600',
            'prerequisites' => 'nullable|string',
            'description' => 'nullable|string',
            'description_en' => 'nullable|string',
        ]);

        Course::create($validated);

        return redirect()->route('admin.academic.courses.index')
            ->with('success', 'تم إضافة المادة بنجاح.');
    }

    /**
     * Show the form for editing a course.
     */
    public function coursesEdit(Course $course)
    {
        $levels = ['100', '200', '300', '400', '500', '600'];

        return view('admin.academic.courses.edit', compact('course', 'levels'));
    }

    /**
     * Update the specified course.
     */
    public function coursesUpdate(Request $request, Course $course)
    {
        $validated = $request->validate([
            'course_code' => [
                'required',
                'string',
                Rule::unique('courses')->ignore($course->id)
            ],
            'course_name' => 'required|string|max:255',
            'course_name_en' => 'nullable|string|max:255',
            'credit_hours' => 'required|integer|min:1|max:10',
            'level' => 'required|in:100,200,300,400,500,600',
            'prerequisites' => 'nullable|string',
            'description' => 'nullable|string',
            'description_en' => 'nullable|string',
        ]);

        $course->update($validated);

        return redirect()->route('admin.academic.courses.index')
            ->with('success', 'تم تحديث بيانات المادة بنجاح.');
    }

    /**
     * Remove the specified course.
     */
    public function coursesDestroy(Course $course)
    {
        try {
            $course->delete();
            return redirect()->route('admin.academic.courses.index')
                ->with('success', 'تم حذف المادة بنجاح.');
        } catch (\Exception $e) {
            return redirect()->route('admin.academic.courses.index')
                ->with('error', 'لا يمكن حذف المادة لأنها مستخدمة في تسجيلات.');
        }
    }

    /**
     * Display a listing of enrollments.
     */
    public function enrollmentsIndex(Request $request)
    {
        $query = Enrollment::with(['student', 'course'])
            ->orderBy('created_at', 'desc');

        // Filter by student
        if ($request->filled('student_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('student_id', 'like', "%{$request->student_id}%")
                  ->orWhere('name', 'like', "%{$request->student_id}%");
            });
        }

        // Filter by course
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        // Filter by academic year
        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        // Filter by semester
        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        $enrollments = $query->paginate(20)->withQueryString();

        $courses = Course::pluck('course_name', 'id');
        $academicYears = Enrollment::distinct()->pluck('academic_year');

        return view('admin.academic.enrollments.index', compact('enrollments', 'courses', 'academicYears'));
    }

    /**
     * Show the form for creating a new enrollment.
     */
    public function enrollmentsCreate()
    {
        $students = Student::active()->orderBy('name')->get();
        $courses = Course::orderBy('course_name')->get();
        $academicYears = ['2023-2024', '2024-2025', '2025-2026'];

        return view('admin.academic.enrollments.create', compact('students', 'courses', 'academicYears'));
    }

    /**
     * Store a newly created enrollment.
     */
    public function enrollmentsStore(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'academic_year' => 'required|string',
            'semester' => 'required|in:الأول,الثاني,الصيفي',
            'status' => 'required|in:registered,dropped,completed',
        ]);

        // Check for duplicate enrollment
        $existingEnrollment = Enrollment::where([
            'student_id' => $validated['student_id'],
            'course_id' => $validated['course_id'],
            'academic_year' => $validated['academic_year'],
            'semester' => $validated['semester'],
        ])->first();

        if ($existingEnrollment) {
            return redirect()->back()
                ->with('error', 'الطالب مسجل مسبقاً في هذه المادة في هذا الفصل.');
        }

        Enrollment::create($validated);

        return redirect()->route('admin.academic.enrollments.index')
            ->with('success', 'تم إضافة التسجيل بنجاح.');
    }

    /**
     * Show the form for editing an enrollment.
     */
    public function enrollmentsEdit(Enrollment $enrollment)
    {
        $students = Student::orderBy('name')->get();
        $courses = Course::orderBy('course_name')->get();
        $academicYears = ['2023-2024', '2024-2025', '2025-2026'];

        return view('admin.academic.enrollments.edit', compact('enrollment', 'students', 'courses', 'academicYears'));
    }

    /**
     * Update the specified enrollment.
     */
    public function enrollmentsUpdate(Request $request, Enrollment $enrollment)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'academic_year' => 'required|string',
            'semester' => 'required|in:الأول,الثاني,الصيفي',
            'status' => 'required|in:registered,dropped,completed',
        ]);

        // Check for duplicate enrollment (excluding current enrollment)
        $existingEnrollment = Enrollment::where([
            'student_id' => $validated['student_id'],
            'course_id' => $validated['course_id'],
            'academic_year' => $validated['academic_year'],
            'semester' => $validated['semester'],
        ])->where('id', '!=', $enrollment->id)->first();

        if ($existingEnrollment) {
            return redirect()->back()
                ->with('error', 'الطالب مسجل مسبقاً في هذه المادة في هذا الفصل.');
        }

        $enrollment->update($validated);

        return redirect()->route('admin.academic.enrollments.index')
            ->with('success', 'تم تحديث التسجيل بنجاح.');
    }

    /**
     * Remove the specified enrollment.
     */
    public function enrollmentsDestroy(Enrollment $enrollment)
    {
        $enrollment->delete();

        return redirect()->route('admin.academic.enrollments.index')
            ->with('success', 'تم حذف التسجيل بنجاح.');
    }

    /**
     * Display academic records management.
     */
    public function academicRecordsIndex(Request $request)
    {
        $query = AcademicRecord::with(['student', 'course'])
            ->orderBy('created_at', 'desc');

        // Filter by student
        if ($request->filled('student_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('student_id', 'like', "%{$request->student_id}%")
                  ->orWhere('name', 'like', "%{$request->student_id}%");
            });
        }

        // Filter by course
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        // Filter by grade range
        if ($request->filled('grade_min')) {
            $query->where('grade', '>=', $request->grade_min);
        }

        if ($request->filled('grade_max')) {
            $query->where('grade', '<=', $request->grade_max);
        }

        $academicRecords = $query->paginate(20)->withQueryString();

        $courses = Course::pluck('course_name', 'id');

        return view('admin.academic.records.index', compact('academicRecords', 'courses'));
    }

    /**
     * Show the form for creating a new academic record.
     */
    public function academicRecordsCreate()
    {
        $students = Student::active()->orderBy('name')->get();
        $courses = Course::orderBy('course_name')->get();

        return view('admin.academic.records.create', compact('students', 'courses'));
    }

    /**
     * Store a newly created academic record.
     */
    public function academicRecordsStore(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'grade' => 'required|numeric|min:0|max:100',
            'credit_hours' => 'required|integer|min:1|max:10',
            'academic_year' => 'required|string',
            'semester' => 'required|in:الأول,الثاني,الصيفي',
        ]);

        DB::beginTransaction();
        try {
            $academicRecord = AcademicRecord::create($validated);

            // Update student's GPA and credit hours
            $this->updateStudentAcademicStats($validated['student_id']);

            DB::commit();

            return redirect()->route('admin.academic.records.index')
                ->with('success', 'تم إضافة الدرجات بنجاح.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حفظ الدرجات.');
        }
    }

    /**
     * Show the form for editing an academic record.
     */
    public function academicRecordsEdit(AcademicRecord $academicRecord)
    {
        $students = Student::orderBy('name')->get();
        $courses = Course::orderBy('course_name')->get();

        return view('admin.academic.records.edit', compact('academicRecord', 'students', 'courses'));
    }

    /**
     * Update the specified academic record.
     */
    public function academicRecordsUpdate(Request $request, AcademicRecord $academicRecord)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'grade' => 'required|numeric|min:0|max:100',
            'credit_hours' => 'required|integer|min:1|max:10',
            'academic_year' => 'required|string',
            'semester' => 'required|in:الأول,الثاني,الصيفي',
        ]);

        DB::beginTransaction();
        try {
            $academicRecord->update($validated);

            // Update student's GPA and credit hours
            $this->updateStudentAcademicStats($validated['student_id']);

            DB::commit();

            return redirect()->route('admin.academic.records.index')
                ->with('success', 'تم تحديث الدرجات بنجاح.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث الدرجات.');
        }
    }

    /**
     * Remove the specified academic record.
     */
    public function academicRecordsDestroy(AcademicRecord $academicRecord)
    {
        $studentId = $academicRecord->student_id;

        $academicRecord->delete();

        // Update student's GPA and credit hours
        $this->updateStudentAcademicStats($studentId);

        return redirect()->route('admin.academic.records.index')
            ->with('success', 'تم حذف الدرجات بنجاح.');
    }

    /**
     * Bulk grade entry.
     */
    public function bulkGradeEntry(Request $request)
    {
        if ($request->isMethod('get')) {
            $courses = Course::orderBy('course_name')->get();
            $academicYears = AcademicRecord::distinct()->pluck('academic_year');

            return view('admin.academic.records.bulk-entry', compact('courses', 'academicYears'));
        }

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'academic_year' => 'required|string',
            'semester' => 'required|in:الأول,الثاني,الصيفي',
            'grades' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->grades as $enrollmentId => $grade) {
                if (!empty($grade) && is_numeric($grade)) {
                    $enrollment = Enrollment::find($enrollmentId);
                    if ($enrollment) {
                        // Create or update academic record
                        AcademicRecord::updateOrCreate(
                            [
                                'student_id' => $enrollment->student_id,
                                'course_id' => $enrollment->course_id,
                                'academic_year' => $request->academic_year,
                                'semester' => $request->semester,
                            ],
                            [
                                'grade' => $grade,
                                'credit_hours' => $enrollment->course->credit_hours,
                            ]
                        );
                    }
                }
            }

            DB::commit();

            return redirect()->route('admin.academic.records.index')
                ->with('success', 'تم حفظ الدرجات بنجاح.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حفظ الدرجات.');
        }
    }

    /**
     * Equivalent courses management.
     */
    public function equivalentCoursesIndex(Request $request)
    {
        $query = EquivalentCourse::with(['course', 'equivalentCourse'])
            ->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('course', function ($q) use ($search) {
                $q->where('course_code', 'like', "%{$search}%")
                  ->orWhere('course_name', 'like', "%{$search}%");
            })->orWhereHas('equivalentCourse', function ($q) use ($search) {
                $q->where('course_code', 'like', "%{$search}%")
                  ->orWhere('course_name', 'like', "%{$search}%");
            });
        }

        $equivalentCourses = $query->paginate(20)->withQueryString();

        return view('admin.academic.equivalent-courses.index', compact('equivalentCourses'));
    }

    /**
     * Show the form for creating a new equivalent course mapping.
     */
    public function equivalentCoursesCreate()
    {
        $courses = Course::orderBy('course_name')->get();

        return view('admin.academic.equivalent-courses.create', compact('courses'));
    }

    /**
     * Store a newly created equivalent course mapping.
     */
    public function equivalentCoursesStore(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'equivalent_course_id' => 'required|different:course_id|exists:courses,id',
            'description' => 'nullable|string',
        ]);

        // Check for existing mapping
        $existing = EquivalentCourse::where([
            'course_id' => $validated['course_id'],
            'equivalent_course_id' => $validated['equivalent_course_id'],
        ])->first();

        if ($existing) {
            return redirect()->back()
                ->with('error', 'تم تحديد هذه المعادلة مسبقاً.');
        }

        EquivalentCourse::create($validated);

        return redirect()->route('admin.academic.equivalent-courses.index')
            ->with('success', 'تم إضافة معادلة المواد بنجاح.');
    }

    /**
     * Remove the specified equivalent course mapping.
     */
    public function equivalentCoursesDestroy(EquivalentCourse $equivalentCourse)
    {
        $equivalentCourse->delete();

        return redirect()->route('admin.academic.equivalent-courses.index')
            ->with('success', 'تم حذف معادلة المواد بنجاح.');
    }

    /**
     * Update student's academic statistics (GPA and credit hours).
     */
    private function updateStudentAcademicStats($studentId)
    {
        $records = AcademicRecord::where('student_id', $studentId)->get();

        $totalCreditHours = 0;
        $totalGradePoints = 0;

        foreach ($records as $record) {
            $totalCreditHours += $record->credit_hours;
            $gradePoints = $record->grade * $record->credit_hours;
            $totalGradePoints += $gradePoints;
        }

        $cumulativeGPA = $totalCreditHours > 0 ? round($totalGradePoints / $totalCreditHours, 2) : 0;

        Student::where('id', $studentId)->update([
            'total_credit_hours' => $totalCreditHours,
            'cumulative_gpa' => $cumulativeGPA,
            'successful_credit_hours' => $records->where('grade', '>=', 50)->sum('credit_hours'),
        ]);
    }

    /**
     * Get enrollments for bulk grade entry.
     */
    public function getEnrollmentsForGradeEntry(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'academic_year' => 'required|string',
            'semester' => 'required|in:الأول,الثاني,الصيفي',
        ]);

        $enrollments = Enrollment::with('student')
            ->where('course_id', $request->course_id)
            ->where('academic_year', $request->academic_year)
            ->where('semester', $request->semester)
            ->get();

        return response()->json($enrollments);
    }
}
