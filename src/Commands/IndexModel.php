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

        $models = $cls::all();

        foreach($models as $model) {
            IndexEntry::index($model);
        }

    }
}
