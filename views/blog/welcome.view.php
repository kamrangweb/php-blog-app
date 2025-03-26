

<section class="hero-section bg-light p-2 mt-3">
   <div class="container">
      <div class="row header-main mt-5 mb-5 ">
         <div class="col-md-6 mb-3">
            <h1 class="header-info">Read and write your blogs in our platform</h1>
            <!-- <button class="btn btn-outline-light btn-lg btn-space m-0 mt-5"><i class="bi bi-pen"></i> Get started</button> -->
            <?php if (!isset($_SESSION['auth'])) {?>
            <a class="m-0" href="<?php echo url('login'); ?>">
            <button class="btn btn-outline-light btn-lg btn-space m-0 mt-5"><i class="fas fa-rocket"></i> Get started</button>
            </a>
            <?php } else { ?> 
            <a class="m-0" href="<?php echo url('admin/posts/create'); ?>">
            <button class="btn btn-outline-light btn-lg btn-space m-0 mt-5"><i class="fas fa-rocket"></i> Get started</button>
            </a>
            <?php }?>
         </div>
         <div class="col-md-6 text-center">
            <img src="<?php echo asset('images/hero-img.webp')?>" class="ml-5 iphone-mock rounded" width="400" alt="hero Image">
         </div>
      </div>
      <div class="row ">
         <h2 class="text-center section-title mb-5 mt-5 text-white">Recent posts</h2>
         <?php  foreach ($params['posts'] as $post): ?>
            <?php $count++;
            if($count == 4) 
            {
              break;}else{
            
              $_SESSION['count'] = $count;
            }
            
            ?>
            <div class="col-12 col-md-6 col-lg-4 mb-3">
                <div class="card">
                    
                    <div class="position-relative">
                      <img class="card-img h-25" src="<?php echo $post->image_path; ?>" alt="Post Image">

                      <div class="card-img-overlay position-absolute">
                          <?php foreach ($post->getTagsOfPost($post->id) as $tag): ?>
                              <span class="badge bg-light text-decoration-none text-dark small">
                                      <?php echo $tag->tag; ?>
                              </span>
                          <?php endforeach; ?>
                      </div>
                    </div>

                    <div class="card-body p-3 d-flex flex-column bd-highlight mb-3">
                      <div class="card-content">
                        <span class="card-text badge bg-info small mb-2">Category: <?php echo $post->category_name; ?></span>

                          <h4 class="card-title"><?php echo mb_strimwidth($post->title, 0, 50, "...");  ?></h4>
                        
                          <small class=" d-grid text-muted small">
                              <span class="d-block"><i class="far fa-clock text-info"></i> <?php echo $post->getCreatedAt(); ?></span>
                              <span>Published by <a href="javascript:void(0)" class="text-decoration-none text-secondary"><?php echo $post->username; ?></a></span>
                          </small>

                          <p class="main-text mt-2"><?php echo $post->getExcerpt(); ?></p>
                        </div>
                        
                        
                        
                        <a href="<?php echo url("posts/$post->id"); ?>" class="d-flex flex-row align-items-center text-decoration-none text-info read-more">
                            <span class="d-block ml-auto">Read more </span>  <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
         
         <?php endforeach; ?>
         <div class="d-grid gap-2 justify-content-center mb-3">
            <a class="mx-auto" href="<?php echo url("posts"); ?>">
            <button class="btn btn-primary btn-lg btn-space max-auto"><i class="bi bi-eye"></i> All posts</button>
            </a>
         </div>
      </div>
   </div>
   </div>
</section>