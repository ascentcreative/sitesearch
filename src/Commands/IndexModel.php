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

        $bar = $this->startBar($models->total(), "Indexing Models: " . $cls);

        while($models->count() > 0) { 
            // hasMorePages()) {

            foreach($models as $model) {
                IndexEntry::index($model);
                $bar->advance();
            }

            $page++;
            $models = $cls::setEagerLoads([])->paginate(2000, ['*'], 'page', $page);

        }

        $bar->advance();

    }


    private function startBar($max, $message=null) {
        $bar = $this->output->createProgressBar($max);
        if($message) {
            $bar->setFormat("%message%\n %current%/%max% [%bar%] %percent:3s%%");
            $bar->setMessage($message);
        }
        $bar->start();
        return $bar;
    }
}
