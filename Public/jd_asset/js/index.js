// 首页大图滚动
var pic_height = $('.photo img').height();// 获取大图高度
window.setTimeout(function(){
	$('.photo').animate({'margin-top':-pic_height},1000,
		function(){
			$(this).remove();
			$('.adver').show();
		});
},2300);

//轮播部分
var img_width = $('.imglist ul li a img').width(); // 获取图片尺寸
var img_count = $('.imglist ul li').size(); // 获取轮播图片个数
var auto_index = 0; //获取图片索引
var Timer = null;  //定时器

//轮播默认显示第一张
$('.imglist ul li').first().show();
$('.pic ul li').first().show();

// 动态添加焦点按钮
for(var i=0;i<img_count;i++){
	$('.focus span').first().addClass('active'); //默认第一个焦点为active
	$('.focus').append('<span></span>');
}


// 图片轮播
function autoPlay(){
	Timer = setInterval(function(){
		auto_index++;
		if(auto_index >= img_count){
			auto_index = 0;
		};
		$('.imglist ul li').eq(auto_index).stop().fadeIn('slow').siblings().fadeOut();
		$('.focus span').eq(auto_index).addClass('active').siblings().removeClass('active');
	},5000);
}
autoPlay();

// 暂停轮播
$('.scroll').hover(  //鼠标滑动到焦点盒子上时暂停轮播
	function(){
		clearInterval(Timer);
	},
	function(){
		autoPlay();
	}
)

// 焦点触发滚动
$('.focus span').click(function(){
	auto_index = $(this).index();
	$(this).addClass('active').siblings().removeClass('active');
	$('.imglist ul li').stop().eq(auto_index).stop().fadeIn().siblings().fadeOut();
});

// 左右按钮切换
$('.left img').click(function(){
	auto_index--;
	if(auto_index < 0){
		auto_index = img_count - 1;
	}
	$('.imglist ul li').stop().eq(auto_index).stop().fadeIn().siblings().fadeOut();
	$('.focus span').eq(auto_index).addClass('active').siblings().removeClass('active');
});
$('.right').click(function(){
	auto_index++;
	if(auto_index>img_count-1){
		auto_index = 0;
	}
	$('.focus span').eq(auto_index).addClass('active').siblings().removeClass('active');
	$('.imglist ul li').eq(auto_index).stop().fadeIn().siblings().fadeOut();
});

// 图片新闻轮播
var new_count = $('.pic ul li').size(); //获取图片个数

// 动态焦点按钮
for(var i=0;i<new_count;i++){
	$('.circle span').first().addClass('cir_active'); //默认第一个焦点为active
	$('.circle').append('<span></span>');
}

//焦点触发切换
var cir_index = 0;
$('.circle span').click(function(){
	cir_index = $(this).index();
	$(this).addClass('cir_active').siblings().removeClass('cir_active');
	$('.pic ul li').eq(cir_index).stop().fadeIn().siblings().fadeOut();
});

//新闻轮播
var newsTimer = null;
function newsPlay(){
	newsTimer = setInterval(function(){
		cir_index++;
		if(cir_index >= new_count){
			cir_index = 0;
		};
		$('.pic ul li').eq(cir_index).stop().fadeIn('slow').siblings().fadeOut();
		$('.circle span').eq(cir_index).addClass('cir_active').siblings().removeClass('cir_active');
	},5000);
}
newsPlay();

//暂停轮播
$('.pic').hover(  //鼠标滑动到焦点盒子上时暂停轮播
	function(){
		clearInterval(newsTimer);
	},
	function(){
		newsPlay();
	}
)


//通知公告
var noticeHeight = $('.notice ul li').height();
var noticeCount = $('.notice ul li').size();
var noticeTimer = null; // 定义定时器
var noticeIndex = 0;
//通知公告滚动
function noticePlay(){
	noticeTimer = setInterval(function(){
		noticeIndex++;
		if(noticeIndex == noticeCount-5){
			noticeIndex = 0;
		}
		$('.notice ul').animate({'margin-top':-noticeHeight*noticeIndex},500);
	},3000);
}
noticePlay();
//暂停滚动
$('.notice ul li').hover(
	function(){
		clearInterval(noticeTimer);
	},
	function(){
		noticePlay();
	}
)
$(function () {
    // 无缝滚动
    var $linkUl = $('.imgLink ul');
    var link_count;
    var link_width;
    link_count = $('.imgLink ul li').size();
    link_width = $('.imgLink ul li').width()+30;
    var totalWidth = 0;
    $('.imgLink ul li').each(function (index, item) {
        var $el = $(item);
        $el.find('img').load(function () {
            totalWidth += item.offsetWidth + 31;
            if(index == link_count - 1)
                $linkUl.css('width', totalWidth);
        });
    })

    var link_index = 0;
    window.scrollTimer = null;
    var $linkUL = $('.imgLink ul');
    window.scroll = function() {
        scrollTimer = setInterval(function(){
            link_index++;
            if(link_index == link_count){
                link_index = 0;
            }
            $linkUL.animate({'margin-left': -($linkUL.children().first().width() + 30)},500, function () {
                var $first_img = $('.imgLink ul li').first();
                $first_img.remove();
                $linkUL.append($first_img);
                $linkUL.css('margin-left', 0);
            });
        },3000);
    };
    // scroll();
})


//暂停滚动
$('.imgLink').hover(
	function(){
		clearInterval(scrollTimer);
	},
	function(){
		scroll();
	}
);

//漂浮窗口漂浮
$('.adver button').click(function(){
	$('.adver').fadeOut();
});
function addEvent(obj,evtType,func,cap){
    cap=cap||false;
    if(obj.addEventListener){
        obj.addEventListener(evtType,func,cap);
        return true;
    }else if(obj.attachEvent){
        if(cap){
            obj.setCapture();
            return true;
        }else{
            return obj.attachEvent("on" + evtType,func);
        }
    }else{
        return false;
    }
}
function getPageScroll(){
    var xScroll,yScroll;
    if (self.pageXOffset) {
        xScroll = self.pageXOffset;
    } else if (document.documentElement && document.documentElement.scrollLeft){
        xScroll = document.documentElement.scrollLeft;
    } else if (document.body) {
        xScroll = document.body.scrollLeft;
    }
    if (self.pageYOffset) {
        yScroll = self.pageYOffset;
    } else if (document.documentElement && document.documentElement.scrollTop){
        yScroll = document.documentElement.scrollTop;
    } else if (document.body) {
        yScroll = document.body.scrollTop;
    }
    arrayPageScroll = new Array(xScroll,yScroll);
    return arrayPageScroll;
}
function GetPageSize(){
    var xScroll, yScroll;
    if (window.innerHeight && window.scrollMaxY) {
        xScroll = document.body.scrollWidth;
        yScroll = window.innerHeight + window.scrollMaxY;
    } else if (document.body.scrollHeight > document.body.offsetHeight){
        xScroll = document.body.scrollWidth;
        yScroll = document.body.scrollHeight;
    } else {
        xScroll = document.body.offsetWidth;
        yScroll = document.body.offsetHeight;
    }
    var windowWidth, windowHeight;
    if (self.innerHeight) {
        windowWidth = self.innerWidth;
        windowHeight = self.innerHeight;
    } else if (document.documentElement && document.documentElement.clientHeight) {
        windowWidth = document.documentElement.clientWidth;
        windowHeight = document.documentElement.clientHeight;
    } else if (document.body) {
        windowWidth = document.body.clientWidth;
        windowHeight = document.body.clientHeight;
    }
    if(yScroll < windowHeight){
        pageHeight = windowHeight;
    } else {
        pageHeight = yScroll;
    }
    if(xScroll < windowWidth){
        pageWidth = windowWidth;
    } else {
        pageWidth = xScroll;
    }
    arrayPageSize = new Array(pageWidth,pageHeight,windowWidth,windowHeight)
    return arrayPageSize;
}

var AdMoveConfig=new Object();
AdMoveConfig.IsInitialized=false;
AdMoveConfig.ScrollX=0;
AdMoveConfig.ScrollY=0;
AdMoveConfig.MoveWidth=0;
AdMoveConfig.MoveHeight=0;
AdMoveConfig.Resize=function(){
    var winsize=GetPageSize();
    AdMoveConfig.MoveWidth=winsize[2];
    AdMoveConfig.MoveHeight=winsize[3];
    AdMoveConfig.Scroll();
}
AdMoveConfig.Scroll=function(){
    var winscroll=getPageScroll();
    AdMoveConfig.ScrollX=winscroll[0];
    AdMoveConfig.ScrollY=winscroll[1];
}
addEvent(window,"resize",AdMoveConfig.Resize);
addEvent(window,"scroll",AdMoveConfig.Scroll);
function AdMove(id){
    if(!AdMoveConfig.IsInitialized){
        AdMoveConfig.Resize();
        AdMoveConfig.IsInitialized=true;
    }
    var obj=document.getElementById(id);
    var W=AdMoveConfig.MoveWidth-obj.offsetWidth;
    var H=AdMoveConfig.MoveHeight-obj.offsetHeight;
    var x = W*Math.random(),y = H*Math.random();
    var rad=(Math.random()+1)*Math.PI/6;
    var kx=Math.sin(rad),ky=Math.cos(rad);
    var dirx = (Math.random()<0.5?1:-1), diry = (Math.random()<0.5?1:-1);
    var step = 1;
    var interval;
    this.SetLocation=function(vx,vy){x=vx;y=vy;}
    this.SetDirection=function(vx,vy){dirx=vx;diry=vy;}
    obj.CustomMethod=function(){
        obj.style.left = (x + AdMoveConfig.ScrollX) + "px";
        obj.style.top = (y + AdMoveConfig.ScrollY) + "px";
        rad=(Math.random()+1)*Math.PI/6;
        W=AdMoveConfig.MoveWidth-obj.offsetWidth;
        H=AdMoveConfig.MoveHeight-obj.offsetHeight;
        x = x + step*kx*dirx;
        if (x < 0){dirx = 1;x = 0;kx=Math.sin(rad);ky=Math.cos(rad);}
        if (x > W){dirx = -1;x = W;kx=Math.sin(rad);ky=Math.cos(rad);}
        y = y + step*ky*diry;
        if (y < 0){diry = 1;y = 0;kx=Math.sin(rad);ky=Math.cos(rad);}
        if (y > H){diry = -1;y = H;kx=Math.sin(rad);ky=Math.cos(rad);}
    }
    this.Run=function(){
        var delay = 10;
        interval=setInterval(obj.CustomMethod,delay);
        obj.onmouseover=function(){clearInterval(interval);}
        obj.onmouseout=function(){interval=setInterval(obj.CustomMethod, delay);}
    }
}
var ad1=new AdMove("ad1");
ad1.Run();
$("#ad1").click(function () {
    $(this).remove();
})
