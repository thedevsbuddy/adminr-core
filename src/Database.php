<?php

namespace Devsbuddy\AdminrCore;

use Illuminate\Database\Eloquent\Model;

class Database extends Model
{

    /**
     * Currently supported datatypes
     * */
    static public function dataTypes()
    {
        return [
            'slug',
            'increments',
            'bigIncrements',
            'string',
            'file',
            'text',
            'longText',
            'integer',
            'tinyInteger',
            'unsignedInteger',
            'unsignedTinyInteger',
            'unsignedBigInteger',
            'double',
            'boolean',
            'enum',
            'json',
            'date',
            'dateTime',
            'time',
            'timestamp',
        ];
    }

    static public function timeTypes()
    {
        return [
            'date',
            'dateTime',
            'time',
            'timestamp',
        ];
    }

    static public function numericTypes()
    {
        return [
            'integer',
            'tinyInteger',
            'bigInteger',
            'unsignedInteger',
            'unsignedTinyInteger',
            'unsignedBigInteger',
            'double',
            'boolean',
        ];
    }

    static public function integerTypes()
    {
        return [
            'integer',
            'tinyInteger',
            'bigInteger',
            'unsignedInteger',
            'unsignedTinyInteger',
            'unsignedBigInteger',
            'boolean',
        ];
    }

    static public function incrementTypes()
    {
        return [
            'increments',
            'bigIncrements',
        ];
    }


    static public function longTextDataTypes()
    {
        return [
            'text',
            'longText'
        ];
    }

    static public function relationshipIdentifiers()
    {
        return [
            'hasMany',
            'hasOne',
            'belongsTo',
            'belongsToMany',
            'hasManyThrough'
        ];
    }
}
