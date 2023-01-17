<?php

namespace AscentCreative\SiteSearch\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Laravel\Scout\Searchable;

class IndexEntry extends Model
{
    use HasFactory, Searchable;

    public $table = "sitesearch_index";

    public $with = ['indexable'];

    public $fillable = ['indexable_type', 'indexable_id', 'content'];

    
    public function toSearchableArray(){
        return ['content'=>$this->content];
    }

    public function indexable() {
        return $this->morphTo();
    }

    // public function getIndexableAttribute() {
        
    //     $eager = session()->get('sitesearch_eager');

    //     if($eager && isset($eager[$this->indexable_type]) && isset($eager[$this->indexable_type][$this->indexable_id]) ) {
    //         return $eager[$this->indexable_type][$this->indexable_id];
    //     } 

    //     return $this->indexable()->first();

    // }

    public static function index($model) {
        IndexEntry::updateOrCreate([
            'indexable_type'=>get_class($model),
            'indexable_id'=>$model->id,
        ], [
            'content'=>preg_replace('/\s+/', ' ', $model->buildIndexContent()),
        ]);
    }

}

