<?php include '_include/head.php'; ?>
<?php include '_include/gnb.php'; ?>
<style>
.dim{display: block;}
.send_tran_pop{display: block;}
</style>

			<section class="con90_wrap">
				<div class="color_block bit_block">
					<div class="clear_fix">
						<strong class="f_left">Bitcoin</strong>
						<b class="f_right">12,234.52 USD</b>
					</div>
					<div class="clear_fix">
						<span class="f_left">=7885.52 USD</span>
						<p class="f_right">1.85521485 BTC</p>
					</div>
				</div>
				
				<ul class="send_con">
					<li>
						<div>
							<div>
								<span class="send_title">수신인 지갑주소</span>
								<img class="scan_qr f_right" src="_images/wallet_addr.gif" alt="주소 확인하기">
							</div>
							<p class="font_gray mt10">지갑 주소를 입력하거나 스캔하십시오</p>
						</div>
					</li>
					<li>
						<div>
							<div>
								<span class="send_title">전송할 코인 갯수</span>
								<a href="" class="f_right all_send">지갑 잔고 다 보내기</a>
								<small class="f_right font_sky">잔고 부족합니다.</small>
							</div>
							<ul class="money_chage clear_fix">
								<li>
									<input type="text" placeholder="0.018">
									<span>BTC</span>
								</li>
								<li>=</li>
								<li>
									<input type="text" placeholder="$0.00">
									<span>USD</span>
								</li>
							</ul>
						</div>
					</li>
					<li>
						<div>
							<span class="send_title">수수료</span>
							<p class="f_right"><span class="font_gray">0.00245</span> BTC</p>
						</div>
					</li>
					<li>
						<div>
							<span class="send_title">인출합계</span>
							<p class="f_right"><span class="font_gray">0.00245</span> BTC</p>
						</div>
					</li>
				</ul>
			</section>
			
			<div class="btn_block_btm_wrap">
				<input type="button" value="보내기" class="btn_basic send_tran_open pop_open">
			</div>

			<!-- 팝업 -->
			<div class="pop_wrap send_tran_pop">
				<p class="pop_title">지갑 잔고가 부족합니다</p>	
				<div>
					<p>지갑에 개스비로 쓸 이더리움이 부족합니다</p>
				</div>
				<div class="pop_close_wrap">
					<a href="javascript:void(0);" class="pop_close">확인</a>
				</div>
			</div>

			<div class="dim"></div>
			<script>
				$(function() {
					$('.send_tran_open').click(function(){
						$('.send_tran_pop').css("display","block");
					});
				});
			</script>
			<!-- 팝업끝 -->


		<div class="gnb_dim"></div>
	</section>



	<script>
		$(function() {
			$(".top_title h3").html("<img src='_images/top_send.png' alt='아이콘'> 비트코인 보내기");
			
		});
	</script>



</body></html>
