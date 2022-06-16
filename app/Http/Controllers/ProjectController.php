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

        $projectsFiltered = [];
        foreach ($projects as $project) {
            $isAnswered = $project->action()->where('user_id', auth()->user()->id)->first();
            if (!$isAnswered) {

                $projectsFiltered[] = ['info' => $project, 'images' => $project->images, 'crowdfunding' => $project->crowdfunding, 'features' => $project->features];
            }
        }

        return response()->json($projectsFiltered);
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

    public function getStats()
    {
        $project = auth()->user()->project;

        // récupère le nombre de like par heure dans la journée
        $likes = $project->action()->where('action', 'like')->where('created_at', '>=', now()->startOfDay())->get();
        $likesByHour = $likes->groupBy(function ($item) {
            return $item->created_at->format('H');
        });

        return response()->json([
            'likes' => [
                'byHour' => $likesByHour,
                'total' => $likes->count(),
            ]
        ]);
    }

    public function get()
    {
        $project = auth()->user()->project;

        return response()->json(['info' => $project, 'images' => $project->images, 'crowdfunding' => $project->crowdfunding, 'features' => $project->features]);
    }

    public function getProject(Project $project)
    {
        return response()->json(['info' => $project, 'images' => $project->images, 'crowdfunding' => $project->crowdfunding, 'features' => $project->features, 'isYourProject' => auth()->user()->id === $project->user_id]);
    }

    public function getCrowdfunding(Project $project)
    {
        return response()->json($project->crowdfunding);
    }

    public function getFeatures(Project $project)
    {
        return response()->json($project->features);
    }

    public function editFeatures(Request $request, Project $project)
    {
        $request->validate([
            'features' => 'required|array',
            'features.*.name' => 'required|string',
            'features.*.description' => 'required|string',
        ]);

        if (auth()->user()->id !== $project->user_id) {
            return response()->json([
                'error' => "USER_CANNOT_EDIT_THIS_PROJECT",
            ], 400);
        }

        $project->features()->delete();

        foreach ($request->features as $feature) {
            $project->features()->create($feature);
        }

        return response()->json($project->features);
    }
}
