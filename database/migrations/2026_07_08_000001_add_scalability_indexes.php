<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index(['approval_status', 'status', 'category_id', 'segment_id', 'barangay_id'], 'products_marketplace_filters_idx');
            $table->index(['user_id', 'approval_status', 'status'], 'products_user_status_idx');
        });

        Schema::table('donations', function (Blueprint $table) {
            $table->index(['approval_status', 'status', 'category_id', 'barangay_id'], 'donations_hub_filters_idx');
            $table->index(['user_id', 'approval_status', 'status'], 'donations_user_status_idx');
            $table->index(['verification_status', 'status'], 'donations_verification_status_idx');
        });

        Schema::table('works', function (Blueprint $table) {
            $table->index(['approval_status', 'user_id'], 'works_approval_user_idx');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->index(['receiver_id', 'is_read'], 'messages_receiver_read_idx');
            $table->index(['user_id', 'receiver_id', 'created_at'], 'messages_conversation_sent_idx');
            $table->index(['receiver_id', 'user_id', 'created_at'], 'messages_conversation_received_idx');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index(['product_id', 'buyer_id'], 'orders_product_buyer_idx');
            $table->index(['buyer_id', 'status'], 'orders_buyer_status_idx');
            $table->index(['product_id', 'status'], 'orders_product_status_idx');
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->index(['status', 'reported_user_id'], 'reports_status_reported_user_idx');
            $table->index(['reporter_id', 'reported_user_id'], 'reports_user_pair_idx');
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->index(['user_id', 'appstatus', 'appdate'], 'appointments_user_status_date_idx');
            $table->index(['upcycler_id', 'appstatus', 'appdate'], 'appointments_upcycler_status_date_idx');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex('appointments_user_status_date_idx');
            $table->dropIndex('appointments_upcycler_status_date_idx');
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->dropIndex('reports_status_reported_user_idx');
            $table->dropIndex('reports_user_pair_idx');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_product_buyer_idx');
            $table->dropIndex('orders_buyer_status_idx');
            $table->dropIndex('orders_product_status_idx');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('messages_receiver_read_idx');
            $table->dropIndex('messages_conversation_sent_idx');
            $table->dropIndex('messages_conversation_received_idx');
        });

        Schema::table('works', function (Blueprint $table) {
            $table->dropIndex('works_approval_user_idx');
        });

        Schema::table('donations', function (Blueprint $table) {
            $table->dropIndex('donations_hub_filters_idx');
            $table->dropIndex('donations_user_status_idx');
            $table->dropIndex('donations_verification_status_idx');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_marketplace_filters_idx');
            $table->dropIndex('products_user_status_idx');
        });
    }
};
