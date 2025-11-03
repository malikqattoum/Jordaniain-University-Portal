<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('academic_year'); // e.g., "2024/2025"
            $table->string('semester_name'); // e.g., "Fall 2024"
            $table->decimal('amount_paid', 10, 2); // Amount paid in this transaction
            $table->decimal('tuition_amount', 10, 2)->default(0); // Portion for tuition
            $table->decimal('semester_fees_amount', 10, 2)->default(0); // Portion for semester fees
            $table->string('payment_method')->default('credit_card'); // credit_card, bank_transfer, cash, etc.
            $table->string('card_type')->nullable(); // local, international
            $table->decimal('processing_fee', 10, 2)->default(0); // Processing fees charged
            $table->string('receipt_number')->unique(); // Unique receipt identifier
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->text('notes')->nullable(); // Additional notes
            $table->json('payment_details')->nullable(); // Store additional payment data
            $table->timestamps();

            $table->index('student_id');
            $table->index('academic_year');
            $table->index('status');
            $table->index('receipt_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
