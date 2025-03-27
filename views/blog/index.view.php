

<div class="container">
    <div class="row mb-5">
        <h1 class="text-center section-title mb-5 mt-5">Blog Posts</h1>
          <div class="row mb-4 mx-auto">
            <div class="col-md-6 mb-3">
                <select id="categoryFilter" class="form-select">
                    <option value="">All Categories</option>
                    <?php foreach ($params['categories'] as $category): ?>
                        <option value="<?php echo $category->category; ?>"><?php echo $category->category; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                      
                <select id="tagFilter" class="form-select">
                    <option value="">All Tags</option>
                    <?php foreach ($params['tags'] as $tag): ?>
                        <option value="<?php echo $tag->tag; ?>"><?php echo $tag->tag; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <?php foreach ($params['posts'] as $post): ?>
            <div class="col-12 col-md-6 col-lg-4 mb-3 blog-card" 
                 data-category="<?php echo $post->category_name; ?>" 
                 data-tags="<?php echo implode(',', array_map(fn($tag) => $tag->tag, $post->getTagsOfPost($post->id))); ?>">
                <div class="card">
                    
                    <div class="position-relative">
                      <img class="card-img" src="<?php echo $post->image_path; ?>" alt="Post Image">

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
    </div>
</div>




