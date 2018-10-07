$('body').on("change", ".image_input", function() {
    const id = $(this).attr('id').split("_")[2];
    const image_url = URL.createObjectURL(this.files[0])	 

    $.post("/LazaLend/php/ajax.php",
    {
        action: "showLoanImage",
        image_id: id,
        image_url: image_url         
    }, function(data) {
        $('#image_' + id).html(data);
    }
    , "json"
    );
});

$('body').on("change", "#loan_images_1", function (){
    $('#go-to-category-btn').prop('disabled', false);
});

function del_loan_image(e) {
    const id = +$(e).attr('id').split("_")[3];
    
    $.post("/LazaLend/php/ajax.php",
    {
        action: "removeLoanImage",
        image_id: id,         
    }, function(data) {
        $('#image_' + id).parent().html(data);
    }
    , "json"
    );

    if(id === 1) {
        $('#go-to-category-btn').prop('disabled', true);
    }
}

function select_category(category_id) {
    $('#select_category').attr('value', category_id);
    
    $('#loan-categories').hide();
    $('#loan-details').show();
}

function go_to_loan_category() {
    $('#loan-images').hide();
    $('#loan-categories').show();
}

function back_to_loan_image() {
    $('#loan-categories').hide();
    $('#loan-images').show();
}

function back_to_loan_category() {
    $('#loan-categories').show();
    $('#loan-details').hide();
}
