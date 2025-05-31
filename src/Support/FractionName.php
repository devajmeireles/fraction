<?php

declare(strict_types=1);

namespace Fraction\Support;

use Illuminate\Support\Str;
use InvalidArgumentException;
use UnitEnum;

/** @internal */
final class FractionName
{
    /**
     * Format the action name to an internal action name.
     */
    public static function format(string|UnitEnum $action): string
    {
        $action = $action instanceof UnitEnum
            ? $action->name
            : $action;

        if (mb_strlen($action) > 50) {
            throw new InvalidArgumentException('Fraction name cannot be longer than 50 characters.');
        }

        return '__fraction.'.Str::of($action)
            ->lower()
            ->trim()
            ->snake()
            ->value();
    }
}
