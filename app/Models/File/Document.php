<?php

namespace App\Models\File;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

use Carbon\Carbon;
use App\Models\Servey\Anketa;

class Document extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "document";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "title",
        "src",
        "url",
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'is_visible' => 1,
        'slug' => null,
        'sort' => null,
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:d.m.Y',
    ];


    public function getCreatedAtFormatAttribute($value)
    {
        
        $created_at_format_raw = $this->attributes['created_at'];
        $date_format = Carbon::createFromFormat('Y-m-d H:i:s', $created_at_format_raw)->format('d.m.Y');
        $time = Carbon::createFromFormat('Y-m-d H:i:s', $created_at_format_raw)->format('H:i:s');

        $value = "{$date_format} в {$time}";
        return $value;
    }

    public function anketa()
    {
        # ссылка на галлерею
        return $this->belongsToMany(Anketa::class, "document_servey", "document_id", "servey_id");
    }

}
