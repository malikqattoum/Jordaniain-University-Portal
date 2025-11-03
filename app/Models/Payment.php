<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_year',
        'semester_name',
        'amount_paid',
        'tuition_amount',
        'semester_fees_amount',
        'payment_method',
        'card_type',
        'processing_fee',
        'receipt_number',
        'status',
        'notes',
        'payment_details'
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'tuition_amount' => 'decimal:2',
        'semester_fees_amount' => 'decimal:2',
        'processing_fee' => 'decimal:2',
        'payment_details' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the student that owns the payment
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Scope to get completed payments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get payments by academic year
     */
    public function scopeByAcademicYear($query, $academicYear)
    {
        return $query->where('academic_year', $academicYear);
    }

    /**
     * Scope to get payments by student
     */
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Get total payments for a student in a semester
     */
    public static function getTotalPaymentsForStudent($studentId, $academicYear, $semesterName = null)
    {
        $query = static::completed()->byStudent($studentId)->byAcademicYear($academicYear);

        if ($semesterName) {
            $query->where('semester_name', $semesterName);
        }

        return $query->sum('amount_paid');
    }

    /**
     * Generate unique receipt number
     */
    public static function generateReceiptNumber()
    {
        $prefix = 'JU';
        $year = date('Y');
        $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $timestamp = time();

        return $prefix . $year . $random . $timestamp;
    }

    /**
     * Get payment status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        switch ($this->status) {
            case 'completed':
                return 'success';
            case 'pending':
                return 'warning';
            case 'failed':
                return 'danger';
            case 'refunded':
                return 'info';
            default:
                return 'secondary';
        }
    }

    /**
     * Get formatted amount (in Jordanian Dinars)
     */
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount_paid, 2) . ' دينار';
    }
}
