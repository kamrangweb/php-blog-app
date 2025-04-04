<?php

namespace App\Http\Controllers;

use App\Domain\Models\Post;
use App\Domain\Models\Tag;
use App\Domain\Models\Category;
use App\Domain\Models\TypingTexts;
use App\Exceptions\NotFoundException;


class BlogController extends Controller
{

    public function welcome()
    {
        $posts = (new Post($this->getDB()))->all();
        $texts = (new TypingTexts($this->getDB()))->getTypingTexts();

        // print_r($texts);

        return $this->view('blog.welcome', compact(['posts','texts']));
    }

    public function index()
    {
        $postsAll = (new Post($this->getDB()))->all();

        $perPage = 6; // Sayfa başına gösterilecek post sayısı
        $totalPages = ceil(count($postsAll) / $perPage);

        $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;

        if ($currentPage < 1) {
            $currentPage = 1;
        } elseif ($currentPage > $totalPages) {
            $currentPage = $totalPages;
        }

        if (isset($_GET['page']) && $_GET['page'] != $currentPage) {
            header("Location: " . ROOT_URL .  "posts?page=$currentPage");
            exit;
        }

        $offset = ($currentPage - 1) * $perPage;

        $posts = (new Post($this->getDB()))->getPostsPage($perPage, $offset);
        $tags = (new Tag($this->getDB()))->all();
        $categories = (new Category($this->getDB())) -> all();

       

        return $this->view('blog.index', compact(['posts','tags', 'categories', 'currentPage', 'totalPages']));
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