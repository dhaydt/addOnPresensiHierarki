<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Userinfo extends Model
{
    protected $table = 'userinfo';
    protected $primaryKey = 'userid';

    protected $fillable = [
        'defaultdeptid',
        'name',
        'gender',
        'birthday',
        'pensiun',
    ];

    /**
     * Get the user that owns the Userinfo
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userid', 'userinfo_id');
    }

    /**
     * Get the department that owns the Userinfo
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Departement::class, 'defaultdeptid', 'DeptID');
    }
}
