<div class="row">
    <article class="blog-post mt-5 mb-5">
        <h2 class="blog-post-title text-center section-title mb-3"><?php echo $params['post']->title; ?></h2>
        <div class="small">
            <span class="badge bg-info small">Category: <?php echo $params['post']->category_name; ?></span>
        </div>
        

        

        <p class="blog-post-meta mt-2">
            <small>
                <i class="far fa-clock text-info"></i> <?php echo $params['post']->getCreatedAt(); ?> |
                Published by <a href="#" class="text-decoration-none text-secondary"><?php echo $params['post']->username; ?></a>
            </small>
        </p>

        <?php foreach ($params['posts'] as $post): ?>

            <?php 

            if($post->title == $params['post']->title ) {?>


        <div class="card">
            <div class="position-relative">
                <img class="card-img show-post-img w-100" src="<?php echo $params['post']->image_path; ?>" alt="Post Image">

                <div class="card-img-overlay position-absolute d-flex flex-wrap align-items-start p-2">

                        <?php foreach ($post->getTagsOfPost($post->id) as $tag): ?>


                        

                        <span class="badge bg-light m-1">
                            <a class="text-decoration-none text-dark small" href="<?php echo url("tags/$tag->id"); ?>">
                                <?php echo $tag->tag; ?>
                            </a>
                        </span>
                    <?php endforeach; ?>
                    
                </div>
            </div>

            <div class="card-body show-text p-4">
                <div class="post-body main-text"><?php echo $params['post']->body; ?></div>

                <div class=" justify-content-between">
                    <a href="<?php echo url('posts'); ?>" class="btn btn-danger float-end">
                        <i class="bi bi-caret-left"></i> Back
                    </a>
                </div>
            </div>
        </div>



        <?php }else{ continue;}?>
        <?php endforeach; ?>
        

    </article>
</div>
