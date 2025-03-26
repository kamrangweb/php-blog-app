<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Models\Post;
use App\Domain\Models\Tag;
use App\Domain\Models\Category;
use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;


class PostController extends Controller
{

    public function index()
    {
        $this->isLoggedIn();

        $user_id = $_SESSION['user'];
        $posts = (new Post($this->getDB()))->getPostsByUserId($user_id);

        return $this->view('admin.posts.index', compact('posts'));
    }


    public function create()
    {
        $this->isLoggedIn();
        $tags = (new Tag($this->getDB()))->all();
        $categories = (new Category($this->getDB())) -> all();
        return $this->view('admin.posts.create', compact(['tags', 'categories']));
    }



    public function store(): void
{
    $this->isLoggedIn();
    
    $post = new Post($this->getDB());
    $tags = $_POST['tags'] ?? [];
    unset($_POST['tags']);

    // Yükleme dizini
    // $uploads_dir = ROOT_URL . 'uploads/';
    $uploads_dir = __DIR__ . '/../../../../public/uploads/'; 

    if (!is_dir($uploads_dir)) {
        mkdir($uploads_dir, 0777, true);
    }

    if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['image_path']['tmp_name'];
        $name = $_FILES['image_path']['name'];
        $ext = pathinfo($name, PATHINFO_EXTENSION);

        $unique_name = uniqid('img_', true) . '.' . $ext;
        $target_path = $uploads_dir . $unique_name;

        if (move_uploaded_file($tmp_name, $target_path)) {
            // $_POST['image_path'] = '/uploads/' . $unique_name; // Veritabanına kaydedilecek yol
            $_POST['image_path'] = ROOT_URL . 'uploads/' . $unique_name; 
        } else {
            die('Dosya yükleme başarısız!');
        }
    } else {
        $_POST['image_path'] = null;
    }

    $_POST['user_id'] = $_SESSION['user'];

    if ($post->create($_POST, $tags)) {
        header('Location: ' . ROOT_URL . 'admin/posts');
        exit;
    } else {
        die('Post kaydedilemedi!');
    }
}



    public function edit(int $id)
    {
        $this->isLoggedIn();
        $postObj = new Post($this->getDB());

        $post = $postObj->findById($id);
        $categories = (new Category($this->getDB())) -> all();

        if ($post) {
            $tags = $postObj -> getTags($post -> id);

            return $this->view('admin.posts.edit', compact(['tags', 'post', 'categories']));
        }

        throw new NotFoundException('Resource not found in the DB');
    }


    


    public function update()
    {
        $this->isLoggedIn();

        $post = (new Post($this->getDB()))->findById($_POST['id']);
        $tags = $_POST['tags'];
        unset($_POST['tags']);



        $uploads_dir = __DIR__ . '/../../../../public/uploads/'; 

        if (!is_dir($uploads_dir)) {
            mkdir($uploads_dir, 0777, true);
        }

        if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['image_path']['tmp_name'];
            $name = $_FILES['image_path']['name'];
            $ext = pathinfo($name, PATHINFO_EXTENSION);

            $unique_name = uniqid('img_', true) . '.' . $ext;
            $target_path = $uploads_dir . $unique_name;

            if (move_uploaded_file($tmp_name, $target_path) && isset($_POST['old_image'])) {
                $_POST['image_path'] = ROOT_URL . 'uploads/' . $unique_name; 
            } else {
                die('Dosya yükleme başarısız!');
            }
        } else {
            $_POST['image_path'] = $post->image_path;
            echo isset(['image_path']['error']);
        }


        






        if ($post) {
            if ($post->update($_POST, $tags)) {
                header('Location: '. ROOT_URL . 'admin/posts?' . $_POST['image_path']);
                return;
            }

            // $this->up
        }

        throw new NotFoundException('Not found');
    }

 
    public function delete(): void
    {
        $this->isLoggedIn();

        $post = (new Post($this->getDB()))->findById($_POST['id']);

        if ($post->destroy()) {
            header('Location: ' . ROOT_URL . 'admin/posts');
        }
    }
}