<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable
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
        ];
    }

    public function roles(): HasOne
    {
        return $this->HasOne(Role::class, 'id', 'role_id');
    }

    public function getRole()
    {
        $user = auth()->user();

        $data = Cache::remember("user_role_" . $user->id, (3600 * 24), function () use ($user) {
            return $this->roles()->first()->toArray();
        });

        return $data;
    }

    protected function isProductionManager(): Attribute
    {
       $role = $this->getRole();

       return Attribute::make(
            get: fn (mixed $value) => $role['key'] === 'production_manager',
        );
    }

    protected function isOperator(): Attribute
    {
       $role = $this->getRole();

       return Attribute::make(
            get: fn (mixed $value) => $role['key'] === 'operator',
        );
    }
    
    public function work_orders(): HasMany
    {
        return $this->hasMany(WorkOrder::class, 'user_id', 'id');
    }
}
