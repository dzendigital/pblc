<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Str;

use App\Models\File\Gallery;
use App\Models\User;
use App\Models\Blog\Item as Blog;


class Account extends Model
{

    use HasFactory;
    use SoftDeletes;

    protected $table = "account";
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        "title",
        "user_id",

        "is_visible",
        "sort",
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        
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

    # генерируем slug вместе с добавлением title: where
    private function getWhere($attr){
        $where = array();
        if ( isset($attr['id']) && !is_null($attr['id']) ) {
            $where[] = array("id", "!=", $attr['id']);
        }
        if ( isset($attr['slug']) && !is_null($attr['slug']) ) {
            $where[] = array("slug", "=", $attr['slug']);
        }
        return $where;
    }

    public function getEmailAttribute($value)
    {
        $result = null;
        $attributes = $this->attributes;
        $_users = $this->user;

        return isset($_users->email) ? $_users->email : "[ошибка в почте пользователя]";
    }
    # генерируем slug вместе с добавлением title
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value; 

        if ( isset($this->attributes['slug']) && !is_null($this->attributes['slug']) ) {
            # 05.01.2022 - заменить на функцию
            $where = $this->getWhere($this->attributes);
            if ( $this->where($where)->count() > 1 ) {
                # не уникальный slug
                $this->attributes['slug'] = $this->attributes['slug'] . "-" . time();
            }  
        }else{
            
            # slug не заполнен при создании
            if ( isset($this->attributes['id']) ) {
                $this->attributes['slug'] = $this->where("id", "=", $this->attributes['id'])->first()->slug;
            }else{
                $this->attributes['slug'] = Str::slug($value);

            }

            # проверка на уникальность slug 
            $where = $this->getWhere($this->attributes);
            # dd(__METHOD__, $where, $this->where($where)->count());
            if ( $this->where($where)->count() != 0 ) {
                # не уникальный slug
                $this->attributes['slug'] = $this->attributes['slug'] . "-" . time();
            }
        }
    }
    /**
     * 
     * Relations
     *
     */
    public function gallery()
    {
        # ссылка на галлерею
        return $this->belongsToMany(Gallery::class, "gallery_account", "item_id", "gallery_id");
    }
    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function blog()
    {
        return $this->hasMany(Blog::class)->withTrashed()->orderBy('publish_at', 'DESC');
    }


}
