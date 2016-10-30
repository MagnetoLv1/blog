<?php

namespace App\Listeners;

use App\Events\PostCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PostEventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  PostCreated  $event
     * @return void
     */
    public function handle(PostCreated $event)
    {
        //
        $post = $event->post;

        \Mail::send(
            'emails.posts.created',
            compact('post'),
            function($message) use ($post){
                $message->to('yours@domain');
                $message->subject('새글이 등록되었습니다.-'.$post->title);
            }
        );
    }
}
