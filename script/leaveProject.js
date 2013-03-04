$(document).ready(function () {
    $('.exit').click(function () {
        var idProj = $(this).attr('data-in');
		var idUser = $(this).attr('usr');
		
        $.ajax({
            type: "POST",
            url: "script/candidarsi.php",
            data: { 'data': idProj, 'type': 'exit', 'user':idUser },
            async: false,
            context: this,
            success: function (result) {
				alert('asd');
				$(this).html('<div class="join" data-in="' + idProj +'" usr="' + idUser + '" ><input type="button" value="Candidarsi" /></div>');
            },
			error: function(){
				alert('errore');
			}
        });
    });
});