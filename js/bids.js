// when all Bids button press
$(".bids-buttons").on('click', '#allBidsBtn', function() {
  $("#borrowBtn").removeClass("active");
  $("#lendBtn").removeClass("active");
  $("#allBidsBtn").addClass("active");

  $.post("/LazaLend/php/ajax.php",
    {
        action: "getAllBids",      
    }, function(data) {
        $('#bids-content').html(data);
    }
    , "json"
  );
});

// when borrow Bids button press
$(".bids-buttons").on('click', '#borrowBtn', function() {
  $("#lendBtn").removeClass("active");
  $("#allBidsBtn").removeClass("active");
  $("#borrowBtn").addClass("active");

  $.post("/LazaLend/php/ajax.php",
    {
        action: "getBorrowBids",       
    }, function(data) {
        $('#bids-content').html(data);
    }
    , "json"
  );
});

// when lend Bids button press
$(".bids-buttons").on('click', '#lendBtn', function() {
  $("#borrowBtn").removeClass("active");
  $("#allBidsBtn").removeClass("active");
  $("#lendBtn").addClass("active");

  $.post("/LazaLend/php/ajax.php",
    {
        action: "getLendBids",      
    }, function(data) {
        $('#bids-content').html(data);
    }
    , "json"
  );
});

// when update offer Bids button press
$(".col-6").on('click', '.updateOfferBtn', function() {
  console.log("click");
  var value = document.getElementById('updatePriceInput').value;
  
  if (value==='' || value < 0) {
    console.log("Input Valid");
  } else {
    console.log(value);
    console.log($(this).data("id"));

    $.post("/LazaLend/php/ajax.php",
      {
          action: "updateBidPrice",
          bid_id: $(this).data("id"),
          bid_price: value
      }, function(data) {
          $('#bid-display').html(data);
      }
      , "json"

    );
  }

});

// when cancel offer Bids button press
$(".col-6").on('click', '.cancelOfferBtn', function() {
    console.log("clickCancel");
  
    console.log($(this).data("id"));

    $.post("/LazaLend/php/ajax_dk.php",
      {
          action: "cancelBidPrice",
          bid_id: $(this).data("id")
      }, function(data) {
          $('#bid-display').html(data);
      }
      , "json"

    );

    // check which btn is active then ajax sidebar 
    if ($("#borrowBtn").hasClass("active")) {
      $.post("/LazaLend/php/ajax_dk.php",
        {
          action: "getBorrowBids",      
        }, function(data) {
          $('#bids-content').html(data);
        }
        , "json"
      );
    } else if ($("#allBidsBtn").hasClass("active")) {
      $.post("/LazaLend/php/ajax_dk.php",
        {
          action: "getAllBids",      
        }, function(data) {
          $('#bids-content').html(data);
        }
        , "json"
      );
    } else {
      $.post("/LazaLend/php/ajax_dk.php",
        {
          action: "getLendBids",      
        }, function(data) {
          $('#bids-content').html(data);
        }
        , "json"
      );
    }
});

// when accept offer Bids button press
$(".col-6").on('click', '.acceptBidBtn', function() {
  console.log("click");
  
  console.log($(this).data("id"));

  $.post("/LazaLend/php/ajax_dk.php",
    {
      action: "acceptBidBtn",
      bid_id: $(this).data("id")       
    }, function(data) {
      $('#bid-display').html(data);
    }
    , "json"
  );

  // check if lend btn or all bid btn is active then ajax sidebar 
  if ($("#allBidsBtn").hasClass("active")) {
    $.post("/LazaLend/php/ajax_dk.php",
      {
        action: "getAllBids",      
      }, function(data) {
        $('#bids-content').html(data);
      }
      , "json"
    );
  } else {
    $.post("/LazaLend/php/ajax_dk.php",
      {
        action: "getLendBids",      
      }, function(data) {
        $('#bids-content').html(data);
      }
      , "json"
    );
  }

});

// on click row to display individual bid information
$(".bids-container").on('click', '.each-bid-row', function (){
  console.log($(this).data("id"));
  console.log($(this).data("owner_id"));
  console.log($(this).data("user_id"));

  // to find out whether the item is a lending or borrowing item
  // if user_id equals to owner_id = lending item
  // else borrowing item
  if ($(this).data("user_id") === $(this).data("owner_id")) {
      console.log("pass");
      $.post("/LazaLend/php/ajax.php",
      {
          action: "getLendBidDisplay",
          bid_id: $(this).data("id")
      }, function(data) {
          $('#bid-display').html(data);
      }
      , "json"

    );
  } else {
    console.log("fail");
    $.post("/LazaLend/php/ajax.php",
      {
          action: "getBorrowBidDisplay",
          bid_id: $(this).data("id")
      }, function(data) {
          $('#bid-display').html(data);
      }
      , "json"

    );
  }
});