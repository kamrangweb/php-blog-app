<?php
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
?>



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
        <div class="d-inline admin-user">

            <h1 class="m-3">USER <?php echo $_SESSION['user']; ?></h1>
            <h1 class="m-3 mb-5">Admin panel</h1>
            <a href="<?php echo url("admin/posts/create"); ?>" class="btn btn-success float-end">
                <i class="bi bi-plus"></i>
                New
            </a>
        </div>
    </div>
</div>
<div class="row mb-2">
    <div class="container">
        <table class="table p-3">
            <thead>
                <tr>
                    <th scope="col">Number</th>
                    <th scope="col">Title</th>
                    <th scope="col">Date</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($params['posts'] as $post): ?>
                    <tr>
                        <th ><span class="rounded-pill text-mute border badge bg-primary small">#<?php echo $post->id; ?></span></th>
                        <td><?php echo $post->title; ?></td>
                        <td><?php echo $post->getCreatedAt(); ?></td>
                        <td class="m-3">
                            <a href="<?php echo url("admin/posts/edit/$post->id"); ?>"
                               class="btn btn-sm btn-primary">
                                <i class="bi bi-pencil-square"></i>
                                Edit
                            </a>

                            <form class="d-inline"
                                  action="<?php echo url("admin/posts/delete"); ?>"
                                  id="form_delete_post"
                                  method="POST">
                                <input type="hidden"
                                       name="id"
                                       value="<?php echo $post->id; ?>">
                                <button type="submit" id="sil" title="DELETE"
                                        class="my-1 btn btn-sm btn-danger">
                                    <i class="bi bi-trash-fill"></i>
                                    Delete
                                </button>
                            </form>
                            
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php clear_session_msg(); ?>