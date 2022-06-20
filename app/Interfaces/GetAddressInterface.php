<?php

namespace App\Interfaces;

interface GetAddressInterface {
    public static function getDivisions(): array;
    public static function getDistricets($divisionId): array;
    public static function getThanas($districtId): array;
}

