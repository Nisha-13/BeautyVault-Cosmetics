<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSkinProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'skin_type',
        'undertone',
        'shade_depth',
        'concerns',
        'allergies',
        'hair_type',
        'hair_porosity',
    ];

    protected $casts = [
        'concerns' => 'array',
        'allergies' => 'array',
        'shade_depth' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
