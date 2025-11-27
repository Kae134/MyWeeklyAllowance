<?php

namespace MyWeeklyAllowance\Validator;

use MyWeeklyAllowance\Exception\MissingAmountException;
use MyWeeklyAllowance\Exception\InvalidAmountException;

class AmountValidator
{
    // Input: amount (float) | Output: void
    public static function validate(float $amount): void
    {
        if ($amount == 0.0) {
            throw new MissingAmountException("Amount is required");
        }

        if ($amount < 0) {
            throw new InvalidAmountException("Amount must be positive");
        }
    }
}
