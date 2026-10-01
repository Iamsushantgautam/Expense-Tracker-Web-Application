<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'profile_pic',
        'monthly_budget',
        'budget_warn_limit',
        'theme_color',
        'dark_mode',
        'is_admin',
    ];

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
            'monthly_budget' => 'decimal:2',
            'dark_mode' => 'boolean',
            'is_admin' => 'boolean',
        ];
    }

    /**
     * Admin controls are strictly granted to sushantgautamlk6393@gmail.com.
     */
    public function getIsAdminAttribute($value): bool
    {
        $email = strtolower(trim($this->attributes['email'] ?? $this->email ?? ''));
        return (bool) $value && $email === 'sushantgautamlk6393@gmail.com';
    }

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function supportMessages(): HasMany
    {
        return $this->hasMany(SupportMessage::class);
    }
}
