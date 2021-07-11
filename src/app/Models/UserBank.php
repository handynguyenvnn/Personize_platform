<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBank extends BaseModel
{
    use HasFactory;

    const IS_NOTIFICATION = 1;

    protected $table = 'users_bankings';

    protected $fillable = [
        'id', 'user_id', 'bank_name', 'branch_name', 'bank_account_number', 'bank_account_holder', 'bank_account_type', 'created_at', 'updated_at', 'deleted_at'
    ];
}

