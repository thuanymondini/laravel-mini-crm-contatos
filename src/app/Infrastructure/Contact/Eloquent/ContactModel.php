<?php

namespace App\Infrastructure\Contact\Eloquent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'contacts';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'score',
        'status',
        'processed_at',
    ];

    protected $casts = [
        'score'        => 'integer',
        'processed_at' => 'datetime',
    ];

    protected static function newFactory(): \Database\Factories\ContactModelFactory
    {
        return \Database\Factories\ContactModelFactory::new();
    }
}
