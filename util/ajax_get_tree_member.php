<?php
include_once('./_common.php');

// $sql = "select * from {$g5['member_table']} where  mb_id in (select c_id from g5_member_bclass where mb_id='".$member['mb_id']."')  ";
$sql = "select * from {$g5['member_table']} where  ";
$sql .= "mb_id like '{$binary_seach}%' ";
$sql .= " order by mb_id";

$result = sql_query($sql);
?>

<style>
	.result_member:hover{
		background:#0079d3;
		color:white;
	}
	.result_btn{
		padding:10px;
		top:305px;
	}
</style>

<?
	for ($i=0; $row=sql_fetch_array($result); $i++) {
	?>
		<div class='rows'>
			<strong class="result_member mbId" onclick="go_member('<?=$row['mb_id']?>');"><?=$row['mb_id']?></strong>
		</div>
	<?
	}
	if ($i == 0) echo "<div class='rows'>해당되는 회원을 찾지못했습니다.</div>";
	
?>