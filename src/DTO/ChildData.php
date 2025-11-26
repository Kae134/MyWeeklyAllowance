<?php

namespace MyWeeklyAllowance\DTO;

class ChildData
{
    public function __construct(
        public readonly string $email,
        public readonly string $name,
        public readonly string $firstname,
        public readonly float $balance,
        public readonly float $weeklyAllowance
    ) {
    }
}
