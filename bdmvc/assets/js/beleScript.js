$(function(){
	$('.btn').click(function(){
		var ogColor = $(this).css('background-color');
		$(this).css('background-color','red').delay(1000).css('background-color', ogColor);
	})

	$('#toggleCompetences').siblings('div.row').hide();

	var toggle = true;

	$('#toggleCompetences').click(function(e){
		e.preventDefault();
		$(this).siblings('div.row').slideToggle();

		if( $("#toggleCompetences a span.fa").hasClass('fa-chevron-up') ){
			$("#toggleCompetences a span.fa").removeClass('fa-chevron-up').addClass('fa-chevron-down');
		} else{
			$("#toggleCompetences a span.fa").removeClass('fa-chevron-down').addClass('fa-chevron-up');
		}

	});
})