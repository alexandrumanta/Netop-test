var MIN_LENGTH = 3;
$( document ).ready(function() {
	$(".search-filter-input").keyup(function() {
    	$(".search-filter-cancel").addClass("is-focused");
		var keyword = $(".search-filter-input").val();
		if (keyword.length >= MIN_LENGTH) {
			$.get( "auto-complete.php", { keyword: keyword } )
			.done(function( data ) {
				$('#results').html('');
				var results = jQuery.parseJSON(data);
				$(results).each(function(key, value) {
					$("#results").show();
					console.log(value);
					var result = '<li class="item" id="' + key + '"><div class="result">' + value.image + '<span>' + value.error +  value.name + '</span><span>' + value.author + '</span><small><strong></strong> ' + value.category + '</small><span>' + value.price + '</span</div></li>';        
					$('#results').append(result);
				});
			});
		}
	});
	$(".search-filter-input").blur(function(){
    		$("#results").fadeOut(500);
    		if ($(this).val() == '') {
				$(".search-filter-cancel").removeClass('is-focused');
			}
    })
	$(".search-filter-cancel").on('click',function() {
		$(".search-filter-input").val('');
		$("#results").fadeOut(500);
		if ($(this).val() == '') {
			$(".search-filter-cancel").removeClass('is-focused');
		}
	});
});



