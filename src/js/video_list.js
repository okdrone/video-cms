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
    var doctor = $('#filter-doctor').val();
    var disease = $('#filter-disease').val();

    console.log(doctor);
    console.log(disease);

    $.getJSON('/video-cms/index.php?g=Video&m=Index&a=search_list&doctor=' + doctor + '&disease=' + disease,function (data, status) {
        cleanSearchList();
        if(data !== undefined && data.code === 0){
            var list_html = '';
            $.each(data.data, function (i, d) {
                list_html += '<div class="list-item">' +
                    '<div class="img"><a href="/video-cms/index.php?g=Video&m=Index&a=video&id=' + d.id + '">' +
                    (d.smeta.thumb === "" ? '<img src="/video-cms/public/images/no-image-box.png" />' : '<img src="/video-cms/data/upload/' + d.smeta.thumb + '" />') +
                    '</a> <div class="tag">' + d.video_time + '</div></div>' +
                '<div class="title"><a href="/video-cms/index.php?g=Video&m=Index&a=video&id=' + d.id + '">' + d.title + '</a></div>' +
                '<div class="info">' + d.hospital + '，' + d.doctor_name + '，' + d.disease_name + '</div></div>';
            });
            $('#search-list').html(list_html);
        }
    });
}

function cleanSearchList(){
    $('#search-list').html('');
}

$('.search_word li a').click(function(){
    var keyword = $(this).html();
    console.log(keyword);
    if(keyword && keyword !== ''){
        search(keyword);
    }
});