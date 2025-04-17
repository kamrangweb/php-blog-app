<?php 

namespace App\Http\Controllers;


class PageController extends Controller{


public function cookiePolicy() {
    // return view('cookie-policy.view.php');
    return require VIEWS_FOLDER . 'cookie-policy.view.php';
}

}
