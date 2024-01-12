jQuery(document).ready(function($){
	$('.sortable').sortable({
		axis: 'y',
		forceHelperSize: true,
		forcePlaceholderSize: true,
		handle: '.dashicons-move',
	});
	$(document)
	.on('click', '.dashicons-remove', function(e){
		e.preventDefault();
		if( $(this).closest('tbody').find('tr').length > 1 ){
			$(this).closest('tr').remove();
		}
	})
	.on('click', '.dashicons-insert', function(e){
		e.preventDefault();
		$(this).closest('tr').clone(0, 0).insertAfter( $(this).closest('tr') );
	});
});