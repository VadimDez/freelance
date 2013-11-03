$(document).ready(function () {
    $('.join').click(function () {
        var idProj = $(this).attr('data-in');
		var idUser = $(this).attr('usr');
        $.ajax({
            type: "POST",
            url: "script/candidarsi.php",
            data: { 'data': idProj, 'type': 'join', 'user':idUser },
            async: false,
            context: this,
            success: function (result) {
				if(result)
				{
					//$(this).html('<div class="exit" data-in="' + idProj +'" usr="' + idUser + '" ><input type="button" value="Esci" /></div>');
                    location.reload();
				}
            },
			error: function(){
				alert('errore');
			}
        });
    });
});