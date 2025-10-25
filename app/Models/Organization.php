<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\OrganizationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Organization extends Model
{
    /** @use HasFactory<OrganizationFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'name' => 'string',
            'vat_number' => 'string',
            'ssn' => 'string',
            'phone' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
