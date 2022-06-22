<?php

namespace App\Http\Controllers;

use App\Events\ReceiveMessage;
use App\Models\Discussion;
use App\Models\DiscussionMember;
use App\Models\User;
use Illuminate\Http\Request;

class DiscussionController extends Controller
{

    public function getDiscussions()
    {
        $discussions = [];
        foreach (DiscussionMember::where('user_id', auth()->user()->id)->get() as $discussionMember) {
            $discussions[] = Discussion::find($discussionMember->discussion_id);
        }

        $formatedDiscussions = [];
        foreach ($discussions as $discussion) {
            $lastMassage = $discussion->messages()->latest()->first();
            $title = $discussion->members()->where('user_id', '!=', auth()->user()->id)->first()->user->username;
            $formatedDiscussions[] = [
                'id' => $discussion->id,
                'title' => $title,
                'newMessage' => $lastMassage ? $lastMassage->is_read : false,
                'lastMessage' =>  $lastMassage ? $lastMassage : "",
                'created_at' => $lastMassage ? $lastMassage->created_at : $discussion->created_at,
            ];
        }
        return response()->json($formatedDiscussions);
    }

    public function create(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $discussionOfUser = DiscussionMember::where('user_id', $request->user_id)->get();
        $discussionOfMe = DiscussionMember::where('user_id', auth()->user()->id)->get();

        // si on trouve une discussion on lui renvoi
        foreach ($discussionOfUser as $discussion) {
            foreach ($discussionOfMe as $discussionMe) {
                if ($discussion->discussion_id == $discussionMe->discussion_id) {
                    return response()->json(Discussion::where('id', $discussion->discussion_id)->first());
                }
            }
        }

        $discussion = Discussion::create();
        DiscussionMember::create([
            'user_id' => auth()->user()->id,
            'discussion_id' => $discussion->id,
        ]);
        DiscussionMember::create([
            'user_id' => $request->user_id,
            'discussion_id' => $discussion->id,
        ]);
        return response()->json($discussion);
    }

    public function getMessages(Discussion $discussion)
    {
        if (!$discussion->members()->where('user_id', auth()->user()->id)->first()) {
            return response()->json(['error' => 'USER_NOT_ALLOWED'], 403);
        }

        $title = $discussion->members()->where('user_id', '!=', auth()->user()->id)->first()->user->username;

        $formatedMessages = [];
        foreach ($discussion->messages()->latest()->get() as $message) {
            if ($message->messageable_type == 'App\Models\User') {
                $author = User::find($message->messageable_id);
                $formatedMessages[] = [
                    'id' => $message->id,
                    'author' => [
                        'id' => $author->id,
                        'avatar' => $author->avatar,
                        'username' => $author->username,
                    ],
                    'body' => $message->body,
                    'is_read' => $message->is_read,
                    'created_at' => $message->created_at,
                ];
            }
        }

        return response()->json([
            'title' => $title,
            'messages' => $formatedMessages
        ]);
    }

    public function createMessage(Request $request, Discussion $discussion)
    {
        $request->validate([
            'body' => 'required|string',
        ]);

        if (!$discussion->members()->where('user_id', auth()->user()->id)->first()) {
            return response()->json(['error' => 'USER_NOT_ALLOWED'], 403);
        }

        $message = $request->user()->messages()->create([
            'discussion_id' => $discussion->id,
            'body' => $request->body,
        ]);

        $formatedMessage = [
            'id' => $message->id,
            'author' => [
                'id' => $request->user()->id,
                'avatar' => $request->user()->avatar,
                'username' => $request->user()->username,
            ],
            'body' => $message->body,
            'is_read' => $message->is_read,
            'created_at' => $message->created_at,
        ];

        foreach ($discussion->members as $member) {
            if ($member->user_id !== auth()->user()->id) {
                event(new ReceiveMessage(
                    auth()->user()->username,
                    auth()->user(),
                    $formatedMessage
                ));
            }
        }

        return response()->json($formatedMessage);
    }
}
