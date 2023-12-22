<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Page;
use App\Models\City\City;

use Illuminate\Support\Str;

class Menu extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "pages_menu";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "parent_id",
        "title",
        "slug",
        "is_onmenu",
        "is_visible",
        "is_managable",
    ];

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

    public function pages()
    {
        # страницы: каждый пункт меню имеет свою контент страницу
        return $this->hasOne(Page::class, "menu_id");
    }

    public function childs() 
    {
        return $this->hasMany(Menu::class, 'parent_id', 'id') ;
    }
    public function parent() 
    {
        return $this->hasOne(Menu::class, 'id', 'parent_id') ;
    }

    public function city()
    {
       # ссылка на город отправления
       return $this->hasOne(City::class, "is_menu");
    }

        
}
