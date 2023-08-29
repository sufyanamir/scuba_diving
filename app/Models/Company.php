<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'company';

    protected $primaryKey = 'company_id';
    
    protected $fillable = [
        'company_name',
        'company_email',
        'company_phone',
        'company_address',
        'company_image', // Add other columns as needed
    ];

    // If you have timestamps (created_at and updated_at) in your table
    public $timestamps = true;

    // Define any relationships with other models here if needed
}
