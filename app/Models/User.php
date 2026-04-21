<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ASSIGNABLE_ROLE_NAMES = ['Admin', 'Super Admin', 'Staff'];

    public const ADMIN_ROLE_NAMES = ['Admin', 'Super Admin'];

    public const ADMIN_STAFF_ROLE_SLUGS = ['admin', 'superadmin', 'super_admin', 'staff', 'koordinator'];

    public const ADMIN_ROLE_SLUGS = ['admin', 'superadmin', 'super_admin'];

    private const ROLE_ALIASES = [
        'koordinator' => 'staff',
        'staff' => 'koordinator',
        'user' => 'member',
        'member' => 'user',
        'jemaat' => 'jemaatgereja',
        'jemaatgereja' => 'jemaat',
    ];

    protected $fillable = ['name', 'email', 'password', 'role', 'role_id', 'status'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'role_permissions',
            'role_id',
            'permission_id',
            'role_id',
            'id'
        );
    }

    public function jemaat(): HasOne
    {
        return $this->hasOne(Jemaat::class, 'user_id');
    }

    public function member(): HasOne
    {
        return $this->hasOne(Member::class, 'user_id');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public function scopeAdminAndStaff(Builder $query): Builder
    {
        return $query
            ->approved()
            ->where(function (Builder $builder): void {
                $builder
                    ->whereIn(DB::raw("lower(replace(role, ' ', ''))"), self::ADMIN_STAFF_ROLE_SLUGS)
                    ->orWhereHas('role', function (Builder $roleQuery): void {
                        $roleQuery->whereIn('name', self::ASSIGNABLE_ROLE_NAMES);
                    });
            });
    }

    public function scopeAdminRoles(Builder $query): Builder
    {
        return $query->where(function (Builder $builder): void {
            $builder
                ->whereIn(DB::raw("lower(replace(role, ' ', ''))"), self::ADMIN_ROLE_SLUGS)
                ->orWhereHas('role', function (Builder $roleQuery): void {
                    $roleQuery->whereIn('name', self::ADMIN_ROLE_NAMES);
                });
        });
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->hasRole('Admin')) {
            return true;
        }

        if (! $this->role_id) {
            return false;
        }

        return $this->permissions()->where('permissions.name', $permission)->exists();
    }

    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (! $this->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    public function hasRole(string|array $roles): bool
    {
        $requestedRoles = array_map(
            static fn (string $role) => str_replace(' ', '', strtolower($role)),
            (array) $roles
        );

        $roleName = str_replace(' ', '', strtolower((string) $this->getAttribute('role')));

        $aliases = array_filter([
            $roleName,
            self::ROLE_ALIASES[$roleName] ?? null,
        ]);

        return (bool) array_intersect($aliases, $requestedRoles);
    }

    public function getRoleNameAttribute(): ?string
    {
        return $this->getAttribute('role');
    }

    public function getLastActiveAttribute(): ?string
    {
        $hasPreloadedLastActivity = array_key_exists('last_activity_at', $this->attributes);
        $lastActivity = $this->getAttribute('last_activity_at');

        if (! $hasPreloadedLastActivity && $lastActivity === null) {
            $lastActivity = DB::table('sessions')
                ->where('user_id', $this->getKey())
                ->max('last_activity');
        }

        if (empty($lastActivity)) {
            return null;
        }

        return Carbon::createFromTimestamp($lastActivity)->diffForHumans();
    }

    public function getRoleDisplayLabelAttribute(): string
    {
        $normalizedRole = strtolower(str_replace(' ', '', (string) $this->role_name));

        return match (true) {
            str_contains($normalizedRole, 'admin') => 'Admin',
            str_contains($normalizedRole, 'koordinator'), str_contains($normalizedRole, 'staff') => 'Staff',
            default => ucfirst((string) $this->role_name),
        };
    }

    public function getRoleBadgeClassAttribute(): string
    {
        return $this->role_display_label === 'Admin'
            ? 'bg-blue-100 text-blue-700'
            : 'bg-teal-100 text-teal-700';
    }
}
