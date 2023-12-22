<?php

namespace App\Models\Servey;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\File\Document;

class Anketa extends Model
{

    use HasFactory;
    use SoftDeletes;

    protected $table = "servey";
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "orgname",
        "orgcomname",
        "orgadress",
        "orgperson",
        "inn",
        "kpp",
        "ogrnip",
        "bankname",
        "bankbik",
        "bankcor",
        "bankcheck",
        "firstname",
        "lastname",
        "secondname",
        "firstname_genetive",
        "lastname_genetive",
        "secondname_genetive",
        "signatoryposition",
        "signatoryreason",
        "email",
        "phone",
        "product",
        "adresmfc",
        "policy"
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

    public function getCreatedAtFormatAttribute($value)
    {
        
        $created_at_format_raw = $this->attributes['created_at'];
        $date_format = Carbon::createFromFormat('Y-m-d H:i:s', $created_at_format_raw)->format('d.m.Y');
        $time = Carbon::createFromFormat('Y-m-d H:i:s', $created_at_format_raw)->format('H:i');

        $value = "{$date_format} в {$time}";
        return $value;
    }
    public function getAdresmfcAttribute($value)
    {
        // используется в виде массива
        return json_decode($this->attributes['adresmfc']);
    }
    public function getPolicyAttribute($value)
    {
        // используется в виде массива
        return ($this->attributes['policy'] == 1 ? "Согласие получено" : "-");
    }

    /**
     * Relations
     */
    public function document()
    {
        # ссылка на галлерею
        return $this->belongsToMany(Document::class, "document_servey", "servey_id", "document_id");
    }
}
