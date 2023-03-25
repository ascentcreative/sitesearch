<?php

namespace AscentCreative\SiteSearch\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Events\Dispatcher;

use AscentCreative\SiteSearch\Models\IndexEntry;


class IndexModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitesearch:index {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index all records of a given model';

    protected $indexService;

   
    /**
     * Execute the console command.
     *
     * @param Dispatcher $events
     *
     * @return mixed
     */
    public function handle(Dispatcher $events)
    {
      
        $cls = $this->argument('model');

        // TODO: Check that $cls uses the Indexable trait.

        IndexEntry::where('indexable_type', $cls)->delete();

        $models = $cls::setEagerLoads([])->paginate(2000);
        $page = 1;

        while($models->hasMorePages()) {

            foreach($models as $model) {
                IndexEntry::index($model);
            }

            $page++;
            $models = $cls::setEagerLoads([])->paginate(2000, ['*'], 'page', $page);

        }

    }
}
