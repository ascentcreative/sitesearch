<?php 
namespace AscentCreative\SiteSearch\Engines;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

use Laravel\Scout\Builder;
use Laravel\Scout\Engines\Engine;
use AscentCreative\SiteSearch\Models\IndexEntry;


class EloquentEngine extends Engine {

     /**
     * Update the given model in the index.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $models
     * @return void
     */
    public function update($models) {
        // No action needed - handled by Indexable Trait
    }

    /**
     * Remove the given model from the index.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $models
     * @return void
     */
    public function delete($models) {
        // no action needed - handled by Indexable Trait
    }

    public function makeQuery(Builder $builder) {
        
        $match = "MATCH (content) AGAINST (? IN NATURAL LANGUAGE MODE)";

        // we're only ever searching Index models
        $query = IndexEntry::
                            //select('*')
                            selectRaw('*, ' . $match . ' as score', Arr::wrap($builder->query))
                            ->whereHas('indexable')
                            // whereNotNull('content')
                            ->whereRaw($match, $builder->query)
                            ->with('indexable');

        foreach($builder->orders as $order) {
            $query->orderBy($order['column'], $order['direction']);
        }
                            // ->where(DB::Raw("MATCH (content) AGAINST (? IN NATURAL LANGUAGE MODE)"), '>', 0.1)
                            // ->orderBy('score');

        return $query;
    }


    /**
     * Perform the given search on the engine.
     *
     * @param  \Laravel\Scout\Builder  $builder
     * @return mixed
     */
    public function search(Builder $builder) {
        
        // dump($builder->model);

        // dump($builder->query);

        // dump(IndexEntry::query());

        return $this->makeQuery($builder); //->get();


    }

    /**
     * Perform the given search on the engine.
     *
     * @param  \Laravel\Scout\Builder  $builder
     * @param  int  $perPage
     * @param  int  $page
     * @return mixed
     */
    public function paginate(Builder $builder, $perPage, $page) {
        // dd('TODO: Paginate method');
        // $builder->limit = $perPage;
        // $builder->offset = ($perPage * $page) - $perPage;
        return $this->makeQuery($builder)->paginate($perPage, $page);
        // // return $this->search($builder); //->paginate($perPage, $page);
        // return $this->makeQuery($builder)->paginate($perPage, $page);
    }

    /**
     * Pluck and return the primary keys of the given results.
     *
     * @param  mixed  $results
     * @return \Illuminate\Support\Collection
     */
    public function mapIds($results) {
        echo 'mapIds';
        // borrowd from mysql engine - may need updating
        return collect($results['results'])->map(function ($result) {
            return $result->getKey();
        });
    }

    /**
     * Map the given results to instances of the given model.
     *
     * @param  \Laravel\Scout\Builder  $builder
     * @param  mixed  $results
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function map(Builder $builder, $results, $model) {
        
        // pull out the related indexable and also insert the relevance score
        // $matches = $results->get();

        // TODO: Figure out why the eager-loaded models aren't used here!
        $matches = $results->map(function($result) {
            if($idxable = $result->indexable) {
                $match = $idxable;
                $match->sitesearch_score = $result->score;
                return $match;
            } else {
                return null;
            }
        });

        // dd($matches->filter());
        // remove any null items
        return $matches->filter();

    }

    /**
     * Map the given results to instances of the given model via a lazy collection.
     *
     * @param  \Laravel\Scout\Builder  $builder
     * @param  mixed  $results
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Illuminate\Support\LazyCollection
     */
    public function lazyMap(Builder $builder, $results, $model) {
        // echo 'lazy?';
        // not sure if this is needed? Will see.
    }

    /**
     * Get the total count from a raw result returned by the engine.
     *
     * @param  mixed  $results
     * @return int
     */
    public function getTotalCount($results) {
        // dump($results);
        return $results->count();
    }

    /**
     * Flush all of the model's records from the engine.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function flush($model) {
        // not sure if this is needed? Will see.
    }

    /**
     * Create a search index.
     *
     * @param  string  $name
     * @param  array  $options
     * @return mixed
     */
    public function createIndex($name, array $options = []) {
        // not sure if this is needed? Will see.
    }

    /**
     * Delete a search index.
     *
     * @param  string  $name
     * @return mixed
     */
    public function deleteIndex($name) {
        // not sure if this is needed? Will see.
    }



}

