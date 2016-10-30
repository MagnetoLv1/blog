<h1>{{ $post->title }}</h1>

<p>{{$post->name}}</p>

<p>{{$post->user->content}}</p>

<p>{{$post->created_at->timezone('Asia/Seoul')}}</p>