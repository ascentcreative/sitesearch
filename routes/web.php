<?php

Route::middleware('web')->group(function() {

    Route::get('/search', [\AscentCreative\SiteSearch\Controllers\SearchController::class, 'search'])->name('sitesearch');

});

