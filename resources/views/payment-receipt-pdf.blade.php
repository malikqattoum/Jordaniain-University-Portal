<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إيصال الدفع - {{ $payment->receipt_number }}</title>
    <style>
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            margin: 20px;
            background-color: #fff;
            color: #333;
            line-height: 1.6;
        }
        .receipt-container {
            max-width: 700px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fff;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #333;
        }
        .header h1 {
            color: #004680;
            margin: 0;
            font-size: 24px;
        }
        .header h2 {
            margin: 10px 0 0;
            font-size: 20px;
            color: #666;
        }
        .info-section {
            margin-bottom: 25px;
        }
        .info-section h3 {
            background-color: #f0f0f0;
            padding: 8px 12px;
            margin: 0 0 10px;
            font-size: 16px;
            border-right: 4px solid #004680;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .info-item {
            margin-bottom: 8px;
        }
        .info-item strong {
            display: inline-block;
            width: 120px;
            text-align: left;
        }
        .payment-details {
            margin: 25px 0;
        }
        .payment-details table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .payment-details th,
        .payment-details td {
            border: 1px solid #ddd;
            padding: 8px 12px;
            text-align: right;
        }
        .payment-details th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .total-row {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- Header -->
        <div class="header">
            <h1>الجامعة الأردنية</h1>
            <h2>إيصال الدفع</h2>
            <p style="margin-top: 10px; font-size: 14px;">Jordan University - Payment Receipt</p>
        </div>

        <!-- Student and Receipt Information -->
        <div class="info-section">
            <h3>معلومات الطالب</h3>
            <div class="info-grid">
                <div class="info-item">
                    <strong>الاسم:</strong> {{ $student->name }}
                </div>
                <div class="info-item">
                    <strong>رقم الطالب:</strong> {{ $student->student_id }}
                </div>
                <div class="info-item">
                    <strong>الكلية:</strong> {{ $student->college }}
                </div>
                <div class="info-item">
                    <strong>التخصص:</strong> {{ $student->major }}
                </div>
            </div>
        </div>

        <div class="info-section">
            <h3>معلومات الإيصال</h3>
            <div class="info-grid">
                <div class="info-item">
                    <strong>رقم الإيصال:</strong> {{ $payment->receipt_number }}
                </div>
                <div class="info-item">
                    <strong>تاريخ الدفع:</strong> {{ $payment->created_at->format('Y-m-d H:i') }}
                </div>
                <div class="info-item">
                    <strong>السنة الأكاديمية:</strong> {{ $payment->academic_year }}
                </div>
                <div class="info-item">
                    <strong>الفصل الدراسي:</strong> {{ $payment->semester_name }}
                </div>
            </div>
        </div>

        <!-- Payment Details -->
        <div class="payment-details">
            <h3 style="margin-bottom: 15px; border-right: 4px solid #004680; padding-right: 10px;">تفاصيل الدفع</h3>
            <table>
                <thead>
                    <tr>
                        <th>الوصف</th>
                        <th>المبلغ (دينار)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>رسوم التعلم</td>
                        <td style="text-align: center;">{{ number_format($payment->tuition_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>رسوم الفصل الدراسي</td>
                        <td style="text-align: center;">{{ number_format($payment->semester_fees_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>رسوم المعالجة</td>
                        <td style="text-align: center;">{{ number_format($payment->processing_fee, 2) }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td><strong>الإجمالي</strong></td>
                        <td style="text-align: center;"><strong>{{ number_format($payment->amount_paid, 2) }}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Payment Method and Status -->
        <div class="info-section">
            <h3>مثول الدفع</h3>
            <div class="info-grid">
                <div class="info-item">
                    @if($payment->payment_method === 'credit_card')
                        <strong>طريقة الدفع:</strong> 
                        @if($payment->card_type === 'local')
                            بطاقة ائتمان محلية
                        @else
                            بطاقة ائتمان دولية
                        @endif
                    @else
                        <strong>طريقة الدفع:</strong> {{ $payment->payment_method }}
                    @endif
                </div>
                <div class="info-item">
                    <strong>الحالة:</strong>
                    <span class="status-badge status-completed">
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
        <div class="footer">
            <p>تم إصدار هذا الإيصال بشكل إلكتروني ولا حاجة للتوقيع الورقي</p>
            <p>ملاحظة: هذا الإيصال مقبول كإثبات دفع رسمي</p>
            <p style="margin-top: 15px; font-size: 10px; color: #888;">{{ date('Y-m-d H:i:s') }} - النظام الأكاديمي</p>
        </div>
    </div>
</body>
</html>