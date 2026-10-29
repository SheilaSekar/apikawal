<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\User;

class UserPolicy
{
    public function view(User $actor, User $target): bool
    {
        return $actor->id === $target->id || $actor->role === Role::Admin;
    }

    public function update(User $actor, User $target): bool
    {
        // Profil hanya boleh diubah pemiliknya; admin tidak dapat mengambil alih data profil orang lain lewat endpoint profil.
        return $actor->id === $target->id;
    }

    public function manage(User $actor): bool
    {
        return $actor->role === Role::Admin;
    }

    public function delete(User $actor, User $target): bool
    {
        return $actor->id === $target->id;
    }
}
