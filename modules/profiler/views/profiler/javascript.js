if (typeof $ === "function"){ // jQuery is loaded
	$(document).ready(function(){
		$('#kohana_profiler_show_hide').click(function(){
			$(this).toggle(
				function () {
					$(this).html("Show Profiler");
				},
				function () {
					$(this).html("Hide Profiler");
				}
			);
			$('#kohana_profiler').slideToggle();
		});
	});	
}else{ // pure javascript in use
	function toggleProfiler(){
		var el = document.getElementById('kohana_profiler');
		var el2 = document.getElementById('kohana_profiler_show_hide');
		
		if (el.style.display != 'none'){
			el.style.display = 'none';
			el2.innerHTML = 'Show Profiler';
		}else{
			el.style.display = 'block';
			el2.innerHTML = 'Hide Profiler';
		}
	}
	document.getElementById('kohana_profiler_show_hide').onclick = toggleProfiler;
}