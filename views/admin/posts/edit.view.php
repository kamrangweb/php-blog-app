<head>

<!-- <script src="<?php echo asset('js/script.js'); ?>" htmeditor_textarea="input_content" full_screen="no" editor_height="480" editor_width="100%" run_local="yes"></script> -->


</head>







<?php if (!empty($_SESSION['errors_upload']['upload'])): ?>
    <div class="row">
        <div class="container">
            <div class="alert alert-danger alert-dismissible d-flex align-items-center mt-3" role="alert">
                <i class="bi bi-exclamation-circle-fill me-2"></i>
                <div>
                    <?php echo $_SESSION['errors_upload']['upload'];?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
<?php endif; ?>







<h1 class="mt-5 mb-5">Edit post <span class="rounded-pill text-mute border badge bg-primary">#<?php echo $params['post']->id; ?></span></h1>
<div class="row mb-5">
    <div class="container">
        <div class="card mb-3 p-5">
            <div class="card-body">
                <form class="row g-3" action="<?php echo url("admin/posts/update"); ?>"
                      id="form_edit_post"
                      method="POST"
                      autocomplete="off" enctype="multipart/form-data">
                    <div class="col-12 mb-2">
                        <label for="input_title" class="form-label">Title</label>
                        <input type="hidden"
                               name="id"
                               value="<?php echo $params['post']->id; ?>">
                        <input type="text"
                               class="form-control"
                               id="input_title"
                               name="title"
                               value="<?php echo $params['post']->title; ?>"
                               placeholder="Post title"
                               required>
                    </div>
                    <div class="col-12 mb-2">
                        <label for="input_content" class="form-label">Content</label>
                        <textarea class="form-control"
                                  id="input_content"
                                  name="body"
                                  rows="6"
                                  required><?php echo $params['post']->body; ?></textarea>
                    </div>

                    <div class="col-12 mb-2">
                        <label for="current_image" class="form-label">Current Image</label>
                        <div>
                            <?php if (!empty($params['post']->image_path)): ?>
                                <img src="<?php echo $params['post']->image_path; ?>" alt="Current Image" class="img-thumbnail" width="150" >
                                <input type="hidden" name="old_image" value="<?php echo $params['post']->image_path; ?>">
                            <?php else: ?>
                                <p>No image uploaded</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="formFile" class="form-label">Image upload</label>
                        <input class="form-control" type="file" id="formFile" name="image_path">
                        <span class="text-danger small">Only jpg / jpeg/ png /gif format allowed.</span>
                        <!-- -->
                    </div>

                    




                    <div class="col-lg-12 col-md-6 col-sm-12 mb-2">
                        <label for="input_categories" class="form-label">Categories</label>
                        <select class="form-select"
                                id="input_categories"
                                name="category_id"
                                aria-label="select example"
                                required>
                                <?php foreach ($params['categories'] as $category): ?>
                                    <option value="<?php echo $category->id; ?>"
                                        <?php echo $category->id ===  $params['post']->category_id ? 'selected' : '';?>>
                                        <?php echo $category->category; ?>
                                    </option>
                                <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-12 col-md-6 col-sm-12 mb-2">
                        <label for="input_tags" class="form-label">Tags</label>
                        <select class="form-select js-example-basic-multiple"
                                id="input_tags"
                                name="tags[]"
                                multiple aria-label="multiple select example"
                                required>
                                <?php foreach ($params['tags'] as $tag): ?>
                                    
                                    <option value="<?php echo $tag->id; ?>"
                                        <?php echo $tag->is_selected ? 'selected' : '';?>>
                                        <?php echo $tag->tag; ?>
                                    </option>
                                <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 mb-2">
                        <div class="float-end">
                            <a href="<?php echo url('admin/posts');?>" class="btn btn-outline-warning">Cancel</a>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i>
                                Done
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

