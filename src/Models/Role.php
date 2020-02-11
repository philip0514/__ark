<?php

namespace Philip0514\Ark\Models;

use Spatie\Permission\Models\Role as Model;

class Role extends Model
{
    protected $guard_name = 'admin';

    protected $guard = 'admin';
}
