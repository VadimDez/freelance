$(document).ready(function() {
    $("#submit").click(function()
	{
		if(($("input[name='feedValue']:checked").val()) && ($.trim($("#feedText").val()).length))
		{
			var feedValue 	= $("input[name='feedValue']:checked").val();
			var feedText	= $.trim($("#feedText").val());
			var idProj		= $("#proj").val();
			alert(feedValue);
			$.ajax({
            type: "POST",
            url: "script/feedback.php",
            data: {'idProj': idProj, 'feedValue': feedValue, 'feedText': feedText},
            async: false,
            context: this,
            success: function () {
				$("#feedback").fadeOut("fast");
				$("#feedback").html("Grazie per il tuo feedback.").addClass("alert alert-success").fadeIn("slow");
            }
        });
			
		}
		else
		{
			$("#error").html('');
			
			$("#error").addClass("alert alert-error");
			
			if(!$("input[name='feedValue']:checked").val())
			{
				$("#error").append("<p>Devi selezionare NEGATIVO o POSITIVO.</p>");
			}
			
			if(!$.trim($("#feedText").val()).length)
			{
				$("#error").append("<p>Campo di feedback non deve essere vuoto.</p>");
			}
		}
	});
});