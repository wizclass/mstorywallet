<input type="hidden" name="SERVICE_ID" value="<?PHP echo $serviceId ?>"> <!-- 서비스아이디 -->
<input type="hidden" name="AMOUNT" value=""> <!-- 결제금액 -->
<input type="hidden" name="ORDER_ID" class="input" value="<?PHP echo $od_id ?>"> <!-- 주문번호 -->
<input type="hidden" name="ORDER_DATE" size=20 class="input" value="<?PHP echo $orderDate ?>"> <!-- 주문일시 -->
<input type="hidden" name="USER_ID" size=20 class="input" value="<?PHP echo $mid ?>"> <!-- 유저아이디 -->
<input type="hidden" name="USER_NAME" size=20 class="input" value=""> <!-- 유저이름 -->
<input type="hidden" name="USER_IP" size=20 class="input" value="<?=$_SERVER['REMOTE_ADDR']?>"> <!-- 고객ip -->
<input type="hidden" name="ITEM_NAME" size=20 class="input" value="<?PHP echo $goods ?>"> <!-- 상품명 -->
<input type="hidden" name="ITEM_CODE" size=20 class="input" value="00A1"> <!-- 상품코드 -->
<input type="hidden" name="INSTALLMENT_PERIOD" size=30 class="input" value="0:3:6:9:12"> <!-- 할부개월수 -->
<input type="hidden" name="CARD_TYPE" value="" />
<input type="hidden" name="PAY_METHOD" value="" />
<input type="hidden" name="CHECK_SUM" class="input" value="">
<input type="hidden" id="TRANSACTION_ID" name="TRANSACTION_ID" value="" />
<input type="hidden" id="AUTH_AMOUNT" name="AUTH_AMOUNT" value="" />
<input type="hidden" id="SERVICE_CODE" name="SERVICE_CODE" value="" />

<div id="display_pay_button" class="btn_confirm">
    <span id="show_req_btn"><input type="button" name="submitChecked" onClick="pay_approval();" value="결제등록요청" class="btn_submit"></span>
    <span id="show_pay_btn" style="display:none;"><input type="button" onClick="forderform_check();" value="주문하기" class="btn_submit"></span>
    <a href="<?php echo G5_SHOP_URL; ?>" class="btn_cancel">취소</a>
</div>