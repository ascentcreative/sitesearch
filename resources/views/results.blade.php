@foreach($results as $result)

    <div>
    {{ $result->title }}
    ( {{ $result->sitesearch_score }} )
    </div>


@endforeach