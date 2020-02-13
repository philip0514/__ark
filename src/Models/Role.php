<?php

namespace Philip0514\Ark\Models;

use Spatie\Permission\Models\Role as Model;

//Traits
use Philip0514\Ark\Traits\Helper;

class Role extends Model
{
    use Helper;

    protected $guard_name = 'admin';

    protected $guard = 'admin';
}
