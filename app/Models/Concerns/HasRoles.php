<?php

namespace App\Models\Concerns;

use App\Models\Donor;
use App\Models\Guardian;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasRoles
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function guardianProfile(): HasOne
    {
        return $this->hasOne(Guardian::class);
    }

    public function donorProfile(): HasOne
    {
        return $this->hasOne(Donor::class);
    }

    public function hasRole(string|Role $role): bool
    {
        $roleName = $role instanceof Role ? $role->name : $role;

        return $this->roles()->where('name', $roleName)->exists();
    }

    public function hasAnyRole(iterable|string ...$roles): bool
    {
        $roleNames = collect($roles)
            ->flatten(1)
            ->map(function ($role): ?string {
                if ($role instanceof Role) {
                    return $role->name;
                }

                if (is_string($role)) {
                    return trim($role);
                }

                return null;
            })
            ->filter()
            ->values();

        if ($roleNames->isEmpty()) {
            return false;
        }

        return $this->roles()->whereIn('name', $roleNames)->exists();
    }

    public function hasPermissionTo(string|Permission $permission): bool
    {
        $permissionName = $permission instanceof Permission ? $permission->name : $permission;

        return $this->roles()->whereHas('permissions', function ($query) use ($permissionName): void {
            $query->where('name', $permissionName);
        })->exists();
    }

    public function assignRole(string|Role $role): void
    {
        $roleId = $role instanceof Role
            ? $role->getKey()
            : Role::query()->where('name', $role)->value('id');

        if (! $roleId) {
            throw new \InvalidArgumentException('Role does not exist.');
        }

        $this->roles()->syncWithoutDetaching([$roleId]);
    }
}
