@extends('layouts.app')


@section('content')
    <h4 class="page-header">상세보기
        <small>
            -{{$post->title}}}
        </small>
    </h4>

    <article>
        <div class="box-meta">
            {{ $post->user->name }}
            .
            {{$post->created_at->diffForHumans()}}
        </div>

        {{!! markdown($post->content) !!}}
        {{$post->content}}
    </article>

    <div class="box-control text-center">

        <a href="{{route('posts.index')}}" class="btn btn-default">
            목록

        </a>

        @can('update',$post)
            <a href="{{route('posts.edit',$post->id)}}" class="btn btn-warning">
                수정
            </a>
        @endcan
        @can('delete',$post)
            <button class="btn btn-danger" id="delete-post " @click='deletePost'>
                삭제
            </button>
        @endcan
    </div>
@endsection




@section('script')

    <script>
        new Vue({
            'el': 'body',
            methods: {
                deletePost: function () {
                    if (confirm('삭제할깡?')) {
                        this.$http.delete('{{route('posts.destroy', $post->id)}}')
                                .then(function (response) {
                                    alert('삭제되었습니다.');
                                    window.location.href = '{{ route('posts.index') }}'
                                });
                    }
                }
            }
        });
    </script>

@endsection