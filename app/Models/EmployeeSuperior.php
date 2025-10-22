<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeSuperior extends Model
{
    protected $table = 'employee_superiors';

    protected $fillable = [
        'userinfo_id',
        'superior_id',
        'setupby_id',
    ];

    /**
     * Get the user that owns the EmployeeSuperior
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userinfo_id', 'userinfo_id');
    }

    public function superior(): BelongsTo
    {
        return $this->belongsTo(User::class, 'superior_id', 'userinfo_id');
    }
}
