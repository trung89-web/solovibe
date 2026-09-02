<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('order_code')->unique(); // VD: FT-20231015-XYZ
            
            // Thông tin người nhận (Lưu cứng vào đơn hàng đề phòng sau này khách đổi địa chỉ)
            $table->string('receiver_name');
            $table->string('receiver_phone');
            $table->string('province');
            $table->string('district');
            $table->string('ward');
            $table->string('specific_address');
            
            $table->decimal('total_amount', 15, 2);
            
            $table->string('payment_method'); // 'cod', 'momo'
            $table->string('payment_status')->default('pending'); // pending, paid, failed
            $table->string('order_status')->default('pending'); // pending, shipping, completed, cancelled
            
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
