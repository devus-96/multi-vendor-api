<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'logo',
        'phone_number',
        'email'
    ];

    public function address(): HasMany
    {
        return $this->hasMany(Address::class);
    }

}
