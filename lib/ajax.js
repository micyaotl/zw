$(function(){
	var LoadMsg = 'Espere por favor...';
	$('#nvg li a').click(function(){
    	if(!$(this).hasClass("cc")) {
		var _Href = $(this).attr('href');
		$('<div id="loading">'+LoadMsg+'</div>').appendTo('body').fadeIn('slow',function(){
			$.ajax({
				type:	'POST',
				url:	_Href,
				data:	"ajax=1",
				dataType:	'html',
				timeout:	5000,
				success: function(d,s){
						$('#loading').fadeOut('slow',function(){
							$(this).remove();
							$('#wrapper').slideUp('slow',function(){
									$(this).html(d).slideDown('slow');
								});
							});
						},
				error: function(o,s,e){
							window.location = _Href;
						}
			});
		});
        }
		return false;
	});
});