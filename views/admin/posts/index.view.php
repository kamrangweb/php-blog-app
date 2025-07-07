<?php

    if (isset($_SESSION['edit_success']['edit']) && $_SESSION['edit_success']['edit'] == 'Edited') {
        echo '<script language="javascript">';
        // echo 'alert("Message successfully sent");';  
        echo 'function clickEdit() { Swal.fire("Edited!", "The post was edited.", "success");  }'; 
        echo 'function clickEditClose() { document.getElementsByClassName("swal2-confirm")[0].click(); }'; 
        echo 'setTimeout(clickEdit, 100);'; 
        echo 'setTimeout(clickEditClose, 2000);';
        echo '</script>';
    }

    unset($_SESSION['edit_success']['edit']);
    
?>

<?php 
    if (isset($_SESSION['add_success']['add']) && $_SESSION['add_success']['add'] == 'Added') {
        echo '<script language="javascript">';
        // echo 'alert("Message successfully sent");';  
        echo 'function clickAdd() { Swal.fire("Added!", "The post was added.", "success");  }'; 
        echo 'function clickAddClose() { document.getElementsByClassName("swal2-confirm")[0].click(); }'; 
        echo 'setTimeout(clickAdd, 100);'; 
        echo 'setTimeout(clickAddClose, 2000);'; 
        echo '</script>';
    }

    unset($_SESSION['add_success']['add']);
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

            <!-- <h1 class="m-3">USER <?php echo $_SESSION['user']; ?></h1> -->
            <h1 class="m-3 mb-3">Admin panel</h1>
            <div class="float-end">
                <a href="<?php echo url("admin/profile"); ?>" class="btn btn-info me-2">
                    <i class="bi bi-person"></i>
                    Profile
                </a>
                <a href="<?php echo url("admin/posts/create"); ?>" class="btn btn-success">
                    <i class="bi bi-plus"></i>
                    New
                </a>
            </div>
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
                    <th scope="col">Updated Date</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($params['posts'] as $post): ?>
                    <tr>
                        <th ><span class="rounded-pill text-mute border badge bg-primary small">#<?php echo $post->id; ?></span></th>
                        <td><?php echo $post->title; ?></td>
                        <td><?php echo $post->getCreatedAt(); ?></td>
                        <td><?php echo $post->getUpdatedAt(); ?></td>
                        <td class="m-3">

                            <a href="<?php echo url("posts/$post->id"); ?>"
                               class="btn btn-sm btn-primary">
                                <i class="bi bi-eye"></i>
                                View
                            </a>
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