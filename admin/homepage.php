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

if (isset($_POST['delete'])) {
    $item_id = $_POST['delete'];
    $done = deleteItem($item_id);
}

//if (isset($_POST['sort'])) {
$choice = $_POST['choice'];
switch ($choice) {
    case "sortIdAsc":
        $items = getAllItemsSortIdAsc();
        break;
    case "sortIdDesc":
        $items = getAllItemsSortIdDesc();
        break;
    case "sortUsernameAsc":
        $items = getAllItemsSortUsernameAsc();
        break;
    case "sortUsernameDesc":
        $items = getAllItemsSortUsernameDesc();
        break;
    case "sortDateAvailableAsc":
        $items = getAllItemsSortDateAvailableAsc();
        break;
    case "sortDateAvailableDesc":
        $items = getAllItemsSortDateAvailableDesc();
        break;
    case "sortPromotedAsc":
        $items = getAllItemsSortPromotedAsc();
        break;
    case "sortPromotedDesc":
        $items = getAllItemsSortPromotedDesc();
        break;
    case "sortFeeAsc":
        $items = getAllItemsSortFeeAsc();
        break;
    case "sortFeeDesc":
        $items = getAllItemsSortFeeDesc();
        break;
    default:
        $items = getAllItems();
        break;

}

//}

//$items = getAllItems();
require $adminRoot . "template/01-head.php";
?>

<div id="filter" class="container">
    <h4>Filter</h4>
    <form method="POST" class= "flex-grow-1 col-md-5">
        <div class="input-group ">
            <div class="input-group-prepend">
                <span class="searchbar-input-addon">
                    <i class="fa fa-caret-down searchbar-select-icon"></i>
                    <select id="dropdown" class="form-control" data-live-search="true" name="choice" >
                        <option value="sortIdAsc">Sort by ID(ASC)</option>
                        <option value="sortIdDesc">Sort by ID(DESC)</option>
                        <option value="sortUsernameAsc">Sort by Username(ASC)</option>
                        <option value="sortUsernameDesc">Sort by Username(DESC)</option>
                        <option value="sortDateAvailableAsc">Sort by Date Available(ASC)</option>
                        <option value="sortDateAvailableDesc">Sort by Date Available(DESC)</option>
                        <option value="sortPromotedAsc">Sort by Promoted(ASC)</option>
                        <option value="sortPromotedDesc">Sort by Promoted(DESC)</option>
                        <option value="sortFeeAsc">Sort by Fee/day(ASC)</option>
                        <option value="sortFeeDesc">Sort by Fee/day(DESC)</option>
                    </select>
                </span>
            </div>

            <div class="input-group-append bg-primary">
                <button class="btn btn-outline-secondary text-white" name="sort" type="submit"><i class="fas fa-search"></i></button>
            </div>
        </div>
    </form>
</div>


<table class="table">
  <thead>
    <tr>
      <th scope="col">ID</th>
      <th scope="col">Name</th>
      <th scope="col">Description</th>
      <th scope="col">Category</th>
      <th scope="col">Promoted</th>
      <th scope="col" class="text-center">User</th>
      <th scope="col" class="text-center">Operations</th>

    </tr>
  </thead>

  <tbody>
    <?php foreach ($items as $item_id => $item) {?>
      <tr>
        <th scope="row"><?=$item_id?></th>
        <td><?=$item['name']?></td>
        <td><?=$item['description']?></td>
        <td><?=$item['categories_name']?></td>
        <?php if ($item[promoted] == t) {?>
          <td class="text-center"><input class="promoted-checkbox" type="checkbox" name="promoted" value="<?=$item_id?>" checked></td>
        <?php } else {?>
          <td class="text-center"><input class="promoted-checkbox" type="checkbox" name="promoted" value="<?=$item_id?>"></td>
        <?php }?>

        <td>
            <a href="individual-user?userid=<?=$item['user_id']?>">
                <div class="d-flex flex-column justify-content-center align-items-center">
                    <img class="user-header-profile" src="<?='/../LazaLend' . $item['profile_image_url']?>" >
                    <span><?=$item['username']?></span>
                </div>
            </a>
            
        </td>

        <td>
          <div class="d-flex flex-row">
            <a class="btn btn-primary m-1" href="edit-listing?id=<?=$item['id']?>&userid=<?=$item['user_id']?>" role="button">Modify</a>
            <form method="POST">
              <button type="submit" class="btn btn-danger m-1" name="delete" value="<?=$item_id?>" >Delete</button>
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