<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite doesn't support ALTER COLUMN nullable directly
        // We need to recreate the table
        DB::statement('DROP TABLE IF EXISTS expenses_old');
        DB::statement('ALTER TABLE expenses RENAME TO expenses_old');
        
        DB::statement('CREATE TABLE expenses (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            expense_category_id INTEGER NOT NULL REFERENCES expense_categories(id) ON DELETE CASCADE,
            title VARCHAR(200) NOT NULL,
            date DATE NOT NULL,
            amount DECIMAL(12,2) NOT NULL,
            bank_or_wallet VARCHAR(100) NULL,
            recipient VARCHAR(200) NULL,
            receipt_url VARCHAR(255) NULL,
            notes TEXT NULL,
            wallet_id INTEGER REFERENCES wallets(id) ON DELETE SET NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )');
        
        DB::statement('INSERT INTO expenses 
            (expense_category_id, title, date, amount, bank_or_wallet, recipient, receipt_url, notes, wallet_id, created_at, updated_at)
            SELECT expense_category_id, title, date, amount, bank_or_wallet, recipient, receipt_url, notes, wallet_id, created_at, updated_at
            FROM expenses_old');
        
        DB::statement('DROP TABLE expenses_old');
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS expenses_old');
        DB::statement('ALTER TABLE expenses RENAME TO expenses_old');
        
        DB::statement('CREATE TABLE expenses (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            expense_category_id INTEGER NOT NULL REFERENCES expense_categories(id) ON DELETE CASCADE,
            title VARCHAR(200) NOT NULL,
            date DATE NOT NULL,
            amount DECIMAL(12,2) NOT NULL,
            bank_or_wallet VARCHAR(100) NOT NULL,
            recipient VARCHAR(200) NULL,
            receipt_url VARCHAR(255) NULL,
            notes TEXT NULL,
            wallet_id INTEGER REFERENCES wallets(id) ON DELETE SET NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )');
        
        DB::statement('INSERT INTO expenses 
            (expense_category_id, title, date, amount, bank_or_wallet, recipient, receipt_url, notes, wallet_id, created_at, updated_at)
            SELECT expense_category_id, title, date, amount, COALESCE(bank_or_wallet, \'\'), recipient, receipt_url, notes, wallet_id, created_at, updated_at
            FROM expenses_old');
        
        DB::statement('DROP TABLE expenses_old');
    }
};
