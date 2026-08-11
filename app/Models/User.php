<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'role',
        'whatsapp',
        'is_active',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin === true;
    }

    /**
     * Teléfono normalizado para enlaces de WhatsApp (api.whatsapp.com/send),
     * con prefijo de país 54 (Argentina).
     */
    public function whatsappPhone(): string
    {
        $telefono = preg_replace('/[^0-9]/', '', $this->whatsapp ?? '');

        if (substr($telefono, 0, 1) === '0') {
            $telefono = '54'.substr($telefono, 1);
        } elseif (substr($telefono, 0, 2) !== '54') {
            $telefono = '54'.$telefono;
        }

        return $telefono;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
