<?php

namespace App\Http\Controllers;

use App\Domain\Models\Post;
use App\Domain\Models\Tag;
use App\Domain\Models\Category;
use App\Exceptions\NotFoundException;


class BlogController extends Controller
{

    public function welcome()
    {
        $posts = (new Post($this->getDB()))->all();

        return $this->view('blog.welcome', compact('posts'));
    }

    public function index()
    {
        $posts = (new Post($this->getDB()))->all();
        $tags = (new Tag($this->getDB()))->all();
        $categories = (new Category($this->getDB())) -> all();

        return $this->view('blog.index', compact(['posts','tags', 'categories']));
    }


    public function show(int $id)
    {
        $post = (new Post($this->getDB()))->findById($id);
        $posts = (new Post($this->getDB()))->all();


        if ($post) {
            return $this->view('blog.show', compact(['post','posts']));
        }

        throw new NotFoundException('Not found in DB');
    }


    
}