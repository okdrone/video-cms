// ### Video list
$('#title_btn').on('click', function () {
    if($(this).hasClass('up')){
        $(this).removeClass('up');
        $(this).addClass('down');
        $('#title_desc').show('slow');
    } else if ($(this).hasClass('down')) {
        $(this).removeClass('down');
        $(this).addClass('up');
        $('#title_desc').hide('slow');
    }
});
function clean_drop_menu(){
    $('#filter-box').find('li').each(function(){
        $(this).removeClass('on');
        $('#' + $(this).attr('toggle-menu')).hide();
    });
}
$('#filter-box').find('li').click(function(){
    if($(this).hasClass('on')){
        clean_drop_menu();
    } else {
        clean_drop_menu();
        $(this).addClass('on');
        var menu = $(this).attr('toggle-menu');
        $('#' + menu).show();
    }
});
$('.drop-menu').click(clean_drop_menu);
$('#ddm1').find('li').click(function(){
    var val = $(this).find('span:eq(0)').html();
    $('#filter-box').find('li[toggle-menu="ddm1"]').html(val);
    $('#filter-doctor').val(val)
    clean_drop_menu();
    refreshSearch();
});
$('#ddm2').find('li').click(function(){
    var val = $(this).find('span:eq(0)').html();
    $('#filter-box').find('li[toggle-menu="ddm2"]').html(val);
    $('#filter-disease').val(val)
    clean_drop_menu();
    refreshSearch();
});

// ### Search Box
$('#search_btn').click(function () {
    $('#search_box').show();
});
$('#search_cancel').click(function () {
    $('#search_box').hide();
});

function search(keyword) {
    window.location = '/video-cms/index.php?g=Video&m=Index&a=search&keyword=' + encodeURI(keyword);
}

function refreshSearch(){
    var doctor = $('#filter-doctor').val().trim();
    var disease = $('#filter-disease').val().trim();

    console.log(doctor);
    console.log(disease);
}

$('.search_word li a').click(function(){
    var keyword = $(this).html();
    console.log(keyword);
    if(keyword && keyword !== ''){
        search(keyword);
    }
});