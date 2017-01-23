

Composer로 라라벨 프로젝트 설치
------------
```bash
composer create-project laravel/laravel myapp --prefer-dist --verbose
```


DB 생성 및 권한추가
------------
.env 파일 DB권한변경
```text
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=myblog
DB_USERNAME=homestead
DB_PASSWORD=secret
```

```mysql

mysql> CREATE DATABASE myblog;
# Query OK, 1 row affected (0.01 sec)

mysql> CREATE USER 'homestead' IDENTIFIED BY 'secret';
# Query OK, 0 rows affected (0.01 sec)

mysql> GRANT ALL PRIVILEGES ON myblog.* TO 'homestead';
# Query OK, 0 rows affected (0.00 sec)

mysql> FLUSH PRIVILEGES;
# Query OK, 0 rows affected (0.00 sec)

mysql> quit
# Bye
```


라라벨 Auth 사용
------------
```bash
php artisan make:auth
```

특정사용자 추가를 위해 Seed 생성
------------
```bash
php artisan make:seeder UsersTableSeeder
```

```php

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\User::create([
            'name' => 'anyman',
            'email' => 'anyman@afreecatv.com',
            'password' => bcrypt('password'),
        ]);
    }
}
```

Seeder 실행
```bash
php artisan db:seed --class=UsersTableSeeder
```

블로그용 Model 만들기 
------------
테이블 생성을 위한 migration 생성
```bash
php artisan make:migration create_posts_table --create=posts
```

테이블 생성
```bash
php artisan migrate
```

Model 생성
```bash
php artisan make:model Post
```

Model 설정  Post.php
```php
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    //
    protected $fillable = ['title','content'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```


User 모델수정 (User.php)
```php
use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable
{
    

    public function Posts(){
        return $this->hasMany(Post::class);
    }


    public function isAdmin(){
        return $this->id == 1;
    }
}

```



블로그 컨트롤러 생성
------------------
Controller 생성
```bash
 php artisan make:controller PostsController --resource
```

router 추가  (web.php)
```php
 Route::resource('posts','PostsController');
```


블로그 Layout 수정
------------------
메인 Layout  view  (resources/views/layouts/app.blade.php)
```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="/css/app.css" rel="stylesheet">

    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
                'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>

@include('layouts.navigation')

<div class="container">
    @yield('content')


</div>


<!-- Scripts -->
<script src="/js/app.js"></script>
@yield('script')
</body>
</html>

```

메뉴 view  (resources/views/layouts/navigation.blade.php)
```blade

<nav class="navbar navbar-default navbar-static-top">
    <div class="container">
        <div class="navbar-header">

            <!-- Collapsed Hamburger -->
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <!-- Branding Image -->
            <a class="navbar-brand" href="{{ url('/') }}">
                {{ config('app.name', 'Laravel') }}
            </a>
        </div>

        <div class="collapse navbar-collapse" id="app-navbar-collapse">
            <!-- Left Side Of Navbar -->
            <ul class="nav navbar-nav">
                &nbsp;<li>
                    <a href="{{route('posts.index')}}">글목록</a>
                </li>
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="nav navbar-nav navbar-right">
                <!-- Authentication Links -->
                @if (Auth::guest())
                    <li><a href="{{ url('/login') }}">Login</a></li>
                    <li><a href="{{ url('/register') }}">Register</a></li>
                @else
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            {{ Auth::user()->name }} <span class="caret"></span>
                        </a>

                        <ul class="dropdown-menu" role="menu">
                            <li>
                                <a href="{{ url('/logout') }}"
                                   onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                    Logout
                                </a>

                                <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        </ul>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>
```



블로그 글목록
------------------

글목록 controller  (PostsController.php)
```php
    public function index()
    {
        //
        $posts = \App\Post::with('user')->paginate(3);

        return view('posts.index', compact('posts'));
    }
```


글목록  view  (resources/views/posts/index.blade.php)
```blade
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
```


블로그 글쓰기
------------------

글쓰기  controller  (PostsController.php)
```php

    public function create()
    {
        $post = new \App\Post;
        return view('posts.create', compact('post'));
    }
```


글쓰기  view  (resources/views/posts/create.blade.php)
```blade
@extends('layouts.app')


@section('content')
    <h4 class="page-header">새 글쓰기
    </h4>

    <form method="post" action="{{route('posts.store')}}">

        {!! csrf_field() !!}
        @include('posts.partial.form')

        <div class="form-group text-center">
            <button type="submit" class="btn btn-primary">Post</button>
        </div>
    </form>
@endsection
```


Auth 적용하기 
------------------
```php

    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }
```


블로그 저장하기
------------------

글쓰기  controller  (PostsController.php)
```php

 /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)
    {
        //

        $post = $request->user()
            ->posts()
            ->create($request->all());

        return redirect(route('posts.show', $post->id));
    }
```

PostsRequest.php 만들기
```bash
php artisan make:seeder UsersTableSeeder
```
PostsRequest.php 소스
```php

 <?php
 
 namespace App\Http\Requests;
 
 use Illuminate\Foundation\Http\FormRequest;
 
 class PostsRequest extends FormRequest
 {
     /**
      * Determine if the user is authorized to make this request.
      *
      * @return bool
      */
     public function authorize()
     {
         return true;
     }
 
     /**
      * Get the validation rules that apply to the request.
      *
      * @return array
      */
     public function rules()
     {
         return [
             'title'=>['required','min:2'],
             'content'=>['required','min:2']
         ];
     }
 }

```

블로그 글보기
------------------
글보기  controller  (PostsController.php)
```php

    
   public function show(Post $post)
      {
  
          $post->load('user');
          return view('posts.show', compact('post'));
      }
```

글보기  view  (resources/views/posts/show.blade.php)
```blade
@extends('layouts.app')


@section('content')
    <h4 class="page-header">상세보기
        <small>
            -{{$post->title}}
        </small>
    </h4>

    <article>
        <div class="box-meta">
            {{ $post->user->name }}
            .
            {{$post->created_at->diffForHumans()}}
        </div>

        {!! markdown($post->content) !!}
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
            'el': '.box-control',
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
```


MarkDown 설치하기
------------------
erusev/parsedown-extra 패키지 설치하기
https://github.com/erusev/parsedown-extra 
```bash
composer require "erusev/parsedown-extra: ~0.7.1"
```

사용자 함수 만들기
```php
<?php
/**
 * Created by PhpStorm.
 * User: yoon2
 * Date: 2016. 9. 30.
 * Time: 오후 5:26
 */

function markdown($markdown)
{
 return   app(ParsedownExtra::class)->text($markdown);

}
```

사용자 함수 Composer에 등록하기 
```json

    "autoload": {
        "classmap": [
            "database"
        ],
        "psr-4": {
            "App\\": "app/"
        },
        "files":[
            "app/helpers.php"
        ]
    },
```


ability 정의(사용자가 특정 동작을 할수 있는지 체크방법)  
------------------
AuthServiceProvider 를 이용한 ability 추가
```php
class AuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerPolicies();

        //어드민 모든권한
        Gate::before( function ($user, $ability) {
            if($user->isAdmin()){
                return true;
            }
        });
        //
        Gate::define('update', function ($user, $model) {
            return $user->id = $model->user_id;
        });

        Gate::define('delete', function ($user, $model) {
            return $user->id = $model->user_id;
        });
    }
}
```




블로그 업데이트
------------------

글수정 controller  (PostsController.php)
```php
    
    public function update(PostsRequest $request, Post $post)
    {

        //권한이 없으며 뒤로
        $this->authorize('update', $post);


        $post->update($request->all());
        return redirect(route('posts.show',$post->id));

    }
```



블로그 삭제하기
------------------

글수정 controller  (PostsController.php)
```php
    public function destroy(Post $post)
    {
        //
        //권한이 없으며 뒤로
        $this->authorize('delete', $post);

        $post->delete();

        return response()->json([],204);
    }
```


Gulp 사용하기
------------------

https://nodejs.org/ko/
```bash
npm install --global gulp
```
```bash
npm install
```
빌드하기
```bash
gulp
```



Vue 사용하기
------------------
Vue.component 주석
```javascript
/*
Vue.component('example', require('./components/Example.vue'));

const app = new Vue({
    el: '#app'
});
*/
```



