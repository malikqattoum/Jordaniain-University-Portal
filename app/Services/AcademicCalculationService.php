<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Enrollment;
use App\Models\AcademicRecord;

class AcademicCalculationService
{
    /**
     * Calculate semester statistics for a student
     */
    public function calculateSemesterStats(Student $student, string $academicYear, int $semester)
    {
        $enrollments = Enrollment::with('course')
            ->where('student_id', $student->id)
            ->where('academic_year', $academicYear)
            ->where('semester', $semester)
            ->get();

        if ($enrollments->isEmpty()) {
            return null;
        }

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
            ->where(function($query) use ($academicYear, $semester) {
                $query->where('academic_year', '<', $academicYear)
                      ->orWhere(function($q) use ($academicYear, $semester) {
                          $q->where('academic_year', $academicYear)
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
        $semesterStatus = $this->determineSemesterStatus($semesterGpa, $cumulativeGpa);

        return [
            'semester_credit_hours' => round($semesterCreditHours, 1),
            'semester_gpa' => round($semesterGpa, 2),
            'cumulative_credit_hours' => round($cumulativeCreditHours, 1),
            'cumulative_gpa' => round($cumulativeGpa, 2),
            'successful_credit_hours' => round($totalSuccessfulHours, 1),
            'semester_status' => $semesterStatus
        ];
    }

    /**
     * Determine semester status based on GPA
     */
    private function determineSemesterStatus(float $semesterGpa, float $cumulativeGpa): string
    {
        if ($semesterGpa >= 3.75) {
            return 'excellent';
        } elseif ($semesterGpa >= 3.5) {
            return 'honor';
        } elseif ($cumulativeGpa < 2.0) {
            return 'probation';
        } elseif ($cumulativeGpa < 2.5) {
            return 'warning';
        }

        return 'regular';
    }

    /**
     * Convert grade letter to grade points
     */
    public function getGradePoints(string $grade): float
    {
        $gradePoints = [
            'A+' => 4.0,
            'A' => 4.0,
            'A-' => 3.7,
            'B+' => 3.5,
            'B' => 3.0,
            'B-' => 2.7,
            'C+' => 2.5,
            'C' => 2.0,
            'C-' => 1.7,
            'D+' => 1.5,
            'D' => 1.0,
            'F' => 0.0,
            'P' => 0.0, // Pass (no grade points)
            'NP' => 0.0, // No Pass
            'W' => 0.0, // Withdrawal
            'I' => 0.0  // Incomplete
        ];

        return $gradePoints[$grade] ?? 0.0;
    }

    /**
     * Check if grade is passing
     */
    public function isPassingGrade(string $grade): bool
    {
        $passingGrades = ['A+', 'A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'C-', 'D+', 'D', 'P'];
        return in_array($grade, $passingGrades);
    }

    /**
     * Calculate required credit hours for Law College (72 hours total)
     */
    public function calculateProgressToGraduation(Student $student): array
    {
        $totalRequiredHours = 141; // Law College requirement
        // Get equivalent courses credit hours
        $equivalentHours = $student->equivalentCourses ? $student->equivalentCourses->sum('credit_hours') : 0;
        // If equivalentCourses is a relation, eager load and sum
        if (method_exists($student, 'equivalentCourses')) {
            $equivalentHours = $student->equivalentCourses()->sum('credit_hours');
        }
        $completedHours = $student->successful_credit_hours + $equivalentHours;
        $remainingHours = max(0, $totalRequiredHours - $completedHours);
        $progressPercentage = min(100, ($completedHours / $totalRequiredHours) * 100);

        return [
            'total_required_hours' => $totalRequiredHours,
            'completed_hours' => $completedHours,
            'remaining_hours' => $remainingHours,
            'progress_percentage' => round($progressPercentage, 1)
        ];
    }
}
