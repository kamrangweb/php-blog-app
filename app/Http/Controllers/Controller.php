<?php

namespace App\Http\Controllers;

use App\Domain\Database\DBConnection;


abstract class Controller
{
    /**
     * @var DBConnection
     */
    protected $db;

    /**
     * @param DBConnection $db
     */
    public function __construct(DBConnection $db)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->db = $db;
    }

    /**
     * @return DBConnection
     */
    protected function getDB(): DBConnection
    {
        return $this->db;
    }


    protected function view(string $view, array $params = null)
    {
        ob_start();
        $viewPath = str_replace('.', '/', $view);
        require VIEWS_FOLDER . $viewPath . '.view.php';
        $content = ob_get_clean();
        return require VIEWS_FOLDER . 'layout.view.php';
    }

    protected function isLoggedIn()
    {
        if (
            isset($_SESSION['auth'])
            && $_SESSION['auth'] === 1
        ) {
            return true;
        }

        header('Location: ' . ROOT_URL . 'login');
    }
}