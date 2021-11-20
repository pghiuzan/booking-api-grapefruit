<?php

namespace App\DTO;

use Laravel\Lumen\Http\Request;

final class TripsFilterDTO extends DataTransferObject
{
    public string $search;
    public string $orderBy;
    public string $orderDirection;
    public string $minPrice;
    public string $maxPrice;

    public static function fromRequest(Request $request): self
    {
        return new self($request->all());
    }
}
