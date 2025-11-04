<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Enrollment;
use App\Models\AcademicRecord;
use App\Services\AcademicCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AcademicResultsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:student');
    }

    public function index(Request $request)
    {
        $student = Auth::guard('student')->user();

        // Get available academic years for the student
        $academicYears = Enrollment::where('student_id', $student->id)
            ->distinct()
            ->pluck('academic_year')
            ->sort()
            ->reverse();

        $selectedYear = $request->get('year', $academicYears->first());
        $selectedSemester = $request->get('semester', 2);

        // Get semesters for selected year
        $semesters = Enrollment::where('student_id', $student->id)
            ->where('academic_year', $selectedYear)
            ->distinct()
            ->pluck('semester')
            ->sort();

        // Get enrollments for selected year and semester
        $enrollments = Enrollment::with('course')
            ->where('student_id', $student->id)
            ->where('academic_year', $selectedYear)
            ->where('semester', $selectedSemester)
            ->get();

        // Get academic record for selected year and semester
        $academicRecord = AcademicRecord::where('student_id', $student->id)
            ->where('academic_year', $selectedYear)
            ->where('semester', $selectedSemester)
            ->first();

        // Calculate semester statistics if no record exists
        if (!$academicRecord && $enrollments->count() > 0) {
            $academicRecord = $this->calculateSemesterStats($student, $selectedYear, $selectedSemester, $enrollments);
        }

        $equivalentCourses = $student->equivalentCourses()->get();

        return view('academic-results', compact(
            'student',
            'enrollments',
            'academicRecord',
            'academicYears',
            'semesters',
            'selectedYear',
            'selectedSemester',
            'equivalentCourses'
        ));
    }

    private function calculateSemesterStats($student, $year, $semester, $enrollments)
    {
        $semesterCreditHours = 0;
        $totalGradePoints = 0;
        $passedCreditHours = 0;

        foreach ($enrollments as $enrollment) {
            $creditHours = $enrollment->course->credit_hours;
            $semesterCreditHours += $creditHours;

            if ($enrollment->is_passed && $enrollment->grade_points > 0) {
                $totalGradePoints += ($enrollment->grade_points * $creditHours);
                $passedCreditHours += $creditHours;
            }
        }

        $semesterGpa = $semesterCreditHours > 0 ? $totalGradePoints / $semesterCreditHours : 0;

        // Calculate cumulative stats
        $previousRecords = AcademicRecord::where('student_id', $student->id)
            ->where(function($query) use ($year, $semester) {
                $query->where('academic_year', '<', $year)
                      ->orWhere(function($q) use ($year, $semester) {
                          $q->where('academic_year', $year)
                            ->where('semester', '<', $semester);
                      });
            })->get();

        $cumulativeCreditHours = $previousRecords->sum('semester_credit_hours') + $semesterCreditHours;
        $totalSuccessfulHours = $previousRecords->sum('successful_credit_hours') + $passedCreditHours;

        $totalCumulativePoints = $previousRecords->sum(function($record) {
            return $record->semester_gpa * $record->semester_credit_hours;
        }) + ($semesterGpa * $semesterCreditHours);

        $cumulativeGpa = $cumulativeCreditHours > 0 ? $totalCumulativePoints / $cumulativeCreditHours : 0;

        // Determine semester status
        $semesterStatus = 'regular';
        if ($semesterGpa >= 3.75) {
            $semesterStatus = 'excellent';
        } elseif ($semesterGpa >= 3.5) {
            $semesterStatus = 'honor';
        } elseif ($semesterGpa < 2.0) {
            $semesterStatus = 'probation';
        } elseif ($semesterGpa < 2.5) {
            $semesterStatus = 'warning';
        }

        return (object) [
            'semester_credit_hours' => round($semesterCreditHours, 1),
            'semester_gpa' => round($semesterGpa, 2),
            'cumulative_credit_hours' => round($cumulativeCreditHours, 1),
            'cumulative_gpa' => round($cumulativeGpa, 2),
            'successful_credit_hours' => round($totalSuccessfulHours, 1),
            'semester_status' => $semesterStatus
        ];
    }

    public function dashboard(AcademicCalculationService $academicService)
    {
        $student = Auth::guard('student')->user();
        $graduationProgress = $academicService->calculateProgressToGraduation($student);

        // Get equivalent courses for the student
        $equivalentCourses = $student->equivalentCourses()->get();

        // Get current semester registered subjects (enrolled status)
        $currentEnrollments = Enrollment::with('course')
            ->where('student_id', $student->id)
            ->where('status', 'enrolled')
            ->get();

        return view('dashboard', compact('student', 'graduationProgress', 'equivalentCourses', 'currentEnrollments'));
    }
}
