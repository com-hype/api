<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $user_interests = auth()->user()->interests;
        $projects = Project::whereHas('categories', function ($query) use ($user_interests) {
            $query->whereIn('interest_id', $user_interests->pluck('id'));
        })->get();

        // remove projects that the user has already liked
        $projects = $projects->filter(function ($project) {
            return !auth()->user()->actions()->where('likeable_id', $project->id)->where('likeable_type', 'App\Models\Project')->count();
        });

        return response()->json($projects);
    }

    public function like(Request $request, Project $project)
    {
        $request->validate([
            'action' => 'required|in:like,dislike',
        ]);

        $user = auth()->user();

        if ($user->id === $project->user_id) {
            return response()->json([
                'error' => "USER_CANNOT_LIKE_HIMSELF",
            ], 400);
        }

        $like = $project->action()->where('user_id', $user->id)->first();

        if ($like) {
            return response()->json([
                'error' => "USER_ALREADY_LIKED_THIS_PROJECT",
            ], 400);
        }

        $project->action()->create([
            'user_id' => $user->id,
            'action' => $request->action,
        ]);

        return response()->json(['status' => 'ok']);
    }
}
