<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Str;

class Item extends Model
{

    use HasFactory;
    use SoftDeletes;

    protected $table = "settings";
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "title",
        "value",
        "is_visible",
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'is_visible' => null,
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:d.m.Y',
    ];

    /**
     * The accessors & mutators
     *
     * @var string
     */

    # генерируем slug вместе с добавлением title
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;

        if ( isset($this->attributes['slug']) && !is_null($this->attributes['slug']) ) {

            # 05.01.2022 - заменить на функцию
            if ( $this->where('slug', $this->attributes['slug'])->count() > 1 ) {
                # не уникальный slug
                $this->attributes['slug'] = $this->attributes['slug'] . "-" . time();
            }
            
        }else{

            # slug не заполнен при создании
            $this->attributes['slug'] = Str::slug($value);

            # проверка на уникальность slug 
            if ( $this->where('slug', $this->attributes['slug'])->count() != 0 ) {
                # не уникальный slug
                $this->attributes['slug'] = $this->attributes['slug'] . "-" . time();
            }
        }

    }
}
