<?php

namespace Philip0514\Ark\Models;

use Spatie\Permission\Models\Permission as Model;

class Permission extends Model
{
    protected $guard_name = 'admin';

    protected $guard = 'admin';
}
