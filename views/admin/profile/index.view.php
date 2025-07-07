<?php if (isset($_SESSION['msg']['success'])): ?>
    <div class="row">
        <div class="container">
            <div class="alert alert-success alert-dismissible d-flex align-items-center mt-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <div><?php echo $_SESSION['msg']['success']; ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="row mt-3">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="m-3 mb-3">My Profile</h1>
            <a href="<?php echo url("admin/posts"); ?>" class="btn btn-primary">
                <i class="bi bi-arrow-left"></i>
                Back to Dashboard
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="container">
        <div class="row">
            <!-- Profile Card -->
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-person-circle" style="font-size: 4rem; color: #007bff;"></i>
                        </div>
                        <h4 class="card-title"><?php echo htmlspecialchars($params['user']->username); ?></h4>
                        <p class="text-muted">Blog Author</p>
                        
                        <div class="row text-center mt-3">
                            <div class="col-6">
                                <div class="border-end">
                                    <h5 class="text-primary mb-0"><?php echo $params['user']->post_count; ?></h5>
                                    <small class="text-muted">Posts</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h5 class="text-success mb-0"><?php echo date('M Y', strtotime($params['user']->created_at)); ?></h5>
                                <small class="text-muted">Joined</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Details -->
            <div class="col-md-8 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Account Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Username:</label>
                                <p class="form-control-plaintext"><?php echo htmlspecialchars($params['user']->username); ?></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">User ID:</label>
                                <p class="form-control-plaintext">#<?php echo $params['user']->id; ?></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Member Since:</label>
                                <p class="form-control-plaintext"><?php echo date('F j, Y', strtotime($params['user']->created_at)); ?></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Total Posts:</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-primary"><?php echo $params['user']->post_count; ?> posts</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Posts Section -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-file-text me-2"></i>My Recent Posts</h5>
                        <a href="<?php echo url("admin/posts/create"); ?>" class="btn btn-success btn-sm">
                            <i class="bi bi-plus"></i> New Post
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($params['posts'])): ?>
                            <div class="text-center py-4">
                                <i class="bi bi-file-text" style="font-size: 3rem; color: #dee2e6;"></i>
                                <h5 class="mt-3 text-muted">No posts yet</h5>
                                <p class="text-muted">Start writing your first blog post!</p>
                                <a href="<?php echo url("admin/posts/create"); ?>" class="btn btn-primary">
                                    <i class="bi bi-pen"></i> Create Your First Post
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_slice($params['posts'], 0, 5) as $post): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <?php if ($post->image_path): ?>
                                                            <img src="<?php echo $post->image_path; ?>" 
                                                                 alt="Post thumbnail" 
                                                                 class="rounded me-2" 
                                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                                        <?php endif; ?>
                                                        <div>
                                                            <strong><?php echo htmlspecialchars($post->title); ?></strong>
                                                            <br>
                                                            <small class="text-muted"><?php echo mb_strimwidth(strip_tags($post->body), 0, 60, "..."); ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <small class="text-muted"><?php echo $post->getCreatedAt(); ?></small>
                                                </td>
                                                <td>
                                                    <a href="<?php echo url("posts/$post->id"); ?>" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       title="View">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="<?php echo url("admin/posts/edit/$post->id"); ?>" 
                                                       class="btn btn-sm btn-outline-secondary" 
                                                       title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php if (count($params['posts']) > 5): ?>
                                <div class="text-center mt-3">
                                    <a href="<?php echo url("admin/posts"); ?>" class="btn btn-outline-primary">
                                        View All Posts (<?php echo count($params['posts']); ?>)
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- 404 Attempts Section -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Recent 404 Attempts</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $logFile = __DIR__ . '/../../../storage/logs/404.log';
                        if (file_exists($logFile)) {
                            $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                            $lines = array_reverse($lines); // Show most recent first
                            $lines = array_slice($lines, 0, 20); // Limit to 20
                            if (count($lines) > 0) {
                                echo '<ul class="list-group">';
                                foreach ($lines as $line) {
                                    // Extract the URL from the log entry
                                    if (preg_match('/tried ([^ ]+)/', $line, $matches)) {
                                        $url = $matches[1];
                                        $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
                                        $ignoreExtensions = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 'webp', 'woff', 'woff2', 'ttf', 'eot', 'map', 'txt', 'xml', 'json'];
                                        if (!in_array($ext, $ignoreExtensions)) {
                                            // Bold the whole line for non-static URLs
                                            echo '<li class="list-group-item small"><strong>' . htmlspecialchars($line, ENT_QUOTES, 'UTF-8') . '</strong></li>';
                                        } else {
                                            // Normal for static file URLs
                                            echo '<li class="list-group-item small">' . htmlspecialchars($line, ENT_QUOTES, 'UTF-8') . '</li>';
                                        }
                                    } else {
                                        echo '<li class="list-group-item small">' . htmlspecialchars($line, ENT_QUOTES, 'UTF-8') . '</li>';
                                    }
                                }
                                echo '</ul>';
                            } else {
                                echo '<div class="text-muted">No 404 attempts logged yet.</div>';
                            }
                        } else {
                            echo '<div class="text-muted">No 404 attempts logged yet.</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php clear_session_msg(); ?> 