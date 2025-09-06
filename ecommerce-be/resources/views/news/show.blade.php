@extends('layouts.app')
@section('title', $data->title )
@section('content')
<section class="jumbotron h-j1 jumbotron-fluid" >
            <div class="container">
                <br />
                 <h1 class="display-4 text-center">{{ $data->title }}</h1>
                     <p class="lead "><strong>POSTED BY: </strong>{{ $data->user->label}} {{ $data->created_at}}</p>
                    <hr class="my-4">
                    <div class="container-fluid">

                        <div>
                                {!! $data->desc !!}
                           <br />
                        </div>
                        <div>


            </div>

            <div class="container" >
                 <div class="be-comment-block">
                    @if(Session::has('comments'))
                        <div class="alert alert-primary alert-dismissible fade show" role="alert">
                            <strong>Success!</strong> Your comment is added for moderation.
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                                <h1 class="comments-title">Comments ({{ count($data->comments) }})</h1>
                                @foreach($data->comments as $comment)
                                <div class="be-comment">
                                    <div class="be-img-comment">
                                        <a href="#">
                                            <img src="https://bootdey.com/img/Content/avatar/avatar{{ rand(1, 3) }}.png" alt="" class="be-ava-comment">
                                        </a>
                                    </div>
                                    <div class="be-comment-content">

                                            <span class="be-comment-name">
                                                <a href="#">{{ $comment->name }}</a>
                                                </span>
                                            <span class="be-comment-time">
                                                <i class="fa fa-clock-o"></i>
                                                {{ $comment->created_at }}
                                            </span>

                                        <p class="be-comment-text">
                                           {{ $comment->name }}
                                        </p>
                                    </div>
                                </div>
                                @endforeach

                                <form class="form-block" action="/comments" method="post">
                                @csrf
                                    <input type="hidden" value="{{ $data->optimus_id }}" name="announcement_id" />
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-6">
                                            <div class="form-group fl_icon">
                                                <div class="icon"><i class="fa fa-user"></i></div>
                                                <input class="form-input" type="text" placeholder="Your name" required name="name">
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 fl_icon">
                                            <div class="form-group fl_icon">
                                                <div class="icon"><i class="fa fa-envelope-o"></i></div>
                                                <input class="form-input" type="email" placeholder="Your email" required name="email">
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-12">
                                            <div class="form-group ">
                                                <textarea class="form-input" required="" placeholder="Your text" required name="desc"></textarea>
                                            </div>
                                        </div>
                                        <a class="btn btn-success ml-1" href="/news" role="button" name="add-comment">Back</a>
                                        <button class="btn btn-primary  ml-1" type="submit">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
            </div>

</section>
@endsection
