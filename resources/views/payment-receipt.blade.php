@extends('layouts.app')

@section('title', 'إيصال الدفع - بوابة طلاب الجامعة الأردنية')
@section('page-title', 'إيصال الدفع')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-file-invoice me-2"></i>
                    إيصال الدفع
                </h5>
                <div>
                    <button class="btn btn-success me-2" onclick="printReceipt()">
                        <i class="fas fa-print me-1"></i>
                        طباعة
                    </button>
                    <a href="{{ route('student.payment-receipt.download', $payment->id) }}" class="btn btn-primary" target="_blank">
                        <i class="fas fa-download me-1"></i>
                        تنزيل PDF
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Receipt Header -->
                <div class="receipt-header text-center mb-4">
                    <h3 class="text-primary">الجامعة الأردنية</h3>
                    <h4>إيصال الدفع</h4>
                    <p class="text-muted">Jordan University - Payment Receipt</p>
                </div>

                <!-- Receipt Information -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="receipt-info-card p-3 bg-light rounded">
                            <h6><i class="fas fa-user me-2"></i> معلومات الطالب</h6>
                            <p><strong>الاسم:</strong> {{ $student->name }}</p>
                            <p><strong>رقم الطالب:</strong> {{ $student->student_id }}</p>
                            <p><strong>الكلية:</strong> {{ $student->college }}</p>
                            <p><strong>التخصص:</strong> {{ $student->major }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="receipt-info-card p-3 bg-light rounded">
                            <h6><i class="fas fa-file-invoice me-2"></i> معلومات الإيصال</h6>
                            <p><strong>رقم الإيصال:</strong> {{ $payment->receipt_number }}</p>
                            <p><strong>تاريخ الدفع:</strong> {{ $payment->created_at->format('Y-m-d H:i') }}</p>
                            <p><strong>السنة الأكاديمية:</strong> {{ $payment->academic_year }}</p>
                            <p><strong>الفصل الدراسي:</strong> {{ $payment->semester_name }}</p>
                        </div>
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>الوصف</th>
                                        <th class="text-end">المبلغ (دينار)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>رسوم التعلم</td>
                                        <td class="text-end">{{ number_format($payment->tuition_amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>رسوم الفصل الدراسي</td>
                                        <td class="text-end">{{ number_format($payment->semester_fees_amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>رسوم المعالجة</td>
                                        <td class="text-end">{{ number_format($payment->processing_fee, 2) }}</td>
                                    </tr>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td class="text-start"><strong>الإجمالي</strong></td>
                                        <td class="text-end"><strong>{{ number_format($payment->amount_paid, 2) }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Payment Method Information -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="receipt-info-card p-3 bg-light rounded">
                            <h6><i class="fas fa-credit-card me-2"></i> طريقة الدفع</h6>
                            @if($payment->payment_method === 'credit_card')
                                <p>
                                    <strong>النوع:</strong> 
                                    @if($payment->card_type === 'local')
                                        بطاقة محلية
                                    @else
                                        بطاقة دولية
                                    @endif
                                </p>
                            @else
                                <p><strong>الطريقة:</strong> {{ $payment->payment_method }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="receipt-info-card p-3 bg-light rounded">
                            <h6><i class="fas fa-info-circle me-2"></i> الحالة</h6>
                            <span class="badge bg-{{ $payment->status_badge_class }}">
                                @switch($payment->status)
                                    @case('completed')
                                        مكتملة
                                        @break
                                    @case('pending')
                                        معلقة
                                        @break
                                    @case('failed')
                                        فاشلة
                                        @break
                                    @case('refunded')
                                        مستردة
                                        @break
                                    @default
                                        {{ $payment->status }}
                                @endswitch
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="receipt-footer text-center mt-5 pt-4 border-top">
                    <p class="text-muted">تم إصدار هذا الإيصال بشكل إلكتروني ولا حاجة للتوقيع الورقي</p>
                    <p class="text-muted">ملاحظة: هذا الإيصال مقبول كإثبات دفع رسمي</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function printReceipt() {
    window.print();
}
</script>

<style>
.receipt-info-card {
    border: 1px solid #dee2e6;
}

@media print {
    .btn {
        display: none;
    }
    
    .card-header {
        display: none;
    }
    
    body {
        background-color: white !important;
    }
    
    .card {
        box-shadow: none !important;
        border: none !important;
    }
}
</style>
@endsection