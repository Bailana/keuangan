<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

#[Fillable(['name', 'email', 'password', 'role', 'avatar_path', 'phone', 'job_title', 'sidebar_collapsed', 'google_id', 'avatar_url'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, CanResetPassword;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'sidebar_collapsed' => 'boolean',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['superadmin', 'admin']);
    }

    public function isAdministrasi(): bool
    {
        return in_array($this->role, ['superadmin', 'admin', 'administrasi']);
    }

    public function isViewer(): bool
    {
        return $this->role === 'viewer';
    }

    public function avatarUrl(): string
    {
        if ($this->avatar_path && Storage::disk('public')->exists($this->avatar_path)) {
            return Storage::url($this->avatar_path);
        }
        if ($this->avatar_url) {
            return $this->avatar_url;
        }
        return '';
    }

    public function canSocialLogin(): bool
    {
        return !empty($this->google_id);
    }
}
