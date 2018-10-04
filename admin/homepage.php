<?php
//check if admin is logged in
session_start();
if (!isset($_SESSION['loggedInAdminId'])) {
    header("location: /LazaLend/admin/");
}

$adminRoot = __DIR__ . "/";
$root = __DIR__ . "/../";
require_once $root . "cfg.php";
require_once $adminRoot . "./template/template.php";

$_M = array(
  'HEAD' => array(
      'TITLE' => 'LazaLend Admin',
      'CSS' => '
          <!-- Include Your CSS Link Here -->
          <link rel="stylesheet" href="./css/homepage.css"></link>
      ',
  ),
  'FOOTER' => array(
      'JS' => '
          <!-- Include Your JavaScript Link Here -->
      ',
  ),
);

$items = getAllItemsChronological();

require $adminRoot . "template/01-head.php";
?>

<table class="table">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Name</th>
      <th scope="col">Description</th>
      <th scope="col">Promoted</th>
      <th scope="col" class="text-center">User</th>

    </tr>
  </thead>

  <tbody>
    <?php foreach ($items as $item_id => $item) {?>
      <tr>
        <th scope="row"><?=$item_id?></th>
        <td><?=$item['name']?></td>
        <td><?=$item['description']?></td>
        <?php if ($item[promoted] == t) {?>
          <td class="text-center"><input class="promoted-checkbox" type="checkbox" name="promoted" value="<?=$item_id?>" checked></td>
        <?php } else {?>
          <td class="text-center"><input class="promoted-checkbox" type="checkbox" name="promoted" value="<?=$item_id?>"></td>
        <?php }?>

        <td>
          <div class="d-flex flex-column justify-content-center align-items-center">
            <img class="user-header-profile" src="<?='/../LazaLend'.$item['profile_image_url']?>" >
            <span><?=$item['username']?></span>
          </div>
          
        </td>
      </tr>
    <?php }?>
  </tbody>
</table>

<?php
require $adminRoot . "template/footer.php";
?>