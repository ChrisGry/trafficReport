/**
 * Created by qiang.luis on 2017/1/10.
 */
jQuery(document).ready(function($){
    var cabinIndex = 1;
    //setInterval("newMap()",5000);//1000为1秒钟
});

function newMap(lng,lat){
    var mapOptions = {
        //minZoom: 12, 地图最小层级
        mapType: BMAP_NORMAL_MAP
    }
    var map = new BMap.Map("container", mapOptions);      //设置卫星图为底图BMAP_PERSPECTIVE_MAP

    // var initPoint = new BMap.Point(112.564739,28.331691);    // 创建点坐标
    var initPoint = new BMap.Point(lng,lat);    // 创建点坐标
    var zoomlevel =17;


    map.centerAndZoom(initPoint,zoomlevel);                 // 初始化地图,设置中心点坐标和地图级别。
  //  map.disableScrollWheelZoom();                   // 启用滚轮放大缩小。
    map.enableKeyboard();                           // 启用键盘操作。
  //  map.disableContinuousZoom();										//启用连续缩放效果

 //   var b = new BMap.Bounds(new BMap.Point(112.276167,27.000359),new BMap.Point(113.108538,29.546152));
    var b = new BMap.Bounds(new BMap.Point(112.276167,27.000359),new BMap.Point(113.108538,29.546152));
    try {    // js中尽然还有try catch方法，可以避免bug引起的错误
        BMapLib.AreaRestriction.setBounds(map, b); // 已map为中心，已b为范围的地图
    } catch (e) {
        // 捕获错误异常
        alert(e);
    }
    // ----- control -----
    map.addControl(new BMap.NavigationControl()); //地图平移缩放控件
    map.addControl(new BMap.ScaleControl()); //显示比例尺在右下角
    //map.addControl(new BMap.OverviewMapControl({anchor: BMAP_ANCHOR_TOP_RIGHT, isOpen: true})); //缩略图控件

    // ----- menu -----
    var contextMenu = new BMap.ContextMenu();
    var txtMenuItem = [
        {
            text:'添加坐标点',
            callback:function(p){
                addCabinMarker(p);
            }
        }
    ];
    for(var i=0; i < txtMenuItem.length; i++){
        contextMenu.addItem(new BMap.MenuItem(txtMenuItem[i].text,txtMenuItem[i].callback,100));
        if(i==0) {
            contextMenu.addSeparator();
        }
    }
    map.addContextMenu(contextMenu);
    // ----- maker -----
    //addCabinMarker(initPoint);
    var cabinContent = "<h4 style='margin:0 0 5px 0;padding:0.2em 0'>当前坐标点</h4>" +
        "<p style='margin:0;line-height:1.5;font-size:13px;text-indent:2em'></p>" +
        "</div>";
    var cabinInfoWindow = new BMap.InfoWindow(cabinContent);
    var cabinIcon = new BMap.Icon("style/images/cabin.png", new BMap.Size(32, 37));
    var cabinMarkerOptions = {
        icon: cabinIcon,
        enableDragging: true,
        draggingCursor: "move",
        title: "坐标点"
    }
    var cabinMarker = new BMap.Marker(initPoint, cabinMarkerOptions);
    cabinMarker.setAnimation(BMAP_ANIMATION_DROP);

    map.addOverlay(cabinMarker);
    var str = $('#container');
    html2canvas([str.get(0)], {
        onrendered: function (chcanvas) {
            var html_canvas = chcanvas.toDataURL();

        }
    });

    //为标注添加点击事件——弹出信息窗口
    cabinMarker.addEventListener("click", function(e){
        this.openInfoWindow(cabinInfoWindow);
        //图片加载完毕重绘infowindow
        document.getElementById('simulatorImg').onload = function (){
            infoWindow.redraw();   //防止在网速较慢，图片未加载时，生成的信息框高度比图片的总高度小，导致图片部分被隐藏
        }
    });

    cabinMarker.addEventListener("dragging", function(e) {
        document.getElementById("position").innerHTML = "像素—x:" + e.pixel.x + "y:" + e.pixel.y +
            "<br>经纬度—经度:" + e.point.lng + "纬度:" + e.point.lat;
        // var canvasOffset = $(canvas).offset();
        // var canvasX = Math.floor(e.pageX - canvasOffset.left);
        // var canvasY = Math.floor(e.pageY - canvasOffset.top);
        //
        // var imageData = ctx.getImageData(canvasX, canvasY, 1, 1);
        // var pixel = imageData.data;
        // console.log(pixel);


        // $.ajax({
        //     cache: true,
        //     type: "POST",
        //     url:"<?php echo site_url('Findtiles/checkcolor');?>",
        //     data:data,// 你的formid
        //     async: false,
        //     error: function(request) {
        //         alert("Connection error");
        //     },
        //     success: function(data) {
        //         console.log(data);
        //         var json = eval("("+data+")");
        //         if(json.ret==1){
        //             console.log(json.msg);
        //             location.reload();
        //         }else{
        //             console.log("download failed");
        //         }
        //     }
        // });
    });




}

function convertImageToCanvas(image) {
    var canvas = document.createElement("canvas");
    canvas.width = image.width;
    canvas.height = image.height;
    canvas.getContext("2d").drawImage(image, 0, 0);

    return canvas;
}

function addCabinMarker(point) {

}




function loading(percent){
    $('.progress span').animate({width:percent},1000,function(){
        $(this).children().html(percent);
        if(percent=='100%'){
            $('#downloadTile').val("下载完成");
            $(this).children().html('下载完成');
        }
    })
}