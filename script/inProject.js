$(document).ready(function () {
    $('#candidarsi').click(function () {
        var idProj = $('#candidarsi').attr('data-in');
		var text   = $('#text').val();
        $.ajax({
            type: "POST",
            url: "script/inProject.php",
            data: {'idProj': idProj, 'type': 'join', 'text': text},
            async: false,
            context: this,
            success: function () {
				$('#candida').parents('#candid').html('<a href="#myModal" role="button" class="btn btn-large btn-block btn-danger" data-toggle="modal" id="rifiuta">Rifiuta</a><div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button><h3 id="myModalLabel">Cancelare la proposta</h3></div><div class="modal-body"><p>Sei sicuro di rifiutare la tua proposta?</p></div><div class="modal-footer"><button class="btn" data-dismiss="modal" aria-hidden="true">Chiudi</button><button class="btn btn-danger" data-dismiss="modal" id="rifiuta" aria-hidden="true" data-in="' + idProj + '">Si</button></div></div><br /><br /><br /><br />');
            }
        });

    });
});

$(document).ready(function () {
    $('#rifiuta').click(function () {
        var idProj = $('#rifiuta').attr('data-in');
        $.ajax({
            type: "POST",
            url: "script/inProject.php",
            data: {'idProj': idProj, 'type': 'left'},
            async: false,
            context: this,
            success: function () {
				$('#rifiuta').parents('#rif').html('<a href="#myModal" role="button" class="btn btn-large btn-block btn-success" data-toggle="modal" id="candida">Candidarsi</a><div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button><h3 id="myModalLabel">La tua proposta</h3></div><div class="modal-body"><input type="text" id="text" /></div><div class="modal-footer"><button class="btn" data-dismiss="modal" aria-hidden="true">Chiudi</button><button class="btn btn-success" data-dismiss="modal" aria-hidden="true" id="candidarsi" data-in="' + idProj + '">Candidarsi</button></div></div><br /><br /><br /><br />');
            }
        });

    });
});

$(document).ready(function () {
    $('#chiudi').click(function () {
        var idProj = $('#chiudi').attr('data-in');
        $.ajax({
            type: "POST",
            url: "script/inProject.php",
            data: {'idProj': idProj, 'type': 'close'},
            async: false,
            context: this,
            success: function () {
				$('#chiudi').hide();
            }
        });

    });
});