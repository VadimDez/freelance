/**
 * Created with JetBrains PhpStorm.
 * User: vadimdez
 * Date: 3/18/13
 * Time: 8:56 PM
 * To change this template use File | Settings | File Templates.
 */
$(document).ready(function () {
    $('#spons').click(function () {
        if($('#textP').val() != '')
        {
            var idProj = $('#spons').attr('data-in');
            var text   = $('#textP').val();
            // non c'e' prezzo - qundi ha premuto tasto sponsorzza
            $.ajax({
                type: "POST",
                url: "script/spons.php",
                data: {'idProj': idProj, 'text': text, 'type': 'spons'},
                async: false,
                context: this,
                success: function () {
                    //$('#candida').parents('#candid').html('<div id="rif"><a href="#rifModal" role="button" class="btn btn-large btn-block btn-danger" data-toggle="modal" id="rifProj">Rifiuta</a><div id="rifModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="riflLabel" aria-hidden="true"> <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button><h3 id="riflLabel">Cancelare la proposta</h3></div><div class="modal-body"><p>Sei sicuro di rifiutare la tua proposta?</p></div><div class="modal-footer"><button class="btn" data-dismiss="modal" aria-hidden="true">Chiudi</button><button class="btn btn-danger" data-dismiss="modal" aria-hidden="true" id="rifiuta" data-in="' + idProj + '">Si</button></div></div></div>');
                    //$('#candida').parents('#candid').html('<a href="#myModal" role="button" class="btn btn-large btn-block btn-danger" data-toggle="modal" id="rifiuta">Rifiuta</a><div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button><h3 id="myModalLabel">Cancelare la proposta</h3></div><div class="modal-body"><p>Sei sicuro di rifiutare la tua proposta?</p></div><div class="modal-footer"><button class="btn" data-dismiss="modal" aria-hidden="true">Chiudi</button><button class="btn btn-danger" data-dismiss="modal" id="rifiuta" aria-hidden="true" data-in="' + idProj + '">Si</button></div></div><br /><br /><br /><br />');
                    location.reload();
                }
            });
        }
        else
        {
            location.reload();
        }
        /*$.ajax({
            type: "POST",
            url: "script/inProject.php",
            data: {'idProj': idProj, 'type': 'join', 'text': text},
            async: false,
            context: this,
            success: function () {
                //$('#candida').parents('#candid').html('<div id="rif"><a href="#rifModal" role="button" class="btn btn-large btn-block btn-danger" data-toggle="modal" id="rifProj">Rifiuta</a><div id="rifModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="riflLabel" aria-hidden="true"> <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button><h3 id="riflLabel">Cancelare la proposta</h3></div><div class="modal-body"><p>Sei sicuro di rifiutare la tua proposta?</p></div><div class="modal-footer"><button class="btn" data-dismiss="modal" aria-hidden="true">Chiudi</button><button class="btn btn-danger" data-dismiss="modal" aria-hidden="true" id="rifiuta" data-in="' + idProj + '">Si</button></div></div></div>');
                //$('#candida').parents('#candid').html('<a href="#myModal" role="button" class="btn btn-large btn-block btn-danger" data-toggle="modal" id="rifiuta">Rifiuta</a><div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button><h3 id="myModalLabel">Cancelare la proposta</h3></div><div class="modal-body"><p>Sei sicuro di rifiutare la tua proposta?</p></div><div class="modal-footer"><button class="btn" data-dismiss="modal" aria-hidden="true">Chiudi</button><button class="btn btn-danger" data-dismiss="modal" id="rifiuta" aria-hidden="true" data-in="' + idProj + '">Si</button></div></div><br /><br /><br /><br />');
                location.reload();
            }
        });*/

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
                //$('#rifiuta').parents('#rif').html('<div id="candid"><a href="#candidaModal" role="button" class="btn btn-large btn-block btn-success" data-toggle="modal" id="candida">Candidarsi</a><div id="candidaModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="candidaLabel" aria-hidden="true"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button><h3 id="candidaLabel">La tua proposta</h3></div><div class="modal-body"><label>Testo della tua proposta</label><textarea id="text" maxlength="5000" rows="10" style="width:98%"/></textarea></div><div class="modal-footer"><button class="btn" data-dismiss="modal" aria-hidden="true">Chiudi</button><button class="btn btn-success" data-dismiss="modal" aria-hidden="true" id="candidarsi" data-in="' + idProj + '">Candidarsi</button></div></div></div>');
                //$('#rifiuta').parents('#rif').html('<a href="#myModal" role="button" class="btn btn-large btn-block btn-success" data-toggle="modal" id="candida">Candidarsi</a><div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button><h3 id="myModalLabel">La tua proposta</h3></div><div class="modal-body"><input type="text" id="text" /></div><div class="modal-footer"><button class="btn" data-dismiss="modal" aria-hidden="true">Chiudi</button><button class="btn btn-success" data-dismiss="modal" aria-hidden="true" id="candidarsi" data-in="' + idProj + '">Candidarsi</button></div></div><br /><br /><br /><br />');
                location.reload();
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

$(document).ready(function() {
    $('#take').click(function(){
        var idProj = $('#take').attr('proj');
        var idUser = $('#take').attr('usr');
        var Pay = $('#cont').find('#pay').val();
        $.ajax({
            type: "POST",
            url: "script/spons.php",
            data: {'idProj': idProj, 'idUser': idUser, 'pay': Pay, 'type':'select'},
            async: false,
            context: this,
            success: function () {
                //$('#take').parents('#choose').html('');
                location.reload();
            }
        });

    });

});
