$(document).ready(function(){
    var transType;
    var pattern;

    $('.open_menu_btn').click(function (e) {
        e.preventDefault();
        $(this).toggleClass('active');
        $('.navbar').stop().slideToggle();
    });

    /*Index Selects*/
    $('.select').click(function(e){
        e.stopPropagation();
    });

    $('.current').click(function (e) {
        e.preventDefault();
        if($(this).parent().hasClass("open"))
        {
            $(this).parent().removeClass('open');
        } else {
            $('.select').removeClass('open');
            $(this).parent().addClass('open');
        }

      	if($('#main .main_form .top .select.open ul').height() > 400) {
            $('#main .main_form .top .select.open ul').addClass('big');
        }
        if($('.section .catalog .cat_top .catalog_filter .select.open ul').height() > 400) {
          	$('.section .catalog .cat_top .catalog_filter .select.open ul').addClass('big');
        }
    });
    $('.select ul').click(function(e){
        e.stopPropagation();
    });

    function setParameterOnCurrent(obj, objSet)
    {
        if(objSet.innerText == 'Любой')
        {
            obj.html(objSet.getAttribute('value'));
        }else {
            obj.html(objSet.innerText);
        }
        if(objSet.value == '')
        {
            obj.parents('.select').find('.current').removeClass('active');
        }else {
            obj.parents('.select').find('.current').addClass('active');
        }
        $("[name = '" + obj.parent().attr('id') +"']").attr("value", ''+objSet.value);
    }

    function bindClickToOption(obj)
    {
        obj.click(function(e){
            e.preventDefault();
            setParameterOnCurrent($(this).parents('.select').find('.current'),
                                    $(this)[0]);
            var data = {};
            var parent = e.target.parentElement.parentElement;
            var id = parent.id;
            if(id == 'transType')
            {
                transType = e.target.value;
                data[id]  = transType;
            }
            if(id == 'wheelDiam') {
                 data['transType'] = transType;
                 data['wheelDiam'] = e.target.value;
            }

            if(id != "tyreSize")
            {
                tyreAjaxRequest('get', "/index.php", data,
                    function(resp)
                    {
                        if(typeof resp.wheelDiamList !== 'undefined' &&
                            id != 'wheelDiam')
                        {
                            var obj = {};
                            obj.innerText = "Выберите диаметр колеса";
                            obj.value = "";
                            obj.getAttribute = function(name){return this[name];};
                            $("#wheelDiam > ul").html(resp.wheelDiamList);
                            bindClickToOption($("#wheelDiam > ul .option"));
                            bindClickToCloseOptionList($("#wheelDiam > ul .option"));
                            setParameterOnCurrent($("#wheelDiam .current"),obj);
                        }
                        if(typeof resp.tyreSizeList !== 'undefined')
                        {
                            var obj = {};
                            obj.innerText = "Выберите размер шины";
                            obj.value = "";
                            obj.getAttribute = function(name){return this[name];};
                            $("#tyreSize > ul").html(resp.tyreSizeList);
                            bindClickToOption($("#tyreSize > ul .option"));
                            bindClickToCloseOptionList($("#tyreSize > ul .option"));
                            setParameterOnCurrent($("#tyreSize .current"),obj);
                        }
                    });
            }
        });
    }
    bindClickToOption($('.select ul .option'));

    $('.select>.list .option').click(function(){
        $('.select>.list .option').removeClass('selected');
        $(this).addClass('selected');
    });

    function bindClickToCloseOptionList(obj)
    {
        obj.click(function(){
            $('.select.open').removeClass('open');
        });
    }
    bindClickToCloseOptionList($('.select ul .option'));

    $('body').click(function(){
        $('.select.open').removeClass('open');
    });

    $('#sendEmail').on('submit', function (e) {
        e.preventDefault();
        var data = {};
        var formdata = $(this).serializeArray();
        data.email = formdata[0].value;
        data.file = 'catalog';
        sendMail(data, "sendEmail");
    });

    function loaderStart(obj)
    {
        obj.addClass('preloader');
        obj.attr("disabled", 1);
    }

    function loaderStop()
    {
        $('.preloader').removeAttr("disabled");
        $('.preloader').removeClass('preloader');
    }

    $('.pop_btn').click(function(){
        pattern = $(this).data('pattern');
    });

    $('#sendBrochureData').on('submit', function (e) {
        e.preventDefault();
        var data = {};
        var formdata = $(this).serializeArray();
        data.email = formdata[0].value;
        data.file = pattern;
        sendMail(data, "sendBrochure");
    });

    function sendMail(data, key)
    {
        if(validate(data.email))
        {
            loaderStart($("#"+key).find(':submit'));
            if(data.file == 'catalog')
            {
                yaCounter50646088.reachGoal('catalogue');
            }
            else {
                yaCounter50646088.reachGoal('brochure');
            }
            tyreAjaxRequest('post','/send-mail', data, function(resp)
            {
                loaderStop();
                if(resp === true)
                {
                    showPopup($("#" + key + "_thanks"));
                }
                else {
                    showPopup($("#" + key + "_error"));
                    console.log(resp);
                }
            });
        }
    }

    $('#callMe').on('submit', function (e) {
        e.preventDefault();
        var formdata = $(this).serializeArray();
        var send = true;
        console.log(formdata);
        formdata.forEach(function(params){
            switch (params.name) {
                case 'email':
                    if(!validate(params.value)) send = false;
                    break;
                case 'phone':
                    if(params.value  === "") {
                        showPopup($("#cantBeEmptyPhone"));
                        send = false;
                    }
                    break;
                default:
                    console.log("not know that parameter: " + params.name);
                    break;
            }
        });
        if(send)
        {
            loaderStart($('#callMe').find(':submit'));
            yaCounter50646088.reachGoal('request');
            tyreAjaxRequest('post','/call-me', formdata, function(resp){
                loaderStop();
                if(resp === true)
                {
                    showPopup($("#callMe_thanks"));
                }else {
                    showPopup($("#callMe_didNotWork"));
                    console.log(resp.message)
                }
            });
        }
    });

    function validate(email) {
        if(email === '' || email == null)
        {
            console.log(email);
            showPopup($('#cantBeEmptyEmail'));
            return false;
        }
        var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
        if(reg.test(email) == false) {
            showPopup($('#notValidEmail'));
            return false;
        }
        return true;
    }

    function showPopup(obj)
    {
        $.magnificPopup.open({
           items: {
             src: obj,
             type: 'inline',
             closeBtnInside: true
           }
         });
    }

    function tyreAjaxRequest(aType, aUrl, aData, aSuccess, aError, aCache, aTimeout)
    {
        if (aCache == null) {
            aCache = false;
        }
        if (aError == null) {
            aError = defaultErrorHandler;
        }
        if (aSuccess == null) {
            aSuccess = defaultSuccessHandler;
        }
        if (aTimeout == null) {
            aTimeout = 600000;
        }
        return $.ajax({
            type: aType,
            error: aError,
            url: aUrl,
            data: aData,
            success: aSuccess,
            cache: aCache,
            timeout: aTimeout
        });
    }

    function defaultErrorHandler(xhr) {
        if (xhr.status === 0) return;
        blink(xhr.responseText, 3);
    }

    function defaultSuccessHandler(data) {
        if (data.message) {
            blink(data.message, 3);
        } else {
            blink('success', 3);
        }
    }

    function collectParameters()
    {
        var params = {};
        params.transType = $("#transType > .current").attr('value');
        params.wheelDiam = $("#wheelDiam > .current").attr('value');
        params.tyreSize = $("#tyreSize > .current").attr('value');
        return params;
    }

    var blinkTimer = 0;
    blink = function(message, timeout, callback) {
        console.log(message);
        clearTimeout(blinkTimer);
        blinkTimer = setTimeout(function(){
            if (typeof callback == 'function') {
                callback();
            }
        }, timeout*1000);
    };

    /*Index Selects*/

    $('.pop_btn').magnificPopup({
        type: 'inline',
        overflowY: 'auto',
        closeBtnInside: true,
        preloader: false,
        midClick: true,
        removalDelay: 300,
        mainClass: 'my-mfp-zoom-in',
    });
    $('.current').click(function (e) {
        if($('#main .main_form .top .select.open ul').height() > 150) {
            $('#main .main_form .top .select.open ul').addClass('big');
        }
        if($('.section .catalog .cat_top .catalog_filter .select.open ul').height() > 150) {
            $('.section .catalog .cat_top .catalog_filter .select.open ul').addClass('big');
        }
    });
});

