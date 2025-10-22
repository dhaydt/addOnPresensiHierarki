<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departement extends Model
{
    protected $table = 'departments';
    protected $primaryKey = 'DeptID';

    protected $fillable = [
        'DeptName',
    ];

    /**
     * Get all of the users for the Departement
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Userinfo::class, 'defaultdeptid', 'DeptID');
    }
}
