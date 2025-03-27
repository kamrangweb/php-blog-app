<?php

namespace App\Http\Controllers;

use App\Domain\Models\User;
use App\Utils\Validator;

class UserController extends Controller
{
   
    public function login()
    {
        return require VIEWS_FOLDER . 'auth/login.view.php';
    }

    public function register()
    {
        return require VIEWS_FOLDER . 'auth/register.view.php';
    }

 
    public function signup()
    {
        $validator = new Validator($_POST);

        $errors = $validator->validate([
            'username' => ['required', 'min:3'],
            'password' => ['required']
        ]);

        if ($errors) {
            $_SESSION['errors'] = $errors;

            header("Location: " . ROOT_URL . "login");



            return;
        }

        $user = new User($this->getDB());
        $userDTO = $user -> findByUsername($_POST['username']);


        if ($userDTO) {

            $_SESSION['errors']['username'][] = 'The user exists';
            header("Location: " . ROOT_URL . "register");

        }else{

            $options = [
                'cost' => 10,
            ];

            $_POST['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT, $options);
            $user -> create($_POST);
            header("Location: " . ROOT_URL . "login");
            return;
        }
    }


    public function signin()
    {
        $validator = new Validator($_POST);

        $errors = $validator->validate([
            'username' => ['required', 'min:3'],
            'password' => ['required']
        ]);

        if ($errors) {
            $_SESSION['errors'] = $errors;

            header("Location: " . ROOT_URL . "login");


            return;
        }

        $user = (new User($this->getDB()))->findByUsername($_POST['username']);

        if ($user) {
            if (password_verify($_POST['password'], $user->password)) {
                $_SESSION['user'] = $user -> id;
                $_SESSION['auth'] = 1;
                $_SESSION['msg']['success'] = 'Welcome '.strtoupper($user->username).'!';

                header("Location: " . ROOT_URL . "admin/posts");

                return;
            }
            $_SESSION['errors']['password'][] = 'Incorrect password';

            header('Location: ' . ROOT_URL . 'login');


            return;
        }

        $_SESSION['errors']['username'][] = 'The user does not exist.';

        header('Location: ' . ROOT_URL . 'login');
    }

    public function logout(): void
    {
        session_destroy();
        header("Location: " . ROOT_URL);
    }
}