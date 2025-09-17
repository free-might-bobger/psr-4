@extends('layouts.app')
@section('title', 'News')
@section('content')
<section class="jumbotron h-j1 jumbotron-fluid" >
            <div class="container">
                <br />
                 <h1 class="display-4 text-center">News</h1>
                    <p class="lead text-center">What's happening in IIT?</p>
                    <hr class="my-4">
                    <div class="container-fluid">
                        @foreach($data as $d)
                        <div>
                            <h1 class="display-6">{{$d->title}}</h1>
                             <p class="lead "><strong>POSTED BY: </strong>{{ $d->user->label}} {{ $d->created_at}}</p>
                            <hr class="my-4">
                            <div>
                                {!! $d->desc !!}
                                <br />
                                <a class="btn btn-primary" href="/news/{{ $d->optimus_id }}" role="button">View Comments</a>
                            </div>

                            <br />
                        </div>
                        @endforeach
                        <br />
                        <br />
                        {{$data->links()}}
                    </div>
            </div>

</section>
@endsection
