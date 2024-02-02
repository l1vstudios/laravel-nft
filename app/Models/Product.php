<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $primaryKey = 'item_id'; // Specify the primary key column

    protected $fillable = ['item_id', 'name', 'price', 'description', 'source']; // Update column names
}
