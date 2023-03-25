<?php
namespace AscentCreative\SiteSearch\Traits;

use AscentCreative\SiteSearch\Models\IndexEntry;


/**
 * Trait which allows a model to be indexed for searching.
 * 
 * When a model is saved, the related Index model is updated, making the content available for FullText Search
 * 
 * For this to happen, the trait uses the 'indexable' property of the host model to determine which fields 
 * are indexed and in what order (used to determine relevance).
 * 
 * When simple field values are to be used, the indexable property may be defined as an array property:
 * 
 *      public $indexable = ['title', 'body', 'summary', ....]
 * 
 * However, some fields may need to be converted / processed to turn into text values 
 * (JSON fields for example may contain nuggets of indexable content). In these cases
 * we can use a mutator to create an array including \Closures. The model will be passed to these closures as a parameter.
 * 
 *      public function getIndexableAttribute() {
 *      
 *          return [
 *              'title',
 *              'body',
 *              function($model) {
 *                  return doSomeStuff($model->JSONField);
 *              },
 *              'anotherField',
 *              ...
 *          ];
 *      
 *      }
 */

trait Indexable {

    public static function bootIndexable() {

        static::saved(function ($model) {
            IndexEntry::index($model);
        });

    }

    public function buildIndexContent() {
        
        $out = [];
        if(is_array($this->indexable)) {

            foreach($this->indexable as $fld) {

                if (is_string($fld)) {
                    $out[] = $this->$fld;
                }

                if ($fld instanceof \Closure) {
                    $out[] = $fld($this);
                }

            }

        }

        // dd($out);

        return join(' ', $out);

    }

    public function indexentry() {
        return $this->morphOne(IndexEntry::class, 'indexable');
    }

    public function scopeFulltextSearch($q, $query) {
        $q->whereHas('indexentry', function($q) use ($query) {
            $q->fulltextSearch($query);
        });
    }

}