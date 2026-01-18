<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_url',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active && $this->hasAnyRole([
            'super_admin',
            'admin',
            'kepala_sekolah', // Standardized
            'kepsek', // Legacy support
            'direktur_pelaksana', // Fixed from 'dia'
            'bendahara',
            'panitia_ppdb',
            'ptk',
            'wali_kelas',
            'tendik',
            'siswa',
        ]);
    }

    public function getFilamentAvatarUrl(): ?string
    {
        if ($this->avatar_url) {
            return Storage::url($this->avatar_url);
        }

        if ($this->guru && $this->guru->foto) {
            return Storage::url($this->guru->foto);
        }

        if ($this->siswa && $this->siswa->foto) {
            return Storage::url($this->siswa->foto);
        }

        return Storage::url('default-avatar.png');
    }

    public function guru()
    {
        return $this->hasOne(\App\Models\Guru::class);
    }

    public function siswa()
    {
        return $this->hasOne(\App\Models\Siswa::class);
    }
}
