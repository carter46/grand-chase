<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContactAndDocumentFieldsToIrsRefundsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('irs_refunds')) {
            return;
        }

        Schema::table('irs_refunds', function (Blueprint $table) {
            if (!Schema::hasColumn('irs_refunds', 'phone')) {
                $table->string('phone', 50)->nullable()->after('ssn');
            }
            if (!Schema::hasColumn('irs_refunds', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('irs_refunds', 'drivers_license_path')) {
                $table->string('drivers_license_path')->nullable()->after('country');
            }
            if (!Schema::hasColumn('irs_refunds', 'id_document_path')) {
                $table->string('id_document_path')->nullable()->after('drivers_license_path');
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('irs_refunds')) {
            return;
        }

        Schema::table('irs_refunds', function (Blueprint $table) {
            $cols = [];
            foreach (['phone', 'date_of_birth', 'drivers_license_path', 'id_document_path'] as $col) {
                if (Schema::hasColumn('irs_refunds', $col)) {
                    $cols[] = $col;
                }
            }
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
}
