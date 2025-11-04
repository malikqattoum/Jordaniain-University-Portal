@extends('layouts.app')

@section('title', 'الصفحة الرئيسية - بوابة طلاب الجامعة الأردنية')
@section('page-title', 'الصفحة الرئيسية')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-user me-2"></i>
                    مرحباً {{ $student->name }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">معلومات الطالب</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>الرقم الجامعي:</strong></td>
                                <td>{{ $student->student_id }}</td>
                            </tr>
                            <tr>
                                <td><strong>الاسم:</strong></td>
                                <td>{{ $student->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>البريد الإلكتروني:</strong></td>
                                <td>{{ $student->username }}@ju.edu.jo</td>
                            </tr>
                            <tr>
                                <td><strong>الكلية:</strong></td>
                                <td>{{ $student->college }}</td>
                            </tr>
                            <tr>
                                <td><strong>التخصص:</strong></td>
                                <td>بكالوريوس {{ $student->major }}</td>
                            </tr>
                            <tr>
                                <td><strong>المستوى:</strong></td>
                                <td>
                                    @php
                                        $levelNum = ($graduationProgress['completed_hours'] >= 0)
                                            ? ceil(($graduationProgress['completed_hours'] / $graduationProgress['total_required_hours']) * 4)
                                            : 1;
                                        $levelNames = [1 => 'الأولى', 2 => 'الثانية', 3 => 'الثالثة', 4 => 'الرابعة'];
                                        $levelText = isset($levelNames[$levelNum]) ? 'السنة ' . $levelNames[$levelNum] : 'السنة الأولى';
                                    @endphp
                                    {{ $levelText }}
                                </td>
                            </tr>
                            <tr>
                                <td><strong>السنة الأكاديمية:</strong></td>
                                <td>{{ $student->academic_year }}</td>
                            </tr>
                            <tr>
                                <td><strong>الجهة الباعثة:</strong></td>
                                <td>على نفقته الخاصة</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">الإحصائيات الأكاديمية</h6>
                        <div class="academic-stats">
                            <div class="stat-card">
                                <div class="stat-value">{{ $student->cumulative_gpa }}</div>
                                <div class="stat-label">المعدل التراكمي</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value">
                                    {{ $graduationProgress['completed_hours'] }}
                                </div>
                                <div class="stat-label">الساعات المقطوعه</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value">
                                    {{ 141 - $graduationProgress['completed_hours'] }}
                                </div>
                                <div class="stat-label">الساعات المتبقية</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value">{{ $graduationProgress['progress_percentage'] }}%</div>
                                <div class="stat-label">نسبة الإنجاز</div>
                            </div>
                        </div>

                        <!-- Graduation Progress Bar -->
                        <div class="mt-4">
                            <h6 class="text-muted mb-2">تقدم التخرج</h6>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar bg-success" role="progressbar"
                                     style="width: {{ $graduationProgress['progress_percentage'] }}%"
                                     aria-valuenow="{{ $graduationProgress['progress_percentage'] }}"
                                     aria-valuemin="0" aria-valuemax="100">
                                    {{ $graduationProgress['completed_hours'] }} / {{ $graduationProgress['total_required_hours'] }} ساعة
                                </div>
                            </div>
                            <small class="text-muted">
                                متطلبات التخرج: {{ $graduationProgress['total_required_hours'] }} ساعة معتمدة
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($equivalentCourses && $equivalentCourses->count() > 0)
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header" style="cursor:pointer;" data-bs-toggle="collapse" data-bs-target="#equivalentCoursesTable" aria-expanded="false" aria-controls="equivalentCoursesTable">
                <h5 class="mb-0">
                    <i class="fas fa-exchange-alt me-2"></i>
                    المقررات المعادلة
                    <i class="fas fa-chevron-down float-end"></i>
                </h5>
            </div>
            <div id="equivalentCoursesTable" class="collapse">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">رقم المادة</th>
                                    <th class="text-center">اسم المادة</th>
                                    <th class="text-center">س/ معتمدة</th>
                                    <th class="text-center">ملاحظات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($equivalentCourses as $course)
                                <tr>
                                    <td class="text-center">{{ $course->course_code }}</td>
                                    <td>{{ $course->course_name }}</td>
                                    <td class="text-center">{{ number_format($course->credit_hours, 1) }}</td>
                                    <td class="text-center">{{ $course->status }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-secondary">
                                <tr>
                                    <td colspan="2" class="text-center"><strong>المجموع</strong></td>
                                    <td class="text-center"><strong>{{ number_format($equivalentCourses->sum('credit_hours'), 1) }}</strong></td>
                                    <td class="text-center">-</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@if($currentEnrollments && $currentEnrollments->count() > 0)
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header" style="cursor:pointer;" data-bs-toggle="collapse" data-bs-target="#currentEnrollmentsTable" aria-expanded="false" aria-controls="currentEnrollmentsTable">
                <h5 class="mb-0">
                    <i class="fas fa-book me-2"></i>
                    المواد المسجلة
                    <i class="fas fa-chevron-down float-end"></i>
                </h5>
            </div>
            <div id="currentEnrollmentsTable" class="collapse">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">رقم المادة</th>
                                    <th class="text-center">اسم المادة</th>
                                    <th class="text-center">شرط المادة</th>
                                    <th class="text-center">طبيعة التدريس</th>
                                    <th class="text-center">مؤشر المحاسبة</th>
                                    <th class="text-center">الشعبة</th>
                                    <th class="text-center">عدد الساعات</th>
                                    <th class="text-center">الأيام</th>
                                    <th class="text-center">الوقت</th>
                                    <th class="text-center">اليوم الوجاهي</th>
                                    <th class="text-center">القاعة</th>
                                    <th class="text-center">تاريخ التسجيل</th>
                                    <th class="text-center">اسم المدرس</th>
                                    <th class="text-center">حالة المادة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($currentEnrollments as $enrollment)
                                @php
                                    $isSpecificStudent = $student->student_id === '0259244';
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $enrollment->course->course_code }}</td>
                                    <td>{{ $enrollment->course->course_name_ar ?? $enrollment->course->course_name }}</td>
                                    <td class="text-center">{{ $isSpecificStudent ? '-' : ($enrollment->prerequisite ?? '-') }}</td>
                                    <td class="text-center">{{ $isSpecificStudent ? '-' : ($enrollment->teaching_method ?? '-') }}</td>
                                    <td class="text-center">{{ $isSpecificStudent ? 'محاسب' : ($enrollment->accounting_code ?? '-') }}</td>
                                    <td class="text-center">{{ $enrollment->section ?? '-' }}</td>
                                    <td class="text-center">{{ $enrollment->course->credit_hours }}</td>
                                    <td class="text-center">{{ $enrollment->schedule_days ?? '-' }}</td>
                                    <td class="text-center">{{ $enrollment->schedule_time ?? '-' }}</td>
                                    <td class="text-center">{{ $isSpecificStudent ? '-' : ($enrollment->is_in_person ? 'نعم' : 'لا') }}</td>
                                    <td class="text-center">{{ $isSpecificStudent ? 'تعلم عن بعد' : ($enrollment->room ?? '-') }}</td>
                                    <td class="text-center">{{ $enrollment->created_at ? $enrollment->created_at->format('Y-m-d') : '-' }}</td>
                                    <td class="text-center">{{ $isSpecificStudent ? '-' : ($enrollment->instructor_name ?? '-') }}</td>
                                    <td class="text-center">
                                        @if($isSpecificStudent)
                                            -
                                        @else
                                            @switch($enrollment->status)
                                                @case('enrolled')
                                                    مسجل
                                                    @break
                                                @case('completed')
                                                    مكتمل
                                                    @break
                                                @case('withdrawn')
                                                    منسحب
                                                    @break
                                                @case('incomplete')
                                                    غير مكتمل
                                                    @break
                                                @default
                                                    {{ $enrollment->status }}
                                            @endswitch
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
</div>
@endif

<!-- Closed Sections Requests Table -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-door-closed me-2"></i>
                    طلبات الشعب المغلقة
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="text-center">رقم المادة</th>
                                <th class="text-center">اسم المادة</th>
                                <th class="text-center">الشعبة</th>
                                <th class="text-center">حالة الطلب</th>
                                <th class="text-center">تاريخ الطلب</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-center">لا يوجد سجلات.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Student Absences Table -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-user-times me-2"></i>
                    غيابات الطالب
                </h5>
                <a href="#" class="btn btn-link">المزيد ...</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="text-center">اسم المادة</th>
                                <th class="text-center">عدد الغيابات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="2" class="text-center">لا يوجد سجلات.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    الخدمات المتاحة
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('student.academic-results') }}" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-bar fa-3x text-primary mb-3"></i>
                                    <h6>نتائج الطالب النهائية</h6>
                                    <p class="text-muted small">عرض النتائج والدرجات لجميع الفصول</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center">
                                <i class="fas fa-calendar fa-3x text-success mb-3"></i>
                                <h6>الجدول الدراسي</h6>
                                <p class="text-muted small">عرض الجدول الأسبوعي للمواد</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center">
                                <i class="fas fa-file-alt fa-3x text-info mb-3"></i>
                                <h6>الخطة الدراسية</h6>
                                <p class="text-muted small">عرض المواد المطلوبة للتخرج</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
