$('.promoted-checkbox').on("change", function() {
  const item_id = $(this).val();
  var is_checked = $(this).is(":checked");

  $.post("/LazaLend/admin/php/ajax.php",
  {
      action: "updatePromotedItem",
      item_id: item_id,
      promoted: is_checked         
  }, function(data) {
      
  }
  , "json"
  );
 
});