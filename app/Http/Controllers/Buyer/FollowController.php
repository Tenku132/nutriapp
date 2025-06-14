<?php

namespace App\Http\Controllers\Buyer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FollowController extends Controller
{
public function follow($id)
{
    $user = auth()->user();
    $alreadyFollowing = $user->following()->where('followed_id', $id)->exists();

    if ($alreadyFollowing) {
        $user->following()->detach($id);
    } else {
        $user->following()->attach($id);
    }

    return redirect()->back();
}
public function unfollow($id)
{
    $user = auth()->user();
    $user->following()->detach($id);

    return redirect()->back();
}
}
