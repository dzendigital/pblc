<?php

namespace App\Models\Blog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Carbon\Carbon;
use Illuminate\Support\Str;

use App\Models\Page\Meta;
use App\Models\Blog\Category;
use App\Models\File\Gallery;
use App\Models\File\Video;

class Item extends Model
{

    use HasFactory;
    use SoftDeletes;

    protected $table = "blog";
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "title",

        "body_short",
        "body_long",
        
        "account_id",
        "category_id",

        "is_slider",
        "is_visible",
        
        "sort",
        "publish_at",
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'is_slider' => null,
        'is_approve' => null,
        'is_visible' => null,
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_slider' => 'integer',
        'created_at' => 'datetime:d.m.Y',
        'publish_at' => 'datetime:d.m.Y',
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

    public function getCreatedAtFormatAttribute($value)
    {
        $format_raw = isset($this->created_at) ? $this->created_at : Carbon::now()->subDays(960);
        $result_raw = Carbon::parse($format_raw)->format('d.m.Y');

        return $result_raw;
    }

    public function getCreatedAtInputAttribute($value)
    {
        $format_raw = isset($this->created_at) ? $this->created_at : Carbon::now()->subDays(960);
        $result_raw = Carbon::parse($format_raw)->format('Y-m-d');

        return $result_raw;
    }

    public function getPublishAtFormatAttribute($value)
    {
        $format_raw = isset($this->publish_at) ? $this->publish_at : Carbon::now()->subDays(960);
        $result_raw = Carbon::parse($format_raw)->format('d.m.Y');

        return $result_raw;
    }
    public function getPublishAtInputAttribute($value)
    {
        $format_raw = isset($this->publish_at) ? $this->publish_at : Carbon::now()->subDays(960);
        $result_raw = Carbon::parse($format_raw)->format('Y-m-d');

        return $result_raw;
    }

    /**
     * 
     * Relations
     *
     */
    public function meta()
    {
        return $this->belongsToMany(Meta::class, "item_id");
    }
    public function category()
    {
        return $this->hasOne(Category::class, "id", "category_id");
    }
    public function gallery()
    {
        return $this->belongsToMany(Gallery::class, "blog_gallery");
    }
    public function video()
    {
        return $this->belongsToMany(Video::class, "blog_video", "video_id");
    }


}
