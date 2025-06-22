<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Models\User;
use App\Domain\Models\Post;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function index()
    {
        $this->isLoggedIn();

        $user_id = $_SESSION['user'];
        $user = (new User($this->getDB()))->getUserWithPostCount($user_id);
        $posts = (new Post($this->getDB()))->getPostsByUserId($user_id);

        return $this->view('admin.profile.index', compact(['user', 'posts']));
    }
} 