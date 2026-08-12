<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait LogsActivity
{
    protected static function bootLogsActivity(): void
    {
        static::created(function ($model) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'create',
                'subject_type' => get_class($model),
                'subject_id' => $model->getKey(),
                'description' => static::buildDescription('create', $model),
                'new_values' => $model->getAttributes(),
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        });

        static::updated(function ($model) {
            $original = $model->getOriginal();
            $changed = [];
            foreach ($model->getAttributes() as $key => $value) {
                if (!array_key_exists($key, $original) || $original[$key] !== $value) {
                    $changed[$key] = ['old' => $original[$key] ?? null, 'new' => $value];
                }
            }

            if (empty($changed)) {
                return;
            }

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'update',
                'subject_type' => get_class($model),
                'subject_id' => $model->getKey(),
                'description' => static::buildDescription('update', $model, $changed),
                'old_values' => $original,
                'new_values' => $model->getAttributes(),
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        });

        static::deleted(function ($model) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'delete',
                'subject_type' => get_class($model),
                'subject_id' => $model->getKey(),
                'description' => static::buildDescription('delete', $model),
                'old_values' => $model->getAttributes(),
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        });
    }

    protected static function buildDescription(string $action, $model, array $changed = []): string
    {
        $user = Auth::user();
        $username = $user ? $user->name : 'System';
        $class = get_class($model);

        if ($class === \App\Models\Income::class) {
            $categoryName = $model->category ? $model->category->name : '-';
            $descriptions = [
                'create' => "$username menambahkan pemasukan Rp " . number_format($model->amount, 0, ',', '.') . " (Kategori: $categoryName)",
                'update' => "$username memperbarui pemasukan #$model->id",
                'delete' => "$username menghapus pemasukan Rp " . number_format($model->amount, 0, ',', '.') . "",
            ];
        } elseif ($class === \App\Models\Expense::class) {
            $descriptions = [
                'create' => "$username menambahkan pengeluaran Rp " . number_format($model->amount, 0, ',', '.') . " untuk: {$model->title}",
                'update' => "$username memperbarui pengeluaran #$model->id",
                'delete' => "$username menghapus pengeluaran Rp " . number_format($model->amount, 0, ',', '.') . " untuk: {$model->title}",
            ];
        } elseif ($class === \App\Models\Wallet::class) {
            $descriptions = [
                'create' => "$username menambahkan dompet: {$model->name}",
                'update' => "$username memperbarui dompet: {$model->name}",
                'delete' => "$username menghapus dompet: {$model->name}",
            ];
        } else {
            $descriptions = [
                'create' => "$username membuat " . class_basename($class) . " #{$model->id}",
                'update' => "$username memperbarui " . class_basename($class) . " #{$model->id}",
                'delete' => "$username menghapus " . class_basename($class) . " #{$model->id}",
            ];
        }

        return $descriptions[$action] ?? "$username melakukan $action pada " . class_basename($class) . " #{$model->id}";
    }
}
