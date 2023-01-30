
 $(document).ready(function() {
	var bool = 1;

  $(".closed a").click(function(e){
	
	e.preventDefault();
	//$(this).parent().parent().animate({width:50,height:50},300);
	$("#lnb").slideToggle('fast', change_icon(bool));
  });

  function change_icon(bool){
	
		console.log("bool"+ bool);
	
	if(bool == 0){
		$(".closed > a > i").text("close");
		$("#info_index").css('border-left','1px solid rgba(255,255,255,0.6)');
		bool = 1;

	}else{
		$(".closed > a > i").text("expand_more");
		$("#info_index").css('height','auto').css('overflow-y','hidden').css('border','0');
		
		bool = 0;
		
	}
  }

   function change_icon2(){

	$(".closed > a > i").text("close");
  }



 var floatPosition = parseInt($("#quickmenu").css('top'));

 $(window).scroll(function() {

  var scrollTop = $(window).scrollTop();

  var newPosition = scrollTop + floatPosition + "px";
	
	//console.log($("#quickMenu").css('top'));

   $("#quickmenu").css('top', newPosition);
   $("#quickmen").stop().animate({

   "top" : newPosition

  }, 500);

 }).scroll();

});
