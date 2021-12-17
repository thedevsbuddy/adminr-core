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
            'primary',
            'unique',
            'slug',
            'increments',
            'tinyIncrements',
            'smallIncrements',
            'mediumIncrements',
            'bigIncrements',
            'char',
            'string',
            'text',
            'mediumText',
            'longText',
            'integer',
            'tinyInteger',
            'smallInteger',
            'mediumInteger',
            'bigInteger',
            'unsignedInteger',
            'unsignedTinyInteger',
            'unsignedSmallInteger',
            'unsignedMediumInteger',
            'unsignedBigInteger',
            'float',
            'double',
            'decimal',
            'unsignedDecimal',
            'boolean',
            'enum',
            'json',
            'jsonb',
            'date',
            'dateTime',
            'dateTimeTz',
            'time',
            'timeTz',
            'timestamp',
            'timestampTz',
            'binary',
            'uuid',
            'ipAddress',
            'macAddress',
            'geometry',
            'point',
            'lineString',
            'polygon',
            'geometryCollection',
            'multiPoint',
            'multiLineString',
            'multiPolygon',
            // 'morphs',
            // 'nullableMorphs',
            // 'rememberToken',
        ];
    }

    static public function timeTypes()
    {
        return [
            'date',
            'dateTime',
            'dateTimeTz',
            'time',
            'timeTz',
            'timestamp',
            'timestampTz',
            'timestamps',
            'nullableTimestamps',
            'timestampsTz',
            'softDeletes',
            'softDeletesTz'
        ];
    }

    static public function numericTypes()
    {
        return [
            'integer',
            'tinyInteger',
            'smallInteger',
            'mediumInteger',
            'bigInteger',
            'unsignedInteger',
            'unsignedTinyInteger',
            'unsignedSmallInteger',
            'unsignedMediumInteger',
            'unsignedBigInteger',
            'float',
            'double',
            'decimal',
            'unsignedDecimal',
            'boolean',
        ];
    }

    static public function integerTypes()
    {
        return [
            'integer',
            'tinyInteger',
            'smallInteger',
            'mediumInteger',
            'bigInteger',
            'unsignedInteger',
            'unsignedTinyInteger',
            'unsignedSmallInteger',
            'unsignedMediumInteger',
            'unsignedBigInteger',
            'boolean',
        ];
    }

    static public function incrementTypes()
    {
        return [
            'increments',
            'tinyIncrements',
            'smallIncrements',
            'mediumIncrements',
            'bigIncrements',
        ];
    }


    static public function longTextDataTypes()
    {
        return [
            'text',
            'mediumText',
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
