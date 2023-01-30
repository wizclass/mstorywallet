
<!DOCTYPE html>
<html lang="ko-KR">
<head>
	<meta charset="utf-8" />
	<title>ESG WALLET</title>

	<link rel="styleSheet" href="./sian.css">
	<script src="http://code.jquery.com/jquery-latest.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	

	
</head>

<body>
<div id="info_index">
<div class="closed"><a href="#" ><i class="material-icons md-36 md-light">close</i></a></div>

<?include_once('./lnb.php');?>

<span class="info_title">ESG WALLET</span>
</div>
</div>



<div class="top mobile">

	<div class="top_back" style="background:url('./img/launcher.png') no-repeat 50% 0; min-width:1024px;height:3770px">
		<div class="top_container"></div>
	</div>

	<!--
	<div id="quickmenu" class="quickmenu1" style="right:0;top:269px;position:absolute;width:140px;" >
		<img src="./img/rnb3.png">
	</div>
	-->
</div>

<script>
 $(document).ready(function() {
		$(".closed > a > i").text("expand_more");
		$("#info_index").css('height','auto').css('overflow-y','hidden').css('border','0');
		bool = 1;
});
</script>
<script type="text/javascript" src="./quick.js"></script>
</body>
</html>

