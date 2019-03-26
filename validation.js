(function($){
$("#changePassword input").on('keyup',function(){
	if($(this).attr('name') != 'oldPass' && $('#changePassword input[name="pass"]').val()){
		$(this).val() == $('#changePassword input[name="pass"]').val()? $('.invalid-feedback').css('display','none'):$('.invalid-feedback').css('display','block');
	}
});
})(jQuery)