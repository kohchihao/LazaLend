$('body').on("change", ".image_input", function() {
    console.log("hr");
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
    $('.loan-submit').prop('disabled', false);
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
        $('.loan-submit').prop('disabled', true);
    }
}