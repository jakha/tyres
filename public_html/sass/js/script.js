$(document).ready(function() {

    $('.open_menu_btn').click(function (e) {
        e.preventDefault();
        $(this).toggleClass('active');
        $('.navbar').toggleClass('open');
    });

    /*Index Selects*/
    $('.select').click(function(e){
        e.stopPropagation();
    });
    $('.current').click(function (e) {
        e.preventDefault();
        $(this).parents('.select').toggleClass('open');
    });
    $('.select ul').click(function(e){
        e.stopPropagation();
    });
    $('.select ul .option').click(function(e){
        e.preventDefault();
        $(this).parents('.select').find('.current').html($(this).html());
    });

    $('.select>.list .option').click(function(){
        $('.select>.list .option').removeClass('selected');
        $(this).addClass('selected');
    });

    $('body').click(function(){
        $('.select.open').removeClass('open');
    });
    $('.select ul .option').click(function(){
        $('.select.open').removeClass('open');
    });
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

});