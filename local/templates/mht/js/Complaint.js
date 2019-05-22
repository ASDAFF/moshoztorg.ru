$(function(){
	var ComplaintClass = function(ComplaintFormParams) {
		try {
			ComplaintFormParams.handler = ComplaintFormParams.handler||"/ajax.php";
			ComplaintFormParams.action = ComplaintFormParams.action||"form";
			ComplaintFormParams.title = ComplaintFormParams.title||"";
			
			/**********************Generate form block**********************************/
			
			var ComplaintBlock = $("<div class='complaint-form'></div>");
				var ComplaintForm = $("<form enctype='multipart/form-data' method='POST' action='"+ComplaintFormParams.handler+"'>");
					//ComplaintForm.append("<input type='checkbox' name='agreementb' value='1' style='display: none' />");
					ComplaintForm.append("<input type='hidden' value='"+ComplaintFormParams.action+"' name='act'>");
					ComplaintForm.append("<input type='hidden' value='1' name='confirm'>");
					var Complaint = $("<div class='complaint-form__complaint complaint-form-complaint'>");
						Complaint.append("<div class='complaint-form__title'>"+ComplaintFormParams.title+"</div>");
						Complaint.append("<div class='complaint-form__close'></div>");
						var ComplaintSteps = $("<div class='complaint-form__steps complaint-form-steps'></div>");
						var ComplaintPoints = $("<div class='complaint-form__points'><ul></ul></div>");
						var ComplaintStep,ComplaintStepNote,ComplaintStepLabel,ComplaintStepInput;
						for(var i=0; i<ComplaintFormParams.fields.length; i++) {
							ComplaintFormParams.fields[i].notis = ComplaintFormParams.fields[i].notis||"";
							ComplaintFormParams.fields[i].label = ComplaintFormParams.fields[i].label||"";
							ComplaintFormParams.fields[i].value = ComplaintFormParams.fields[i].value||"";
							ComplaintStep = $("<div class='complaint-form-steps__step complaint-form-steps-step'></div>");
								ComplaintStepNote = $("<span class='complaint-form-steps-step__notis'>"+ComplaintFormParams.fields[i].notis+"</span>");
								ComplaintStepLabel = $("<label class='complaint-form-steps-step__label complaint-form-steps-step__animated' for='"+ComplaintFormParams.fields[i].name+"'>"+ComplaintFormParams.fields[i].label+"</label>");
								if(ComplaintFormParams.fields[i].type == "textarea"){
									ComplaintStepInput = $("<textarea class='complaint-form-steps-step__input complaint-form-steps-step__animated'></textarea>");
								} else if(ComplaintFormParams.fields[i].type == "captcha") {
									ComplaintStepInput = $("<input class='complaint-form-steps-step__input complaint-form-steps-step__animated' name='captcha' type='text'/>");
									var captchaStepHidden = $("<input type='hidden' name='captcha_check'/>");
									var captchaStepImage = $("<img class='captcha-img' />");
								} else {
									ComplaintStepInput = $("<input class='complaint-form-steps-step__input complaint-form-steps-step__animated' type='"+ComplaintFormParams.fields[i].type+"'/>");
								}
								ComplaintStepInput.attr("id",ComplaintFormParams.fields[i].name);
								ComplaintStepInput.attr("name",ComplaintFormParams.fields[i].name);
								ComplaintStepInput.attr("value",ComplaintFormParams.fields[i].value);
								ComplaintStepInput.attr("placeholder","");
								ComplaintStepInput.data("required",ComplaintFormParams.fields[i].required||false);
							ComplaintStep.append(ComplaintStepNote);
							ComplaintStep.append(ComplaintStepLabel);

							if(ComplaintFormParams.fields[i].type == "captcha") {
								ComplaintStep.append(captchaStepHidden);
								ComplaintStep.append(captchaStepImage);
							}
							ComplaintStep.append(ComplaintStepInput);

							ComplaintSteps.append(ComplaintStep);
							ComplaintPoints.append("<li></li>");
						}	
							ComplaintStep = $("<div class='complaint-form-steps__step complaint-form-steps-step is-result'></div>");
								ComplaintStep.append("<div class='complaint-form-steps-step__result is-process'>"+ComplaintFormParams.resilt.process+"</div>");
								ComplaintStep.append("<div class='complaint-form-steps-step__result is-success'>"+ComplaintFormParams.resilt.success+"</div>");
								ComplaintStep.append("<div class='complaint-form-steps-step__result is-fail'>"+ComplaintFormParams.resilt.fail+"</div>");
							ComplaintSteps.append(ComplaintStep);
							ComplaintPoints.append("<li></li>");
						Complaint.append(ComplaintSteps);
						Complaint.append(ComplaintPoints);
                        Complaint.append("<div class='complaint-form-agreement-wrap'><label class='complaint-agreement'>" +
                            "<input type='checkbox' name='agreementb' value='1' checked>" +
                            "<a href=\"javascript:;\" data-fancybox=\"modal\" data-src=\"#agreement-detail\">Я даю свое согласие на обработку моих" +
                            "персональных данных, в соответствии с Федеральным законом от 27.07.2006 года №152-ФЗ «О персональных данных», на условиях и для целей," +
                            "определенных в Согласии на обработку персональных данных</a></label></div>");
						Complaint.append("<div class='complaint-form__button-block complaint-form-button-block'>" +

                            "<div class='complaint-form-button-block__next'>Далее</div>" +
                            "<div class='complaint-form-button-block__notis'>или нажмите enter</div>" +
                        "</div>");
					ComplaintForm.append(Complaint);
			ComplaintBlock.append(ComplaintForm);
			$("body").append(ComplaintBlock);
			/**********************Handels block*************************************/
			
			var ComplaintTelInput = ComplaintBlock.find("input.complaint-form-steps-step__input[type='tel']");
			ComplaintTelInput.val("");
			ComplaintTelInput.attr("placeholder","+7 (___) ___ - __ - __");
			ComplaintTelInput.attr("type","tel");
			ComplaintTelInput.data("pattern","/^\\+7\s\([0-9]{3}\)\s[0-9]{3}\s-\s[0-9]{2}\s-\s[0-9]{2}$/");
			ComplaintTelInput.mask('+7? (999) 999 - 99 - 99');

			var ComplaintTextInput = ComplaintBlock.find("textarea.complaint-form-steps-step__input[name='text']");
			//ComplaintTextInput.inputmask('Regex', { regex: "[а-яА-Я]{5,30}" });
			//ComplaintTextInput.mask("[а-яА-Я]{5,30}");

			var ComplaintCloseButton = ComplaintBlock.find(".complaint-form__close");
			var ComplaintInitButton = ComplaintFormParams.initButton;
			var ComplaintNextButton = ComplaintBlock.find(".complaint-form-button-block__next");
			var ComplaintInput = ComplaintBlock.find("input.complaint-form-steps-step__input");
			var ComplaintForm = ComplaintBlock.find("form");
			var ComplaintResultsProcess = ComplaintBlock.find(".complaint-form-steps-step__result.is-process");
			var ComplaintResultsSuccess = ComplaintBlock.find(".complaint-form-steps-step__result.is-success");
			var ComplaintResultsFail = ComplaintBlock.find(".complaint-form-steps-step__result.is-fail");
			
			/**********************Processors block**********************************/
			
			this.CurrentStep = 0;
			
			this.step = function(StepNumber){
				this.CurrentStep = StepNumber;
				
				var ComplaintCurrentSteps = ComplaintBlock.find(".complaint-form-steps__step");
				var ComplaintLastStepNumber = ComplaintCurrentSteps.size()-1;
				var ComplaintCurrentStep = ComplaintBlock.find(".complaint-form-steps__step:eq("+StepNumber+")");
				var ComplaintCurrentPoints = ComplaintBlock.find(".complaint-form__points li");
				var ComplaintCurrentPoint = ComplaintBlock.find(".complaint-form__points li:eq("+StepNumber+")");
				var ComplaintCurrentInput = ComplaintCurrentStep.find(".complaint-form-steps-step__input");
				var ComplaintCurrentLabel = ComplaintCurrentStep.find(".complaint-form-steps-step__label");
				var ComplaintCurrentAnimated = ComplaintCurrentStep.find(".complaint-form-steps-step__animated");
				var ComplaintCurrentStepHeight = ComplaintCurrentStep.height();
				
				if(StepNumber==0){
					ComplaintCurrentSteps.removeClass("is-active");
					ComplaintCurrentStep.addClass("is-active");
					ComplaintCurrentInput.focus();
					ComplaintCurrentPoints.removeClass("is-active");
					ComplaintCurrentPoint.addClass("is-active");
				}else if(StepNumber != ComplaintLastStepNumber){
					var ComplaintPrevStep = ComplaintBlock.find(".complaint-form-steps__step:eq("+(StepNumber-1)+")");
					var ComplaintPrevInput = ComplaintPrevStep.find(".complaint-form-steps-step__input");
					var ComplaintPrevLabel = ComplaintPrevStep.find(".complaint-form-steps-step__label");
					var ComplaintPrevAnimated = ComplaintPrevStep.find(".complaint-form-steps-step__animated");
					ComplaintCurrentStep.css({
						"top":$(document).height()*0.5
					});
					ComplaintCurrentStep.show();
					ComplaintPrevLabel.stop().animate({"top":-$(document).height()*0.5},"slow","easeInCubic");
					ComplaintCurrentLabel.stop().animate({"top":-$(document).height()*0.5-ComplaintCurrentStepHeight},"slow","easeInCubic");
					setTimeout(
						function(){
							ComplaintPrevInput.stop().animate({"top":-$(document).height()*0.5},"slow","easeInCubic");
							ComplaintCurrentInput.stop().animate({"top":-$(document).height()*0.5-ComplaintCurrentStepHeight},"slow","easeInCubic",
								function(){
									ComplaintCurrentInput.focus();
									ComplaintCurrentPoints.removeClass("is-active");
									ComplaintCurrentPoint.addClass("is-active");
									ComplaintCurrentStep.stop().css({
										"top":"",
										"display":""
									});	
									ComplaintCurrentAnimated.stop().css({
										"top":"",
										"display":""
									});
									ComplaintCurrentStep.addClass("is-active");
									ComplaintPrevAnimated.stop().css({
										"top":"",
										"display":""
									});	
									ComplaintPrevStep.removeClass("is-active");
								}
							);
						},
						100
					);
				} else {
					var ComplaintPrevStep = ComplaintBlock.find(".complaint-form-steps__step:eq("+(StepNumber-1)+")");
					var ComplaintPrevInput = ComplaintPrevStep.find(".complaint-form-steps-step__input");
					var ComplaintPrevLabel = ComplaintPrevStep.find(".complaint-form-steps-step__label");
					var ComplaintPrevAnimated = ComplaintPrevStep.find(".complaint-form-steps-step__animated");
					ComplaintCurrentStep.css({
						"top":$(document).height()*0.5
					});
					ComplaintCurrentStep.show();
					ComplaintResultsProcess.show();
					ComplaintPrevLabel.stop().animate({"top":-$(document).height()*0.5},"slow","easeInCubic");
					setTimeout(
						function(){
							ComplaintPrevInput.stop().animate({"top":-$(document).height()*0.5},"slow","easeInCubic");
							ComplaintResultsProcess.stop().animate({"top":-$(document).height()*0.5-ComplaintCurrentStepHeight},"slow","easeInCubic",
								function(){
									ComplaintCurrentPoints.removeClass("is-active");
									ComplaintCurrentPoint.addClass("is-active");
									ComplaintCurrentStep.stop().css({
										"top":"",
										"display":""
									});	
									ComplaintCurrentAnimated.stop().css({
										"top":"",
										"display":""
									});	
									ComplaintResultsProcess.stop().css({
										"top":""
									});	
									ComplaintCurrentStep.addClass("is-active");
									ComplaintPrevAnimated.stop().css({
										"top":"",
										"display":""
									});	
									ComplaintPrevStep.removeClass("is-active");
									ComplaintForm.submit();
								}
							);
						},
						100
					);
				}
			}
			
			this.clear = function(){
				ComplaintBlock.find(".complaint-form-steps-step__result").removeClass("is-active");
				ComplaintBlock.find(".complaint-form-steps-step__notis").hide();
				ComplaintBlock.find(".complaint-form-steps-step__input").val("");
				ComplaintBlock.find(".complaint-form-steps-step__animated").css({
					"top":"0",
					"display":""
				});
				ComplaintBlock.find(".complaint-form__button-block").show();
				ComplaintBlock.find(".complaint-form-steps-step__result").hide();
			}
			
			this.init = function(){
				this.clear();
				this.step(0);

				$.ajax({
			    	url: '/local/php_interface/captcha-code.php',
			    	type: 'post',
			    	data: 'captcha=yes',
			    	success: function(data){
			    		ComplaintBlock
			    			.find("input[name='captcha_check']")
			    			.val(data);
			    		ComplaintBlock
			    			.find("img.captcha-img")
			    			.attr("src", "/bitrix/tools/captcha.php?captcha_sid="+data);
			        }
			    });

				ComplaintBlock.show(500);
			}
			
			this.close = function(){
				ComplaintBlock.hide(500);
				this.clear();
				this.step(0);
			}
			
			this.next = function(){
				var ComplaintStep = ComplaintBlock.find(".complaint-form-steps__step:eq("+that.CurrentStep+")");
				var ComplaintInput = ComplaintStep.find(".complaint-form-steps-step__input");
				var LastStep = ComplaintBlock.find(".complaint-form-steps__step").length - 1;

				if(LastStep == that.CurrentStep) {
					that.close();
					return;
				}

				if(ComplaintInput.attr("name") == "text") {
					var message = ComplaintInput.val().trim();
					var messageLength = message.length;
					var isLetterConsist = /[a-zA-Zа-яА-Яё]+/gi.test(message);
					if(messageLength > 5 && isLetterConsist) {
						ComplaintStep.find(".complaint-form-steps-step__notis").hide();
						this.step(that.CurrentStep+1);
					} else {
						if(!isLetterConsist) {
							ComplaintStep.find(".complaint-form-steps-step__notis")
							.text("Сообщение должно содержать буквы. ");
						}
						if(messageLength <= 5) {
							ComplaintStep.find(".complaint-form-steps-step__notis")
							.text("Сообщение должно быть больше 5 символов. ");
						}
						ComplaintStep.find(".complaint-form-steps-step__notis").show();	
					}
				} else {
					if(
						!ComplaintInput.data("required") ||
						(
							ComplaintInput.val().trim() != ""  &&
							ComplaintInput.attr("name") != "text" &&
							(
								typeof ComplaintInput.data("pattern") === "undefined" ||
								ComplaintInput.data("pattern").test(ComplaintInput.val().trim())
							)
						)
					) {
						ComplaintStep.find(".complaint-form-steps-step__notis").hide();
						this.step(that.CurrentStep+1);
					} else {
						ComplaintStep.find(".complaint-form-steps-step__notis").show();
					}
				}

			}

			/**********************Events block**************************************/
			
			var that = this;
			
			ComplaintNextButton.click(
				function(){
					that.next();
				}
			);
			
			ComplaintCloseButton.click(
				function(){
					that.close();
				}
			);
			
			ComplaintInitButton.click(
				function(){
					that.init();
				}
			);
			
			ComplaintInput.keypress(
				function(e){
					if(e.which == 13||e.keyCode == 13){
						that.next();
					}
				}
			);
	
			ComplaintForm.ajaxForm({
				beforeSubmit : function(fields){
					$.each(fields, function(i, field){
						if(field.name == 'confirm'){
							field.value = 0;
						}
					});
					ComplaintResultsProcess.show();
					ComplaintResultsSuccess.hide();
					ComplaintResultsFail.hide();
				},
				success: function(response){
					ComplaintResultsProcess.hide();
					eval('response = ' + response + ';');
					if(response.ok == '1'){
						ComplaintResultsSuccess.show();
					}else{
						if(response.fields.length > 0) {
							for(var i = 0; i<response.fields.length; i++) {
								ComplaintResultsFail.append("<br/><span>"+response.fields[i].ru +"</span>");
							}
						}
						ComplaintResultsFail.show();
					}				
				},
				error: function(){
					ComplaintResultsProcess.hide();
					ComplaintResultsFail.show();
				}
			});
			
		}catch(e){
			
			console.log(e);	
			
		}
	};
	var Complaint = new ComplaintClass({
		"action": "complain-form",
		"initButton": $("a[href='#complaint']"),
		"title": "Написать жалобу",
		"fields": [
			{
				"name":"name",
				"type":"text",
				"required":true,
				"label":"Как вас зовут?",
				"notis":"Вы не сообщили, как к вам обращаться"
			},
			{
				"name":"contact",
				"type":"tel",
				"required":true,
				"label":"Как с вами связаться?",
				"notis":"Вы не сообщили, как с вами связаться"
			},
			{
				"name":"text",
				"type":"textarea",
				"required":true,
				"label":"Ваша жалоба",
				"notis":"Вы не сообщили, какая у вас жалоба"
			},
			{
				"name": "captcha",
				"type": "captcha",
				"required": true,
				"label": "Введите код",
				"notis": "Вы не ввели код" 
			}
		],
		"resilt":{
			"process":"Ваша заявка обрабатывается",
			"success":"Ваша заявка принята.<br><span>Мы рассмотрим её и свяжемся с вами в ближайшее время.</span>",
			"fail":"Произошел сбой.<br><span>Во время отправки произошел сбой. Попробуйте повторить отправку позже.</span>"
		}
	});
});