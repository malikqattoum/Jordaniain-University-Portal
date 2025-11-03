<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'last_login_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    // Role constants
    const ROLE_SUPER_ADMIN = 'super_admin';
    const ROLE_ACADEMIC_ADMIN = 'academic_admin';
    const ROLE_FINANCE_ADMIN = 'finance_admin';
    const ROLE_REGISTRAR = 'registrar';

    /**
     * Set the admin's password and hash it.
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }

    /**
     * Check if admin has specific role
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Check if admin has any of the given roles
     */
    public function hasAnyRole($roles)
    {
        return in_array($this->role, $roles);
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin()
    {
        $this->update(['last_login_at' => now()]);
    }

    /**
     * Scope to get only active admins
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by role
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }
}
