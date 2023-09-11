<?php

Route::middleware('web')->group(function() {

    if(config('sitesearch.useroutes')) {
        Route::get('/search', [\AscentCreative\SiteSearch\Controllers\SearchController::class, 'search'])->name('sitesearch');
    }

});

