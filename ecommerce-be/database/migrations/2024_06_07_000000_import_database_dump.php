<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $sqlPath = database_path('ecommerce_db.sql');

        if (!file_exists($sqlPath)) {
            throw new Exception("SQL dump file not found at: {$sqlPath}");
        }

        $sql = file_get_contents($sqlPath);

        // Remove MySQL-specific comments and directives that might cause issues
        $sql = preg_replace('/\/\*![0-9]{5}.*?\*\//', '', $sql);
        $sql = preg_replace('/SET SQL_MODE.*?;/', '', $sql);
        $sql = preg_replace('/START TRANSACTION;/', '', $sql);
        $sql = preg_replace('/SET time_zone.*?;/', '', $sql);
        $sql = preg_replace('/COMMIT;/', '', $sql);

        // Split by semicolon to execute individual statements
        $statements = array_filter(array_map('trim', explode(';', $sql)));

        foreach ($statements as $statement) {
            if (!empty($statement) && !preg_match('/^--/', $statement)) {
                DB::unprepared($statement);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop all tables
        $tables = DB::select("SHOW TABLES");
        $databaseName = DB::getDatabaseName();

        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];
            DB::statement("DROP TABLE IF EXISTS `{$tableName}`");
        }
    }
};
