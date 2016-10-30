@extends('layouts.app')


@section('content')
    <h4 class="page-header">글목록

        <a href="{{route('posts.create')}}" class="btn btn-primary pull-right">
            새 글 쓰기

        </a>
    </h4>
    @if($posts)
        <ul>
            @foreach($posts as $post)
                <li><a href="{{route('posts.show',$post->id)}}">
                    {{$post->title}}
                    </a>
                </li>
                <small>
                    by {{$post->user->name}}
                </small>
            @endforeach
        </ul>

        <div class="text-center">
            {!! $posts->render() !!}
        </div>
    @else
        <p class="text-center">
            글이 없습니다.
        </p>
    @endif
@endsection