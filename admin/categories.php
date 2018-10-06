<?php

session_start();
$adminRoot = __DIR__ . "/";
$root = __DIR__ . "/../";
require_once $root . "cfg.php";
require_once $adminRoot . "./template/template.php";

$_M = array(
  'HEAD' => array(
      'TITLE' => 'LazaLend Admin',
      'CSS' => '
          <!-- Include Your CSS Link Here -->
          <link rel="stylesheet" href="./css/categories.css"></link>
      ',
  ),
  'FOOTER' => array(
      'JS' => '
          <!-- Include Your JavaScript Link Here -->
      ',
  ),
);

if (!isset($_SESSION['loggedInAdminId'])) {
    header("location: /LazaLend/admin/homepage");
}

if (isset($_POST['delete'])) {
  $category_id = $_POST['delete'];
  deleteCategory($category_id);
}

if (isset($_POST['add_category'])) {
  $name = $_POST['name'];
  $image_url = $_POST['image_url'];
  insertCategory($name,$image_url);
}
$categories = getAllCategories();

require $adminRoot . "template/01-head.php";
?>
<div class="container d-flex flex-row-reverse ">
  <a href="#" class="btn btn-success m-2" data-target="#add-modal" data-toggle="modal">Add Category</a>
</div>

<div class="modal" id="add-modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Add Category</h4><br>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <div class="addmodal-container">
            <form class="login" method="POST">
                <input type="text" name="name" placeholder="Name">
                <input type="text" name="image_url" placeholder="Cover Image URL">
                <input type="submit" name="add_category" class="login addmodal-submit" value="Submit">
            </form>
        </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>

<table class="table">
  <thead>
    <tr>
      <th scope="col">Category ID</th>
      <th scope="col">Name</th>
      <th scope="col" class="text-center">Cover Image</th>
      <th scope="col">Operations</th>
    </tr>
  </thead>

  <tbody>
    <?php foreach ($categories as $category_id => $category) {?>
        <tr>
          <th scope="row"><?=$category['id']?></th>
          <td><?=$category['name']?></td>
  
          
          <td>    
            <div class="d-flex flex-column justify-content-center align-items-center">
                <img class="img-carousel" src="<?=$category['image_url']?>" >
                <span><?=$user['username']?></span>
            </div>
          </td>

          <td>
            <div class="d-flex flex-row">
              <form method="POST">
                <button type="submit" class="btn btn-danger m-1" name="delete" value="<?=$category['id']?>" >Delete</button>
              </form>
            </div>
          </td>
        </tr>
      <?php }?>
  </tbody>
</table>


<?php
require $adminRoot . "template/footer.php";
?>