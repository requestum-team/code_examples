<?php declare(strict_types=1);

namespace App\Exception\TwoFactor;

use JetBrains\PhpStorm\Pure;

abstract class TwoFactorException extends \Exception
{
    public const MESSAGE = "Error";

    #[Pure] public function __construct()
    {
        parent::__construct(static::MESSAGE);
    }
}
