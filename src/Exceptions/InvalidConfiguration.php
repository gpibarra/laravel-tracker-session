<?php

namespace gpibarra\TrackerSession\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\Model;
use gpibarra\TrackerSession\Models\TrackerSession;

class InvalidConfiguration extends Exception
{
    public static function modelIsNotValid(string $className)
    {
        return new static("The given model class `$className` does not extend `".TrackerSession::class.'` or it does not extend `'.Model::class.'`');
    }
}
