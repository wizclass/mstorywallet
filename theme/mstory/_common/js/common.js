
$(function(){
	//파일올리기
	var fileTarget = $('.filebox'); 
	fileTarget.on('change', function(){ // 값이 변경되면 
		
		if(window.FileReader){ // modern browser 
			var filename = $(this)[0].files[0].name; 
		} else { // old IE 
			var filename = $(this).val().split('/').pop().split('\\').pop(); // 파일명만 추출 
		} // 추출한 파일명 삽입 
		$(this).siblings('.upload-name').val(filename); 
	}); 
});


// var debug = '<?=$is_debug?>';
var debug = "";
var thisTheme = "dark";

if(getCookie('mode')){
	var Theme = getCookie('mode'); 
}else{
	var Theme = thisTheme;
}

// 인풋 숫자
$(document).on('keyup','input[inputmode=number]',function(event){
	this.value = this.value.replace(/[^0-9]/g,'');   // 입력값이 숫자가 아니면 공백	
}); 

// 인풋 세자리콤마
$(document).on('keyup','input[inputmode=numeric]',function(event){
	this.value = this.value.replace(/[^0-9]/g,'');   // 입력값이 숫자가 아니면 공백
	this.value = this.value.replace(/,/g,'');          // ,값 공백처리
	this.value = this.value.replace(/\B(?=(\d{3})+(?!\d))/g, ","); // 정규식을 이용해서 3자리 마다 , 추가 	
}); 

// 인풋 숫자 + -
$(document).on('keyup','input[inputmode=price]',function(event){
	this.value = this.value.replace(/[^0-9]/g,'');   // 입력값이 숫자가 아니면 공백
	this.value = this.value.replace(/,/g,'');          // ,값 공백처리
	this.value = this.value.replace(/\B(?=(\d{3})+(?!\d))/g, ","); // 정규식을 이용해서 3자리 마다 , 추가 	
}); 

// 인풋 숫자 + -
$(document).on('keyup','input[inputmode=person_key]',function(event){
	// this.value = this.value.replace(/(?<=.{1})./gi, "*")   
}); 


// 개인정보 마스킹 처리 
let maskingFunc = { 
	
	checkNull : function (str){ 
		if(typeof str == "undefined" || str == null || str == ""){ 
			return true; 
		} else{ 
			return false; 
		} 
	}, 
	
	/* ※ 이메일 마스킹 ex1) 
	원본 데이터 : abcdefg12345@naver.com 변경 데이터 : ab**********@naver.com ex2) 
	원본 데이터 : abcdefg12345@naver.com 변경 데이터 : ab**********@nav****** 
	*/ 
	
	email : function(str){ 
		let originStr = str; 
		let emailStr = originStr.match(/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9._-]+)/gi); 
		let strLength; 
		
		if(this.checkNull(originStr) == true || this.checkNull(emailStr) == true){ 
			return originStr; 
		}else{ 
			strLength = emailStr.toString().split('@')[0].length - 3;
			// ex1) abcdefg12345@naver.com => ab**********@naver.com // 
			return originStr.toString().replace(new RegExp('.(?=.{0,' + strLength + '}@)', 'g'), '*'); 	
			// ex2) abcdefg12345@naver.com => ab**********@nav****** 
			return originStr.toString().replace(new RegExp('.(?=.{0,' + strLength + '}@)', 'g'), '*').replace(/.{6}$/, "******");
		} 
	},


	/* ※ 휴대폰 번호 마스킹 ex1) 
	원본 데이터 : 01012345678, 변경 데이터 : 010****5678 ex2) 
	원본 데이터 : 010-1234-5678, 변경 데이터 : 010-****-5678 ex3) 
	원본 데이터 : 0111234567, 변경 데이터 : 011***4567 ex4) 
	원본 데이터 : 011-123-4567, 변경 데이터 : 011-***-4567 
	*/ 
	phone : function(str){ 
		

		let originStr = str;
		let phoneStr;
		let maskingStr;

		if(this.checkNull(originStr) == true){ 
			return originStr;
		} 

		if (originStr.toString().split('-').length != 3) { // 1) -가 없는 경우 

			if(originStr.length < 11){
				phoneStr =  originStr.match(/\d{10}/gi)
			}else{
				phoneStr = 	originStr.match(/\d{11}/gi);
			}


			if(this.checkNull(phoneStr) == true){ 
				return originStr;
			} 
			
			if(originStr.length < 11) { 
				// 1.1) 0110000000 
				maskingStr = originStr.toString().replace(phoneStr, phoneStr.toString().replace(/(\d{3})(\d{3})(\d{4})/gi,'$1***$3'));
			} else { 
				// 1.2) 01000000000 
				maskingStr = originStr.toString().replace(phoneStr, phoneStr.toString().replace(/(\d{3})(\d{4})(\d{4})/gi,'$1****$3'));
			} 

		}else { // 2) -가 있는 경우 
			phoneStr = originStr.match(/\d{2,3}-\d{3,4}-\d{4}/gi);

			if(this.checkNull(phoneStr) == true){ 
				return originStr;
			} 

			if(/-[0-9]{3}-/.test(phoneStr)) { 
				// 2.1) 00-000-0000 
				maskingStr = originStr.toString().replace(phoneStr, phoneStr.toString().replace(/-[0-9]{3}-/g, "-***-"));
			}else if(/-[0-9]{4}-/.test(phoneStr)) { 
				// 2.2) 00-0000-0000 
				maskingStr = originStr.toString().replace(phoneStr, phoneStr.toString().replace(/-[0-9]{4}-/g, "-****-"));
			} 
		} 
		
		return maskingStr;
	}, 

	/* ※ 주민등록 번호 마스킹 (Resident Registration Number, RRN Masking) 
	ex1) 원본 데이터 : 990101-1234567, 변경 데이터 : 990101-1****** ex2) 
	원본 데이터 : 9901011234567, 변경 데이터 : 9901011****** 
	*/ 
	rrn : function(str){ let originStr = str;
			let rrnStr;
			let maskingStr;
			let strLength;
			if(this.checkNull(originStr) == true){ 
				return originStr;
			} 
			rrnStr = originStr.match(/(?:[0-9]{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[1,2][0-9]|3[0,1]))-[1-4]{1}[0-9]{6}\b/gi);
				
				if(this.checkNull(rrnStr) == false){ 
					strLength = rrnStr.toString().split('-').length;
					maskingStr = originStr.toString().replace(rrnStr,rrnStr.toString().replace(/(-?)([1-4]{1})([0-9]{6})\b/gi,"$1$2******"));
				}else { 
					rrnStr = originStr.match(/\d{13}/gi);
				if(this.checkNull(rrnStr) == false){ 
					strLength = rrnStr.toString().split('-').length;
					maskingStr = originStr.toString().replace(rrnStr,rrnStr.toString().replace(/([0-9]{6})$/gi,"******"));
				}else{ 
					return originStr;
				} 

			} return maskingStr;
	}, 
			
	/* ※ 이름 마스킹 
	ex1) 원본 데이터 : 갓댐희, 변경 데이터 : 갓댐* 
	ex2) 원본 데이터 : 하늘에수, 변경 데이터 : 하늘** 
	ex3) 원본 데이터 : 갓댐, 변경 데이터 : 갓* 
	*/ 
	name : function(str){ 
		let originStr = str;
		let maskingStr;
		let strLength;

		if(this.checkNull(originStr) == true){ 
			return originStr;
		} 
		
		strLength = originStr.length;
		if(strLength < 3){ 
			maskingStr = originStr.replace(/(?=.{1})./gi, "*");
		}else { 
			maskingStr = originStr.replace(/(?=.{2})./gi, "*");
		} 
	return maskingStr;
	} 
};

function shift_coin(val, num = 0) {

	let _num = Number("1".padEnd(num+1, '0'));
	return Math.floor(val * _num) / _num;
}

// 숫자에 콤마 찍기
function Price(x,demical=8){
	let _num = Number("1".padEnd(demical+1, '0'));
	let result_num = Math.floor(x * _num) / _num;
	return result_num.toLocaleString('ko-KR', { maximumFractionDigits: 8 });
}

function price_custom(x,demical){
	let _num = Number("1".padEnd(demical+1, '0'));
	let result_num = (x * _num) / _num;

	return result_num.toLocaleString('ko-KR', { maximumFractionDigits: 8 });
}

// 숫자에 콤마 제거
function conv_number(val) {
	let reg = /,/g;
	let number = val;
	if(reg.test(val)){
		number = val.replace(reg,'');
	}

	return number;
}

// 숫자만 입력
function onlyNumber(id){
	document.getElementById(id).oninput = function(){
	  // if empty
	  if(!this.value) return;

	  // if non numeric
	  let isNum = this.value[this.value.length - 1].match(/[0-9.]/g);
	  if(!isNum) this.value = this.value.substring(0, this.value.length - 1);
	}
}

// 코인 숫자 8자리
function coin_val(val){
	return Number(val).toFixed(8);
}

// 코인 소수점 특정자리수 버림 계산값 
function calculate_math(val,point){
	var cal1 = val * (Math.pow(10, point));
	var cal2 = Math.round(cal1);
	var cal3 = cal2 / (Math.pow(10, point));
	return cal3;
}

// 보너스게이지
function move(bonus_per,main = 0) {
	var total_bonus_point = 0;
	if(bonus_per != undefined){
		var total_bonus_point = bonus_per;
	}

	if(debug){console.log(' total_bonus_point  =  '+total_bonus_point);}

	if(total_bonus_point >= '100'){total_bonus_point = '100'};

	var elem = document.getElementById("total_B_bar");
	var width = 1;
	var id = setInterval(frame, 20);
	function frame() {
		if(width >= 100){
			$('#total_B_bar').addClass('deg100');
			$('#total_B_bar').addClass('active');
			$('.bonus_per').addClass('active')
		}

		if(width >= 75){
			$('#total_B_bar').addClass('deg75');
		}
		if(width >= 50){
			$('#total_B_bar').addClass('deg50');
		}
		if(width >= 25){
			$('#total_B_bar').addClass('deg25');
		}

		if (width >= total_bonus_point) {
			clearInterval(id);

			// 수당초과시 팝업 
			/* if(total_bonus_point > 75 && main == 1){dialogModal('Total Bonus', 'Total Bonus more than 75%', 'warning');} */

		} else {
			width++;
			elem.style.width = width + '%';
		}
	}
}

function go_to_url(target){
	location.href="/page.php?id="+ target;
}

function getCookie(name) {

	var i, x, y, ARRcookies = document.cookie.split(";");
	for (i = 0; i < ARRcookies.length; i++) {

			x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));

			y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);

			x = x.replace(/^\s+|\s+$/g, "");

			if (x == name) {

					return unescape(y);

			}
	}

}

function setCookie(name, value, days) {
	if (days) {
			var date = new Date();

			date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));

			var expires = "; expires=" + date.toGMTString();
	} else {
		var expires = "";
	}
		document.cookie = name + "=" + value + expires + "; path=/";
	}

	
function commonModal(title, htmlBody, bodyHeight){
	$('#commonModal').modal('show');
	$('#commonModal .modal-header .modal-title').html(title);
	$('#commonModal .modal-body').html(htmlBody);
	if(bodyHeight){
		$('#commonModal .modal-body').css('height',bodyHeight+'px');
	} 
	$('#closeModal').focus();
}

function confirmModal(htmlBody, category, param, dim = true){
	$('#confirmModal').modal('show');
	/* $('#confirmModal .modal-header .modal-title').html(title); */

	if(dim == false) {
		var dimhide = '';
	} else {
		var dimhide = "dimHide();";
	}

	if(category == 'giftcard_buy') {
		$('#confirmModal .modal-body').html(htmlBody);
		$('#confirmModal .modal-footer').html("<button type='button' class='btn btn-secondary cancle btn wd btn_defualt closed' data-dismiss='modal' onclick='"+dimhide+"'>취소하기</button>");
		$('#confirmModal .modal-footer').append("<button type='button' class='btn wd btn_defualt closed modal_basic_btn' data-dismiss='modal' id='modal_return_url' onclick='"+category+'('+param+')'+"'>구매하기</button>");
	}
	else if(category == 'giftcard_buy_fail') {

	}
	$('#confirmModal').focus();
}

function dialogModal(title, htmlBody, category,dim = true){
	
	$('#dialogModal').modal('show');
	$('#dialogModal .modal-header .modal-title').html(title);

	if(dim == false){
		var dimhide = '';
	}else{
		var dimhide = "dimHide();";
	}

	if(category == 'success'){
		$('#dialogModal .modal-body').html("<div class=chkimg><img src='"+g5_url+"/img/success_check.png'></div>" + htmlBody);
		$('#dialogModal .modal-footer').html("<button type='button' class='btn wd btn_defualt closed modal_basic_btn' data-dismiss='modal' id='modal_return_url' onclick='"+dimhide+"'>확인</button>");
	}else if(category == 'confirm'){
		$('#dialogModal .modal-body').html("<div class=chkimg><img src='"+g5_url+"/img/notice.svg'></div>" + htmlBody);
		$('#dialogModal .modal-footer').html("<button type='button' class='btn btn-secondary cancle' data-dismiss='modal' onclick='"+dimhide+"'>Cancle</button> <button type='button' class='btn btn-primary confirm' id='modal_confirm' data-dismiss='modal' >OK</button>");
		
	}else if(category == 'warning'){
		$('#dialogModal .modal-body').html("<div class=chkimg><img src='"+g5_url+"/img/notice.svg'></div>" + htmlBody);
		$('#dialogModal .modal-footer').html("<button type='button' class='btn wd btn_defualt closed modal_basic_btn' data-dismiss='modal' id='modal_return_url' onclick='"+dimhide+"'>닫기</button>");
		
	}else if(category == 'input_confirm'){
		$('#dialogModal .modal-body').html("<div class=chkimg><img src='"+g5_url+"/img/notice.svg'></div>" + htmlBody);
		$('#dialogModal .modal-footer').html("<button type='button' class='btn btn-secondary cancle' data-dismiss='modal' onclick='"+dimhide+"'>Cancle</button> <button type='button' class='btn btn-primary confirm' id='modal_confirm' data-dismiss='modal' >OK</button>");
	}else if(category == 'failed'){
		$('#dialogModal .modal-body').html("<div class=chkimg><img src='"+g5_url+"/img/notice.svg'></div>" + htmlBody);
		$('#dialogModal .modal-footer').html("<button type='button' class='btn wd btn_defualt main_btn2 closed' data-dismiss='modal' id='modal_return_back' onclick='"+dimhide+"'>확인</button>");
	}else if(category == 'find_warning'){
		$('#dialogModal .modal-body').html("<div class=chkimg><img src='"+g5_url+"/img/exclamation.png'></div>" + htmlBody);
		$('#dialogModal .modal-footer').html("<button type='button' class='btn wd btn_defualt main_btn2 closed' data-dismiss='modal' id='modal_return_back' onclick='"+dimhide+"'>확인</button>");
	}else if(category == 'find_success'){
		$('#dialogModal .modal-body').html("<div class=chkimg><img src='"+g5_url+"/img/email.svg'></div>" + htmlBody);
		$('#dialogModal .modal-footer').html("<button type='button' class='btn wd btn_defualt main_btn2 closed' data-dismiss='modal' id='modal_return_url' onclick='"+dimhide+"'>확인</button>");
	}else if(category == 'kyc_warning'){
		$('#dialogModal .modal-body').html("<div class=chkimg><img src='"+g5_url+"/img/notice.svg'></div>" + htmlBody);
		$('#dialogModal .modal-footer').html("<button type='button' class='btn wd btn_defualt btn_cancel closed' data-dismiss='modal' id='modal_return_url' onclick='"+dimhide+"'>닫기</button><a href='/page.php?id=profile' class='btn wd btn_defualt main_btn'>KYC 인증</a>");
	}else if(category == 'tr_password'){
		$('#dialogModal .modal-body').html("<div class=chkimg><img src='"+g5_url+"/img/notice.svg'></div>" + htmlBody);
		$('#dialogModal .modal-footer').html("<a href='/page.php?id=profile' class='btn wd btn_defualt main_btn'>비밀번호 변경하기</a>");
	}

	$('#dialogModal').focus();
	
}

function commonModalTerms(htmlBody, bodyHeight) {
    $('#commonModalTerms').modal('show');
    $('#commonModalTerms .modal-body').html(htmlBody);

    if (bodyHeight) {
        $('#commonModalTerms .modal-body').css('height', bodyHeight);
    }
    $('#closeModalTerms').focus();

    $(document).on('click','#closeModalTerms',function () {
		$('#check1').prop('checked',true);
        $('#commonModalTerms').modal('hide');
    });
}

// 테마 변경 함수
function mode_init() {
	var url = location.href;
	if(getCookie('mode')){
		var Theme = getCookie('mode'); 
	}else{
		var Theme = thisTheme;
	}

	if($('.top_title').children().length == 1) {
		$.removeCookie('mode', { path: '/' });
	} 

	// if(Theme == 'dark') {
	// 	$('.left_gnbWrap .close img').attr('src',g5_theme_url+'/img/gnb/close_dark.png');
		
	// 	if(url.indexOf('profile') != -1 || url.indexOf('news') != -1 || url.indexOf('referral_link') != -1) {
	// 		$('header .top_title').css('background','#17191d');
	// 		$('#wrapper').css('background','#17191d');
	// 	}
	// } else if(Theme == 'white') {
	// 	$('.left_gnbWrap .close img').attr('src',g5_theme_url+'/img/gnb/close.png');

	// 	if(url.indexOf('profile') != -1 || url.indexOf('news') != -1 || url.indexOf('referral_link') != -1) {
	// 		$('header .top_title').css('background','#fff')
	// 		$('#wrapper').css('background','#fff')
	// 	}
	// }

	$('#mode_select').on('change',function() {
		mode_change(this.value);
	})

	$('#mode_select').val(Theme).change();
}

// 테마 모드 변경 셀렉
function mode_change(mode) {
	var profile_icon1 = '';
	var profile_icon2 = '';
	var profile_icon3 = '';
	var url = location.href;

	setCookie('mode',mode,1,'/');

	profile_icon1 = "<img src='"+g5_theme_url+"/img/person_information_"+mode+".png'/>";
	profile_icon2 = "<img src='"+g5_theme_url+"/img/security_setting_"+mode+".png'/>";
	profile_icon3 = "<img src='"+g5_theme_url+"/img/recommendation_information_"+mode+".png'/>";

	$('body').attr('class',mode);
	$('.profile-box .title .p1').html(profile_icon1);
	$('.profile-box .title .p2').html(profile_icon2);
	$('.profile-box .title .p3').html(profile_icon3);

	var img_src_down = g5_theme_url + "/img/arrow_up_" + mode + ".png";
	$('.updown').attr('src',img_src_down);

	$('.left_gnbWrap .close img').attr('src', g5_theme_url+'/img/gnb/close_'+mode+'.png');

	if(url.indexOf('profile') != -1 || url.indexOf('news') != -1 || url.indexOf('referral_link') != -1) {
		if(mode == 'white') {
			$('header .top_title').css('background','#fff');
			$('#wrapper').css('background','#fff')
		} else if(mode == 'dark') {
			$('header .top_title').css('background','#17191d')	
			$('#wrapper').css('background','#17191d')
		}
	} 
}

function mode_colorset(mode) {
	var title_color = '';
	megachart.render();
	zetachart.render();
	zetapluschart.render();
	superchart.render();
	container_circle.render();
	chart.render();
	
	if(mode == 'white') {
		title_color = '#333';
		
		megachart.updateOptions({
			title: {
				style: {
					color: title_color
				}
			},
			colors: ['#FD6585'],
			fill: {
				type: 'gradient',
				gradient: {
				gradientToColors: ['#f8b874']
				}
			}
		});
		
		zetachart.updateOptions({
			title: {
				style: {
					color: title_color
				}
			},
			colors: ['#EE9AE5'],
			fill: {
			  type: 'gradient',
			  gradient: {
				gradientToColors: ['#8959f9']
			  }
			}
		});
		zetapluschart.updateOptions({
			title: {
				style: {
					color: title_color
				}
			},
			colors: ['#8959f9'],
			fill: {
			  type: 'gradient',
			  gradient: {
				gradientToColors: ['#4C83FF']
			  }
			}
		});
		superchart.updateOptions({
			title: {
				style: {
					color: title_color
				}
			},
			colors: ['#2ad0fa'],
			fill: {
			  type: 'gradient',
			  gradient: {
				gradientToColors: ['#0c51cf']
			  }
			}
		});
		container_circle.updateOptions({
			colors: ['#ff4500', '#ef21fd', '#6f00ff', '#0260b9','#008000']
		});
		chart.updateOptions({
			colors: ['#ff4500', '#ef21fd', '#6f00ff', '#0260b9','#008000']
		});
		
	} else if(mode == 'dark') {
		title_color = '#fff';
		megachart.updateOptions({
		  title: {
			style: {
			  color: title_color
			}
		  },
		  colors: ['#2d7dcb'],
		  fill: {
			type: 'gradient',
			gradient: {
			  gradientToColors: ['#004e99']
			}
		  }
		});
		zetachart.updateOptions({
			title: {
				style: {
					color: title_color
				}
			},
			colors: ['#287d9b'],
			fill: {
			  type: 'gradient',
			  gradient: {
				gradientToColors: ['#034d64']
			  }
			}
		});
		zetapluschart.updateOptions({
			title: {
				style: {
					color: title_color
				}
			},
			colors: ['#414d5a'],
			fill: {
			  type: 'gradient',
			  gradient: {
				gradientToColors: ['#252e38']
			  }
			}
		});
		superchart.updateOptions({
			title: {
				style: {
					color: title_color
				}
			},
			colors: ['#db2eb3'],
			fill: {
			  type: 'gradient',
			  gradient: {
				gradientToColors: ['#733f88']
			  }
			}
		});
		container_circle.updateOptions({
			colors: ['#2d7dcb', '#287d9b', '#5b6066', '#db2eb3','#266099']
		});
		chart.updateOptions({
			colors: ['#2d7dcb', '#287d9b', '#5b6066', '#db2eb3','#266099']
		});
	}
}

function mode_colorset2(mode) {
	var title_color = '';
	var chart = new ApexCharts(document.querySelector("#myChart2"), options);
	chart.render();

	if(mode == 'white') {
		chart.updateOptions({
			colors: ['#ff4500', '#ef21fd', '#6f00ff', '#0260b9','#008000'],
			title: {
				style: {
					color: '#333'
				}
			},
			plotOptions: {
				pie: {
					donut: {
						labels: {
							value: {
								color: '#333'
							}
						}
					}
				}
				
			}
		})
	} else if(mode == 'dark') {
		title_color = '#fff';
		chart.updateOptions({
			colors: ['#2d7dcb', '#287d9b', '#5b6066', '#db2eb3','#266099'],
			title: {
				style: {
					color: '#fff'
				}
			},
			plotOptions: {
				pie: {
					donut: {
						labels: {
							value: {
								color: '#fff'
							}
						}
					}
				}
				
			}
		})
	}
}

function show_alert_terms(args) {
    commonModalTerms(`<p>${args}`, 'auto');
}

function ajax(url, type, data, callback, async=true) {
       
    $.ajax({
        url: url,
        type: type,
        dataType: "json",
        cache: false,
        async: async,
        data: data,
        success: (result) => {
            callback(result);
        }
    });
        
}

function count_down(){

	let time = 299;
	let min = "";
	let sec = "";

	$("#mb_hp").attr("readonly", true);
	$('#hp_button').attr('disabled', true);
	$('#timer_auth').show();
	$('#timer_down').css('color', 'red');

	let _count_down = setInterval(function() {
	min = parseInt(time / 60);
	sec = time % 60;
	sec_temp = "";
	if(sec < 10){ sec_temp = "0"; }
	document.getElementById('timer_down').innerHTML = "남은시간 : 0" +min + ":"+sec_temp + sec;
	time--;

	if (time < 0) {
		clearInterval(_count_down);
		dialogModal('', '시간이 초과되었습니다.', 'failed');
		$('#modal_return_url').click(function(){
			window.location.reload();
		})
	}

	}, 1000);

	return _count_down;
}
