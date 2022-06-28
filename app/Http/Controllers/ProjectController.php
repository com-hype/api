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
                $projectsFiltered[] = ['info' => $project, 'images' => $project->images, 'crowdfunding' => $project->crowdfunding, 'features' => $project->features, 'categories' => $project->categories];
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


        $likes = $project->likes;

        $likesByHour = [];
        $likesByHourLabel = [];
        $likesByDay = [];
        $likesByDayLabel = [];
        $likesByMonth = [];
        $likesByMonthLabel = [];
        for ($i = 0; $i < 24; $i++) {
            $tmp_hour = date('Y-m-d H:i:s', strtotime('-' . $i . ' hours'));

            $likesByHour[] = $likes->where('created_at', '>=', $tmp_hour)
                    ->where('created_at', '<', date('Y-m-d H:i:s', strtotime($tmp_hour . ' +1 hour')))
                    ->count();

            $likesByHourLabel[] = date('H:i', strtotime($tmp_hour)),
        }

        for ($i = 0; $i <= 7; $i++) {
            $tmp_day = date('Y-m-d', strtotime('-' . $i . ' days'));

            $likesByDay[] = $likes->where('created_at', '>=', $tmp_day)
                ->where('created_at', '<', date('Y-m-d', strtotime($tmp_day . ' +1 day')))
                ->count();

            $likesByDayLabel[] = date('d/m', strtotime($tmp_day));

        }

        for ($i = 0; $i <= 12; $i++) {
            $tmp_month = date('Y-m', strtotime('-' . $i . ' months'));

            $likesByMonth[] = $likes->where('created_at', '>=', $tmp_month)
                    ->where('created_at', '<', date('Y-m', strtotime($tmp_month . ' +1 month')))
                    ->count();
            $likesByMonthLabel[] = date('m/Y', strtotime($tmp_month));
        }

        return response()->json([
            'likes' => [
                'day' =>[
                    'labels' => $likesByDayLabel,
                    'data' => $likesByDay,
                ],
                'week' => [
                    'labels' => $likesByDayLabel,
                    'data' => $likesByDay,
                ],
                'month' => [
                    'labels' => $likesByMonthLabel,
                    'data' => $likesByMonth,
                ],
                'total' => $likes->count(),
            ],
            'prints' => $project->likes()->count() + $project->dislikes()->count(),
            'amount' => $project->crowdfunding->amount
        ]);
    }

    public function get()
    {
        $project = auth()->user()->project;

        return response()->json(['info' => $project, 'images' => $project->images, 'crowdfunding' => $project->crowdfunding, 'features' => $project->features, 'categories' => $project->categories]);
    }

    public function getProject(Project $project)
    {
        return response()->json([
            'info' => $project,
            'images' => $project->images,
            'crowdfunding' => $project->crowdfunding,
            'features' => $project->features,
            'isYourProject' => auth()->user()->id === $project->user_id,
            'categories' => $project->categories,
            'author' => User::find($project->user_id),
        ]);
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
