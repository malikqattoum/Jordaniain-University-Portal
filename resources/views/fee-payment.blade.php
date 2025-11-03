@extends('layouts.app')

@section('title', 'تسديد الرسوم الجامعية - بوابة طلاب الجامعة الأردنية')
@section('page-title', 'تسديد الرسوم الجامعية')

@section('content')
<div class="row">
    <!-- Payment Methods Card -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-credit-card me-2"></i>
                    طرق الدفع المتاحة
                </h5>
            </div>
            <div class="card-body">
                <p class="mb-3">
                    يتم دفع الرسوم الجامعية من خلال طرق الدفع المتاحة :
                </p>
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        <strong>بطاقات الإئتمان Master Card أو Visa Card</strong>
                        <br>
                        <small class="text-muted">
                            (هذه الخدمه متاحة متاحة للرسوم المطلوبه بالدينار )
                        </small>
                    </li>
                </ul>
                <div class="alert alert-info" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>ملاحظة هامة:</strong>
                    <br>
                    عند التسديد باستخدام بطاقات الائتمان سيتم إضافة عمولة بقيمة
                    <strong>0.55%</strong> للبطاقات المحلية، و <strong>1.95%</strong> للبطاقات الدوليه
                    أضافة الى عمولة ثابتة بقيمة <strong>1.30 دينار</strong> لجميع البطاقات.
                </div>
            </div>
        </div>
    </div>

    <!-- Amount Due Card -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-calculator me-2"></i>
                    تفاصيل الرسوم المستحقة
                </h5>
            </div>
            <div class="card-body">
                @if($feeBreakdown['credit_hours'] > 0)
                    <!-- Fee Breakdown Details -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="fee-detail-item">
                                <strong>التخصص:</strong>
                                <span>{{ $feeBreakdown['major_info']['name'] }}</span>
                            </div>
                            <div class="fee-detail-item">
                                <strong>عدد الساعات الفصلية:</strong>
                                <span>{{ number_format($feeBreakdown['credit_hours'], 1) }} ساعة</span>
                            </div>
                            <div class="fee-detail-item">
                                <strong>سعر الساعة الواحدة:</strong>
                                <span>{{ number_format($feeBreakdown['hourly_rate'], 2) }} دينار</span>
                            </div>
                            <div class="fee-detail-item bg-light p-3 rounded mb-3">
                                <strong>رسوم التعلم (المصروفات الدراسية):</strong>
                                <div class="fs-5 text-primary">
                                    {{ number_format($feeBreakdown['tuition_amount'], 2) }} دينار
                                </div>
                                <small class="text-muted">
                                    ({{ number_format($feeBreakdown['credit_hours'], 1) }} ساعة × {{ number_format($feeBreakdown['hourly_rate'], 2) }} دينار)
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            @if($feeBreakdown['semester_fee_info'])
                            <div class="fee-detail-item bg-light p-3 rounded mb-3">
                                <strong>رسوم الفصل الدراسي:</strong>
                                <div class="fs-5 text-success">
                                    {{ number_format($feeBreakdown['semester_fees_amount'], 2) }} دينار
                                </div>
                                <small class="text-muted">
                                    {{ $feeBreakdown['semester_fee_info']->semester_name }}
                                </small>
                            </div>
                            @endif
                            <div class="fee-detail-item">
                                <strong>رسوم البطاقات المحلية:</strong>
                                <span>{{ number_format($feeBreakdown['payment_fees']['local']['amount'], 2) }} دينار</span>
                                <small class="text-muted d-block">
                                    ({{ number_format($feeBreakdown['payment_fees']['local']['percentage'] * 100, 2) }}% + {{ number_format($feeBreakdown['payment_fees']['local']['fixed_fee'], 2) }} دينار)
                                </small>
                            </div>
                            <div class="fee-detail-item">
                                <strong>رسوم البطاقات الدولية:</strong>
                                <span>{{ number_format($feeBreakdown['payment_fees']['international']['amount'], 2) }} دينار</span>
                                <small class="text-muted d-block">
                                    ({{ number_format($feeBreakdown['payment_fees']['international']['percentage'] * 100, 2) }}% + {{ number_format($feeBreakdown['payment_fees']['international']['fixed_fee'], 2) }} دينار)
                                </small>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Total Amount Due -->
                    <div class="row">
                        <div class="col-12">
                            <div class="total-amount-display text-center">
                                <h4 class="text-primary">
                                    <strong>المبلغ الإجمالي المستحق:</strong>
                                </h4>
                                <div class="amount-value">
                                    {{ number_format($feeBreakdown['total_amount_due'], 2) }} دينار
                                </div>
                                <small class="text-muted">
                                    (يشمل رسوم المعالجة المتوسطة)
                                </small>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        المبلغ الأساسي: {{ number_format($feeBreakdown['base_amount'], 2) }} دينار
                                        + رسوم المعالجة المتوسطة
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Buttons -->
                    <div class="row mt-4">
                        <div class="col-12 text-center">
                            <button type="button" class="btn btn-primary btn-lg me-3" onclick="proceedWithPayment('local')">
                                <i class="fas fa-credit-card me-2"></i>
                                دفع بالبطاقة المحلية
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-lg" onclick="proceedWithPayment('international')">
                                <i class="fas fa-globe me-2"></i>
                                دفع بالبطاقة الدولية
                            </button>
                        </div>
                    </div>
                @else
                    <!-- No fees due -->
                    <div class="text-center">
                        <div class="mb-3">
                            <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                        </div>
                        <h4 class="text-success">لا يوجد رسوم مطلوبة</h4>
                        <p class="text-muted">
                            المبلغ المستحق: 0.0 دينار
                        </p>
                        <p class="text-muted">
                            لا يوجد أي رسوم مطلوبة للفصل الدراسي الحالي.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Additional Information Card -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    معلومات إضافية
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">معلومات مهمة:</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-clock text-info me-2"></i>
                                متاح للدفع على مدار 24 ساعة
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-shield-alt text-success me-2"></i>
                                آمن ومشفر بالكامل
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-receipt text-primary me-2"></i>
                                ستتلقى إيصالاً إلكترونياً فوراً
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">في حالة وجود مشاكل:</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-phone text-success me-2"></i>
                                اتصل بمكتب شؤون الطلبة
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-envelope text-primary me-2"></i>
                                راسلنا عبر البريد الإلكتروني
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-question-circle text-warning me-2"></i>
                                زرنا في مكتب التسجيل
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.fee-detail-item {
    margin-bottom: 1rem;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.fee-detail-item:last-child {
    border-bottom: none;
}

.total-amount-display {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 2rem;
    border-radius: 10px;
    border: 2px solid #dee2e6;
}

.amount-value {
    font-size: 2.5rem;
    font-weight: bold;
    color: #1e3c72;
    margin: 1rem 0;
}

@media (max-width: 768px) {
    .amount-value {
        font-size: 2rem;
    }

    .total-amount-display {
        padding: 1.5rem;
    }
}
</style>

<script>
function proceedWithPayment(cardType) {
    if (cardType === 'local') {
        alert('سيتم توجيهك إلى صفحة الدفع بالبطاقة المحلية');
        // Here you would integrate with actual payment gateway
        // For now, just show an alert
    } else if (cardType === 'international') {
        alert('سيتم توجيهك إلى صفحة الدفع بالبطاقة الدولية');
        // Here you would integrate with actual payment gateway
        // For now, just show an alert
    }
}
</script>
@endsection
