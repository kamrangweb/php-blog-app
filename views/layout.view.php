<?php
   // error_reporting(E_ALL);
   // ini_set('display_errors', '1');
   ?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
      <meta http-equiv="X-UA-Compatible" content="ie=edge">
      <title>Blog App</title>
      <!-- SEO Meta Tags -->
      <title>My Blog - Stay Updated with the Latest Posts</title>
      <meta name="description" content="Explore insightful articles and updates on technology, coding, and tutorials.">
      <meta name="keywords" content="blog, technology, coding, tutorials, news">
      <meta name="author" content="John Doe">
      <meta name="robots" content="index, follow">
      <!-- Open Graph / Facebook Meta Tags -->
      <meta property="og:type" content="article">
      <meta property="og:title" content="My Blog - Stay Updated with the Latest Posts">
      <meta property="og:description" content="Explore insightful articles and updates on technology, coding, and tutorials.">
      <meta property="og:image" content="https://example.com/default-image.jpg">
      <meta property="og:url" content="https://example.com/blog-post">
      <meta property="og:site_name" content="My Blog">
      <!-- Twitter Card Meta Tags -->
      <meta name="twitter:card" content="summary_large_image">
      <meta name="twitter:title" content="My Blog - Stay Updated with the Latest Posts">
      <meta name="twitter:description" content="Explore insightful articles and updates on technology, coding, and tutorials.">
      <meta name="twitter:image" content="https://example.com/default-image.jpg">
      <!-- Canonical URL -->
      <link rel="canonical" href="https://example.com/blog-post">
      <!-- Favicon -->
      <link rel="icon" type="image/png" href="https://example.com/favicon.png">
      <!-- Stylesheets -->
      <!-- Bootstrap Icons CSS -->
      <!-- <link rel="stylesheet" href="<?php echo asset('css/bootstrap-icons.css'); ?>"> -->
      <!-- Bootstrap Core CSS -->
      <link rel="stylesheet" href="<?php echo asset('css/bootstrap.min.css'); ?>">
      <!-- Custom styles for this template -->
      <link href="<?php echo asset('css/navbar-top-fixed.css'); ?>" rel="stylesheet">
      <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" integrity="sha384-tViUnnbYAV00FLIhhi3v/dWt3Jxw4gZQcNoSCxCIFNJVCx7/D55/wXsrNIRANwdD" crossorigin="anonymous">
 
      <!-- APP CSS -->
      <link rel="stylesheet" href="<?php echo asset('css/custom.css'); ?>">
      <link rel="stylesheet" href="<?php echo asset('css/upload.css'); ?>">

      <!-- Selected2 -->
      <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
   </head>
   <body>
   <?php
      if (!isset($_COOKIE['infoCookies'])) {
         $cookiePolicy = str_replace('public/', '', ROOT_URL) . 'views/cookie-policy.view.php';


         echo '
         <div id="cookies" class="cookie-popup">
            <p>
                  This website uses cookies to improve your experience and technical performance.
                  See our <a class="cookieLinks" target="_blank" href="' . $cookiePolicy . '">cookie policy</a>.
            </p>
            <button onclick="hideCookie()" class="cookie-btn">OK</button>
         </div>';
      }
   ?>

      <section id="top-bar" class="">
         <div class="container">
            <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark p-2">
               <div class="container">
                  <a class="navbar-brand nav-link font-weight-bold ml-2 border-0 text-decoration-none" style="outline:0;" href="<?php echo url('/'); ?>">Blogger</a>
                  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                  <span class="navbar-toggler-icon"></span>
                  </button>
                  <div class="collapse navbar-collapse" id="navbarCollapse">
                     <ul class="navbar-nav me-auto mb-2 mb-md-0">
                        <li class="nav-item">
                           <a class="nav-link" href="<?php echo url('posts'); ?>">Articles</a>
                        </li>
                     </ul>
                     <ul class="navbar-nav ml-auto mb-2 mb-md-0">
                        <?php if (!isset($_SESSION['auth'])): ?>
                        <li class="nav-item  ">
                           <a class="nav-link btn btn-lg btn-outline-light text-muted m-0" href="<?php echo url('login'); ?>">Login</a>
                        </li>
                        <?php endif; ?>
                     </ul>
                     <?php if (isset($_SESSION['auth'])): ?>
                     <ul class="navbar-nav ml-auto mb-2 mb-md-0">
                        <?php if (!isset($_SESSION['auth'])): ?>
                        <li class="nav-item ">
                           <a class="nav-link" href="<?php echo url('login'); ?>">Login</a>
                        </li>
                        <?php endif; ?>
                        <li class="nav-item">
                           <a class="nav-link" href="<?php echo url('admin/posts'); ?>">Dashboard</a>
                        </li>
                        <li class="nav-item">
                           <a class="nav-link" href="<?php echo url('logout'); ?>">Logout</a>
                        </li>
                     </ul>
                     <?php endif; ?>
                  </div>
               </div>
            </nav>
         </div>
      </section>
      <section id="">
         <div class="container">
            <?php echo $content; ?>
         </div>
      </section>

      <?php 
         $actual_link = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
         if (($actual_link == ROOT_URL)){?>
         <section class="p-0">
            <div class="row m-0 p-0">
               <div class="col col-md-6 mx-auto mb-5">

               <?php 
                  $allPosts = $params['posts'];
                  $totalPosts = count($allPosts);

                  $numPosts = min(5, $totalPosts);

                  $randomIndexes = array_rand(range(0, $totalPosts - 1), $numPosts);

                  if (!is_array($randomIndexes)) {
                     $randomIndexes = [$randomIndexes];
                  }

                  $randomPosts = array_map(fn($index) => $allPosts[$index], $randomIndexes);
               ?>

                 
                  <div id="recentPostsCarousel" class="carousel slide w-lg-50 w-md-50 mx-auto" data-bs-ride="carousel">
                     <div class="carousel-indicators">
                        <?php foreach ($randomPosts as $index => $post): ?>
                           <button type="button" data-bs-target="#recentPostsCarousel" data-bs-slide-to="<?php echo $index; ?>" class="<?php echo $index === 0 ? 'active' : ''; ?>" aria-label="Slide <?php echo $index + 1; ?>"></button>
                        <?php endforeach; ?>
                     </div>
                     
                     <div class="carousel-inner mb-5 ">
                        <?php foreach ($randomPosts as $index => $post): ?>
                           <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                           <div class="d-flex justify-content-center">
                              <img src="<?php echo $post->image_path; ?>" class="d-block w-100 w-md-50 rounded" alt="Post Image">
                           </div>
                           <div class="carousel-caption d-flex flex-column align-items-center justify-content-center text-white bg-dark bg-opacity-50 p-3">
                              <h4><?php echo mb_strimwidth($post->title, 0, 50, "..."); ?></h4>
                              <p><?php echo strip_tags($post->getExcerpt(), "<strong><br><b>"); ?></p>
                              <a href="<?php echo url("posts/$post->id"); ?>" class="btn btn-outline-light">Read More</a>
                           </div>
                           </div>
                        <?php endforeach; ?>
                     </div>

                     <button class="carousel-control-prev" type="button" data-bs-target="#recentPostsCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                     </button>
                     <button class="carousel-control-next" type="button" data-bs-target="#recentPostsCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                     </button>
                  </div>
               </div>
            </div>
         </section>
      <?php }?>
      <!-- Call to Action Section -->
      <section id="cta">
         <h1 class="text-center cta-text ">Write your story and make impression</h1>
         <div class="btn-section text-center">
            <?php if (!isset($_SESSION['auth'])) {?>
            <a class="nav-link btn btn-primary-soft fw-500  bg-green-soft text-green text-green" href="<?php echo url('login'); ?>">
            <button class="btn btn-primary btn-lg btn-space"><i class="bi bi-pen"></i> Get started</button>
            </a>
            <?php } else { ?> 
            <a class="nav-link btn btn-primary-soft fw-500  bg-green-soft text-green text-green" href="<?php echo url('admin/posts/create'); ?>">
            <button class="btn btn-primary btn-lg btn-space"><i class="bi bi-pen"></i> Get started</button>
            </a>
            <?php }?>
         </div>
      </section>
      <footer class="bg-white footer">
         <div class="container py-5">
            <div class="row ">
               <div class="col-lg-6 col-md-6 mb-4 mb-lg-0">
                  <img src="<?php echo asset('images/hero-img.webp')?>" alt="footer-img" width="180" class="mb-3 rounded-pill">
                  <p class="font-italic text-muted">Explore insightful blogs, stories, articles and updates <br>on technology, coding, and tutorials.</p>
               </div>
               <div class="col-lg-6 col-md-6 mb-4 mb-lg-0">
                  <div class="row d-flex ">
                     <div class="col-md-6 mb-3">
                        <h6 class="text-uppercase font-weight-bold mb-4">Useful links</h6>
                        <ul class="list-unstyled mb-0">
                           <?php if (isset($_SESSION['auth'])) {?>
                           <li class="mb-2"><a href="<?php echo url('admin/posts'); ?>" class="text-muted text-decoration-none">Dashboard</a></li>
                           <?php }?>
                           <li class="mb-2"><a href="<?php echo url('/'); ?>" class="text-muted text-decoration-none">Home</a></li>
                           <li class="mb-2"><a href="<?php echo url('posts'); ?>" class="text-muted text-decoration-none">Articles</a></li>
                        </ul>
                     </div>
                     <div class="col-md-6 mb-3">
                        <h6 class="text-uppercase font-weight-bold mb-4">Social media links</h6>
                        <ul class="list-inline mt-4">
                           <li class="list-inline-item"><a href="#" target="_blank" title="twitter"><i class="fa fa-twitter"></i></a></li>
                           <li class="list-inline-item"><a href="#" target="_blank" title="facebook"><i class="fa fa-facebook"></i></a></li>
                           <li class="list-inline-item"><a href="#" target="_blank" title="instagram"><i class="fa fa-instagram"></i></a></li>
                           <li class="list-inline-item"><a href="#" target="_blank" title="pinterest"><i class="fa fa-pinterest"></i></a></li>
                           <li class="list-inline-item"><a href="#" target="_blank" title="vimeo"><i class="fa fa-vimeo"></i></a></li>
                        </ul>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- Copyrights -->
         <div class="bg-light py-4">
            <div class="container text-center">
               <p class="text-muted mb-0 py-2">© 2025 All rights reserved.</p>
            </div>
         </div>
      </footer>
      <!-- Bootstrap's Javascript -->
      <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
      <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
      <script src="<?php echo asset('js/bootstrap.bundle.min.js'); ?>"></script>
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
      <!-- Custom Javascript -->
      <script src="<?php echo asset('js/sweet.js'); ?>"></script>
      <script src="<?php echo asset('js/filter.js'); ?>"></script>
      <script src="<?php echo asset('js/upload.js'); ?>"></script>
      <script src="<?php echo asset('js/typing-text.js'); ?>"></script>
      <script src="<?php echo asset('js/script.js'); ?>" htmeditor_textarea="input_content" full_screen="no" editor_height="350" editor_width="100%" run_local="no"></script>
                        
      <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
      <script>
         $(document).ready(function() {
            $('.js-example-basic-multiple').select2();
         });
      </script>

      <script>
    document.addEventListener("DOMContentLoaded", function () {
        HTMEditor.init({
            textarea: "input_content",
            full_screen: "no",
            editor_height: "480",
            editor_width: "100%",
            run_local: "yes",
            enter_mode: "p", // Forces paragraphs instead of <div>
            force_p_newlines: true,
            forced_root_block: "p", // Ensures every block starts as <p>
            paste_as_text: true, // Paste text without extra formatting
            paste_auto_cleanup_on_paste: true // Cleans unwanted tags when pasting
        });
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById("form_edit_post").addEventListener("submit", function () {
            let editorContent = document.getElementById("input_content").value;

            // Remove unnecessary <div> and wrap everything inside a single <p>
            editorContent = editorContent.replace(/<div>/g, "<p>").replace(/<\/div>/g, "</p>");
            
            // Ensure text inside a <p> and not in separate tags
            if (!editorContent.startsWith("<p>")) {
                editorContent = "<p>" + editorContent + "</p>";
            }

            document.getElementById("input_content").value = editorContent;
        });
    });


</script>
   </body>
</html>