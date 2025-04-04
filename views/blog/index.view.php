<div class="container">
    <div class="row mb-5">
        <h1 class="text-center section-title mb-5 mt-5">Blog Posts</h1>
        
        <!-- Search Input -->
        <div class="row mb-4 mx-auto p-0">
            
            <div class="col-md-12 mb-3">
                <input type="text" id="searchInput" class="form-control" placeholder="Search posts...">
                <div id="searchHints" class="mt-2">
                    <span class="badge bg-secondary search-hint">PHP</span>
                    <span class="badge bg-secondary search-hint">Laravel</span>
                    <span class="badge bg-secondary search-hint">React</span>
                    <span class="badge bg-secondary search-hint">WordPress</span>
                    <span class="badge bg-secondary search-hint">E-commerce</span>
                </div>
            </div>
        </div>
        
        <div class="row mb-4 mx-auto p-0">
            <div class="col-md-6 m-0 mb-3">
                <select id="categoryFilter" class="form-select">
                    <option value="">All Categories</option>
                    <?php foreach ($params['categories'] as $category): ?>
                        <option value="<?php echo $category->category; ?>"><?php echo $category->category; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 m-0">
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
                 data-tags="<?php echo implode(',', array_map(fn($tag) => $tag->tag, $post->getTagsOfPost($post->id))); ?>"
                 data-title="<?php echo strtolower($post->title); ?>"
                 data-excerpt="<?php echo strtolower(strip_tags($post->getExcerpt())); ?>">
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
                          <p class="main-text mt-2"><?php echo strip_tags($post->getExcerpt(), "<p>"); ?></p>
                        </div>
                        <a href="<?php echo url("posts/$post->id"); ?>" class="d-flex flex-row align-items-center text-decoration-none text-info read-more">
                            <span class="d-block ml-auto">Read more </span>  <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        
        <div id="notFoundImg" class="d-flex flex-column justify-content-center w-25 mx-auto text-center" style="display: none;">
            <img src="<?php echo asset('images/not-found.jpeg');?>" alt="not-found-image" />
            <p class="text-muted mt-3">No results found</p>
        </div>
        
        <div class="col-12 d-flex justify-content-center mt-4">
            <nav>
                <ul class="pagination">
                    <?php if ($params['currentPage'] > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $params['currentPage'] - 1; ?>">Previous</a>
                        </li>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $params['totalPages']; $i++): ?>
                        <li class="page-item <?php echo $i == $params['currentPage'] ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"> <?php echo $i; ?> </a>
                        </li>
                    <?php endfor; ?>
                    <?php if ($params['currentPage'] < $params['totalPages']): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $params['currentPage'] + 1; ?>">Next</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");
    const searchHints = document.querySelectorAll(".search-hint");

    searchHints.forEach(hint => {
        hint.addEventListener("click", function () {
            searchInput.value = this.textContent;
            filterPosts(); // Automatically triggers the search
        });
    });

    searchInput.addEventListener("input", filterPosts);

    function filterPosts() {
        let query = searchInput.value.toLowerCase();
        let posts = document.querySelectorAll(".blog-card");

        posts.forEach(post => {
            let title = post.querySelector(".card-title").textContent.toLowerCase();
            let category = post.getAttribute("data-category").toLowerCase();
            let tags = post.getAttribute("data-tags").toLowerCase();

            if (title.includes(query) || category.includes(query) || tags.includes(query)) {
                post.style.display = "block";
            } else {
                post.style.display = "none";
            }
        });

        // // Show "No results found" if all posts are hidden
        // document.getElementById("notFoundImg").style.display = 
        //     [...posts].every(post => post.style.display === "none") ? "block" : "none";
    }
});
</script>
