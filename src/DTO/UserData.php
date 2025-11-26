<?php

namespace MyWeeklyAllowance\DTO;

class UserData
{
    public function __construct(
        public readonly string $email,
        public readonly string $name,
        public readonly string $firstname,
        public readonly string $userType,
        public readonly float $balance
    ) {
    }
}
