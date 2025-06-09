@extends('layouts.app')

@section('title', 'نتائج الطالب النهائية - بوابة طلاب الجامعة الأردنية')
@section('page-title', 'نتائج الطالب النهائية')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-filter me-2"></i>
                    تصفية النتائج
                </h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('academic-results') }}">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="year" class="form-label">السنة الدراسية</label>
                            <select name="year" id="year" class="form-select" onchange="this.form.submit()">
                                <option value="">---</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                        {{ $year }} / {{ $year + 1 }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="semester" class="form-label">الفصل الدراسي</label>
                            <select name="semester" id="semester" class="form-select" onchange="this.form.submit()">
                                <option value="">---</option>
                                @foreach($semesters as $semester)
                                    <option value="{{ $semester }}" {{ $selectedSemester == $semester ? 'selected' : '' }}>
                                        @if($semester == 1) الأول
                                        @elseif($semester == 2) الثاني
                                        @elseif($semester == 3) الصيفي
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if($enrollments->count() > 0)
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    المواد المسجلة - {{ $selectedYear }}/{{ $selectedYear + 1 }} - الفصل
                    @if($selectedSemester == 1) الأول
                    @elseif($selectedSemester == 2) الثاني
                    @elseif($selectedSemester == 3) الصيفي
                    @endif
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>رقم المادة</th>
                                <th>اسم المادة</th>
                                <th>عدد الساعات</th>
                                <th>العلامة</th>
                                <th>النقاط</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($enrollments as $enrollment)
                            <tr>
                                <td>{{ $enrollment->course->course_code }}</td>
                                <td>{{ $enrollment->course->course_name_ar }}</td>
                                <td>{{ $enrollment->course->credit_hours }}</td>
                                <td>
                                    @if($enrollment->grade)
                                        <span class="badge bg-{{ $enrollment->is_passed ? 'success' : 'danger' }}">
                                            {{ $enrollment->getGradeInArabic() }}  {{ $enrollment->grade }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">لم تحدد</span>
                                    @endif
                                </td>
                                <td>{{ $enrollment->grade_points ?? '-' }}</td>
                                <td>
                                    @if($enrollment->is_passed)
                                        <span class="badge bg-success">ناجح</span>
                                    @elseif($enrollment->grade)
                                        <span class="badge bg-danger">راسب</span>
                                    @else
                                        <span class="badge bg-warning">قيد المراجعة</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@if($academicRecord)
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    الإحصائيات الأكاديمية
                </h5>
            </div>
            <div class="card-body">
                <div class="academic-stats">
                    <div class="stat-card">
                        <div class="stat-value">{{ $academicRecord->semester_credit_hours }}</div>
                        <div class="stat-label">الساعات الفصلية</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">{{ $academicRecord->semester_gpa }}</div>
                        <div class="stat-label">المعدل الفصلي</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">{{ $academicRecord->cumulative_credit_hours + ($equivalentCourses ? $equivalentCourses->sum('credit_hours') : 0) }}</div>
                        <div class="stat-label">الساعات التراكمية</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">{{ $academicRecord->cumulative_gpa }}</div>
                        <div class="stat-label">المعدل التراكمي</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">{{ $academicRecord->successful_credit_hours + ($equivalentCourses ? $equivalentCourses->sum('credit_hours') : 0) }}</div>
                        <div class="stat-label">س.م. بنجاح</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">
                            @if($academicRecord->semester_status == 'regular') منتظم
                            @elseif($academicRecord->semester_status == 'probation') إنذار أكاديمي
                            @elseif($academicRecord->semester_status == 'warning') تحذير
                            @elseif($academicRecord->semester_status == 'excellent') ممتاز
                            @elseif($academicRecord->semester_status == 'honor') شرف
                            @else {{ $academicRecord->semester_status }}
                            @endif
                        </div>
                        <div class="stat-label">مؤشر الفصل</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@else
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">لا توجد نتائج للفصل المحدد</h5>
                <p class="text-muted">يرجى اختيار سنة وفصل دراسي آخر</p>
            </div>
        </div>
    </div>
</div>
@endif

<style>
.academic-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1rem;
}

.stat-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 1.5rem;
    border-radius: 10px;
    text-align: center;
    border: 1px solid #dee2e6;
    transition: transform 0.3s, box-shadow 0.3s;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.stat-value {
    font-size: 1.8rem;
    font-weight: bold;
    color: #1e3c72;
    margin-bottom: 0.5rem;
}

.stat-label {
    color: #6c757d;
    font-size: 0.9rem;
    font-weight: 500;
}

.table th {
    background-color: #343a40;
    color: white;
    border: none;
}

.badge {
    font-size: 0.8rem;
    padding: 0.5rem 0.75rem;
}
</style>
@endsection
