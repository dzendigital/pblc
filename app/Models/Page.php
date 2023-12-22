<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Menu;

class Page extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "pages";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "menu_id",
        "title",
        "slug",
        "is_visible",
        "body",
        "meta_title",
        "meta_description",
        "meta_keywords",
    ];

    public function menu()
    {
        # меню: принадлежность к элементу меню
        return $this->belongsTo(Menu::class);
    }
    /*
     *
     * Страница может возвращать или blade шаблон или content (из панели управления)
     *
     */
    public function getContent() {
        dd(__METHOD__, $this->view);
        if (!empty($this->view)){
            return view( $this->view, ['page' => $this] )->render();
        }else{
            return $this->content;
        }
    }
}
