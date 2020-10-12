@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-center"><b>SIMPLE TODO LISTS</b></h1>
    <h3 class="text-center" style="margin-top: 0px;">FROM RUBY GARAGE</h3>

    
    
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            {{ $projects->links() }}
        </div>

        <projects
            class="col-md-8 col-md-offset-2"
            :data-projects="{{ json_encode($projects->items()) }}"
            :current-page="{{ $projects->currentPage() }}"
        ></projects>
    </div>
</div>
@endsection
