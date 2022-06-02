<?php

namespace App\Http\Controllers;

use App\Models\UserHobbies;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function finishRegister(Request $request)
    {
        $request->validate([
            'type' => 'required|in:discoverer,presenter',
        ]);

        $user = auth()->user();
        if ($user->status !== 'in_registration') {
            return response()->json([
                'error' => "USER_ALREADY_REGISTERED",
            ], 400);
        }

        if ($request->type == 'discoverer') {
            $request->validate([
                'hobbies' => 'required|string',
            ]);
            if ($user->hobbies) {
                return response()->json([
                    'error' => "USER_HAS_HOBBIES",
                ], 400);
            }
            $user = $this->_discovererRegistration($request->hobbies);
        }

        if ($request->type == 'presenter') {
            $request->validate([
                'categories' => 'required|string',
                'wish' => 'required|in:discover_projects,find_partner,build_network,raise_funds',
            ]);
            if ($user->project) {
                return response()->json([
                    'error' => "USER_HAS_PROJECT",
                ], 400);
            }
            // $user = $this->discoverRegistration($request);
        }

        return response()->json($user);
    }

    private function _discovererRegistration($hobbies)
    {
        $user = auth()->user();

        $hobbiesCreated = UserHobbies::create([
            'user_id' => $user->id,
            'hobbies' => $hobbies,
        ]);

        $user->update([
            'type' => 'discoverer',
            'status' => 'active',
        ]);

        return ["user" => $user, "hobbies" => $hobbiesCreated, "project" => null];
    }
}
