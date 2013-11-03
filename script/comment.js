$(document).ready(function() {
    $('#send').click(function(){
        var idProj = $('#send').attr('proj');
        var idProp = $('#send').attr('prop');
        var text   = $('#send').parent().find('#text').val();

        if(text != '')
        {
            $.ajax({
                type: "POST",
                url: "script/comment.php",
                data: {'idProj': idProj, 'idProp': idProp, 'text': text},
                async: false,
                context: this,
                success: function () {
                    location.reload();
                }
            });
        }


    });

});