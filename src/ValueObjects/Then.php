<?php

declare(strict_types=1);

namespace Fraction\ValueObjects;

use Fraction\Exceptions\PreventLoop;
use Fraction\Support\FractionName;
use UnitEnum;

final class Then
{
    /**
     * @throws PreventLoop
     */
    public function __construct(public string $action, public string|UnitEnum $then)
    {
        if (FractionName::format($action) === FractionName::format($then)) {
            throw new PreventLoop();
        }
    }
}
