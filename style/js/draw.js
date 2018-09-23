/**
 * Created by qiang.luis on 2017/2/4.
 */



var canvas;
var ctx;
var flag=1; //1正常，0交叉
// var glasscan ;
// var glassContext;

var images = [ // predefined array of used images
    'maptile/12/763/200.png',
    'images/pic2.jpg',
    'images/pic3.jpg',
    'images/pic4.jpg',
    'images/pic5.jpg',
    'images/pic6.jpg',
    'images/pic7.jpg',
    'images/pic8.jpg',
    'images/pic9.jpg',
    'images/pic10.jpg'
];
var iActiveImage = 0;
var updateOutput = function(e)
{
    var list   = e.length ? e : $(e.target),
        output = list.data('output');
    if (window.JSON) {
        output.val(window.JSON.stringify(list.nestable('serialize')));//, null, 2));
    } else {
        output.val('JSON browser support required for this demo.');
    }
};


function draw(){

    // drawing active image
    var url =$("#imgUrl").val();
    var tile = $("#tileid").val();
    var image = new Image();
    console.log(url);
    image.onload = function () {
        ctx.drawImage(image, 0, 0, image.width, image.height); // draw the image on the canvas
    }
    image.src = url;

    // creating canvas object
    canvas = document.getElementById('panel');
    // glasscan = document.getElementById("glasscan");
    ctx = canvas.getContext('2d');
    // glassContext = glasscan.getContext("2d");

    //设置字体样式
    ctx.font = "50px Courier New";
    //设置字体填充颜色
    ctx.fillStyle = "#337ab7";
    //从坐标点(50,50)开始绘制文字
    ctx.fillText(tile, 100, 150);

    $('#panel').mousemove(function(e) { // mouse move handler
        var canvasOffset = $(canvas).offset();
        var canvasX = Math.floor(e.pageX - canvasOffset.left);
        var canvasY = Math.floor(e.pageY - canvasOffset.top);
    });

    // $('#panel').mouseout(function(e) {
    //     // glasscan.style.display = "none";
    // });

    $('#panel').click(function(e) { // mouse click handler
        var canvasOffset = $(canvas).offset();
        var canvasX = Math.floor(e.pageX - canvasOffset.left);
        var canvasY = Math.floor(e.pageY - canvasOffset.top);

        var imageData = ctx.getImageData(canvasX, canvasY, 1, 1);
        var pixel = imageData.data;
        var r = pixel[0];
        var g = pixel[1];
        var b = pixel[2];
        var a = pixel[3] / 255
        a = Math.round(a * 100) / 100;
        var rHex = r.toString(16);
        r < 16 && (rHex = "0" + rHex);
        var gHex = g.toString(16);
        g < 16 && (gHex = "0" + gHex);
        var bHex = b.toString(16);
        b < 16 && (bHex = "0" + bHex);
        // var rgbaColor = "rgba(" + r + "," + g + "," + b + "," + a + ")";
        // var rgbColor = "rgb(" + r + "," + g + "," + b + ")";
        var hexColor = "#" + rHex + gHex + bHex;
        console.log(hexColor);
        if(hexColor=="#17bf00"||hexColor=="#f33131" || hexColor=="#ff9e19"||hexColor=="#ba0101"){
            ctx.fillStyle="#f70505";
            ctx.fillRect(canvasX,canvasY,4,4);
            ctx.save();

            ctx.font = "5px Courier New";
            //设置字体填充颜色
            ctx.fillStyle = "#337ab7";
            //从坐标点(50,50)开始绘制文字
            var font = "("+canvasX+","+canvasY+")";
            ctx.fillText(font, canvasX+3, canvasY+3);

            var dataId = canvasX+","+canvasY;
            var innerHtml = "<li class='dd-item'  data-id='"+dataId+"'><div class='dd-handle'>"+canvasX+","+canvasY+"</div><i class='js-remove' onclick='removePoint("+canvasX+","+canvasY+")'>✖</i></li>";
            if(flag==1){

                $("#points").append(innerHtml);
                updateOutput($('#nestable2').data('output', $('#nestable2-output')));
            }else{
                $("#jiaocha").append(innerHtml);
                updateOutput($('#nestable3').data('output', $('#nestable3-output')));
            }
        }else{
            $("#tip-input").val("请选取在道路上的点");
        }

    });
    updateOutput($('#nestable2').data('output', $('#nestable2-output')));
}

function jiaochaPoint(){
    rdochecked("chklist");
    if ($(".chkbox").prev().prop("checked") == true) {
        $(".chkbox").prev().removeAttr("checked");
        flag=1;
    }
    else {
        $(".chkbox").prev().prop("checked", "checked");
        flag=0;
    }
    rdochecked("chklist");
}



//判断是否选中
function rdochecked(tag) {
    $('.' + tag).each(function (i) {
        var rdobox = $('.' + tag).eq(i).next();
        if ($('.' + tag).eq(i).prop("checked") == false) {
            rdobox.removeClass("checked");
            rdobox.addClass("unchecked");
            rdobox.find(".check-image").css("background", "url('style/images/input-unchecked.png')");
        }
        else {
            rdobox.removeClass("unchecked");
            rdobox.addClass("checked");
            rdobox.find(".check-image").css("background", "url('style/images/input-checked.png')");
        }
    });
}


function convertCanvasToImage() {
    var image = new Image();
    image.src = canvas.toDataURL("image/jpg");
    return image.src;
}
function reset(){
    canvas.height = 256;
    $(".xcConfirm").toggle();
}



function savePoint(){
    var textare = $("#nestable2-output").val();

    if(textare =="[]" || ($("#jiaocha>li").length!=0&&$("#nestable3-output").val()=="[]")){
        $("#tip-input").val("与下方数据不一致，请单击任意要保存类型的像素坐标按钮或者刷新");
        // alert("请单击上方要保存类型的像素按钮");
        return;
    }
    $("input[name='img']").val(convertCanvasToImage());
    var form_id ='#cluster-points';
    $.ajax({
        cache: false,
        type: "POST",
        url:"drawroute/savePoints",
        data:$(form_id).serialize(),// 你的formid
        async: true,
        error: function(request) {
            alert(request);
        },
        success: function(data) {
             alert(data);
            // canvas.height = 256;
            // $("#points").html('');
            // $(".xcConfirm").toggle();
            // var lng = $('input[name="lng"]').val();
            // var lat = $('input[name="lat"]').val();
            // newMap(lng,lat);
            window.location.reload();
        }
    });
}

function saveCurrent() {
    var roadid = $('input[name="roadId"]').val();
    $.ajax({
        cache: false,
        type: "GET",
        url:"userlogin/signout?roadid="+roadid,
        async: true,
        error: function(request) {
            alert(request);
        },
        success: function(data) {
            href="userlogin/login";
            window.location.href = href;
        }
    });
}

function saveRoad(){
    var form_id ='#road';
    $.ajax({
        cache: false,
        type: "POST",
        url:"drawroute/saveRoad",
        data:$(form_id).serialize(),// 你的formid
        async: true,
        error: function(request) {
            alert(request);
        },
        success: function(data) {
             alert(data);
            window.location.reload();
        }
    });
}



function removePoint(x,y){

    var e = window.event;
    //获取元素
    obj = e.target || e.srcElement;
    var a = $(obj).parent().remove();
    ctx.fillStyle="#17bf00";
    ctx.fillRect(x,y,4,4);
    if(flag==1){
        updateOutput($('#nestable2').data('output', $('#nestable2-output')));
    }else{

        updateOutput($('#nestable3').data('output', $('#nestable3-output')));
    }
}

