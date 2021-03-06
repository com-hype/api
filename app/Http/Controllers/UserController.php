<?php

namespace App\Http\Controllers;

use App\Models\Interest;
use App\Models\Project;
use App\Models\UserHobbies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function presenterRegistration(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:40',
            'avatar' => 'image|mimes:jpeg,png,jpg',
            'description' => 'required|string|max:150',
            'categories' => 'required|string',

            'crowdfunding_description' => 'required|string',
            'crowdfunding_goal' => 'required|integer',
        ]);

        if (auth()->user()->status !== 'in_registration') {
            return response()->json([
                'error' => "USER_ALREADY_REGISTERED",
            ], 400);
        }

        if (!empty($request->avatar)) {
            $avatar = cloudinary()->upload($request->file('avatar')->getRealPath(), [
                'folder' => 'avatars'
            ])->getSecurePath();
        } else {
            $avatar = null;
        }

        $project = auth()->user()->project()->create([
            'name' => $request->name,
            'title' => $request->title,
            'description' => $request->description,
            'avatar' => $avatar,
        ]);

        $categories = explode(',', $request->categories);

        foreach ($categories as $categorieName) {
            $categorie = Interest::where('name', $categorieName)->first();
            if (!$categorie) {
                $categorie = Interest::create([
                    'name' => $categorieName,
                ]);
            }
            auth()->user()->interests()->save($categorie);
            $project->categories()->save($categorie); //sync
        }

        $project->crowdfunding()->create([
            'description' => $request->crowdfunding_description,
            'goal' => $request->crowdfunding_goal,
        ]);

        auth()->user()->update([
            'type' => 'presenter',
            'status' => 'active',
        ]);

        return ["user" => auth()->user(), "project" => $project];
    }

    public function discovererRegistration(Request $request)
    {
        $request->validate([
            'interests' => 'required|string',

        ]);

        if (auth()->user()->status !== 'in_registration') {
            return response()->json([
                'error' => "USER_ALREADY_REGISTERED",
            ], 400);
        }

        $interests = explode(',', $request->interests);

        foreach ($interests as $interestName) {
            $interest = Interest::where('name', $interestName)->first();
            if (!$interest) {
                $interest = Interest::create([
                    'name' => $interestName,
                ]);
            }
            auth()->user()->interests()->save($interest); //sync
        }

        auth()->user()->update([
            'type' => 'discoverer',
            'status' => 'active',
        ]);
        return ["user" => auth()->user(), "interests" => auth()->user()->interests];
    }

    public function likedProject()
    {
        $likes = auth()->user()->likes()->get();
        $projects = [];
        foreach ($likes as $like) {
            if ($like->likeable_type == 'App\Models\Project') {

                $tmp_project = Project::find($like->likeable_id);
                $projects[] = [
                    'info' => $tmp_project,
                    'images' => $tmp_project->images()->get(),
                ];
            }
        }

        return response()->json(
            $projects,
            200
        );
    }
}
