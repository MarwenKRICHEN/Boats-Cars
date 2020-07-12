$("input#search").keyup(function() {
    var input = $(this);
    $('article p b').each(function () {
        if($(this).text().toLowerCase().indexOf(input.val().toLowerCase()) > -1 ) {
            $(this).parent().parent().show();
        }else{
            $(this).parent().parent().hide();
        }
    })
})