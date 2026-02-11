<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartialProcessingData extends Model
{
    use HasFactory;

    protected $table = 'partial_processing_data';

    protected $fillable = [
        'package_id',
        'field_name',
        'provided_value'
    ];

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }
}