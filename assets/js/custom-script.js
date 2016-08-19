
$('#item_type').on('change', function () {
    var type = $(this).val();
    var request = $.ajax({
        url: base_url.concat('/items/get_makes/' + type),
        cache: false
    });

});
$('.link').on('click', function () {
    var link = $(this).attr('id');
    window.location = base_url.concat("/" + link + ".html");
});


$('.item_profile').on('click', function () {
    var request = $(this).attr('id');
    window.location = base_url.concat('/procurements/profile/' + request + ".html");
});
$('.supplier').on('click', function () {
    var request = $(this).attr('id');
    window.location = base_url.concat('/suppliers/profile/' + request + ".html");
});
$('.po_link').on('click', function () {
    var request = $(this).attr('id');
    window.location = base_url.concat('/procurements/lpo/' + request + ".html");
});
$('.link_category').on('click', function () {
    var request = $(this).attr('id');
    window.location = base_url.concat('/items/category/' + request + ".html");
});
$('.request').on('click', function () {
    $(this).html("Requesting...");
    $(this).attr("disabled", 'disabled');
    var request = $.ajax({
        url: base_url.concat($(this).attr("id"))
    });
    request.done(function (msg) {
            if (msg == "success")
                $(this).html("Requested");
        }
    );

});

$('.ajax').on('click', function () {
    $(this).attr("disabled", "disabled");
    console.log(base_url.concat("/" + $(this).val()));
    var request = $.ajax({
        url: base_url.concat("/" + $(this).val())
    });
    request.done(function (msg) {
        Materialize.toast(msg, 7000);
        location.reload(true);
        //$("#content").load(location.href + " #content");
        //alert(msg);
        }
    );

});

$("#define_component").on('click', function () {
    var request = $.ajax({
        url: base_url.concat("/components/define/" + $('#component_name').val())
    });
    request.done(function (msg) {
            if (!parseInt(msg)) {
                alert(msg);
                return false;
            }
            window.location = base_url.concat("/components/profile/" + msg + ".html")
        }
    );
});


