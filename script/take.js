$(document).ready(function() {
	$('#take').click(function(){
		var idProj = $('#take').attr('proj');
		var idUser = $('#take').attr('usr');
		
		$.ajax({
            type: "POST",
            url: "script/take.php",
            data: {'idProj': idProj, 'idUser': idUser},
            async: false,
            context: this,
            success: function () {
				$('#take').parents('#choose').html('');
            }
        });
		
	});
    
});