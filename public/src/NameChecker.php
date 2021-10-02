<?php

namespace App;

class NameChecker
{
    public static function hasMoreThanOneName(string $fullName): bool
    {
        $names = explode(" ", $fullName);
        return count($names) > 1;
    }
}
