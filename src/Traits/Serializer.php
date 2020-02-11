<?php
namespace Philip0514\Ark\Traits;

use League\Fractal\Manager;

/**
 * Trait Serializer
 *
 * @package App\Traits
 */
trait Serializer
{
    protected function manager($resource)
    {
        $manager = new Manager();
        $data = $manager->createData($resource)->toArray();

        return $data['data'];
    }
}