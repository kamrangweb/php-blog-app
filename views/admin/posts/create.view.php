<h1 class="mt-5 mb-5">Creating a new post</h1>
<div class="row mb-5">
    <div class="container">
        <div class="card p-2">
            <div class="card-body">
                <form class="row g-3" action="<?php echo url("admin/posts/store"); ?>"
                      id="form_create_post"
                      method="POST"
                      autocomplete="off" enctype="multipart/form-data">
                    <div class="col-12 mb-2">
                        <label for="input_title" class="form-label">Title</label>
                        <input type="text"
                               class="form-control"
                               id="input_title"
                               name="title"
                               value="<?php echo $_POST['title'] ?? ''; ?>"
                               placeholder="Post title"
                               required>
                    </div>
                    <div class="col-12 mb-2">
                        <label for="input_content" class="form-label">Content</label>
                        <textarea class="form-control"
                                  id="input_content"
                                  name="body"
                                  rows="6"
                                  required><?php echo $_POST['body'] ?? ''; ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-lg-4">Image upload</label>
                            <div class="">
                                <div class="fileupload fileupload-new" data-provides="fileupload">
                                    <span class="btn btn-file btn-default">
                                        <span class="fileupload-new">Choose</span>
                                        <span class="fileupload-exists">Change</span>
                                        <input type="file" name="image_path">
                                    </span>
                                    <span class="fileupload-preview"></span>
                                    <a href="#" class="close fileupload-exists" data-dismiss="fileupload" style="float: none">×</a>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-12 mb-2">
                        <label for="input_categories" class="form-label">Categories</label>
                        <select class="form-select"
                                id="input_categories"
                                name="category_id"
                                aria-label="select example"
                                required>
                                <?php foreach ($params['categories'] as $category): ?>
                                    <option value="<?php echo $category->id; ?>">
                                        <?php echo $category->category; ?>
                                    </option>
                                <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 mb-2">
                        <label for="input_tags" class="form-label">Tags</label>
                        <select class="form-select"
                                id="input_tags"
                                name="tags[]"
                                multiple aria-label="multiple select example"
                                required>
                                <?php foreach ($params['tags'] as $tag): ?>
                                    <option value="<?php echo $tag->id; ?>">
                                        <?php echo $tag->tag; ?>
                                    </option>
                                <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 mb-2">
                        <div class="float-end">
                            <a href="<?php echo url('admin/posts');?>" class="btn btn-outline-warning">Cancel</a>
                            <button type="button" class="btn btn-success" onclick="clickAdd(this);">
                                <i class="bi bi-plus"></i>
                                Add post
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>