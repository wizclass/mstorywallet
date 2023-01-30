<?
	$imsrc = $_GET['img'];
	$imgfull = './img/'.$imsrc.'.jpg';
	$alt= $_GET['alt'];


	$data = getimagesize($imgfull);
	$img_width = $data[0];
	$img_height = $data[1];
?>

<!DOCTYPE html>
<html lang="ko-KR">
<head>
	<meta charset="utf-8" />
	<title>케이스타애드</title>

	<link rel="styleSheet" href="./sian.css">
	<script src="http://code.jquery.com/jquery-latest.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	
</head>

<body>
<div id="info_index">
<div class="closed"><a href="#" ><i class="material-icons md-36 md-light">close</i></a></div>

<?include_once('./lnb.php');?>

<span class="info_title"><a href="./images.php?img=<?=$imsrc?>" target="_blank" class="ori"><?=$alt?> 시안</a></span>
</div>
</div>


<div class="top">
	<div class="top_back" style="background:url(<?=$imgfull?>) no-repeat 50% 0; min-width:1024px;width:100%;height:<?=$img_height?>px;">
		<div class="top_container"></div>
	</div>
</div>


<script type="text/javascript" src="./quick.js"></script>

<script>
 $(document).ready(function() {
		
		$("#lnb").slideToggle('fast',function(){
		

	
		$(".closed > a > i").text("expand_more");
		$("#info_index").css('height','auto').css('overflow-y','hidden').css('border','0');
		bool = 1;
	
		
		} )
	}
);

</script>
</body>
</html>
