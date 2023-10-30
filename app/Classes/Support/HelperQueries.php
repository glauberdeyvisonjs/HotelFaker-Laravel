<?php

namespace App\Classes\Support;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class HelperQueries
{
    /**
     * @param $array
     * @return string
     */
    public static function arrayToSQL($array): string
    {
        if ($array != null && count($array) > 0) {
            $sql = '("' . implode('","', $array) . '")';
        } else {
            $sql = '()';
        }

        return $sql;
    }

    /**
     * @param $object
     * @param String $key
     * @return string
     */
    public static function objectToSQL($object, string $key = 'id'): string
    {
        $object = collect(json_decode(json_encode($object)));
        return '(' . $object->implode($key, ',') . ')';
    }
}
