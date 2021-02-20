<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = "products";

    protected $fillable = [
        'external_id', 'name'
    ];


    public function tags() {
        return $this->belongsToMany(
            Tag::class,
            'product_tags',
            'product_id',
            'tag_id'
        );
    }

}
