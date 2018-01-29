(function (window, document, $, undefined){
"use strict";
var H=$("html"),
W=$(window),
D=$(document),
F=$.fancybox=function (){
F.open.apply(this, arguments);
},
IE=navigator.userAgent.match(/msie/i),
didUpdate=null,
isTouch=document.createTouch!==undefined,
isQuery=function(obj){
return obj&&obj.hasOwnProperty&&obj instanceof $;
},
isString=function(str){
return str&&$.type(str)==="string";
},
isPercentage=function(str){
return isString(str)&&str.indexOf('%') > 0;
},
isScrollable=function(el){
return (el&&!(el.style.overflow&&el.style.overflow==='hidden')&&((el.clientWidth&&el.scrollWidth > el.clientWidth)||(el.clientHeight&&el.scrollHeight > el.clientHeight)));
},
getScalar=function(orig, dim){
var value=parseInt(orig, 10)||0;
if(dim&&isPercentage(orig)){
value=F.getViewport()[ dim ] / 100 * value;
}
return Math.ceil(value);
},
getValue=function(value, dim){
return getScalar(value, dim) + 'px';
};
$.extend(F, {
version: '2.1.5',
defaults: {
padding:15,
margin:20,
width:800,
height:600,
minWidth:100,
minHeight:100,
maxWidth:9999,
maxHeight:9999,
pixelRatio: 1,
autoSize:true,
autoHeight:false,
autoWidth:false,
autoResize:true,
autoCenter:!isTouch,
fitToView:true,
aspectRatio:false,
topRatio:0.5,
leftRatio:0.5,
scrolling:'auto', // 'auto', 'yes' or 'no'
wrapCSS:'',
arrows:true,
closeBtn:true,
closeClick:false,
nextClick:false,
mouseWheel:true,
autoPlay:false,
playSpeed:3000,
preload:3,
modal:false,
loop:true,
ajax:{
dataType:'html',
headers:{ 'X-fancyBox': true }},
iframe:{
scrolling:'auto',
preload:true
},
swf:{
wmode: 'transparent',
allowfullscreen:'true',
allowscriptaccess:'always'
},
keys:{
next:{
13:'left',
34:'up',
39:'left',
40:'up'
},
prev:{
8:'right',
33:'down',
37:'right',
38:'down'
},
close:[27],
play:[32],
toggle:[70]
},
direction:{
next:'left',
prev:'right'
},
scrollOutside:true,
index:0,
type:null,
href:null,
content:null,
title:null,
tpl: {
wrap:'<div class="fancybox-wrap" tabIndex="-1"><div class="fancybox-skin"><div class="fancybox-outer"><div class="fancybox-inner"></div></div></div></div>',
image:'<img class="fancybox-image" src="{href}" alt="" />',
iframe:'<iframe id="fancybox-frame{rnd}" name="fancybox-frame{rnd}" class="fancybox-iframe" frameborder="0" vspace="0" hspace="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen' + (IE ? ' allowtransparency="true"':'') + '></iframe>',
error:'<p class="fancybox-error">The requested content cannot be loaded.<br/>Please try again later.</p>',
closeBtn:'<a title="Close" class="fancybox-item fancybox-close" href="javascript:;"></a>',
next:'<a title="Next" class="fancybox-nav fancybox-next" href="javascript:;"><span></span></a>',
prev:'<a title="Previous" class="fancybox-nav fancybox-prev" href="javascript:;"><span></span></a>'
},
openEffect:'fade', // 'elastic', 'fade' or 'none'
openSpeed:250,
openEasing:'swing',
openOpacity:true,
openMethod:'zoomIn',
closeEffect:'fade', // 'elastic', 'fade' or 'none'
closeSpeed:250,
closeEasing:'swing',
closeOpacity:true,
closeMethod:'zoomOut',
nextEffect:'elastic', // 'elastic', 'fade' or 'none'
nextSpeed:250,
nextEasing:'swing',
nextMethod:'changeIn',
prevEffect:'elastic', // 'elastic', 'fade' or 'none'
prevSpeed:250,
prevEasing:'swing',
prevMethod:'changeOut',
helpers:{
overlay:true,
title:true
},
onCancel:$.noop,
beforeLoad:$.noop,
afterLoad:$.noop,
beforeShow:$.noop,
afterShow:$.noop,
beforeChange:$.noop,
beforeClose:$.noop,
afterClose:$.noop
},
group:{},
opts:{},
previous:null,
coming:null,
current:null,
isActive:false,
isOpen:false,
isOpened:false,
wrap:null,
skin:null,
outer:null,
inner:null,
player:{
timer:null,
isActive:false
},
ajaxLoad:null,
imgPreload:null,
transitions:{},
helpers:{},
open: function (group, opts){
if(!group){
return;
}
if(!$.isPlainObject(opts)){
opts={};}
if(false===F.close(true)){
return;
}
if(!$.isArray(group)){
group=isQuery(group) ? $(group).get():[group];
}
$.each(group, function(i, element){
var obj={},
href,
title,
content,
type,
rez,
hrefParts,
selector;
if($.type(element)==="object"){
if(element.nodeType){
element=$(element);
}
if(isQuery(element)){
obj={
href:element.data('fancybox-href')||element.attr('href'),
title:element.data('fancybox-title')||element.attr('title'),
isDom:true,
element:element
};
if($.metadata){
$.extend(true, obj, element.metadata());
}}else{
obj=element;
}}
href=opts.href||obj.href||(isString(element) ? element:null);
title=opts.title!==undefined ? opts.title:obj.title||'';
content=opts.content||obj.content;
type=content ? 'html':(opts.type||obj.type);
if(!type&&obj.isDom){
type=element.data('fancybox-type');
if(!type){
rez=element.prop('class').match(/fancybox\.(\w+)/);
type=rez ? rez[1]:null;
}}
if(isString(href)){
if(!type){
if(F.isImage(href)){
type='image';
}else if(F.isSWF(href)){
type='swf';
}else if(href.charAt(0)==='#'){
type='inline';
}else if(isString(element)){
type='html';
content=element;
}}
if(type==='ajax'){
hrefParts=href.split(/\s+/, 2);
href=hrefParts.shift();
selector=hrefParts.shift();
}}
if(!content){
if(type==='inline'){
if(href){
content=$(isString(href) ? href.replace(/.*(?=#[^\s]+$)/, ''):href);
}else if(obj.isDom){
content=element;
}}else if(type==='html'){
content=href;
}else if(!type&&!href&&obj.isDom){
type='inline';
content=element;
}}
$.extend(obj, {
href:href,
type:type,
content:content,
title:title,
selector:selector
});
group[ i ]=obj;
});
F.opts=$.extend(true, {}, F.defaults, opts);
if(opts.keys!==undefined){
F.opts.keys=opts.keys ? $.extend({}, F.defaults.keys, opts.keys):false;
}
F.group=group;
return F._start(F.opts.index);
},
cancel: function (){
var coming=F.coming;
if(!coming||false===F.trigger('onCancel')){
return;
}
F.hideLoading();
if(F.ajaxLoad){
F.ajaxLoad.abort();
}
F.ajaxLoad=null;
if(F.imgPreload){
F.imgPreload.onload=F.imgPreload.onerror=null;
}
if(coming.wrap){
coming.wrap.stop(true, true).trigger('onReset').remove();
}
F.coming=null;
if(!F.current){
F._afterZoomOut(coming);
}},
close: function (event){
F.cancel();
if(false===F.trigger('beforeClose')){
return;
}
F.unbindEvents();
if(!F.isActive){
return;
}
if(!F.isOpen||event===true){
$('.fancybox-wrap').stop(true).trigger('onReset').remove();
F._afterZoomOut();
}else{
F.isOpen=F.isOpened=false;
F.isClosing=true;
$('.fancybox-item, .fancybox-nav').remove();
F.wrap.stop(true, true).removeClass('fancybox-opened');
F.transitions[ F.current.closeMethod ]();
}},
play: function(action){
var clear=function (){
clearTimeout(F.player.timer);
},
set=function (){
clear();
if(F.current&&F.player.isActive){
F.player.timer=setTimeout(F.next, F.current.playSpeed);
}},
stop=function (){
clear();
D.unbind('.player');
F.player.isActive=false;
F.trigger('onPlayEnd');
},
start=function (){
if(F.current&&(F.current.loop||F.current.index < F.group.length - 1)){
F.player.isActive=true;
D.bind({
'onCancel.player beforeClose.player':stop,
'onUpdate.player':set,
'beforeLoad.player':clear
});
set();
F.trigger('onPlayStart');
}};
if(action===true||(!F.player.isActive&&action!==false)){
start();
}else{
stop();
}},
next: function(direction){
var current=F.current;
if(current){
if(!isString(direction)){
direction=current.direction.next;
}
F.jumpto(current.index + 1, direction, 'next');
}},
prev: function(direction){
var current=F.current;
if(current){
if(!isString(direction)){
direction=current.direction.prev;
}
F.jumpto(current.index - 1, direction, 'prev');
}},
jumpto: function(index, direction, router){
var current=F.current;
if(!current){
return;
}
index=getScalar(index);
F.direction=direction||current.direction[ (index >=current.index ? 'next':'prev') ];
F.router=router||'jumpto';
if(current.loop){
if(index < 0){
index=current.group.length + (index % current.group.length);
}
index=index % current.group.length;
}
if(current.group[ index ]!==undefined){
F.cancel();
F._start(index);
}},
reposition: function (e, onlyAbsolute){
var current=F.current,
wrap=current ? current.wrap:null,
pos;
if(wrap){
pos=F._getPosition(onlyAbsolute);
if(e&&e.type==='scroll'){
delete pos.position;
wrap.stop(true, true).animate(pos, 200);
}else{
wrap.css(pos);
current.pos=$.extend({}, current.dim, pos);
}}
},
update: function (e){
var type=(e&&e.type),
anyway = !type||type==='orientationchange';
if(anyway){
clearTimeout(didUpdate);
didUpdate=null;
}
if(!F.isOpen||didUpdate){
return;
}
didUpdate=setTimeout(function(){
var current=F.current;
if(!current||F.isClosing){
return;
}
F.wrap.removeClass('fancybox-tmp');
if(anyway||type==='load'||(type==='resize'&&current.autoResize)){
F._setDimension();
}
if(!(type==='scroll'&&current.canShrink)){
F.reposition(e);
}
F.trigger('onUpdate');
didUpdate=null;
}, (anyway&&!isTouch ? 0:300));
},
toggle: function(action){
if(F.isOpen){
F.current.fitToView=$.type(action)==="boolean" ? action:!F.current.fitToView;
if(isTouch){
F.wrap.removeAttr('style').addClass('fancybox-tmp');
F.trigger('onUpdate');
}
F.update();
}},
hideLoading: function (){
D.unbind('.loading');
$('#fancybox-loading').remove();
},
showLoading: function (){
var el, viewport;
F.hideLoading();
el=$('<div id="fancybox-loading"><div></div></div>').click(F.cancel).appendTo('body');
D.bind('keydown.loading', function(e){
if((e.which||e.keyCode)===27){
e.preventDefault();
F.cancel();
}});
if(!F.defaults.fixed){
viewport=F.getViewport();
el.css({
position:'absolute',
top:(viewport.h * 0.5) + viewport.y,
left:(viewport.w * 0.5) + viewport.x
});
}},
getViewport: function (){
var locked=(F.current&&F.current.locked)||false,
rez={
x: W.scrollLeft(),
y: W.scrollTop()
};
if(locked){
rez.w=locked[0].clientWidth;
rez.h=locked[0].clientHeight;
}else{
rez.w=isTouch&&window.innerWidth  ? window.innerWidth:W.width();
rez.h=isTouch&&window.innerHeight ? window.innerHeight:W.height();
}
return rez;
},
unbindEvents: function (){
if(F.wrap&&isQuery(F.wrap)){
F.wrap.unbind('.fb');
}
D.unbind('.fb');
W.unbind('.fb');
},
bindEvents: function (){
var current=F.current,
keys;
if(!current){
return;
}
W.bind('orientationchange.fb' + (isTouch ? '':' resize.fb') + (current.autoCenter&&!current.locked ? ' scroll.fb':''), F.update);
keys=current.keys;
if(keys){
D.bind('keydown.fb', function (e){
var code=e.which||e.keyCode,
target=e.target||e.srcElement;
if(code===27&&F.coming){
return false;
}
if(!e.ctrlKey&&!e.altKey&&!e.shiftKey&&!e.metaKey&&!(target&&(target.type||$(target).is('[contenteditable]')))){
$.each(keys, function(i, val){
if(current.group.length > 1&&val[ code ]!==undefined){
F[ i ](val[ code ]);
e.preventDefault();
return false;
}
if($.inArray(code, val) > -1){
F[ i ] ();
e.preventDefault();
return false;
}});
}});
}
if($.fn.mousewheel&&current.mouseWheel){
F.wrap.bind('mousewheel.fb', function (e, delta, deltaX, deltaY){
var target=e.target||null,
parent=$(target),
canScroll=false;
while (parent.length){
if(canScroll||parent.is('.fancybox-skin')||parent.is('.fancybox-wrap')){
break;
}
canScroll=isScrollable(parent[0]);
parent=$(parent).parent();
}
if(delta!==0&&!canScroll){
if(F.group.length > 1&&!current.canShrink){
if(deltaY > 0||deltaX > 0){
F.prev(deltaY > 0 ? 'down':'left');
}else if(deltaY < 0||deltaX < 0){
F.next(deltaY < 0 ? 'up':'right');
}
e.preventDefault();
}}
});
}},
trigger: function (event, o){
var ret, obj=o||F.coming||F.current;
if(!obj){
return;
}
if($.isFunction(obj[event])){
ret=obj[event].apply(obj, Array.prototype.slice.call(arguments, 1));
}
if(ret===false){
return false;
}
if(obj.helpers){
$.each(obj.helpers, function (helper, opts){
if(opts&&F.helpers[helper]&&$.isFunction(F.helpers[helper][event])){
F.helpers[helper][event]($.extend(true, {}, F.helpers[helper].defaults, opts), obj);
}});
}
D.trigger(event);
},
isImage: function (str){
return isString(str)&&str.match(/(^data:image\/.*,)|(\.(jp(e|g|eg)|gif|png|bmp|webp|svg)((\?|#).*)?$)/i);
},
isSWF: function (str){
return isString(str)&&str.match(/\.(swf)((\?|#).*)?$/i);
},
_start: function (index){
var coming={},
obj,
href,
type,
margin,
padding;
index=getScalar(index);
obj=F.group[ index ]||null;
if(!obj){
return false;
}
coming=$.extend(true, {}, F.opts, obj);
margin=coming.margin;
padding=coming.padding;
if($.type(margin)==='number'){
coming.margin=[margin, margin, margin, margin];
}
if($.type(padding)==='number'){
coming.padding=[padding, padding, padding, padding];
}
if(coming.modal){
$.extend(true, coming, {
closeBtn:false,
closeClick:false,
nextClick:false,
arrows:false,
mouseWheel:false,
keys:null,
helpers: {
overlay:{
closeClick:false
}}
});
}
if(coming.autoSize){
coming.autoWidth=coming.autoHeight=true;
}
if(coming.width==='auto'){
coming.autoWidth=true;
}
if(coming.height==='auto'){
coming.autoHeight=true;
}
/*
* Add reference to the group, so it`s possible to access from callbacks, example:
* afterLoad:function(){
*     this.title='Image ' + (this.index + 1) + ' of ' + this.group.length + (this.title ? ' - ' + this.title:'');
* }
*/
coming.group=F.group;
coming.index=index;
F.coming=coming;
if(false===F.trigger('beforeLoad')){
F.coming=null;
return;
}
type=coming.type;
href=coming.href;
if(!type){
F.coming=null;
if(F.current&&F.router&&F.router!=='jumpto'){
F.current.index=index;
return F[ F.router ](F.direction);
}
return false;
}
F.isActive=true;
if(type==='image'||type==='swf'){
coming.autoHeight=coming.autoWidth=false;
coming.scrolling='visible';
}
if(type==='image'){
coming.aspectRatio=true;
}
if(type==='iframe'&&isTouch){
coming.scrolling='scroll';
}
coming.wrap=$(coming.tpl.wrap).addClass('fancybox-' + (isTouch ? 'mobile':'desktop') + ' fancybox-type-' + type + ' fancybox-tmp ' + coming.wrapCSS).appendTo(coming.parent||'body');
$.extend(coming, {
skin:$('.fancybox-skin',  coming.wrap),
outer:$('.fancybox-outer', coming.wrap),
inner:$('.fancybox-inner', coming.wrap)
});
$.each(["Top", "Right", "Bottom", "Left"], function(i, v){
coming.skin.css('padding' + v, getValue(coming.padding[ i ]));
});
F.trigger('onReady');
if(type==='inline'||type==='html'){
if(!coming.content||!coming.content.length){
return F._error('content');
}}else if(!href){
return F._error('href');
}
if(type==='image'){
F._loadImage();
}else if(type==='ajax'){
F._loadAjax();
}else if(type==='iframe'){
F._loadIframe();
}else{
F._afterLoad();
}},
_error: function(type){
$.extend(F.coming, {
type:'html',
autoWidth:true,
autoHeight:true,
minWidth:0,
minHeight:0,
scrolling:'no',
hasError:type,
content:F.coming.tpl.error
});
F._afterLoad();
},
_loadImage: function (){
var img=F.imgPreload=new Image();
img.onload=function (){
this.onload=this.onerror=null;
F.coming.width=this.width / F.opts.pixelRatio;
F.coming.height=this.height / F.opts.pixelRatio;
F._afterLoad();
};
img.onerror=function (){
this.onload=this.onerror=null;
F._error('image');
};
img.src=F.coming.href;
if(img.complete!==true){
F.showLoading();
}},
_loadAjax: function (){
var coming=F.coming;
F.showLoading();
F.ajaxLoad=$.ajax($.extend({}, coming.ajax, {
url: coming.href,
error: function (jqXHR, textStatus){
if(F.coming&&textStatus!=='abort'){
F._error('ajax', jqXHR);
}else{
F.hideLoading();
}},
success: function (data, textStatus){
if(textStatus==='success'){
coming.content=data;
F._afterLoad();
}}
}));
},
_loadIframe: function(){
var coming=F.coming,
iframe=$(coming.tpl.iframe.replace(/\{rnd\}/g, new Date().getTime()))
.attr('scrolling', isTouch ? 'auto':coming.iframe.scrolling)
.attr('src', coming.href);
$(coming.wrap).bind('onReset', function (){
try {
$(this).find('iframe').hide().attr('src', '//about:blank').end().empty();
} catch (e){}});
if(coming.iframe.preload){
F.showLoading();
iframe.one('load', function(){
$(this).data('ready', 1);
if(!isTouch){
$(this).bind('load.fb', F.update);
}
$(this).parents('.fancybox-wrap').width('100%').removeClass('fancybox-tmp').show();
F._afterLoad();
});
}
coming.content=iframe.appendTo(coming.inner);
if(!coming.iframe.preload){
F._afterLoad();
}},
_preloadImages: function(){
var group=F.group,
current=F.current,
len=group.length,
cnt=current.preload ? Math.min(current.preload, len - 1):0,
item,
i;
for (i=1; i <=cnt; i +=1){
item=group[ (current.index + i) % len ];
if(item.type==='image'&&item.href){
new Image().src=item.href;
}}
},
_afterLoad: function (){
var coming=F.coming,
previous=F.current,
placeholder='fancybox-placeholder',
current,
content,
type,
scrolling,
href,
embed;
F.hideLoading();
if(!coming||F.isActive===false){
return;
}
if(false===F.trigger('afterLoad', coming, previous)){
coming.wrap.stop(true).trigger('onReset').remove();
F.coming=null;
return;
}
if(previous){
F.trigger('beforeChange', previous);
previous.wrap.stop(true).removeClass('fancybox-opened')
.find('.fancybox-item, .fancybox-nav')
.remove();
}
F.unbindEvents();
current=coming;
content=coming.content;
type=coming.type;
scrolling=coming.scrolling;
$.extend(F, {
wrap:current.wrap,
skin:current.skin,
outer:current.outer,
inner:current.inner,
current:current,
previous:previous
});
href=current.href;
switch (type){
case 'inline':
case 'ajax':
case 'html':
if(current.selector){
content=$('<div>').html(content).find(current.selector);
}else if(isQuery(content)){
if(!content.data(placeholder)){
content.data(placeholder, $('<div class="' + placeholder + '"></div>').insertAfter(content).hide());
}
content=content.show().detach();
current.wrap.bind('onReset', function (){
if($(this).find(content).length){
content.hide().replaceAll(content.data(placeholder)).data(placeholder, false);
}});
}
break;
case 'image':
content=current.tpl.image.replace('{href}', href);
break;
case 'swf':
content='<object id="fancybox-swf" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="100%" height="100%"><param name="movie" value="' + href + '"></param>';
embed='';
$.each(current.swf, function(name, val){
content +='<param name="' + name + '" value="' + val + '"></param>';
embed   +=' ' + name + '="' + val + '"';
});
content +='<embed src="' + href + '" type="application/x-shockwave-flash" width="100%" height="100%"' + embed + '></embed></object>';
break;
}
if(!(isQuery(content)&&content.parent().is(current.inner))){
current.inner.append(content);
}
F.trigger('beforeShow');
current.inner.css('overflow', scrolling==='yes' ? 'scroll':(scrolling==='no' ? 'hidden':scrolling));
F._setDimension();
F.reposition();
F.isOpen=false;
F.coming=null;
F.bindEvents();
if(!F.isOpened){
$('.fancybox-wrap').not(current.wrap).stop(true).trigger('onReset').remove();
}else if(previous.prevMethod){
F.transitions[ previous.prevMethod ]();
}
F.transitions[ F.isOpened ? current.nextMethod:current.openMethod ]();
F._preloadImages();
},
_setDimension: function (){
var viewport=F.getViewport(),
steps=0,
canShrink=false,
canExpand=false,
wrap=F.wrap,
skin=F.skin,
inner=F.inner,
current=F.current,
width=current.width,
height=current.height,
minWidth=current.minWidth,
minHeight=current.minHeight,
maxWidth=current.maxWidth,
maxHeight=current.maxHeight,
scrolling=current.scrolling,
scrollOut=current.scrollOutside ? current.scrollbarWidth:0,
margin=current.margin,
wMargin=getScalar(margin[1] + margin[3]),
hMargin=getScalar(margin[0] + margin[2]),
wPadding,
hPadding,
wSpace,
hSpace,
origWidth,
origHeight,
origMaxWidth,
origMaxHeight,
ratio,
width_,
height_,
maxWidth_,
maxHeight_,
iframe,
body;
wrap.add(skin).add(inner).width('auto').height('auto').removeClass('fancybox-tmp');
wPadding=getScalar(skin.outerWidth(true)  - skin.width());
hPadding=getScalar(skin.outerHeight(true) - skin.height());
wSpace=wMargin + wPadding;
hSpace=hMargin + hPadding;
origWidth=isPercentage(width)  ? (viewport.w - wSpace) * getScalar(width)  / 100:width;
origHeight=isPercentage(height) ? (viewport.h - hSpace) * getScalar(height) / 100:height;
if(current.type==='iframe'){
iframe=current.content;
if(current.autoHeight&&iframe.data('ready')===1){
try {
if(iframe[0].contentWindow.document.location){
inner.width(origWidth).height(9999);
body=iframe.contents().find('body');
if(scrollOut){
body.css('overflow-x', 'hidden');
}
origHeight=body.outerHeight(true);
}} catch (e){}}
}else if(current.autoWidth||current.autoHeight){
inner.addClass('fancybox-tmp');
if(!current.autoWidth){
inner.width(origWidth);
}
if(!current.autoHeight){
inner.height(origHeight);
}
if(current.autoWidth){
origWidth=inner.width();
}
if(current.autoHeight){
origHeight=inner.height();
}
inner.removeClass('fancybox-tmp');
}
width=getScalar(origWidth);
height=getScalar(origHeight);
ratio=origWidth / origHeight;
minWidth=getScalar(isPercentage(minWidth) ? getScalar(minWidth, 'w') - wSpace:minWidth);
maxWidth=getScalar(isPercentage(maxWidth) ? getScalar(maxWidth, 'w') - wSpace:maxWidth);
minHeight=getScalar(isPercentage(minHeight) ? getScalar(minHeight, 'h') - hSpace:minHeight);
maxHeight=getScalar(isPercentage(maxHeight) ? getScalar(maxHeight, 'h') - hSpace:maxHeight);
origMaxWidth=maxWidth;
origMaxHeight=maxHeight;
if(current.fitToView){
maxWidth=Math.min(viewport.w - wSpace, maxWidth);
maxHeight=Math.min(viewport.h - hSpace, maxHeight);
}
maxWidth_=viewport.w - wMargin;
maxHeight_=viewport.h - hMargin;
if(current.aspectRatio){
if(width > maxWidth){
width=maxWidth;
height=getScalar(width / ratio);
}
if(height > maxHeight){
height=maxHeight;
width=getScalar(height * ratio);
}
if(width < minWidth){
width=minWidth;
height=getScalar(width / ratio);
}
if(height < minHeight){
height=minHeight;
width=getScalar(height * ratio);
}}else{
width=Math.max(minWidth, Math.min(width, maxWidth));
if(current.autoHeight&&current.type!=='iframe'){
inner.width(width);
height=inner.height();
}
height=Math.max(minHeight, Math.min(height, maxHeight));
}
if(current.fitToView){
inner.width(width).height(height);
wrap.width(width + wPadding);
width_=wrap.width();
height_=wrap.height();
if(current.aspectRatio){
while ((width_ > maxWidth_||height_ > maxHeight_)&&width > minWidth&&height > minHeight){
if(steps++ > 19){
break;
}
height=Math.max(minHeight, Math.min(maxHeight, height - 10));
width=getScalar(height * ratio);
if(width < minWidth){
width=minWidth;
height=getScalar(width / ratio);
}
if(width > maxWidth){
width=maxWidth;
height=getScalar(width / ratio);
}
inner.width(width).height(height);
wrap.width(width + wPadding);
width_=wrap.width();
height_=wrap.height();
}}else{
width=Math.max(minWidth,  Math.min(width,  width  - (width_  - maxWidth_)));
height=Math.max(minHeight, Math.min(height, height - (height_ - maxHeight_)));
}}
if(scrollOut&&scrolling==='auto'&&height < origHeight&&(width + wPadding + scrollOut) < maxWidth_){
width +=scrollOut;
}
inner.width(width).height(height);
wrap.width(width + wPadding);
width_=wrap.width();
height_=wrap.height();
canShrink=(width_ > maxWidth_||height_ > maxHeight_)&&width > minWidth&&height > minHeight;
canExpand=current.aspectRatio ? (width < origMaxWidth&&height < origMaxHeight&&width < origWidth&&height < origHeight):((width < origMaxWidth||height < origMaxHeight)&&(width < origWidth||height < origHeight));
$.extend(current, {
dim:{
width:getValue(width_),
height:getValue(height_)
},
origWidth:origWidth,
origHeight:origHeight,
canShrink:canShrink,
canExpand:canExpand,
wPadding:wPadding,
hPadding:hPadding,
wrapSpace:height_ - skin.outerHeight(true),
skinSpace:skin.height() - height
});
if(!iframe&&current.autoHeight&&height > minHeight&&height < maxHeight&&!canExpand){
inner.height('auto');
}},
_getPosition: function (onlyAbsolute){
var current=F.current,
viewport=F.getViewport(),
margin=current.margin,
width=F.wrap.width()  + margin[1] + margin[3],
height=F.wrap.height() + margin[0] + margin[2],
rez={
position: 'absolute',
top:margin[0],
left:margin[3]
};
if(current.autoCenter&&current.fixed&&!onlyAbsolute&&height <=viewport.h&&width <=viewport.w){
rez.position='fixed';
}else if(!current.locked){
rez.top  +=viewport.y;
rez.left +=viewport.x;
}
rez.top=getValue(Math.max(rez.top,  rez.top  + ((viewport.h - height) * current.topRatio)));
rez.left=getValue(Math.max(rez.left, rez.left + ((viewport.w - width)  * current.leftRatio)));
return rez;
},
_afterZoomIn: function (){
var current=F.current;
if(!current){
return;
}
F.isOpen=F.isOpened=true;
F.wrap.css('overflow', 'visible').addClass('fancybox-opened');
F.update();
if(current.closeClick||(current.nextClick&&F.group.length > 1)){
F.inner.css('cursor', 'pointer').bind('click.fb', function(e){
if(!$(e.target).is('a')&&!$(e.target).parent().is('a')){
e.preventDefault();
F[ current.closeClick ? 'close':'next' ]();
}});
}
if(current.closeBtn){
$(current.tpl.closeBtn).appendTo(F.skin).bind('click.fb', function(e){
e.preventDefault();
F.close();
});
}
if(current.arrows&&F.group.length > 1){
if(current.loop||current.index > 0){
$(current.tpl.prev).appendTo(F.outer).bind('click.fb', F.prev);
}
if(current.loop||current.index < F.group.length - 1){
$(current.tpl.next).appendTo(F.outer).bind('click.fb', F.next);
}}
F.trigger('afterShow');
if(!current.loop&&current.index===current.group.length - 1){
F.play(false);
}else if(F.opts.autoPlay&&!F.player.isActive){
F.opts.autoPlay=false;
F.play();
}},
_afterZoomOut: function(obj){
obj=obj||F.current;
$('.fancybox-wrap').trigger('onReset').remove();
$.extend(F, {
group:{},
opts:{},
router:false,
current:null,
isActive:false,
isOpened:false,
isOpen:false,
isClosing:false,
wrap:null,
skin:null,
outer:null,
inner:null
});
F.trigger('afterClose', obj);
}});
F.transitions={
getOrigPosition: function (){
var current=F.current,
element=current.element,
orig=current.orig,
pos={},
width=50,
height=50,
hPadding=current.hPadding,
wPadding=current.wPadding,
viewport=F.getViewport();
if(!orig&&current.isDom&&element.is(':visible')){
orig=element.find('img:first');
if(!orig.length){
orig=element;
}}
if(isQuery(orig)){
pos=orig.offset();
if(orig.is('img')){
width=orig.outerWidth();
height=orig.outerHeight();
}}else{
pos.top=viewport.y + (viewport.h - height) * current.topRatio;
pos.left=viewport.x + (viewport.w - width)  * current.leftRatio;
}
if(F.wrap.css('position')==='fixed'||current.locked){
pos.top  -=viewport.y;
pos.left -=viewport.x;
}
pos={
top:getValue(pos.top  - hPadding * current.topRatio),
left:getValue(pos.left - wPadding * current.leftRatio),
width:getValue(width  + wPadding),
height:getValue(height + hPadding)
};
return pos;
},
step: function (now, fx){
var ratio,
padding,
value,
prop=fx.prop,
current=F.current,
wrapSpace=current.wrapSpace,
skinSpace=current.skinSpace;
if(prop==='width'||prop==='height'){
ratio=fx.end===fx.start ? 1:(now - fx.start) / (fx.end - fx.start);
if(F.isClosing){
ratio=1 - ratio;
}
padding=prop==='width' ? current.wPadding:current.hPadding;
value=now - padding;
F.skin[ prop ](getScalar(prop==='width' ?  value:value - (wrapSpace * ratio)));
F.inner[ prop ](getScalar(prop==='width' ?  value:value - (wrapSpace * ratio) - (skinSpace * ratio)));
}},
zoomIn: function (){
var current=F.current,
startPos=current.pos,
effect=current.openEffect,
elastic=effect==='elastic',
endPos=$.extend({opacity:1}, startPos);
delete endPos.position;
if(elastic){
startPos=this.getOrigPosition();
if(current.openOpacity){
startPos.opacity=0.1;
}}else if(effect==='fade'){
startPos.opacity=0.1;
}
F.wrap.css(startPos).animate(endPos, {
duration:effect==='none' ? 0:current.openSpeed,
easing:current.openEasing,
step:elastic ? this.step:null,
complete:F._afterZoomIn
});
},
zoomOut: function (){
var current=F.current,
effect=current.closeEffect,
elastic=effect==='elastic',
endPos={opacity:0.1};
if(elastic){
endPos=this.getOrigPosition();
if(current.closeOpacity){
endPos.opacity=0.1;
}}
F.wrap.animate(endPos, {
duration:effect==='none' ? 0:current.closeSpeed,
easing:current.closeEasing,
step:elastic ? this.step:null,
complete:F._afterZoomOut
});
},
changeIn: function (){
var current=F.current,
effect=current.nextEffect,
startPos=current.pos,
endPos={ opacity:1 },
direction=F.direction,
distance=200,
field;
startPos.opacity=0.1;
if(effect==='elastic'){
field=direction==='down'||direction==='up' ? 'top':'left';
if(direction==='down'||direction==='right'){
startPos[ field ]=getValue(getScalar(startPos[ field ]) - distance);
endPos[ field ]='+=' + distance + 'px';
}else{
startPos[ field ]=getValue(getScalar(startPos[ field ]) + distance);
endPos[ field ]='-=' + distance + 'px';
}}
if(effect==='none'){
F._afterZoomIn();
}else{
F.wrap.css(startPos).animate(endPos, {
duration:current.nextSpeed,
easing:current.nextEasing,
complete:F._afterZoomIn
});
}},
changeOut: function (){
var previous=F.previous,
effect=previous.prevEffect,
endPos={ opacity:0.1 },
direction=F.direction,
distance=200;
if(effect==='elastic'){
endPos[ direction==='down'||direction==='up' ? 'top':'left' ]=(direction==='up'||direction==='left' ? '-':'+') + '=' + distance + 'px';
}
previous.wrap.animate(endPos, {
duration:effect==='none' ? 0:previous.prevSpeed,
easing:previous.prevEasing,
complete:function (){
$(this).trigger('onReset').remove();
}});
}};
F.helpers.overlay={
defaults:{
closeClick:true,
speedOut:200,
showEarly:true,
css:{},
locked:!isTouch,
fixed:true
},
overlay:null,
fixed:false,
el:$('html'),
create:function(opts){
opts=$.extend({}, this.defaults, opts);
if(this.overlay){
this.close();
}
this.overlay=$('<div class="fancybox-overlay"></div>').appendTo(F.coming ? F.coming.parent:opts.parent);
this.fixed=false;
if(opts.fixed&&F.defaults.fixed){
this.overlay.addClass('fancybox-overlay-fixed');
this.fixed=true;
}},
open:function(opts){
var that=this;
opts=$.extend({}, this.defaults, opts);
if(this.overlay){
this.overlay.unbind('.overlay').width('auto').height('auto');
}else{
this.create(opts);
}
if(!this.fixed){
W.bind('resize.overlay', $.proxy(this.update, this));
this.update();
}
if(opts.closeClick){
this.overlay.bind('click.overlay', function(e){
if($(e.target).hasClass('fancybox-overlay')){
if(F.isActive){
F.close();
}else{
that.close();
}
return false;
}});
}
this.overlay.css(opts.css).show();
},
close:function(){
var scrollV, scrollH;
W.unbind('resize.overlay');
if(this.el.hasClass('fancybox-lock')){
$('.fancybox-margin').removeClass('fancybox-margin');
scrollV=W.scrollTop();
scrollH=W.scrollLeft();
this.el.removeClass('fancybox-lock');
W.scrollTop(scrollV).scrollLeft(scrollH);
}
$('.fancybox-overlay').remove().hide();
$.extend(this, {
overlay:null,
fixed:false
});
},
update:function (){
var width='100%', offsetWidth;
this.overlay.width(width).height('100%');
if(IE){
offsetWidth=Math.max(document.documentElement.offsetWidth, document.body.offsetWidth);
if(D.width() > offsetWidth){
width=D.width();
}}else if(D.width() > W.width()){
width=D.width();
}
this.overlay.width(width).height(D.height());
},
onReady:function (opts, obj){
var overlay=this.overlay;
$('.fancybox-overlay').stop(true, true);
if(!overlay){
this.create(opts);
}
if(opts.locked&&this.fixed&&obj.fixed){
if(!overlay){
this.margin=D.height() > W.height() ? $('html').css('margin-right').replace("px", ""):false;
}
obj.locked=this.overlay.append(obj.wrap);
obj.fixed=false;
}
if(opts.showEarly===true){
this.beforeShow.apply(this, arguments);
}},
beforeShow:function(opts, obj){
var scrollV, scrollH;
if(obj.locked){
if(this.margin!==false){
$('*').filter(function(){
return ($(this).css('position')==='fixed'&&!$(this).hasClass("fancybox-overlay")&&!$(this).hasClass("fancybox-wrap"));
}).addClass('fancybox-margin');
this.el.addClass('fancybox-margin');
}
scrollV=W.scrollTop();
scrollH=W.scrollLeft();
this.el.addClass('fancybox-lock');
W.scrollTop(scrollV).scrollLeft(scrollH);
}
this.open(opts);
},
onUpdate:function(){
if(!this.fixed){
this.update();
}},
afterClose: function (opts){
if(this.overlay&&!F.coming){
this.overlay.fadeOut(opts.speedOut, $.proxy(this.close, this));
}}
};
F.helpers.title={
defaults:{
type:'float', // 'float', 'inside', 'outside' or 'over',
position:'bottom' // 'top' or 'bottom'
},
beforeShow: function (opts){
var current=F.current,
text=current.title,
type=opts.type,
title,
target;
if($.isFunction(text)){
text=text.call(current.element, current);
}
if(!isString(text)||$.trim(text)===''){
return;
}
title=$('<div class="fancybox-title fancybox-title-' + type + '-wrap">' + text + '</div>');
switch (type){
case 'inside':
target=F.skin;
break;
case 'outside':
target=F.wrap;
break;
case 'over':
target=F.inner;
break;
default: // 'float'
target=F.skin;
title.appendTo('body');
if(IE){
title.width(title.width());
}
title.wrapInner('<span class="child"></span>');
F.current.margin[2] +=Math.abs(getScalar(title.css('margin-bottom')));
break;
}
title[ (opts.position==='top' ? 'prependTo':'appendTo') ](target);
}};
$.fn.fancybox=function (options){
var index,
that=$(this),
selector=this.selector||'',
run=function(e){
var what=$(this).blur(), idx=index, relType, relVal;
if(!(e.ctrlKey||e.altKey||e.shiftKey||e.metaKey)&&!what.is('.fancybox-wrap')){
relType=options.groupAttr||'data-fancybox-group';
relVal=what.attr(relType);
if(!relVal){
relType='rel';
relVal=what.get(0)[ relType ];
}
if(relVal&&relVal!==''&&relVal!=='nofollow'){
what=selector.length ? $(selector):that;
what=what.filter('[' + relType + '="' + relVal + '"]');
idx=what.index(this);
}
options.index=idx;
if(F.open(what, options)!==false){
e.preventDefault();
}}
};
options=options||{};
index=options.index||0;
if(!selector||options.live===false){
that.unbind('click.fb-start').bind('click.fb-start', run);
}else{
D.undelegate(selector, 'click.fb-start').delegate(selector + ":not('.fancybox-item, .fancybox-nav')", 'click.fb-start', run);
}
this.filter('[data-fancybox-start=1]').trigger('click');
return this;
};
D.ready(function(){
var w1, w2;
if($.scrollbarWidth===undefined){
$.scrollbarWidth=function(){
var parent=$('<div style="width:50px;height:50px;overflow:auto"><div/></div>').appendTo('body'),
child=parent.children(),
width=child.innerWidth() - child.height(99).innerWidth();
parent.remove();
return width;
};}
if($.support.fixedPosition===undefined){
$.support.fixedPosition=(function(){
var elem=$('<div style="position:fixed;top:20px;"></div>').appendTo('body'),
fixed=(elem[0].offsetTop===20||elem[0].offsetTop===15);
elem.remove();
return fixed;
}());
}
$.extend(F.defaults, {
scrollbarWidth:$.scrollbarWidth(),
fixed:$.support.fixedPosition,
parent:$('body')
});
w1=$(window).width();
H.addClass('fancybox-lock-test');
w2=$(window).width();
H.removeClass('fancybox-lock-test');
$("<style type='text/css'>.fancybox-margin{margin-right:" + (w2 - w1) + "px;}</style>").appendTo("head");
});
}(window, document, jQuery));
(function(factory){
if(typeof define==="function"&&define.amd){
define([ "jquery" ], factory);
}else{
factory(jQuery);
}}(function($){
$.ui=$.ui||{};
$.extend($.ui, {
version: "1.11.4",
keyCode: {
BACKSPACE: 8,
COMMA: 188,
DELETE: 46,
DOWN: 40,
END: 35,
ENTER: 13,
ESCAPE: 27,
HOME: 36,
LEFT: 37,
PAGE_DOWN: 34,
PAGE_UP: 33,
PERIOD: 190,
RIGHT: 39,
SPACE: 32,
TAB: 9,
UP: 38
}});
$.fn.extend({
scrollParent: function(includeHidden){
var position=this.css("position"),
excludeStaticParent=position==="absolute",
overflowRegex=includeHidden ? /(auto|scroll|hidden)/:/(auto|scroll)/,
scrollParent=this.parents().filter(function(){
var parent=$(this);
if(excludeStaticParent&&parent.css("position")==="static"){
return false;
}
return overflowRegex.test(parent.css("overflow") + parent.css("overflow-y") + parent.css("overflow-x"));
}).eq(0);
return position==="fixed"||!scrollParent.length ? $(this[ 0 ].ownerDocument||document):scrollParent;
},
uniqueId: (function(){
var uuid=0;
return function(){
return this.each(function(){
if(!this.id){
this.id="ui-id-" + ( ++uuid);
}});
};})(),
removeUniqueId: function(){
return this.each(function(){
if(/^ui-id-\d+$/.test(this.id)){
$(this).removeAttr("id");
}});
}});
function focusable(element, isTabIndexNotNaN){
var map, mapName, img,
nodeName=element.nodeName.toLowerCase();
if("area"===nodeName){
map=element.parentNode;
mapName=map.name;
if(!element.href||!mapName||map.nodeName.toLowerCase()!=="map"){
return false;
}
img=$("img[usemap='#" + mapName + "']")[ 0 ];
return !!img&&visible(img);
}
return(/^(input|select|textarea|button|object)$/.test(nodeName) ?
!element.disabled :
"a"===nodeName ?
element.href||isTabIndexNotNaN :
isTabIndexNotNaN) &&
visible(element);
}
function visible(element){
return $.expr.filters.visible(element) &&
!$(element).parents().addBack().filter(function(){
return $.css(this, "visibility")==="hidden";
}).length;
}
$.extend($.expr[ ":" ], {
data: $.expr.createPseudo ?
$.expr.createPseudo(function(dataName){
return function(elem){
return !!$.data(elem, dataName);
};}) :
function(elem, i, match){
return !!$.data(elem, match[ 3 ]);
},
focusable: function(element){
return focusable(element, !isNaN($.attr(element, "tabindex")));
},
tabbable: function(element){
var tabIndex=$.attr(element, "tabindex"),
isTabIndexNaN=isNaN(tabIndex);
return(isTabIndexNaN||tabIndex >=0)&&focusable(element, !isTabIndexNaN);
}});
if(!$("<a>").outerWidth(1).jquery){
$.each([ "Width", "Height" ], function(i, name){
var side=name==="Width" ? [ "Left", "Right" ]:[ "Top", "Bottom" ],
type=name.toLowerCase(),
orig={
innerWidth: $.fn.innerWidth,
innerHeight: $.fn.innerHeight,
outerWidth: $.fn.outerWidth,
outerHeight: $.fn.outerHeight
};
function reduce(elem, size, border, margin){
$.each(side, function(){
size -=parseFloat($.css(elem, "padding" + this))||0;
if(border){
size -=parseFloat($.css(elem, "border" + this + "Width"))||0;
}
if(margin){
size -=parseFloat($.css(elem, "margin" + this))||0;
}});
return size;
}
$.fn[ "inner" + name ]=function(size){
if(size===undefined){
return orig[ "inner" + name ].call(this);
}
return this.each(function(){
$(this).css(type, reduce(this, size) + "px");
});
};
$.fn[ "outer" + name]=function(size, margin){
if(typeof size!=="number"){
return orig[ "outer" + name ].call(this, size);
}
return this.each(function(){
$(this).css(type, reduce(this, size, true, margin) + "px");
});
};});
}
if(!$.fn.addBack){
$.fn.addBack=function(selector){
return this.add(selector==null ?
this.prevObject:this.prevObject.filter(selector)
);
};}
if($("<a>").data("a-b", "a").removeData("a-b").data("a-b")){
$.fn.removeData=(function(removeData){
return function(key){
if(arguments.length){
return removeData.call(this, $.camelCase(key));
}else{
return removeData.call(this);
}};})($.fn.removeData);
}
$.ui.ie = !!/msie [\w.]+/.exec(navigator.userAgent.toLowerCase());
$.fn.extend({
focus: (function(orig){
return function(delay, fn){
return typeof delay==="number" ?
this.each(function(){
var elem=this;
setTimeout(function(){
$(elem).focus();
if(fn){
fn.call(elem);
}}, delay);
}) :
orig.apply(this, arguments);
};})($.fn.focus),
disableSelection: (function(){
var eventType="onselectstart" in document.createElement("div") ?
"selectstart" :
"mousedown";
return function(){
return this.bind(eventType + ".ui-disableSelection", function(event){
event.preventDefault();
});
};})(),
enableSelection: function(){
return this.unbind(".ui-disableSelection");
},
zIndex: function(zIndex){
if(zIndex!==undefined){
return this.css("zIndex", zIndex);
}
if(this.length){
var elem=$(this[ 0 ]), position, value;
while(elem.length&&elem[ 0 ]!==document){
position=elem.css("position");
if(position==="absolute"||position==="relative"||position==="fixed"){
value=parseInt(elem.css("zIndex"), 10);
if(!isNaN(value)&&value!==0){
return value;
}}
elem=elem.parent();
}}
return 0;
}});
$.ui.plugin={
add: function(module, option, set){
var i,
proto=$.ui[ module ].prototype;
for(i in set){
proto.plugins[ i ]=proto.plugins[ i ]||[];
proto.plugins[ i ].push([ option, set[ i ] ]);
}},
call: function(instance, name, args, allowDisconnected){
var i,
set=instance.plugins[ name ];
if(!set){
return;
}
if(!allowDisconnected&&(!instance.element[ 0 ].parentNode||instance.element[ 0 ].parentNode.nodeType===11)){
return;
}
for(i=0; i < set.length; i++){
if(instance.options[ set[ i ][ 0 ] ]){
set[ i ][ 1 ].apply(instance.element, args);
}}
}};
var widget_uuid=0,
widget_slice=Array.prototype.slice;
$.cleanData=(function(orig){
return function(elems){
var events, elem, i;
for(i=0; (elem=elems[i])!=null; i++){
try {
events=$._data(elem, "events");
if(events&&events.remove){
$(elem).triggerHandler("remove");
}} catch(e){}}
orig(elems);
};})($.cleanData);
$.widget=function(name, base, prototype){
var fullName, existingConstructor, constructor, basePrototype,
proxiedPrototype={},
namespace=name.split(".")[ 0 ];
name=name.split(".")[ 1 ];
fullName=namespace + "-" + name;
if(!prototype){
prototype=base;
base=$.Widget;
}
$.expr[ ":" ][ fullName.toLowerCase() ]=function(elem){
return !!$.data(elem, fullName);
};
$[ namespace ]=$[ namespace ]||{};
existingConstructor=$[ namespace ][ name ];
constructor=$[ namespace ][ name ]=function(options, element){
if(!this._createWidget){
return new constructor(options, element);
}
if(arguments.length){
this._createWidget(options, element);
}};
$.extend(constructor, existingConstructor, {
version: prototype.version,
_proto: $.extend({}, prototype),
_childConstructors: []
});
basePrototype=new base();
basePrototype.options=$.widget.extend({}, basePrototype.options);
$.each(prototype, function(prop, value){
if(!$.isFunction(value)){
proxiedPrototype[ prop ]=value;
return;
}
proxiedPrototype[ prop ]=(function(){
var _super=function(){
return base.prototype[ prop ].apply(this, arguments);
},
_superApply=function(args){
return base.prototype[ prop ].apply(this, args);
};
return function(){
var __super=this._super,
__superApply=this._superApply,
returnValue;
this._super=_super;
this._superApply=_superApply;
returnValue=value.apply(this, arguments);
this._super=__super;
this._superApply=__superApply;
return returnValue;
};})();
});
constructor.prototype=$.widget.extend(basePrototype, {
widgetEventPrefix: existingConstructor ? (basePrototype.widgetEventPrefix||name):name
}, proxiedPrototype, {
constructor: constructor,
namespace: namespace,
widgetName: name,
widgetFullName: fullName
});
if(existingConstructor){
$.each(existingConstructor._childConstructors, function(i, child){
var childPrototype=child.prototype;
$.widget(childPrototype.namespace + "." + childPrototype.widgetName, constructor, child._proto);
});
delete existingConstructor._childConstructors;
}else{
base._childConstructors.push(constructor);
}
$.widget.bridge(name, constructor);
return constructor;
};
$.widget.extend=function(target){
var input=widget_slice.call(arguments, 1),
inputIndex=0,
inputLength=input.length,
key,
value;
for(; inputIndex < inputLength; inputIndex++){
for(key in input[ inputIndex ]){
value=input[ inputIndex ][ key ];
if(input[ inputIndex ].hasOwnProperty(key)&&value!==undefined){
if($.isPlainObject(value)){
target[ key ]=$.isPlainObject(target[ key ]) ?
$.widget.extend({}, target[ key ], value) :
$.widget.extend({}, value);
}else{
target[ key ]=value;
}}
}}
return target;
};
$.widget.bridge=function(name, object){
var fullName=object.prototype.widgetFullName||name;
$.fn[ name ]=function(options){
var isMethodCall=typeof options==="string",
args=widget_slice.call(arguments, 1),
returnValue=this;
if(isMethodCall){
this.each(function(){
var methodValue,
instance=$.data(this, fullName);
if(options==="instance"){
returnValue=instance;
return false;
}
if(!instance){
return $.error("cannot call methods on " + name + " prior to initialization; " +
"attempted to call method '" + options + "'");
}
if(!$.isFunction(instance[options])||options.charAt(0)==="_"){
return $.error("no such method '" + options + "' for " + name + " widget instance");
}
methodValue=instance[ options ].apply(instance, args);
if(methodValue!==instance&&methodValue!==undefined){
returnValue=methodValue&&methodValue.jquery ?
returnValue.pushStack(methodValue.get()) :
methodValue;
return false;
}});
}else{
if(args.length){
options=$.widget.extend.apply(null, [ options ].concat(args));
}
this.each(function(){
var instance=$.data(this, fullName);
if(instance){
instance.option(options||{});
if(instance._init){
instance._init();
}}else{
$.data(this, fullName, new object(options, this));
}});
}
return returnValue;
};};
$.Widget=function(){};
$.Widget._childConstructors=[];
$.Widget.prototype={
widgetName: "widget",
widgetEventPrefix: "",
defaultElement: "<div>",
options: {
disabled: false,
create: null
},
_createWidget: function(options, element){
element=$(element||this.defaultElement||this)[ 0 ];
this.element=$(element);
this.uuid=widget_uuid++;
this.eventNamespace="." + this.widgetName + this.uuid;
this.bindings=$();
this.hoverable=$();
this.focusable=$();
if(element!==this){
$.data(element, this.widgetFullName, this);
this._on(true, this.element, {
remove: function(event){
if(event.target===element){
this.destroy();
}}
});
this.document=$(element.style ?
element.ownerDocument :
element.document||element);
this.window=$(this.document[0].defaultView||this.document[0].parentWindow);
}
this.options=$.widget.extend({},
this.options,
this._getCreateOptions(),
options);
this._create();
this._trigger("create", null, this._getCreateEventData());
this._init();
},
_getCreateOptions: $.noop,
_getCreateEventData: $.noop,
_create: $.noop,
_init: $.noop,
destroy: function(){
this._destroy();
this.element
.unbind(this.eventNamespace)
.removeData(this.widgetFullName)
.removeData($.camelCase(this.widgetFullName));
this.widget()
.unbind(this.eventNamespace)
.removeAttr("aria-disabled")
.removeClass(this.widgetFullName + "-disabled " +
"ui-state-disabled");
this.bindings.unbind(this.eventNamespace);
this.hoverable.removeClass("ui-state-hover");
this.focusable.removeClass("ui-state-focus");
},
_destroy: $.noop,
widget: function(){
return this.element;
},
option: function(key, value){
var options=key,
parts,
curOption,
i;
if(arguments.length===0){
return $.widget.extend({}, this.options);
}
if(typeof key==="string"){
options={};
parts=key.split(".");
key=parts.shift();
if(parts.length){
curOption=options[ key ]=$.widget.extend({}, this.options[ key ]);
for(i=0; i < parts.length - 1; i++){
curOption[ parts[ i ] ]=curOption[ parts[ i ] ]||{};
curOption=curOption[ parts[ i ] ];
}
key=parts.pop();
if(arguments.length===1){
return curOption[ key ]===undefined ? null:curOption[ key ];
}
curOption[ key ]=value;
}else{
if(arguments.length===1){
return this.options[ key ]===undefined ? null:this.options[ key ];
}
options[ key ]=value;
}}
this._setOptions(options);
return this;
},
_setOptions: function(options){
var key;
for(key in options){
this._setOption(key, options[ key ]);
}
return this;
},
_setOption: function(key, value){
this.options[ key ]=value;
if(key==="disabled"){
this.widget()
.toggleClass(this.widgetFullName + "-disabled", !!value);
if(value){
this.hoverable.removeClass("ui-state-hover");
this.focusable.removeClass("ui-state-focus");
}}
return this;
},
enable: function(){
return this._setOptions({ disabled: false });
},
disable: function(){
return this._setOptions({ disabled: true });
},
_on: function(suppressDisabledCheck, element, handlers){
var delegateElement,
instance=this;
if(typeof suppressDisabledCheck!=="boolean"){
handlers=element;
element=suppressDisabledCheck;
suppressDisabledCheck=false;
}
if(!handlers){
handlers=element;
element=this.element;
delegateElement=this.widget();
}else{
element=delegateElement=$(element);
this.bindings=this.bindings.add(element);
}
$.each(handlers, function(event, handler){
function handlerProxy(){
if(!suppressDisabledCheck &&
(instance.options.disabled===true ||
$(this).hasClass("ui-state-disabled"))){
return;
}
return(typeof handler==="string" ? instance[ handler ]:handler)
.apply(instance, arguments);
}
if(typeof handler!=="string"){
handlerProxy.guid=handler.guid =
handler.guid||handlerProxy.guid||$.guid++;
}
var match=event.match(/^([\w:-]*)\s*(.*)$/),
eventName=match[1] + instance.eventNamespace,
selector=match[2];
if(selector){
delegateElement.delegate(selector, eventName, handlerProxy);
}else{
element.bind(eventName, handlerProxy);
}});
},
_off: function(element, eventName){
eventName=(eventName||"").split(" ").join(this.eventNamespace + " ") +
this.eventNamespace;
element.unbind(eventName).undelegate(eventName);
this.bindings=$(this.bindings.not(element).get());
this.focusable=$(this.focusable.not(element).get());
this.hoverable=$(this.hoverable.not(element).get());
},
_delay: function(handler, delay){
function handlerProxy(){
return(typeof handler==="string" ? instance[ handler ]:handler)
.apply(instance, arguments);
}
var instance=this;
return setTimeout(handlerProxy, delay||0);
},
_hoverable: function(element){
this.hoverable=this.hoverable.add(element);
this._on(element, {
mouseenter: function(event){
$(event.currentTarget).addClass("ui-state-hover");
},
mouseleave: function(event){
$(event.currentTarget).removeClass("ui-state-hover");
}});
},
_focusable: function(element){
this.focusable=this.focusable.add(element);
this._on(element, {
focusin: function(event){
$(event.currentTarget).addClass("ui-state-focus");
},
focusout: function(event){
$(event.currentTarget).removeClass("ui-state-focus");
}});
},
_trigger: function(type, event, data){
var prop, orig,
callback=this.options[ type ];
data=data||{};
event=$.Event(event);
event.type=(type===this.widgetEventPrefix ?
type :
this.widgetEventPrefix + type).toLowerCase();
event.target=this.element[ 0 ];
orig=event.originalEvent;
if(orig){
for(prop in orig){
if(!(prop in event)){
event[ prop ]=orig[ prop ];
}}
}
this.element.trigger(event, data);
return !($.isFunction(callback) &&
callback.apply(this.element[0], [ event ].concat(data))===false ||
event.isDefaultPrevented());
}};
$.each({ show: "fadeIn", hide: "fadeOut" }, function(method, defaultEffect){
$.Widget.prototype[ "_" + method ]=function(element, options, callback){
if(typeof options==="string"){
options={ effect: options };}
var hasOptions,
effectName = !options ?
method :
options===true||typeof options==="number" ?
defaultEffect :
options.effect||defaultEffect;
options=options||{};
if(typeof options==="number"){
options={ duration: options };}
hasOptions = !$.isEmptyObject(options);
options.complete=callback;
if(options.delay){
element.delay(options.delay);
}
if(hasOptions&&$.effects&&$.effects.effect[ effectName ]){
element[ method ](options);
}else if(effectName!==method&&element[ effectName ]){
element[ effectName ](options.duration, options.easing, callback);
}else{
element.queue(function(next){
$(this)[ method ]();
if(callback){
callback.call(element[ 0 ]);
}
next();
});
}};});
var widget=$.widget;
var mouseHandled=false;
$(document).mouseup(function(){
mouseHandled=false;
});
var mouse=$.widget("ui.mouse", {
version: "1.11.4",
options: {
cancel: "input,textarea,button,select,option",
distance: 1,
delay: 0
},
_mouseInit: function(){
var that=this;
this.element
.bind("mousedown." + this.widgetName, function(event){
return that._mouseDown(event);
})
.bind("click." + this.widgetName, function(event){
if(true===$.data(event.target, that.widgetName + ".preventClickEvent")){
$.removeData(event.target, that.widgetName + ".preventClickEvent");
event.stopImmediatePropagation();
return false;
}});
this.started=false;
},
_mouseDestroy: function(){
this.element.unbind("." + this.widgetName);
if(this._mouseMoveDelegate){
this.document
.unbind("mousemove." + this.widgetName, this._mouseMoveDelegate)
.unbind("mouseup." + this.widgetName, this._mouseUpDelegate);
}},
_mouseDown: function(event){
if(mouseHandled){
return;
}
this._mouseMoved=false;
(this._mouseStarted&&this._mouseUp(event));
this._mouseDownEvent=event;
var that=this,
btnIsLeft=(event.which===1),
elIsCancel=(typeof this.options.cancel==="string"&&event.target.nodeName ? $(event.target).closest(this.options.cancel).length:false);
if(!btnIsLeft||elIsCancel||!this._mouseCapture(event)){
return true;
}
this.mouseDelayMet = !this.options.delay;
if(!this.mouseDelayMet){
this._mouseDelayTimer=setTimeout(function(){
that.mouseDelayMet=true;
}, this.options.delay);
}
if(this._mouseDistanceMet(event)&&this._mouseDelayMet(event)){
this._mouseStarted=(this._mouseStart(event)!==false);
if(!this._mouseStarted){
event.preventDefault();
return true;
}}
if(true===$.data(event.target, this.widgetName + ".preventClickEvent")){
$.removeData(event.target, this.widgetName + ".preventClickEvent");
}
this._mouseMoveDelegate=function(event){
return that._mouseMove(event);
};
this._mouseUpDelegate=function(event){
return that._mouseUp(event);
};
this.document
.bind("mousemove." + this.widgetName, this._mouseMoveDelegate)
.bind("mouseup." + this.widgetName, this._mouseUpDelegate);
event.preventDefault();
mouseHandled=true;
return true;
},
_mouseMove: function(event){
if(this._mouseMoved){
if($.ui.ie&&(!document.documentMode||document.documentMode < 9)&&!event.button){
return this._mouseUp(event);
}else if(!event.which){
return this._mouseUp(event);
}}
if(event.which||event.button){
this._mouseMoved=true;
}
if(this._mouseStarted){
this._mouseDrag(event);
return event.preventDefault();
}
if(this._mouseDistanceMet(event)&&this._mouseDelayMet(event)){
this._mouseStarted =
(this._mouseStart(this._mouseDownEvent, event)!==false);
(this._mouseStarted ? this._mouseDrag(event):this._mouseUp(event));
}
return !this._mouseStarted;
},
_mouseUp: function(event){
this.document
.unbind("mousemove." + this.widgetName, this._mouseMoveDelegate)
.unbind("mouseup." + this.widgetName, this._mouseUpDelegate);
if(this._mouseStarted){
this._mouseStarted=false;
if(event.target===this._mouseDownEvent.target){
$.data(event.target, this.widgetName + ".preventClickEvent", true);
}
this._mouseStop(event);
}
mouseHandled=false;
return false;
},
_mouseDistanceMet: function(event){
return (Math.max(Math.abs(this._mouseDownEvent.pageX - event.pageX),
Math.abs(this._mouseDownEvent.pageY - event.pageY)
) >=this.options.distance
);
},
_mouseDelayMet: function(){
return this.mouseDelayMet;
},
_mouseStart: function(){},
_mouseDrag: function(){},
_mouseStop: function(){},
_mouseCapture: function(){ return true; }});
(function(){
$.ui=$.ui||{};
var cachedScrollbarWidth, supportsOffsetFractions,
max=Math.max,
abs=Math.abs,
round=Math.round,
rhorizontal=/left|center|right/,
rvertical=/top|center|bottom/,
roffset=/[\+\-]\d+(\.[\d]+)?%?/,
rposition=/^\w+/,
rpercent=/%$/,
_position=$.fn.position;
function getOffsets(offsets, width, height){
return [
parseFloat(offsets[ 0 ]) *(rpercent.test(offsets[ 0 ]) ? width / 100:1),
parseFloat(offsets[ 1 ]) *(rpercent.test(offsets[ 1 ]) ? height / 100:1)
];
}
function parseCss(element, property){
return parseInt($.css(element, property), 10)||0;
}
function getDimensions(elem){
var raw=elem[0];
if(raw.nodeType===9){
return {
width: elem.width(),
height: elem.height(),
offset: { top: 0, left: 0 }};}
if($.isWindow(raw)){
return {
width: elem.width(),
height: elem.height(),
offset: { top: elem.scrollTop(), left: elem.scrollLeft() }};}
if(raw.preventDefault){
return {
width: 0,
height: 0,
offset: { top: raw.pageY, left: raw.pageX }};}
return {
width: elem.outerWidth(),
height: elem.outerHeight(),
offset: elem.offset()
};}
$.position={
scrollbarWidth: function(){
if(cachedScrollbarWidth!==undefined){
return cachedScrollbarWidth;
}
var w1, w2,
div=$("<div style='display:block;position:absolute;width:50px;height:50px;overflow:hidden;'><div style='height:100px;width:auto;'></div></div>"),
innerDiv=div.children()[0];
$("body").append(div);
w1=innerDiv.offsetWidth;
div.css("overflow", "scroll");
w2=innerDiv.offsetWidth;
if(w1===w2){
w2=div[0].clientWidth;
}
div.remove();
return (cachedScrollbarWidth=w1 - w2);
},
getScrollInfo: function(within){
var overflowX=within.isWindow||within.isDocument ? "" :
within.element.css("overflow-x"),
overflowY=within.isWindow||within.isDocument ? "" :
within.element.css("overflow-y"),
hasOverflowX=overflowX==="scroll" ||
(overflowX==="auto"&&within.width < within.element[0].scrollWidth),
hasOverflowY=overflowY==="scroll" ||
(overflowY==="auto"&&within.height < within.element[0].scrollHeight);
return {
width: hasOverflowY ? $.position.scrollbarWidth():0,
height: hasOverflowX ? $.position.scrollbarWidth():0
};},
getWithinInfo: function(element){
var withinElement=$(element||window),
isWindow=$.isWindow(withinElement[0]),
isDocument = !!withinElement[ 0 ]&&withinElement[ 0 ].nodeType===9;
return {
element: withinElement,
isWindow: isWindow,
isDocument: isDocument,
offset: withinElement.offset()||{ left: 0, top: 0 },
scrollLeft: withinElement.scrollLeft(),
scrollTop: withinElement.scrollTop(),
width: isWindow||isDocument ? withinElement.width():withinElement.outerWidth(),
height: isWindow||isDocument ? withinElement.height():withinElement.outerHeight()
};}};
$.fn.position=function(options){
if(!options||!options.of){
return _position.apply(this, arguments);
}
options=$.extend({}, options);
var atOffset, targetWidth, targetHeight, targetOffset, basePosition, dimensions,
target=$(options.of),
within=$.position.getWithinInfo(options.within),
scrollInfo=$.position.getScrollInfo(within),
collision=(options.collision||"flip").split(" "),
offsets={};
dimensions=getDimensions(target);
if(target[0].preventDefault){
options.at="left top";
}
targetWidth=dimensions.width;
targetHeight=dimensions.height;
targetOffset=dimensions.offset;
basePosition=$.extend({}, targetOffset);
$.each([ "my", "at" ], function(){
var pos=(options[ this ]||"").split(" "),
horizontalOffset,
verticalOffset;
if(pos.length===1){
pos=rhorizontal.test(pos[ 0 ]) ?
pos.concat([ "center" ]) :
rvertical.test(pos[ 0 ]) ?
[ "center" ].concat(pos) :
[ "center", "center" ];
}
pos[ 0 ]=rhorizontal.test(pos[ 0 ]) ? pos[ 0 ]:"center";
pos[ 1 ]=rvertical.test(pos[ 1 ]) ? pos[ 1 ]:"center";
horizontalOffset=roffset.exec(pos[ 0 ]);
verticalOffset=roffset.exec(pos[ 1 ]);
offsets[ this ]=[
horizontalOffset ? horizontalOffset[ 0 ]:0,
verticalOffset ? verticalOffset[ 0 ]:0
];
options[ this ]=[
rposition.exec(pos[ 0 ])[ 0 ],
rposition.exec(pos[ 1 ])[ 0 ]
];
});
if(collision.length===1){
collision[ 1 ]=collision[ 0 ];
}
if(options.at[ 0 ]==="right"){
basePosition.left +=targetWidth;
}else if(options.at[ 0 ]==="center"){
basePosition.left +=targetWidth / 2;
}
if(options.at[ 1 ]==="bottom"){
basePosition.top +=targetHeight;
}else if(options.at[ 1 ]==="center"){
basePosition.top +=targetHeight / 2;
}
atOffset=getOffsets(offsets.at, targetWidth, targetHeight);
basePosition.left +=atOffset[ 0 ];
basePosition.top +=atOffset[ 1 ];
return this.each(function(){
var collisionPosition, using,
elem=$(this),
elemWidth=elem.outerWidth(),
elemHeight=elem.outerHeight(),
marginLeft=parseCss(this, "marginLeft"),
marginTop=parseCss(this, "marginTop"),
collisionWidth=elemWidth + marginLeft + parseCss(this, "marginRight") + scrollInfo.width,
collisionHeight=elemHeight + marginTop + parseCss(this, "marginBottom") + scrollInfo.height,
position=$.extend({}, basePosition),
myOffset=getOffsets(offsets.my, elem.outerWidth(), elem.outerHeight());
if(options.my[ 0 ]==="right"){
position.left -=elemWidth;
}else if(options.my[ 0 ]==="center"){
position.left -=elemWidth / 2;
}
if(options.my[ 1 ]==="bottom"){
position.top -=elemHeight;
}else if(options.my[ 1 ]==="center"){
position.top -=elemHeight / 2;
}
position.left +=myOffset[ 0 ];
position.top +=myOffset[ 1 ];
if(!supportsOffsetFractions){
position.left=round(position.left);
position.top=round(position.top);
}
collisionPosition={
marginLeft: marginLeft,
marginTop: marginTop
};
$.each([ "left", "top" ], function(i, dir){
if($.ui.position[ collision[ i ] ]){
$.ui.position[ collision[ i ] ][ dir ](position, {
targetWidth: targetWidth,
targetHeight: targetHeight,
elemWidth: elemWidth,
elemHeight: elemHeight,
collisionPosition: collisionPosition,
collisionWidth: collisionWidth,
collisionHeight: collisionHeight,
offset: [ atOffset[ 0 ] + myOffset[ 0 ], atOffset [ 1 ] + myOffset[ 1 ] ],
my: options.my,
at: options.at,
within: within,
elem: elem
});
}});
if(options.using){
using=function(props){
var left=targetOffset.left - position.left,
right=left + targetWidth - elemWidth,
top=targetOffset.top - position.top,
bottom=top + targetHeight - elemHeight,
feedback={
target: {
element: target,
left: targetOffset.left,
top: targetOffset.top,
width: targetWidth,
height: targetHeight
},
element: {
element: elem,
left: position.left,
top: position.top,
width: elemWidth,
height: elemHeight
},
horizontal: right < 0 ? "left":left > 0 ? "right":"center",
vertical: bottom < 0 ? "top":top > 0 ? "bottom":"middle"
};
if(targetWidth < elemWidth&&abs(left + right) < targetWidth){
feedback.horizontal="center";
}
if(targetHeight < elemHeight&&abs(top + bottom) < targetHeight){
feedback.vertical="middle";
}
if(max(abs(left), abs(right)) > max(abs(top), abs(bottom))){
feedback.important="horizontal";
}else{
feedback.important="vertical";
}
options.using.call(this, props, feedback);
};}
elem.offset($.extend(position, { using: using }));
});
};
$.ui.position={
fit: {
left: function(position, data){
var within=data.within,
withinOffset=within.isWindow ? within.scrollLeft:within.offset.left,
outerWidth=within.width,
collisionPosLeft=position.left - data.collisionPosition.marginLeft,
overLeft=withinOffset - collisionPosLeft,
overRight=collisionPosLeft + data.collisionWidth - outerWidth - withinOffset,
newOverRight;
if(data.collisionWidth > outerWidth){
if(overLeft > 0&&overRight <=0){
newOverRight=position.left + overLeft + data.collisionWidth - outerWidth - withinOffset;
position.left +=overLeft - newOverRight;
}else if(overRight > 0&&overLeft <=0){
position.left=withinOffset;
}else{
if(overLeft > overRight){
position.left=withinOffset + outerWidth - data.collisionWidth;
}else{
position.left=withinOffset;
}}
}else if(overLeft > 0){
position.left +=overLeft;
}else if(overRight > 0){
position.left -=overRight;
}else{
position.left=max(position.left - collisionPosLeft, position.left);
}},
top: function(position, data){
var within=data.within,
withinOffset=within.isWindow ? within.scrollTop:within.offset.top,
outerHeight=data.within.height,
collisionPosTop=position.top - data.collisionPosition.marginTop,
overTop=withinOffset - collisionPosTop,
overBottom=collisionPosTop + data.collisionHeight - outerHeight - withinOffset,
newOverBottom;
if(data.collisionHeight > outerHeight){
if(overTop > 0&&overBottom <=0){
newOverBottom=position.top + overTop + data.collisionHeight - outerHeight - withinOffset;
position.top +=overTop - newOverBottom;
}else if(overBottom > 0&&overTop <=0){
position.top=withinOffset;
}else{
if(overTop > overBottom){
position.top=withinOffset + outerHeight - data.collisionHeight;
}else{
position.top=withinOffset;
}}
}else if(overTop > 0){
position.top +=overTop;
}else if(overBottom > 0){
position.top -=overBottom;
}else{
position.top=max(position.top - collisionPosTop, position.top);
}}
},
flip: {
left: function(position, data){
var within=data.within,
withinOffset=within.offset.left + within.scrollLeft,
outerWidth=within.width,
offsetLeft=within.isWindow ? within.scrollLeft:within.offset.left,
collisionPosLeft=position.left - data.collisionPosition.marginLeft,
overLeft=collisionPosLeft - offsetLeft,
overRight=collisionPosLeft + data.collisionWidth - outerWidth - offsetLeft,
myOffset=data.my[ 0 ]==="left" ?
-data.elemWidth :
data.my[ 0 ]==="right" ?
data.elemWidth :
0,
atOffset=data.at[ 0 ]==="left" ?
data.targetWidth :
data.at[ 0 ]==="right" ?
-data.targetWidth :
0,
offset=-2 * data.offset[ 0 ],
newOverRight,
newOverLeft;
if(overLeft < 0){
newOverRight=position.left + myOffset + atOffset + offset + data.collisionWidth - outerWidth - withinOffset;
if(newOverRight < 0||newOverRight < abs(overLeft)){
position.left +=myOffset + atOffset + offset;
}}else if(overRight > 0){
newOverLeft=position.left - data.collisionPosition.marginLeft + myOffset + atOffset + offset - offsetLeft;
if(newOverLeft > 0||abs(newOverLeft) < overRight){
position.left +=myOffset + atOffset + offset;
}}
},
top: function(position, data){
var within=data.within,
withinOffset=within.offset.top + within.scrollTop,
outerHeight=within.height,
offsetTop=within.isWindow ? within.scrollTop:within.offset.top,
collisionPosTop=position.top - data.collisionPosition.marginTop,
overTop=collisionPosTop - offsetTop,
overBottom=collisionPosTop + data.collisionHeight - outerHeight - offsetTop,
top=data.my[ 1 ]==="top",
myOffset=top ?
-data.elemHeight :
data.my[ 1 ]==="bottom" ?
data.elemHeight :
0,
atOffset=data.at[ 1 ]==="top" ?
data.targetHeight :
data.at[ 1 ]==="bottom" ?
-data.targetHeight :
0,
offset=-2 * data.offset[ 1 ],
newOverTop,
newOverBottom;
if(overTop < 0){
newOverBottom=position.top + myOffset + atOffset + offset + data.collisionHeight - outerHeight - withinOffset;
if(newOverBottom < 0||newOverBottom < abs(overTop)){
position.top +=myOffset + atOffset + offset;
}}else if(overBottom > 0){
newOverTop=position.top - data.collisionPosition.marginTop + myOffset + atOffset + offset - offsetTop;
if(newOverTop > 0||abs(newOverTop) < overBottom){
position.top +=myOffset + atOffset + offset;
}}
}},
flipfit: {
left: function(){
$.ui.position.flip.left.apply(this, arguments);
$.ui.position.fit.left.apply(this, arguments);
},
top: function(){
$.ui.position.flip.top.apply(this, arguments);
$.ui.position.fit.top.apply(this, arguments);
}}
};
(function(){
var testElement, testElementParent, testElementStyle, offsetLeft, i,
body=document.getElementsByTagName("body")[ 0 ],
div=document.createElement("div");
testElement=document.createElement(body ? "div":"body");
testElementStyle={
visibility: "hidden",
width: 0,
height: 0,
border: 0,
margin: 0,
background: "none"
};
if(body){
$.extend(testElementStyle, {
position: "absolute",
left: "-1000px",
top: "-1000px"
});
}
for(i in testElementStyle){
testElement.style[ i ]=testElementStyle[ i ];
}
testElement.appendChild(div);
testElementParent=body||document.documentElement;
testElementParent.insertBefore(testElement, testElementParent.firstChild);
div.style.cssText="position: absolute; left: 10.7432222px;";
offsetLeft=$(div).offset().left;
supportsOffsetFractions=offsetLeft > 10&&offsetLeft < 11;
testElement.innerHTML="";
testElementParent.removeChild(testElement);
})();
})();
var position=$.ui.position;
$.widget("ui.draggable", $.ui.mouse, {
version: "1.11.4",
widgetEventPrefix: "drag",
options: {
addClasses: true,
appendTo: "parent",
axis: false,
connectToSortable: false,
containment: false,
cursor: "auto",
cursorAt: false,
grid: false,
handle: false,
helper: "original",
iframeFix: false,
opacity: false,
refreshPositions: false,
revert: false,
revertDuration: 500,
scope: "default",
scroll: true,
scrollSensitivity: 20,
scrollSpeed: 20,
snap: false,
snapMode: "both",
snapTolerance: 20,
stack: false,
zIndex: false,
drag: null,
start: null,
stop: null
},
_create: function(){
if(this.options.helper==="original"){
this._setPositionRelative();
}
if(this.options.addClasses){
this.element.addClass("ui-draggable");
}
if(this.options.disabled){
this.element.addClass("ui-draggable-disabled");
}
this._setHandleClassName();
this._mouseInit();
},
_setOption: function(key, value){
this._super(key, value);
if(key==="handle"){
this._removeHandleClassName();
this._setHandleClassName();
}},
_destroy: function(){
if(( this.helper||this.element).is(".ui-draggable-dragging")){
this.destroyOnClear=true;
return;
}
this.element.removeClass("ui-draggable ui-draggable-dragging ui-draggable-disabled");
this._removeHandleClassName();
this._mouseDestroy();
},
_mouseCapture: function(event){
var o=this.options;
this._blurActiveElement(event);
if(this.helper||o.disabled||$(event.target).closest(".ui-resizable-handle").length > 0){
return false;
}
this.handle=this._getHandle(event);
if(!this.handle){
return false;
}
this._blockFrames(o.iframeFix===true ? "iframe":o.iframeFix);
return true;
},
_blockFrames: function(selector){
this.iframeBlocks=this.document.find(selector).map(function(){
var iframe=$(this);
return $("<div>")
.css("position", "absolute")
.appendTo(iframe.parent())
.outerWidth(iframe.outerWidth())
.outerHeight(iframe.outerHeight())
.offset(iframe.offset())[ 0 ];
});
},
_unblockFrames: function(){
if(this.iframeBlocks){
this.iframeBlocks.remove();
delete this.iframeBlocks;
}},
_blurActiveElement: function(event){
var document=this.document[ 0 ];
if(!this.handleElement.is(event.target)){
return;
}
try {
if(document.activeElement&&document.activeElement.nodeName.toLowerCase()!=="body"){
$(document.activeElement).blur();
}} catch(error){}},
_mouseStart: function(event){
var o=this.options;
this.helper=this._createHelper(event);
this.helper.addClass("ui-draggable-dragging");
this._cacheHelperProportions();
if($.ui.ddmanager){
$.ui.ddmanager.current=this;
}
this._cacheMargins();
this.cssPosition=this.helper.css("position");
this.scrollParent=this.helper.scrollParent(true);
this.offsetParent=this.helper.offsetParent();
this.hasFixedAncestor=this.helper.parents().filter(function(){
return $(this).css("position")==="fixed";
}).length > 0;
this.positionAbs=this.element.offset();
this._refreshOffsets(event);
this.originalPosition=this.position=this._generatePosition(event, false);
this.originalPageX=event.pageX;
this.originalPageY=event.pageY;
(o.cursorAt&&this._adjustOffsetFromHelper(o.cursorAt));
this._setContainment();
if(this._trigger("start", event)===false){
this._clear();
return false;
}
this._cacheHelperProportions();
if($.ui.ddmanager&&!o.dropBehaviour){
$.ui.ddmanager.prepareOffsets(this, event);
}
this._normalizeRightBottom();
this._mouseDrag(event, true);
if($.ui.ddmanager){
$.ui.ddmanager.dragStart(this, event);
}
return true;
},
_refreshOffsets: function(event){
this.offset={
top: this.positionAbs.top - this.margins.top,
left: this.positionAbs.left - this.margins.left,
scroll: false,
parent: this._getParentOffset(),
relative: this._getRelativeOffset()
};
this.offset.click={
left: event.pageX - this.offset.left,
top: event.pageY - this.offset.top
};},
_mouseDrag: function(event, noPropagation){
if(this.hasFixedAncestor){
this.offset.parent=this._getParentOffset();
}
this.position=this._generatePosition(event, true);
this.positionAbs=this._convertPositionTo("absolute");
if(!noPropagation){
var ui=this._uiHash();
if(this._trigger("drag", event, ui)===false){
this._mouseUp({});
return false;
}
this.position=ui.position;
}
this.helper[ 0 ].style.left=this.position.left + "px";
this.helper[ 0 ].style.top=this.position.top + "px";
if($.ui.ddmanager){
$.ui.ddmanager.drag(this, event);
}
return false;
},
_mouseStop: function(event){
var that=this,
dropped=false;
if($.ui.ddmanager&&!this.options.dropBehaviour){
dropped=$.ui.ddmanager.drop(this, event);
}
if(this.dropped){
dropped=this.dropped;
this.dropped=false;
}
if((this.options.revert==="invalid"&&!dropped)||(this.options.revert==="valid"&&dropped)||this.options.revert===true||($.isFunction(this.options.revert)&&this.options.revert.call(this.element, dropped))){
$(this.helper).animate(this.originalPosition, parseInt(this.options.revertDuration, 10), function(){
if(that._trigger("stop", event)!==false){
that._clear();
}});
}else{
if(this._trigger("stop", event)!==false){
this._clear();
}}
return false;
},
_mouseUp: function(event){
this._unblockFrames();
if($.ui.ddmanager){
$.ui.ddmanager.dragStop(this, event);
}
if(this.handleElement.is(event.target)){
this.element.focus();
}
return $.ui.mouse.prototype._mouseUp.call(this, event);
},
cancel: function(){
if(this.helper.is(".ui-draggable-dragging")){
this._mouseUp({});
}else{
this._clear();
}
return this;
},
_getHandle: function(event){
return this.options.handle ?
!!$(event.target).closest(this.element.find(this.options.handle)).length :
true;
},
_setHandleClassName: function(){
this.handleElement=this.options.handle ?
this.element.find(this.options.handle):this.element;
this.handleElement.addClass("ui-draggable-handle");
},
_removeHandleClassName: function(){
this.handleElement.removeClass("ui-draggable-handle");
},
_createHelper: function(event){
var o=this.options,
helperIsFunction=$.isFunction(o.helper),
helper=helperIsFunction ?
$(o.helper.apply(this.element[ 0 ], [ event ])) :
(o.helper==="clone" ?
this.element.clone().removeAttr("id") :
this.element);
if(!helper.parents("body").length){
helper.appendTo((o.appendTo==="parent" ? this.element[0].parentNode:o.appendTo));
}
if(helperIsFunction&&helper[ 0 ]===this.element[ 0 ]){
this._setPositionRelative();
}
if(helper[0]!==this.element[0]&&!(/(fixed|absolute)/).test(helper.css("position"))){
helper.css("position", "absolute");
}
return helper;
},
_setPositionRelative: function(){
if(!(/^(?:r|a|f)/).test(this.element.css("position"))){
this.element[ 0 ].style.position="relative";
}},
_adjustOffsetFromHelper: function(obj){
if(typeof obj==="string"){
obj=obj.split(" ");
}
if($.isArray(obj)){
obj={ left: +obj[0], top: +obj[1]||0 };}
if("left" in obj){
this.offset.click.left=obj.left + this.margins.left;
}
if("right" in obj){
this.offset.click.left=this.helperProportions.width - obj.right + this.margins.left;
}
if("top" in obj){
this.offset.click.top=obj.top + this.margins.top;
}
if("bottom" in obj){
this.offset.click.top=this.helperProportions.height - obj.bottom + this.margins.top;
}},
_isRootNode: function(element){
return(/(html|body)/i).test(element.tagName)||element===this.document[ 0 ];
},
_getParentOffset: function(){
var po=this.offsetParent.offset(),
document=this.document[ 0 ];
if(this.cssPosition==="absolute"&&this.scrollParent[0]!==document&&$.contains(this.scrollParent[0], this.offsetParent[0])){
po.left +=this.scrollParent.scrollLeft();
po.top +=this.scrollParent.scrollTop();
}
if(this._isRootNode(this.offsetParent[ 0 ])){
po={ top: 0, left: 0 };}
return {
top: po.top + (parseInt(this.offsetParent.css("borderTopWidth"), 10)||0),
left: po.left + (parseInt(this.offsetParent.css("borderLeftWidth"), 10)||0)
};},
_getRelativeOffset: function(){
if(this.cssPosition!=="relative"){
return { top: 0, left: 0 };}
var p=this.element.position(),
scrollIsRootNode=this._isRootNode(this.scrollParent[ 0 ]);
return {
top: p.top -(parseInt(this.helper.css("top"), 10)||0) +(!scrollIsRootNode ? this.scrollParent.scrollTop():0),
left: p.left -(parseInt(this.helper.css("left"), 10)||0) +(!scrollIsRootNode ? this.scrollParent.scrollLeft():0)
};},
_cacheMargins: function(){
this.margins={
left: (parseInt(this.element.css("marginLeft"), 10)||0),
top: (parseInt(this.element.css("marginTop"), 10)||0),
right: (parseInt(this.element.css("marginRight"), 10)||0),
bottom: (parseInt(this.element.css("marginBottom"), 10)||0)
};},
_cacheHelperProportions: function(){
this.helperProportions={
width: this.helper.outerWidth(),
height: this.helper.outerHeight()
};},
_setContainment: function(){
var isUserScrollable, c, ce,
o=this.options,
document=this.document[ 0 ];
this.relativeContainer=null;
if(!o.containment){
this.containment=null;
return;
}
if(o.containment==="window"){
this.containment=[
$(window).scrollLeft() - this.offset.relative.left - this.offset.parent.left,
$(window).scrollTop() - this.offset.relative.top - this.offset.parent.top,
$(window).scrollLeft() + $(window).width() - this.helperProportions.width - this.margins.left,
$(window).scrollTop() +($(window).height()||document.body.parentNode.scrollHeight) - this.helperProportions.height - this.margins.top
];
return;
}
if(o.containment==="document"){
this.containment=[
0,
0,
$(document).width() - this.helperProportions.width - this.margins.left,
($(document).height()||document.body.parentNode.scrollHeight) - this.helperProportions.height - this.margins.top
];
return;
}
if(o.containment.constructor===Array){
this.containment=o.containment;
return;
}
if(o.containment==="parent"){
o.containment=this.helper[ 0 ].parentNode;
}
c=$(o.containment);
ce=c[ 0 ];
if(!ce){
return;
}
isUserScrollable=/(scroll|auto)/.test(c.css("overflow"));
this.containment=[
(parseInt(c.css("borderLeftWidth"), 10)||0) +(parseInt(c.css("paddingLeft"), 10)||0),
(parseInt(c.css("borderTopWidth"), 10)||0) +(parseInt(c.css("paddingTop"), 10)||0),
(isUserScrollable ? Math.max(ce.scrollWidth, ce.offsetWidth):ce.offsetWidth) -
(parseInt(c.css("borderRightWidth"), 10)||0) -
(parseInt(c.css("paddingRight"), 10)||0) -
this.helperProportions.width -
this.margins.left -
this.margins.right,
(isUserScrollable ? Math.max(ce.scrollHeight, ce.offsetHeight):ce.offsetHeight) -
(parseInt(c.css("borderBottomWidth"), 10)||0) -
(parseInt(c.css("paddingBottom"), 10)||0) -
this.helperProportions.height -
this.margins.top -
this.margins.bottom
];
this.relativeContainer=c;
},
_convertPositionTo: function(d, pos){
if(!pos){
pos=this.position;
}
var mod=d==="absolute" ? 1:-1,
scrollIsRootNode=this._isRootNode(this.scrollParent[ 0 ]);
return {
top: (
pos.top	+
this.offset.relative.top * mod +
this.offset.parent.top * mod -
(( this.cssPosition==="fixed" ? -this.offset.scroll.top:(scrollIsRootNode ? 0:this.offset.scroll.top)) * mod)
),
left: (
pos.left +
this.offset.relative.left * mod +
this.offset.parent.left * mod	-
(( this.cssPosition==="fixed" ? -this.offset.scroll.left:(scrollIsRootNode ? 0:this.offset.scroll.left)) * mod)
)
};},
_generatePosition: function(event, constrainPosition){
var containment, co, top, left,
o=this.options,
scrollIsRootNode=this._isRootNode(this.scrollParent[ 0 ]),
pageX=event.pageX,
pageY=event.pageY;
if(!scrollIsRootNode||!this.offset.scroll){
this.offset.scroll={
top: this.scrollParent.scrollTop(),
left: this.scrollParent.scrollLeft()
};}
if(constrainPosition){
if(this.containment){
if(this.relativeContainer){
co=this.relativeContainer.offset();
containment=[
this.containment[ 0 ] + co.left,
this.containment[ 1 ] + co.top,
this.containment[ 2 ] + co.left,
this.containment[ 3 ] + co.top
];
}else{
containment=this.containment;
}
if(event.pageX - this.offset.click.left < containment[0]){
pageX=containment[0] + this.offset.click.left;
}
if(event.pageY - this.offset.click.top < containment[1]){
pageY=containment[1] + this.offset.click.top;
}
if(event.pageX - this.offset.click.left > containment[2]){
pageX=containment[2] + this.offset.click.left;
}
if(event.pageY - this.offset.click.top > containment[3]){
pageY=containment[3] + this.offset.click.top;
}}
if(o.grid){
top=o.grid[1] ? this.originalPageY + Math.round((pageY - this.originalPageY) / o.grid[1]) * o.grid[1]:this.originalPageY;
pageY=containment ? ((top - this.offset.click.top >=containment[1]||top - this.offset.click.top > containment[3]) ? top:((top - this.offset.click.top >=containment[1]) ? top - o.grid[1]:top + o.grid[1])):top;
left=o.grid[0] ? this.originalPageX + Math.round((pageX - this.originalPageX) / o.grid[0]) * o.grid[0]:this.originalPageX;
pageX=containment ? ((left - this.offset.click.left >=containment[0]||left - this.offset.click.left > containment[2]) ? left:((left - this.offset.click.left >=containment[0]) ? left - o.grid[0]:left + o.grid[0])):left;
}
if(o.axis==="y"){
pageX=this.originalPageX;
}
if(o.axis==="x"){
pageY=this.originalPageY;
}}
return {
top: (
pageY -
this.offset.click.top	-
this.offset.relative.top -
this.offset.parent.top +
(this.cssPosition==="fixed" ? -this.offset.scroll.top:(scrollIsRootNode ? 0:this.offset.scroll.top))
),
left: (
pageX -
this.offset.click.left -
this.offset.relative.left -
this.offset.parent.left +
(this.cssPosition==="fixed" ? -this.offset.scroll.left:(scrollIsRootNode ? 0:this.offset.scroll.left))
)
};},
_clear: function(){
this.helper.removeClass("ui-draggable-dragging");
if(this.helper[0]!==this.element[0]&&!this.cancelHelperRemoval){
this.helper.remove();
}
this.helper=null;
this.cancelHelperRemoval=false;
if(this.destroyOnClear){
this.destroy();
}},
_normalizeRightBottom: function(){
if(this.options.axis!=="y"&&this.helper.css("right")!=="auto"){
this.helper.width(this.helper.width());
this.helper.css("right", "auto");
}
if(this.options.axis!=="x"&&this.helper.css("bottom")!=="auto"){
this.helper.height(this.helper.height());
this.helper.css("bottom", "auto");
}},
_trigger: function(type, event, ui){
ui=ui||this._uiHash();
$.ui.plugin.call(this, type, [ event, ui, this ], true);
if(/^(drag|start|stop)/.test(type)){
this.positionAbs=this._convertPositionTo("absolute");
ui.offset=this.positionAbs;
}
return $.Widget.prototype._trigger.call(this, type, event, ui);
},
plugins: {},
_uiHash: function(){
return {
helper: this.helper,
position: this.position,
originalPosition: this.originalPosition,
offset: this.positionAbs
};}});
$.ui.plugin.add("draggable", "connectToSortable", {
start: function(event, ui, draggable){
var uiSortable=$.extend({}, ui, {
item: draggable.element
});
draggable.sortables=[];
$(draggable.options.connectToSortable).each(function(){
var sortable=$(this).sortable("instance");
if(sortable&&!sortable.options.disabled){
draggable.sortables.push(sortable);
sortable.refreshPositions();
sortable._trigger("activate", event, uiSortable);
}});
},
stop: function(event, ui, draggable){
var uiSortable=$.extend({}, ui, {
item: draggable.element
});
draggable.cancelHelperRemoval=false;
$.each(draggable.sortables, function(){
var sortable=this;
if(sortable.isOver){
sortable.isOver=0;
draggable.cancelHelperRemoval=true;
sortable.cancelHelperRemoval=false;
sortable._storedCSS={
position: sortable.placeholder.css("position"),
top: sortable.placeholder.css("top"),
left: sortable.placeholder.css("left")
};
sortable._mouseStop(event);
sortable.options.helper=sortable.options._helper;
}else{
sortable.cancelHelperRemoval=true;
sortable._trigger("deactivate", event, uiSortable);
}});
},
drag: function(event, ui, draggable){
$.each(draggable.sortables, function(){
var innermostIntersecting=false,
sortable=this;
sortable.positionAbs=draggable.positionAbs;
sortable.helperProportions=draggable.helperProportions;
sortable.offset.click=draggable.offset.click;
if(sortable._intersectsWith(sortable.containerCache)){
innermostIntersecting=true;
$.each(draggable.sortables, function(){
this.positionAbs=draggable.positionAbs;
this.helperProportions=draggable.helperProportions;
this.offset.click=draggable.offset.click;
if(this!==sortable &&
this._intersectsWith(this.containerCache) &&
$.contains(sortable.element[ 0 ], this.element[ 0 ])){
innermostIntersecting=false;
}
return innermostIntersecting;
});
}
if(innermostIntersecting){
if(!sortable.isOver){
sortable.isOver=1;
draggable._parent=ui.helper.parent();
sortable.currentItem=ui.helper
.appendTo(sortable.element)
.data("ui-sortable-item", true);
sortable.options._helper=sortable.options.helper;
sortable.options.helper=function(){
return ui.helper[ 0 ];
};
event.target=sortable.currentItem[ 0 ];
sortable._mouseCapture(event, true);
sortable._mouseStart(event, true, true);
sortable.offset.click.top=draggable.offset.click.top;
sortable.offset.click.left=draggable.offset.click.left;
sortable.offset.parent.left -=draggable.offset.parent.left -
sortable.offset.parent.left;
sortable.offset.parent.top -=draggable.offset.parent.top -
sortable.offset.parent.top;
draggable._trigger("toSortable", event);
draggable.dropped=sortable.element;
$.each(draggable.sortables, function(){
this.refreshPositions();
});
draggable.currentItem=draggable.element;
sortable.fromOutside=draggable;
}
if(sortable.currentItem){
sortable._mouseDrag(event);
ui.position=sortable.position;
}}else{
if(sortable.isOver){
sortable.isOver=0;
sortable.cancelHelperRemoval=true;
sortable.options._revert=sortable.options.revert;
sortable.options.revert=false;
sortable._trigger("out", event, sortable._uiHash(sortable));
sortable._mouseStop(event, true);
sortable.options.revert=sortable.options._revert;
sortable.options.helper=sortable.options._helper;
if(sortable.placeholder){
sortable.placeholder.remove();
}
ui.helper.appendTo(draggable._parent);
draggable._refreshOffsets(event);
ui.position=draggable._generatePosition(event, true);
draggable._trigger("fromSortable", event);
draggable.dropped=false;
$.each(draggable.sortables, function(){
this.refreshPositions();
});
}}
});
}});
$.ui.plugin.add("draggable", "cursor", {
start: function(event, ui, instance){
var t=$("body"),
o=instance.options;
if(t.css("cursor")){
o._cursor=t.css("cursor");
}
t.css("cursor", o.cursor);
},
stop: function(event, ui, instance){
var o=instance.options;
if(o._cursor){
$("body").css("cursor", o._cursor);
}}
});
$.ui.plugin.add("draggable", "opacity", {
start: function(event, ui, instance){
var t=$(ui.helper),
o=instance.options;
if(t.css("opacity")){
o._opacity=t.css("opacity");
}
t.css("opacity", o.opacity);
},
stop: function(event, ui, instance){
var o=instance.options;
if(o._opacity){
$(ui.helper).css("opacity", o._opacity);
}}
});
$.ui.plugin.add("draggable", "scroll", {
start: function(event, ui, i){
if(!i.scrollParentNotHidden){
i.scrollParentNotHidden=i.helper.scrollParent(false);
}
if(i.scrollParentNotHidden[ 0 ]!==i.document[ 0 ]&&i.scrollParentNotHidden[ 0 ].tagName!=="HTML"){
i.overflowOffset=i.scrollParentNotHidden.offset();
}},
drag: function(event, ui, i){
var o=i.options,
scrolled=false,
scrollParent=i.scrollParentNotHidden[ 0 ],
document=i.document[ 0 ];
if(scrollParent!==document&&scrollParent.tagName!=="HTML"){
if(!o.axis||o.axis!=="x"){
if(( i.overflowOffset.top + scrollParent.offsetHeight) - event.pageY < o.scrollSensitivity){
scrollParent.scrollTop=scrolled=scrollParent.scrollTop + o.scrollSpeed;
}else if(event.pageY - i.overflowOffset.top < o.scrollSensitivity){
scrollParent.scrollTop=scrolled=scrollParent.scrollTop - o.scrollSpeed;
}}
if(!o.axis||o.axis!=="y"){
if(( i.overflowOffset.left + scrollParent.offsetWidth) - event.pageX < o.scrollSensitivity){
scrollParent.scrollLeft=scrolled=scrollParent.scrollLeft + o.scrollSpeed;
}else if(event.pageX - i.overflowOffset.left < o.scrollSensitivity){
scrollParent.scrollLeft=scrolled=scrollParent.scrollLeft - o.scrollSpeed;
}}
}else{
if(!o.axis||o.axis!=="x"){
if(event.pageY - $(document).scrollTop() < o.scrollSensitivity){
scrolled=$(document).scrollTop($(document).scrollTop() - o.scrollSpeed);
}else if($(window).height() - (event.pageY - $(document).scrollTop()) < o.scrollSensitivity){
scrolled=$(document).scrollTop($(document).scrollTop() + o.scrollSpeed);
}}
if(!o.axis||o.axis!=="y"){
if(event.pageX - $(document).scrollLeft() < o.scrollSensitivity){
scrolled=$(document).scrollLeft($(document).scrollLeft() - o.scrollSpeed);
}else if($(window).width() - (event.pageX - $(document).scrollLeft()) < o.scrollSensitivity){
scrolled=$(document).scrollLeft($(document).scrollLeft() + o.scrollSpeed);
}}
}
if(scrolled!==false&&$.ui.ddmanager&&!o.dropBehaviour){
$.ui.ddmanager.prepareOffsets(i, event);
}}
});
$.ui.plugin.add("draggable", "snap", {
start: function(event, ui, i){
var o=i.options;
i.snapElements=[];
$(o.snap.constructor!==String ?(o.snap.items||":data(ui-draggable)"):o.snap).each(function(){
var $t=$(this),
$o=$t.offset();
if(this!==i.element[0]){
i.snapElements.push({
item: this,
width: $t.outerWidth(), height: $t.outerHeight(),
top: $o.top, left: $o.left
});
}});
},
drag: function(event, ui, inst){
var ts, bs, ls, rs, l, r, t, b, i, first,
o=inst.options,
d=o.snapTolerance,
x1=ui.offset.left, x2=x1 + inst.helperProportions.width,
y1=ui.offset.top, y2=y1 + inst.helperProportions.height;
for (i=inst.snapElements.length - 1; i >=0; i--){
l=inst.snapElements[i].left - inst.margins.left;
r=l + inst.snapElements[i].width;
t=inst.snapElements[i].top - inst.margins.top;
b=t + inst.snapElements[i].height;
if(x2 < l - d||x1 > r + d||y2 < t - d||y1 > b + d||!$.contains(inst.snapElements[ i ].item.ownerDocument, inst.snapElements[ i ].item)){
if(inst.snapElements[i].snapping){
(inst.options.snap.release&&inst.options.snap.release.call(inst.element, event, $.extend(inst._uiHash(), { snapItem: inst.snapElements[i].item })));
}
inst.snapElements[i].snapping=false;
continue;
}
if(o.snapMode!=="inner"){
ts=Math.abs(t - y2) <=d;
bs=Math.abs(b - y1) <=d;
ls=Math.abs(l - x2) <=d;
rs=Math.abs(r - x1) <=d;
if(ts){
ui.position.top=inst._convertPositionTo("relative", { top: t - inst.helperProportions.height, left: 0 }).top;
}
if(bs){
ui.position.top=inst._convertPositionTo("relative", { top: b, left: 0 }).top;
}
if(ls){
ui.position.left=inst._convertPositionTo("relative", { top: 0, left: l - inst.helperProportions.width }).left;
}
if(rs){
ui.position.left=inst._convertPositionTo("relative", { top: 0, left: r }).left;
}}
first=(ts||bs||ls||rs);
if(o.snapMode!=="outer"){
ts=Math.abs(t - y1) <=d;
bs=Math.abs(b - y2) <=d;
ls=Math.abs(l - x1) <=d;
rs=Math.abs(r - x2) <=d;
if(ts){
ui.position.top=inst._convertPositionTo("relative", { top: t, left: 0 }).top;
}
if(bs){
ui.position.top=inst._convertPositionTo("relative", { top: b - inst.helperProportions.height, left: 0 }).top;
}
if(ls){
ui.position.left=inst._convertPositionTo("relative", { top: 0, left: l }).left;
}
if(rs){
ui.position.left=inst._convertPositionTo("relative", { top: 0, left: r - inst.helperProportions.width }).left;
}}
if(!inst.snapElements[i].snapping&&(ts||bs||ls||rs||first)){
(inst.options.snap.snap&&inst.options.snap.snap.call(inst.element, event, $.extend(inst._uiHash(), { snapItem: inst.snapElements[i].item })));
}
inst.snapElements[i].snapping=(ts||bs||ls||rs||first);
}}
});
$.ui.plugin.add("draggable", "stack", {
start: function(event, ui, instance){
var min,
o=instance.options,
group=$.makeArray($(o.stack)).sort(function(a, b){
return (parseInt($(a).css("zIndex"), 10)||0) - (parseInt($(b).css("zIndex"), 10)||0);
});
if(!group.length){ return; }
min=parseInt($(group[0]).css("zIndex"), 10)||0;
$(group).each(function(i){
$(this).css("zIndex", min + i);
});
this.css("zIndex", (min + group.length));
}});
$.ui.plugin.add("draggable", "zIndex", {
start: function(event, ui, instance){
var t=$(ui.helper),
o=instance.options;
if(t.css("zIndex")){
o._zIndex=t.css("zIndex");
}
t.css("zIndex", o.zIndex);
},
stop: function(event, ui, instance){
var o=instance.options;
if(o._zIndex){
$(ui.helper).css("zIndex", o._zIndex);
}}
});
var draggable=$.ui.draggable;
$.widget("ui.droppable", {
version: "1.11.4",
widgetEventPrefix: "drop",
options: {
accept: "*",
activeClass: false,
addClasses: true,
greedy: false,
hoverClass: false,
scope: "default",
tolerance: "intersect",
activate: null,
deactivate: null,
drop: null,
out: null,
over: null
},
_create: function(){
var proportions,
o=this.options,
accept=o.accept;
this.isover=false;
this.isout=true;
this.accept=$.isFunction(accept) ? accept:function(d){
return d.is(accept);
};
this.proportions=function(){
if(arguments.length){
proportions=arguments[ 0 ];
}else{
return proportions ?
proportions :
proportions={
width: this.element[ 0 ].offsetWidth,
height: this.element[ 0 ].offsetHeight
};}};
this._addToManager(o.scope);
o.addClasses&&this.element.addClass("ui-droppable");
},
_addToManager: function(scope){
$.ui.ddmanager.droppables[ scope ]=$.ui.ddmanager.droppables[ scope ]||[];
$.ui.ddmanager.droppables[ scope ].push(this);
},
_splice: function(drop){
var i=0;
for(; i < drop.length; i++){
if(drop[ i ]===this){
drop.splice(i, 1);
}}
},
_destroy: function(){
var drop=$.ui.ddmanager.droppables[ this.options.scope ];
this._splice(drop);
this.element.removeClass("ui-droppable ui-droppable-disabled");
},
_setOption: function(key, value){
if(key==="accept"){
this.accept=$.isFunction(value) ? value:function(d){
return d.is(value);
};}else if(key==="scope"){
var drop=$.ui.ddmanager.droppables[ this.options.scope ];
this._splice(drop);
this._addToManager(value);
}
this._super(key, value);
},
_activate: function(event){
var draggable=$.ui.ddmanager.current;
if(this.options.activeClass){
this.element.addClass(this.options.activeClass);
}
if(draggable){
this._trigger("activate", event, this.ui(draggable));
}},
_deactivate: function(event){
var draggable=$.ui.ddmanager.current;
if(this.options.activeClass){
this.element.removeClass(this.options.activeClass);
}
if(draggable){
this._trigger("deactivate", event, this.ui(draggable));
}},
_over: function(event){
var draggable=$.ui.ddmanager.current;
if(!draggable||(draggable.currentItem||draggable.element)[ 0 ]===this.element[ 0 ]){
return;
}
if(this.accept.call(this.element[ 0 ],(draggable.currentItem||draggable.element))){
if(this.options.hoverClass){
this.element.addClass(this.options.hoverClass);
}
this._trigger("over", event, this.ui(draggable));
}},
_out: function(event){
var draggable=$.ui.ddmanager.current;
if(!draggable||(draggable.currentItem||draggable.element)[ 0 ]===this.element[ 0 ]){
return;
}
if(this.accept.call(this.element[ 0 ],(draggable.currentItem||draggable.element))){
if(this.options.hoverClass){
this.element.removeClass(this.options.hoverClass);
}
this._trigger("out", event, this.ui(draggable));
}},
_drop: function(event, custom){
var draggable=custom||$.ui.ddmanager.current,
childrenIntersection=false;
if(!draggable||(draggable.currentItem||draggable.element)[ 0 ]===this.element[ 0 ]){
return false;
}
this.element.find(":data(ui-droppable)").not(".ui-draggable-dragging").each(function(){
var inst=$(this).droppable("instance");
if(inst.options.greedy &&
!inst.options.disabled &&
inst.options.scope===draggable.options.scope &&
inst.accept.call(inst.element[ 0 ],(draggable.currentItem||draggable.element)) &&
$.ui.intersect(draggable, $.extend(inst, { offset: inst.element.offset() }), inst.options.tolerance, event)
){ childrenIntersection=true; return false; }});
if(childrenIntersection){
return false;
}
if(this.accept.call(this.element[ 0 ],(draggable.currentItem||draggable.element))){
if(this.options.activeClass){
this.element.removeClass(this.options.activeClass);
}
if(this.options.hoverClass){
this.element.removeClass(this.options.hoverClass);
}
this._trigger("drop", event, this.ui(draggable));
return this.element;
}
return false;
},
ui: function(c){
return {
draggable:(c.currentItem||c.element),
helper: c.helper,
position: c.position,
offset: c.positionAbs
};}});
$.ui.intersect=(function(){
function isOverAxis(x, reference, size){
return(x >=reference)&&(x <(reference + size));
}
return function(draggable, droppable, toleranceMode, event){
if(!droppable.offset){
return false;
}
var x1=(draggable.positionAbs||draggable.position.absolute).left + draggable.margins.left,
y1=(draggable.positionAbs||draggable.position.absolute).top + draggable.margins.top,
x2=x1 + draggable.helperProportions.width,
y2=y1 + draggable.helperProportions.height,
l=droppable.offset.left,
t=droppable.offset.top,
r=l + droppable.proportions().width,
b=t + droppable.proportions().height;
switch(toleranceMode){
case "fit":
return(l <=x1&&x2 <=r&&t <=y1&&y2 <=b);
case "intersect":
return(l < x1 +(draggable.helperProportions.width / 2) &&
x2 -(draggable.helperProportions.width / 2) < r &&
t < y1 +(draggable.helperProportions.height / 2) &&
y2 -(draggable.helperProportions.height / 2) < b);
case "pointer":
return isOverAxis(event.pageY, t, droppable.proportions().height)&&isOverAxis(event.pageX, l, droppable.proportions().width);
case "touch":
return (
(y1 >=t&&y1 <=b) ||
(y2 >=t&&y2 <=b) ||
(y1 < t&&y2 > b)
)&&(
(x1 >=l&&x1 <=r) ||
(x2 >=l&&x2 <=r) ||
(x1 < l&&x2 > r)
);
default:
return false;
}};})();
$.ui.ddmanager={
current: null,
droppables: { "default": [] },
prepareOffsets: function(t, event){
var i, j,
m=$.ui.ddmanager.droppables[ t.options.scope ]||[],
type=event ? event.type:null,
list=(t.currentItem||t.element).find(":data(ui-droppable)").addBack();
droppablesLoop: for(i=0; i < m.length; i++){
if(m[ i ].options.disabled||(t&&!m[ i ].accept.call(m[ i ].element[ 0 ],(t.currentItem||t.element)))){
continue;
}
for(j=0; j < list.length; j++){
if(list[ j ]===m[ i ].element[ 0 ]){
m[ i ].proportions().height=0;
continue droppablesLoop;
}}
m[ i ].visible=m[ i ].element.css("display")!=="none";
if(!m[ i ].visible){
continue;
}
if(type==="mousedown"){
m[ i ]._activate.call(m[ i ], event);
}
m[ i ].offset=m[ i ].element.offset();
m[ i ].proportions({ width: m[ i ].element[ 0 ].offsetWidth, height: m[ i ].element[ 0 ].offsetHeight });
}},
drop: function(draggable, event){
var dropped=false;
$.each(( $.ui.ddmanager.droppables[ draggable.options.scope ]||[]).slice(), function(){
if(!this.options){
return;
}
if(!this.options.disabled&&this.visible&&$.ui.intersect(draggable, this, this.options.tolerance, event)){
dropped=this._drop.call(this, event)||dropped;
}
if(!this.options.disabled&&this.visible&&this.accept.call(this.element[ 0 ],(draggable.currentItem||draggable.element))){
this.isout=true;
this.isover=false;
this._deactivate.call(this, event);
}});
return dropped;
},
dragStart: function(draggable, event){
draggable.element.parentsUntil("body").bind("scroll.droppable", function(){
if(!draggable.options.refreshPositions){
$.ui.ddmanager.prepareOffsets(draggable, event);
}});
},
drag: function(draggable, event){
if(draggable.options.refreshPositions){
$.ui.ddmanager.prepareOffsets(draggable, event);
}
$.each($.ui.ddmanager.droppables[ draggable.options.scope ]||[], function(){
if(this.options.disabled||this.greedyChild||!this.visible){
return;
}
var parentInstance, scope, parent,
intersects=$.ui.intersect(draggable, this, this.options.tolerance, event),
c = !intersects&&this.isover ? "isout":(intersects&&!this.isover ? "isover":null);
if(!c){
return;
}
if(this.options.greedy){
scope=this.options.scope;
parent=this.element.parents(":data(ui-droppable)").filter(function(){
return $(this).droppable("instance").options.scope===scope;
});
if(parent.length){
parentInstance=$(parent[ 0 ]).droppable("instance");
parentInstance.greedyChild=(c==="isover");
}}
if(parentInstance&&c==="isover"){
parentInstance.isover=false;
parentInstance.isout=true;
parentInstance._out.call(parentInstance, event);
}
this[ c ]=true;
this[c==="isout" ? "isover":"isout"]=false;
this[c==="isover" ? "_over":"_out"].call(this, event);
if(parentInstance&&c==="isout"){
parentInstance.isout=false;
parentInstance.isover=true;
parentInstance._over.call(parentInstance, event);
}});
},
dragStop: function(draggable, event){
draggable.element.parentsUntil("body").unbind("scroll.droppable");
if(!draggable.options.refreshPositions){
$.ui.ddmanager.prepareOffsets(draggable, event);
}}
};
var droppable=$.ui.droppable;
$.widget("ui.resizable", $.ui.mouse, {
version: "1.11.4",
widgetEventPrefix: "resize",
options: {
alsoResize: false,
animate: false,
animateDuration: "slow",
animateEasing: "swing",
aspectRatio: false,
autoHide: false,
containment: false,
ghost: false,
grid: false,
handles: "e,s,se",
helper: false,
maxHeight: null,
maxWidth: null,
minHeight: 10,
minWidth: 10,
zIndex: 90,
resize: null,
start: null,
stop: null
},
_num: function(value){
return parseInt(value, 10)||0;
},
_isNumber: function(value){
return !isNaN(parseInt(value, 10));
},
_hasScroll: function(el, a){
if($(el).css("overflow")==="hidden"){
return false;
}
var scroll=(a&&a==="left") ? "scrollLeft":"scrollTop",
has=false;
if(el[ scroll ] > 0){
return true;
}
el[ scroll ]=1;
has=(el[ scroll ] > 0);
el[ scroll ]=0;
return has;
},
_create: function(){
var n, i, handle, axis, hname,
that=this,
o=this.options;
this.element.addClass("ui-resizable");
$.extend(this, {
_aspectRatio: !!(o.aspectRatio),
aspectRatio: o.aspectRatio,
originalElement: this.element,
_proportionallyResizeElements: [],
_helper: o.helper||o.ghost||o.animate ? o.helper||"ui-resizable-helper":null
});
if(this.element[0].nodeName.match(/^(canvas|textarea|input|select|button|img)$/i)){
this.element.wrap($("<div class='ui-wrapper' style='overflow: hidden;'></div>").css({
position: this.element.css("position"),
width: this.element.outerWidth(),
height: this.element.outerHeight(),
top: this.element.css("top"),
left: this.element.css("left")
})
);
this.element=this.element.parent().data("ui-resizable", this.element.resizable("instance")
);
this.elementIsWrapper=true;
this.element.css({
marginLeft: this.originalElement.css("marginLeft"),
marginTop: this.originalElement.css("marginTop"),
marginRight: this.originalElement.css("marginRight"),
marginBottom: this.originalElement.css("marginBottom")
});
this.originalElement.css({
marginLeft: 0,
marginTop: 0,
marginRight: 0,
marginBottom: 0
});
this.originalResizeStyle=this.originalElement.css("resize");
this.originalElement.css("resize", "none");
this._proportionallyResizeElements.push(this.originalElement.css({
position: "static",
zoom: 1,
display: "block"
}));
this.originalElement.css({ margin: this.originalElement.css("margin") });
this._proportionallyResize();
}
this.handles=o.handles ||
(!$(".ui-resizable-handle", this.element).length ?
"e,s,se":{
n: ".ui-resizable-n",
e: ".ui-resizable-e",
s: ".ui-resizable-s",
w: ".ui-resizable-w",
se: ".ui-resizable-se",
sw: ".ui-resizable-sw",
ne: ".ui-resizable-ne",
nw: ".ui-resizable-nw"
});
this._handles=$();
if(this.handles.constructor===String){
if(this.handles==="all"){
this.handles="n,e,s,w,se,sw,ne,nw";
}
n=this.handles.split(",");
this.handles={};
for (i=0; i < n.length; i++){
handle=$.trim(n[i]);
hname="ui-resizable-" + handle;
axis=$("<div class='ui-resizable-handle " + hname + "'></div>");
axis.css({ zIndex: o.zIndex });
if("se"===handle){
axis.addClass("ui-icon ui-icon-gripsmall-diagonal-se");
}
this.handles[handle]=".ui-resizable-" + handle;
this.element.append(axis);
}}
this._renderAxis=function(target){
var i, axis, padPos, padWrapper;
target=target||this.element;
for (i in this.handles){
if(this.handles[i].constructor===String){
this.handles[i]=this.element.children(this.handles[ i ]).first().show();
}else if(this.handles[ i ].jquery||this.handles[ i ].nodeType){
this.handles[ i ]=$(this.handles[ i ]);
this._on(this.handles[ i ], { "mousedown": that._mouseDown });
}
if(this.elementIsWrapper&&this.originalElement[0].nodeName.match(/^(textarea|input|select|button)$/i)){
axis=$(this.handles[i], this.element);
padWrapper=/sw|ne|nw|se|n|s/.test(i) ? axis.outerHeight():axis.outerWidth();
padPos=[ "padding",
/ne|nw|n/.test(i) ? "Top" :
/se|sw|s/.test(i) ? "Bottom" :
/^e$/.test(i) ? "Right":"Left" ].join("");
target.css(padPos, padWrapper);
this._proportionallyResize();
}
this._handles=this._handles.add(this.handles[ i ]);
}};
this._renderAxis(this.element);
this._handles=this._handles.add(this.element.find(".ui-resizable-handle"));
this._handles.disableSelection();
this._handles.mouseover(function(){
if(!that.resizing){
if(this.className){
axis=this.className.match(/ui-resizable-(se|sw|ne|nw|n|e|s|w)/i);
}
that.axis=axis&&axis[1] ? axis[1]:"se";
}});
if(o.autoHide){
this._handles.hide();
$(this.element)
.addClass("ui-resizable-autohide")
.mouseenter(function(){
if(o.disabled){
return;
}
$(this).removeClass("ui-resizable-autohide");
that._handles.show();
})
.mouseleave(function(){
if(o.disabled){
return;
}
if(!that.resizing){
$(this).addClass("ui-resizable-autohide");
that._handles.hide();
}});
}
this._mouseInit();
},
_destroy: function(){
this._mouseDestroy();
var wrapper,
_destroy=function(exp){
$(exp)
.removeClass("ui-resizable ui-resizable-disabled ui-resizable-resizing")
.removeData("resizable")
.removeData("ui-resizable")
.unbind(".resizable")
.find(".ui-resizable-handle")
.remove();
};
if(this.elementIsWrapper){
_destroy(this.element);
wrapper=this.element;
this.originalElement.css({
position: wrapper.css("position"),
width: wrapper.outerWidth(),
height: wrapper.outerHeight(),
top: wrapper.css("top"),
left: wrapper.css("left")
}).insertAfter(wrapper);
wrapper.remove();
}
this.originalElement.css("resize", this.originalResizeStyle);
_destroy(this.originalElement);
return this;
},
_mouseCapture: function(event){
var i, handle,
capture=false;
for (i in this.handles){
handle=$(this.handles[i])[0];
if(handle===event.target||$.contains(handle, event.target)){
capture=true;
}}
return !this.options.disabled&&capture;
},
_mouseStart: function(event){
var curleft, curtop, cursor,
o=this.options,
el=this.element;
this.resizing=true;
this._renderProxy();
curleft=this._num(this.helper.css("left"));
curtop=this._num(this.helper.css("top"));
if(o.containment){
curleft +=$(o.containment).scrollLeft()||0;
curtop +=$(o.containment).scrollTop()||0;
}
this.offset=this.helper.offset();
this.position={ left: curleft, top: curtop };
this.size=this._helper ? {
width: this.helper.width(),
height: this.helper.height()
}:{
width: el.width(),
height: el.height()
};
this.originalSize=this._helper ? {
width: el.outerWidth(),
height: el.outerHeight()
}:{
width: el.width(),
height: el.height()
};
this.sizeDiff={
width: el.outerWidth() - el.width(),
height: el.outerHeight() - el.height()
};
this.originalPosition={ left: curleft, top: curtop };
this.originalMousePosition={ left: event.pageX, top: event.pageY };
this.aspectRatio=(typeof o.aspectRatio==="number") ?
o.aspectRatio :
((this.originalSize.width / this.originalSize.height)||1);
cursor=$(".ui-resizable-" + this.axis).css("cursor");
$("body").css("cursor", cursor==="auto" ? this.axis + "-resize":cursor);
el.addClass("ui-resizable-resizing");
this._propagate("start", event);
return true;
},
_mouseDrag: function(event){
var data, props,
smp=this.originalMousePosition,
a=this.axis,
dx=(event.pageX - smp.left)||0,
dy=(event.pageY - smp.top)||0,
trigger=this._change[a];
this._updatePrevProperties();
if(!trigger){
return false;
}
data=trigger.apply(this, [ event, dx, dy ]);
this._updateVirtualBoundaries(event.shiftKey);
if(this._aspectRatio||event.shiftKey){
data=this._updateRatio(data, event);
}
data=this._respectSize(data, event);
this._updateCache(data);
this._propagate("resize", event);
props=this._applyChanges();
if(!this._helper&&this._proportionallyResizeElements.length){
this._proportionallyResize();
}
if(!$.isEmptyObject(props)){
this._updatePrevProperties();
this._trigger("resize", event, this.ui());
this._applyChanges();
}
return false;
},
_mouseStop: function(event){
this.resizing=false;
var pr, ista, soffseth, soffsetw, s, left, top,
o=this.options, that=this;
if(this._helper){
pr=this._proportionallyResizeElements;
ista=pr.length&&(/textarea/i).test(pr[0].nodeName);
soffseth=ista&&this._hasScroll(pr[0], "left") ? 0:that.sizeDiff.height;
soffsetw=ista ? 0:that.sizeDiff.width;
s={
width: (that.helper.width()  - soffsetw),
height: (that.helper.height() - soffseth)
};
left=(parseInt(that.element.css("left"), 10) +
(that.position.left - that.originalPosition.left))||null;
top=(parseInt(that.element.css("top"), 10) +
(that.position.top - that.originalPosition.top))||null;
if(!o.animate){
this.element.css($.extend(s, { top: top, left: left }));
}
that.helper.height(that.size.height);
that.helper.width(that.size.width);
if(this._helper&&!o.animate){
this._proportionallyResize();
}}
$("body").css("cursor", "auto");
this.element.removeClass("ui-resizable-resizing");
this._propagate("stop", event);
if(this._helper){
this.helper.remove();
}
return false;
},
_updatePrevProperties: function(){
this.prevPosition={
top: this.position.top,
left: this.position.left
};
this.prevSize={
width: this.size.width,
height: this.size.height
};},
_applyChanges: function(){
var props={};
if(this.position.top!==this.prevPosition.top){
props.top=this.position.top + "px";
}
if(this.position.left!==this.prevPosition.left){
props.left=this.position.left + "px";
}
if(this.size.width!==this.prevSize.width){
props.width=this.size.width + "px";
}
if(this.size.height!==this.prevSize.height){
props.height=this.size.height + "px";
}
this.helper.css(props);
return props;
},
_updateVirtualBoundaries: function(forceAspectRatio){
var pMinWidth, pMaxWidth, pMinHeight, pMaxHeight, b,
o=this.options;
b={
minWidth: this._isNumber(o.minWidth) ? o.minWidth:0,
maxWidth: this._isNumber(o.maxWidth) ? o.maxWidth:Infinity,
minHeight: this._isNumber(o.minHeight) ? o.minHeight:0,
maxHeight: this._isNumber(o.maxHeight) ? o.maxHeight:Infinity
};
if(this._aspectRatio||forceAspectRatio){
pMinWidth=b.minHeight * this.aspectRatio;
pMinHeight=b.minWidth / this.aspectRatio;
pMaxWidth=b.maxHeight * this.aspectRatio;
pMaxHeight=b.maxWidth / this.aspectRatio;
if(pMinWidth > b.minWidth){
b.minWidth=pMinWidth;
}
if(pMinHeight > b.minHeight){
b.minHeight=pMinHeight;
}
if(pMaxWidth < b.maxWidth){
b.maxWidth=pMaxWidth;
}
if(pMaxHeight < b.maxHeight){
b.maxHeight=pMaxHeight;
}}
this._vBoundaries=b;
},
_updateCache: function(data){
this.offset=this.helper.offset();
if(this._isNumber(data.left)){
this.position.left=data.left;
}
if(this._isNumber(data.top)){
this.position.top=data.top;
}
if(this._isNumber(data.height)){
this.size.height=data.height;
}
if(this._isNumber(data.width)){
this.size.width=data.width;
}},
_updateRatio: function(data){
var cpos=this.position,
csize=this.size,
a=this.axis;
if(this._isNumber(data.height)){
data.width=(data.height * this.aspectRatio);
}else if(this._isNumber(data.width)){
data.height=(data.width / this.aspectRatio);
}
if(a==="sw"){
data.left=cpos.left + (csize.width - data.width);
data.top=null;
}
if(a==="nw"){
data.top=cpos.top + (csize.height - data.height);
data.left=cpos.left + (csize.width - data.width);
}
return data;
},
_respectSize: function(data){
var o=this._vBoundaries,
a=this.axis,
ismaxw=this._isNumber(data.width)&&o.maxWidth&&(o.maxWidth < data.width),
ismaxh=this._isNumber(data.height)&&o.maxHeight&&(o.maxHeight < data.height),
isminw=this._isNumber(data.width)&&o.minWidth&&(o.minWidth > data.width),
isminh=this._isNumber(data.height)&&o.minHeight&&(o.minHeight > data.height),
dw=this.originalPosition.left + this.originalSize.width,
dh=this.position.top + this.size.height,
cw=/sw|nw|w/.test(a), ch=/nw|ne|n/.test(a);
if(isminw){
data.width=o.minWidth;
}
if(isminh){
data.height=o.minHeight;
}
if(ismaxw){
data.width=o.maxWidth;
}
if(ismaxh){
data.height=o.maxHeight;
}
if(isminw&&cw){
data.left=dw - o.minWidth;
}
if(ismaxw&&cw){
data.left=dw - o.maxWidth;
}
if(isminh&&ch){
data.top=dh - o.minHeight;
}
if(ismaxh&&ch){
data.top=dh - o.maxHeight;
}
if(!data.width&&!data.height&&!data.left&&data.top){
data.top=null;
}else if(!data.width&&!data.height&&!data.top&&data.left){
data.left=null;
}
return data;
},
_getPaddingPlusBorderDimensions: function(element){
var i=0,
widths=[],
borders=[
element.css("borderTopWidth"),
element.css("borderRightWidth"),
element.css("borderBottomWidth"),
element.css("borderLeftWidth")
],
paddings=[
element.css("paddingTop"),
element.css("paddingRight"),
element.css("paddingBottom"),
element.css("paddingLeft")
];
for(; i < 4; i++){
widths[ i ]=(parseInt(borders[ i ], 10)||0);
widths[ i ] +=(parseInt(paddings[ i ], 10)||0);
}
return {
height: widths[ 0 ] + widths[ 2 ],
width: widths[ 1 ] + widths[ 3 ]
};},
_proportionallyResize: function(){
if(!this._proportionallyResizeElements.length){
return;
}
var prel,
i=0,
element=this.helper||this.element;
for(; i < this._proportionallyResizeElements.length; i++){
prel=this._proportionallyResizeElements[i];
if(!this.outerDimensions){
this.outerDimensions=this._getPaddingPlusBorderDimensions(prel);
}
prel.css({
height: (element.height() - this.outerDimensions.height)||0,
width: (element.width() - this.outerDimensions.width)||0
});
}},
_renderProxy: function(){
var el=this.element, o=this.options;
this.elementOffset=el.offset();
if(this._helper){
this.helper=this.helper||$("<div style='overflow:hidden;'></div>");
this.helper.addClass(this._helper).css({
width: this.element.outerWidth() - 1,
height: this.element.outerHeight() - 1,
position: "absolute",
left: this.elementOffset.left + "px",
top: this.elementOffset.top + "px",
zIndex: ++o.zIndex
});
this.helper
.appendTo("body")
.disableSelection();
}else{
this.helper=this.element;
}},
_change: {
e: function(event, dx){
return { width: this.originalSize.width + dx };},
w: function(event, dx){
var cs=this.originalSize, sp=this.originalPosition;
return { left: sp.left + dx, width: cs.width - dx };},
n: function(event, dx, dy){
var cs=this.originalSize, sp=this.originalPosition;
return { top: sp.top + dy, height: cs.height - dy };},
s: function(event, dx, dy){
return { height: this.originalSize.height + dy };},
se: function(event, dx, dy){
return $.extend(this._change.s.apply(this, arguments),
this._change.e.apply(this, [ event, dx, dy ]));
},
sw: function(event, dx, dy){
return $.extend(this._change.s.apply(this, arguments),
this._change.w.apply(this, [ event, dx, dy ]));
},
ne: function(event, dx, dy){
return $.extend(this._change.n.apply(this, arguments),
this._change.e.apply(this, [ event, dx, dy ]));
},
nw: function(event, dx, dy){
return $.extend(this._change.n.apply(this, arguments),
this._change.w.apply(this, [ event, dx, dy ]));
}},
_propagate: function(n, event){
$.ui.plugin.call(this, n, [ event, this.ui() ]);
(n!=="resize"&&this._trigger(n, event, this.ui()));
},
plugins: {},
ui: function(){
return {
originalElement: this.originalElement,
element: this.element,
helper: this.helper,
position: this.position,
size: this.size,
originalSize: this.originalSize,
originalPosition: this.originalPosition
};}});
$.ui.plugin.add("resizable", "animate", {
stop: function(event){
var that=$(this).resizable("instance"),
o=that.options,
pr=that._proportionallyResizeElements,
ista=pr.length&&(/textarea/i).test(pr[0].nodeName),
soffseth=ista&&that._hasScroll(pr[0], "left") ? 0:that.sizeDiff.height,
soffsetw=ista ? 0:that.sizeDiff.width,
style={ width: (that.size.width - soffsetw), height: (that.size.height - soffseth) },
left=(parseInt(that.element.css("left"), 10) +
(that.position.left - that.originalPosition.left))||null,
top=(parseInt(that.element.css("top"), 10) +
(that.position.top - that.originalPosition.top))||null;
that.element.animate($.extend(style, top&&left ? { top: top, left: left }:{}), {
duration: o.animateDuration,
easing: o.animateEasing,
step: function(){
var data={
width: parseInt(that.element.css("width"), 10),
height: parseInt(that.element.css("height"), 10),
top: parseInt(that.element.css("top"), 10),
left: parseInt(that.element.css("left"), 10)
};
if(pr&&pr.length){
$(pr[0]).css({ width: data.width, height: data.height });
}
that._updateCache(data);
that._propagate("resize", event);
}}
);
}});
$.ui.plugin.add("resizable", "containment", {
start: function(){
var element, p, co, ch, cw, width, height,
that=$(this).resizable("instance"),
o=that.options,
el=that.element,
oc=o.containment,
ce=(oc instanceof $) ? oc.get(0):(/parent/.test(oc)) ? el.parent().get(0):oc;
if(!ce){
return;
}
that.containerElement=$(ce);
if(/document/.test(oc)||oc===document){
that.containerOffset={
left: 0,
top: 0
};
that.containerPosition={
left: 0,
top: 0
};
that.parentData={
element: $(document),
left: 0,
top: 0,
width: $(document).width(),
height: $(document).height()||document.body.parentNode.scrollHeight
};}else{
element=$(ce);
p=[];
$([ "Top", "Right", "Left", "Bottom" ]).each(function(i, name){
p[ i ]=that._num(element.css("padding" + name));
});
that.containerOffset=element.offset();
that.containerPosition=element.position();
that.containerSize={
height:(element.innerHeight() - p[ 3 ]),
width:(element.innerWidth() - p[ 1 ])
};
co=that.containerOffset;
ch=that.containerSize.height;
cw=that.containerSize.width;
width=(that._hasScroll(ce, "left") ? ce.scrollWidth:cw);
height=(that._hasScroll(ce) ? ce.scrollHeight:ch) ;
that.parentData={
element: ce,
left: co.left,
top: co.top,
width: width,
height: height
};}},
resize: function(event){
var woset, hoset, isParent, isOffsetRelative,
that=$(this).resizable("instance"),
o=that.options,
co=that.containerOffset,
cp=that.position,
pRatio=that._aspectRatio||event.shiftKey,
cop={
top: 0,
left: 0
},
ce=that.containerElement,
continueResize=true;
if(ce[ 0 ]!==document&&(/static/).test(ce.css("position"))){
cop=co;
}
if(cp.left <(that._helper ? co.left:0)){
that.size.width=that.size.width +
(that._helper ?
(that.position.left - co.left) :
(that.position.left - cop.left));
if(pRatio){
that.size.height=that.size.width / that.aspectRatio;
continueResize=false;
}
that.position.left=o.helper ? co.left:0;
}
if(cp.top <(that._helper ? co.top:0)){
that.size.height=that.size.height +
(that._helper ?
(that.position.top - co.top) :
that.position.top);
if(pRatio){
that.size.width=that.size.height * that.aspectRatio;
continueResize=false;
}
that.position.top=that._helper ? co.top:0;
}
isParent=that.containerElement.get(0)===that.element.parent().get(0);
isOffsetRelative=/relative|absolute/.test(that.containerElement.css("position"));
if(isParent&&isOffsetRelative){
that.offset.left=that.parentData.left + that.position.left;
that.offset.top=that.parentData.top + that.position.top;
}else{
that.offset.left=that.element.offset().left;
that.offset.top=that.element.offset().top;
}
woset=Math.abs(that.sizeDiff.width +
(that._helper ?
that.offset.left - cop.left :
(that.offset.left - co.left)));
hoset=Math.abs(that.sizeDiff.height +
(that._helper ?
that.offset.top - cop.top :
(that.offset.top - co.top)));
if(woset + that.size.width >=that.parentData.width){
that.size.width=that.parentData.width - woset;
if(pRatio){
that.size.height=that.size.width / that.aspectRatio;
continueResize=false;
}}
if(hoset + that.size.height >=that.parentData.height){
that.size.height=that.parentData.height - hoset;
if(pRatio){
that.size.width=that.size.height * that.aspectRatio;
continueResize=false;
}}
if(!continueResize){
that.position.left=that.prevPosition.left;
that.position.top=that.prevPosition.top;
that.size.width=that.prevSize.width;
that.size.height=that.prevSize.height;
}},
stop: function(){
var that=$(this).resizable("instance"),
o=that.options,
co=that.containerOffset,
cop=that.containerPosition,
ce=that.containerElement,
helper=$(that.helper),
ho=helper.offset(),
w=helper.outerWidth() - that.sizeDiff.width,
h=helper.outerHeight() - that.sizeDiff.height;
if(that._helper&&!o.animate&&(/relative/).test(ce.css("position"))){
$(this).css({
left: ho.left - cop.left - co.left,
width: w,
height: h
});
}
if(that._helper&&!o.animate&&(/static/).test(ce.css("position"))){
$(this).css({
left: ho.left - cop.left - co.left,
width: w,
height: h
});
}}
});
$.ui.plugin.add("resizable", "alsoResize", {
start: function(){
var that=$(this).resizable("instance"),
o=that.options;
$(o.alsoResize).each(function(){
var el=$(this);
el.data("ui-resizable-alsoresize", {
width: parseInt(el.width(), 10), height: parseInt(el.height(), 10),
left: parseInt(el.css("left"), 10), top: parseInt(el.css("top"), 10)
});
});
},
resize: function(event, ui){
var that=$(this).resizable("instance"),
o=that.options,
os=that.originalSize,
op=that.originalPosition,
delta={
height: (that.size.height - os.height)||0,
width: (that.size.width - os.width)||0,
top: (that.position.top - op.top)||0,
left: (that.position.left - op.left)||0
};
$(o.alsoResize).each(function(){
var el=$(this), start=$(this).data("ui-resizable-alsoresize"), style={},
css=el.parents(ui.originalElement[0]).length ?
[ "width", "height" ] :
[ "width", "height", "top", "left" ];
$.each(css, function(i, prop){
var sum=(start[prop]||0) + (delta[prop]||0);
if(sum&&sum >=0){
style[prop]=sum||null;
}});
el.css(style);
});
},
stop: function(){
$(this).removeData("resizable-alsoresize");
}});
$.ui.plugin.add("resizable", "ghost", {
start: function(){
var that=$(this).resizable("instance"), o=that.options, cs=that.size;
that.ghost=that.originalElement.clone();
that.ghost
.css({
opacity: 0.25,
display: "block",
position: "relative",
height: cs.height,
width: cs.width,
margin: 0,
left: 0,
top: 0
})
.addClass("ui-resizable-ghost")
.addClass(typeof o.ghost==="string" ? o.ghost:"");
that.ghost.appendTo(that.helper);
},
resize: function(){
var that=$(this).resizable("instance");
if(that.ghost){
that.ghost.css({
position: "relative",
height: that.size.height,
width: that.size.width
});
}},
stop: function(){
var that=$(this).resizable("instance");
if(that.ghost&&that.helper){
that.helper.get(0).removeChild(that.ghost.get(0));
}}
});
$.ui.plugin.add("resizable", "grid", {
resize: function(){
var outerDimensions,
that=$(this).resizable("instance"),
o=that.options,
cs=that.size,
os=that.originalSize,
op=that.originalPosition,
a=that.axis,
grid=typeof o.grid==="number" ? [ o.grid, o.grid ]:o.grid,
gridX=(grid[0]||1),
gridY=(grid[1]||1),
ox=Math.round((cs.width - os.width) / gridX) * gridX,
oy=Math.round((cs.height - os.height) / gridY) * gridY,
newWidth=os.width + ox,
newHeight=os.height + oy,
isMaxWidth=o.maxWidth&&(o.maxWidth < newWidth),
isMaxHeight=o.maxHeight&&(o.maxHeight < newHeight),
isMinWidth=o.minWidth&&(o.minWidth > newWidth),
isMinHeight=o.minHeight&&(o.minHeight > newHeight);
o.grid=grid;
if(isMinWidth){
newWidth +=gridX;
}
if(isMinHeight){
newHeight +=gridY;
}
if(isMaxWidth){
newWidth -=gridX;
}
if(isMaxHeight){
newHeight -=gridY;
}
if(/^(se|s|e)$/.test(a)){
that.size.width=newWidth;
that.size.height=newHeight;
}else if(/^(ne)$/.test(a)){
that.size.width=newWidth;
that.size.height=newHeight;
that.position.top=op.top - oy;
}else if(/^(sw)$/.test(a)){
that.size.width=newWidth;
that.size.height=newHeight;
that.position.left=op.left - ox;
}else{
if(newHeight - gridY <=0||newWidth - gridX <=0){
outerDimensions=that._getPaddingPlusBorderDimensions(this);
}
if(newHeight - gridY > 0){
that.size.height=newHeight;
that.position.top=op.top - oy;
}else{
newHeight=gridY - outerDimensions.height;
that.size.height=newHeight;
that.position.top=op.top + os.height - newHeight;
}
if(newWidth - gridX > 0){
that.size.width=newWidth;
that.position.left=op.left - ox;
}else{
newWidth=gridX - outerDimensions.width;
that.size.width=newWidth;
that.position.left=op.left + os.width - newWidth;
}}
}});
var resizable=$.ui.resizable;
var selectable=$.widget("ui.selectable", $.ui.mouse, {
version: "1.11.4",
options: {
appendTo: "body",
autoRefresh: true,
distance: 0,
filter: "*",
tolerance: "touch",
selected: null,
selecting: null,
start: null,
stop: null,
unselected: null,
unselecting: null
},
_create: function(){
var selectees,
that=this;
this.element.addClass("ui-selectable");
this.dragged=false;
this.refresh=function(){
selectees=$(that.options.filter, that.element[0]);
selectees.addClass("ui-selectee");
selectees.each(function(){
var $this=$(this),
pos=$this.offset();
$.data(this, "selectable-item", {
element: this,
$element: $this,
left: pos.left,
top: pos.top,
right: pos.left + $this.outerWidth(),
bottom: pos.top + $this.outerHeight(),
startselected: false,
selected: $this.hasClass("ui-selected"),
selecting: $this.hasClass("ui-selecting"),
unselecting: $this.hasClass("ui-unselecting")
});
});
};
this.refresh();
this.selectees=selectees.addClass("ui-selectee");
this._mouseInit();
this.helper=$("<div class='ui-selectable-helper'></div>");
},
_destroy: function(){
this.selectees
.removeClass("ui-selectee")
.removeData("selectable-item");
this.element
.removeClass("ui-selectable ui-selectable-disabled");
this._mouseDestroy();
},
_mouseStart: function(event){
var that=this,
options=this.options;
this.opos=[ event.pageX, event.pageY ];
if(this.options.disabled){
return;
}
this.selectees=$(options.filter, this.element[0]);
this._trigger("start", event);
$(options.appendTo).append(this.helper);
this.helper.css({
"left": event.pageX,
"top": event.pageY,
"width": 0,
"height": 0
});
if(options.autoRefresh){
this.refresh();
}
this.selectees.filter(".ui-selected").each(function(){
var selectee=$.data(this, "selectable-item");
selectee.startselected=true;
if(!event.metaKey&&!event.ctrlKey){
selectee.$element.removeClass("ui-selected");
selectee.selected=false;
selectee.$element.addClass("ui-unselecting");
selectee.unselecting=true;
that._trigger("unselecting", event, {
unselecting: selectee.element
});
}});
$(event.target).parents().addBack().each(function(){
var doSelect,
selectee=$.data(this, "selectable-item");
if(selectee){
doSelect=(!event.metaKey&&!event.ctrlKey)||!selectee.$element.hasClass("ui-selected");
selectee.$element
.removeClass(doSelect ? "ui-unselecting":"ui-selected")
.addClass(doSelect ? "ui-selecting":"ui-unselecting");
selectee.unselecting = !doSelect;
selectee.selecting=doSelect;
selectee.selected=doSelect;
if(doSelect){
that._trigger("selecting", event, {
selecting: selectee.element
});
}else{
that._trigger("unselecting", event, {
unselecting: selectee.element
});
}
return false;
}});
},
_mouseDrag: function(event){
this.dragged=true;
if(this.options.disabled){
return;
}
var tmp,
that=this,
options=this.options,
x1=this.opos[0],
y1=this.opos[1],
x2=event.pageX,
y2=event.pageY;
if(x1 > x2){ tmp=x2; x2=x1; x1=tmp; }
if(y1 > y2){ tmp=y2; y2=y1; y1=tmp; }
this.helper.css({ left: x1, top: y1, width: x2 - x1, height: y2 - y1 });
this.selectees.each(function(){
var selectee=$.data(this, "selectable-item"),
hit=false;
if(!selectee||selectee.element===that.element[0]){
return;
}
if(options.tolerance==="touch"){
hit=(!(selectee.left > x2||selectee.right < x1||selectee.top > y2||selectee.bottom < y1));
}else if(options.tolerance==="fit"){
hit=(selectee.left > x1&&selectee.right < x2&&selectee.top > y1&&selectee.bottom < y2);
}
if(hit){
if(selectee.selected){
selectee.$element.removeClass("ui-selected");
selectee.selected=false;
}
if(selectee.unselecting){
selectee.$element.removeClass("ui-unselecting");
selectee.unselecting=false;
}
if(!selectee.selecting){
selectee.$element.addClass("ui-selecting");
selectee.selecting=true;
that._trigger("selecting", event, {
selecting: selectee.element
});
}}else{
if(selectee.selecting){
if((event.metaKey||event.ctrlKey)&&selectee.startselected){
selectee.$element.removeClass("ui-selecting");
selectee.selecting=false;
selectee.$element.addClass("ui-selected");
selectee.selected=true;
}else{
selectee.$element.removeClass("ui-selecting");
selectee.selecting=false;
if(selectee.startselected){
selectee.$element.addClass("ui-unselecting");
selectee.unselecting=true;
}
that._trigger("unselecting", event, {
unselecting: selectee.element
});
}}
if(selectee.selected){
if(!event.metaKey&&!event.ctrlKey&&!selectee.startselected){
selectee.$element.removeClass("ui-selected");
selectee.selected=false;
selectee.$element.addClass("ui-unselecting");
selectee.unselecting=true;
that._trigger("unselecting", event, {
unselecting: selectee.element
});
}}
}});
return false;
},
_mouseStop: function(event){
var that=this;
this.dragged=false;
$(".ui-unselecting", this.element[0]).each(function(){
var selectee=$.data(this, "selectable-item");
selectee.$element.removeClass("ui-unselecting");
selectee.unselecting=false;
selectee.startselected=false;
that._trigger("unselected", event, {
unselected: selectee.element
});
});
$(".ui-selecting", this.element[0]).each(function(){
var selectee=$.data(this, "selectable-item");
selectee.$element.removeClass("ui-selecting").addClass("ui-selected");
selectee.selecting=false;
selectee.selected=true;
selectee.startselected=true;
that._trigger("selected", event, {
selected: selectee.element
});
});
this._trigger("stop", event);
this.helper.remove();
return false;
}});
var sortable=$.widget("ui.sortable", $.ui.mouse, {
version: "1.11.4",
widgetEventPrefix: "sort",
ready: false,
options: {
appendTo: "parent",
axis: false,
connectWith: false,
containment: false,
cursor: "auto",
cursorAt: false,
dropOnEmpty: true,
forcePlaceholderSize: false,
forceHelperSize: false,
grid: false,
handle: false,
helper: "original",
items: "> *",
opacity: false,
placeholder: false,
revert: false,
scroll: true,
scrollSensitivity: 20,
scrollSpeed: 20,
scope: "default",
tolerance: "intersect",
zIndex: 1000,
activate: null,
beforeStop: null,
change: null,
deactivate: null,
out: null,
over: null,
receive: null,
remove: null,
sort: null,
start: null,
stop: null,
update: null
},
_isOverAxis: function(x, reference, size){
return(x >=reference)&&(x <(reference + size));
},
_isFloating: function(item){
return (/left|right/).test(item.css("float"))||(/inline|table-cell/).test(item.css("display"));
},
_create: function(){
this.containerCache={};
this.element.addClass("ui-sortable");
this.refresh();
this.offset=this.element.offset();
this._mouseInit();
this._setHandleClassName();
this.ready=true;
},
_setOption: function(key, value){
this._super(key, value);
if(key==="handle"){
this._setHandleClassName();
}},
_setHandleClassName: function(){
this.element.find(".ui-sortable-handle").removeClass("ui-sortable-handle");
$.each(this.items, function(){
(this.instance.options.handle ?
this.item.find(this.instance.options.handle):this.item)
.addClass("ui-sortable-handle");
});
},
_destroy: function(){
this.element
.removeClass("ui-sortable ui-sortable-disabled")
.find(".ui-sortable-handle")
.removeClass("ui-sortable-handle");
this._mouseDestroy();
for(var i=this.items.length - 1; i >=0; i--){
this.items[i].item.removeData(this.widgetName + "-item");
}
return this;
},
_mouseCapture: function(event, overrideHandle){
var currentItem=null,
validHandle=false,
that=this;
if(this.reverting){
return false;
}
if(this.options.disabled||this.options.type==="static"){
return false;
}
this._refreshItems(event);
$(event.target).parents().each(function(){
if($.data(this, that.widgetName + "-item")===that){
currentItem=$(this);
return false;
}});
if($.data(event.target, that.widgetName + "-item")===that){
currentItem=$(event.target);
}
if(!currentItem){
return false;
}
if(this.options.handle&&!overrideHandle){
$(this.options.handle, currentItem).find("*").addBack().each(function(){
if(this===event.target){
validHandle=true;
}});
if(!validHandle){
return false;
}}
this.currentItem=currentItem;
this._removeCurrentsFromItems();
return true;
},
_mouseStart: function(event, overrideHandle, noActivation){
var i, body,
o=this.options;
this.currentContainer=this;
this.refreshPositions();
this.helper=this._createHelper(event);
this._cacheHelperProportions();
this._cacheMargins();
this.scrollParent=this.helper.scrollParent();
this.offset=this.currentItem.offset();
this.offset={
top: this.offset.top - this.margins.top,
left: this.offset.left - this.margins.left
};
$.extend(this.offset, {
click: {
left: event.pageX - this.offset.left,
top: event.pageY - this.offset.top
},
parent: this._getParentOffset(),
relative: this._getRelativeOffset()
});
this.helper.css("position", "absolute");
this.cssPosition=this.helper.css("position");
this.originalPosition=this._generatePosition(event);
this.originalPageX=event.pageX;
this.originalPageY=event.pageY;
(o.cursorAt&&this._adjustOffsetFromHelper(o.cursorAt));
this.domPosition={ prev: this.currentItem.prev()[0], parent: this.currentItem.parent()[0] };
if(this.helper[0]!==this.currentItem[0]){
this.currentItem.hide();
}
this._createPlaceholder();
if(o.containment){
this._setContainment();
}
if(o.cursor&&o.cursor!=="auto"){
body=this.document.find("body");
this.storedCursor=body.css("cursor");
body.css("cursor", o.cursor);
this.storedStylesheet=$("<style>*{ cursor: "+o.cursor+" !important; }</style>").appendTo(body);
}
if(o.opacity){
if(this.helper.css("opacity")){
this._storedOpacity=this.helper.css("opacity");
}
this.helper.css("opacity", o.opacity);
}
if(o.zIndex){
if(this.helper.css("zIndex")){
this._storedZIndex=this.helper.css("zIndex");
}
this.helper.css("zIndex", o.zIndex);
}
if(this.scrollParent[0]!==this.document[0]&&this.scrollParent[0].tagName!=="HTML"){
this.overflowOffset=this.scrollParent.offset();
}
this._trigger("start", event, this._uiHash());
if(!this._preserveHelperProportions){
this._cacheHelperProportions();
}
if(!noActivation){
for(i=this.containers.length - 1; i >=0; i--){
this.containers[ i ]._trigger("activate", event, this._uiHash(this));
}}
if($.ui.ddmanager){
$.ui.ddmanager.current=this;
}
if($.ui.ddmanager&&!o.dropBehaviour){
$.ui.ddmanager.prepareOffsets(this, event);
}
this.dragging=true;
this.helper.addClass("ui-sortable-helper");
this._mouseDrag(event);
return true;
},
_mouseDrag: function(event){
var i, item, itemElement, intersection,
o=this.options,
scrolled=false;
this.position=this._generatePosition(event);
this.positionAbs=this._convertPositionTo("absolute");
if(!this.lastPositionAbs){
this.lastPositionAbs=this.positionAbs;
}
if(this.options.scroll){
if(this.scrollParent[0]!==this.document[0]&&this.scrollParent[0].tagName!=="HTML"){
if((this.overflowOffset.top + this.scrollParent[0].offsetHeight) - event.pageY < o.scrollSensitivity){
this.scrollParent[0].scrollTop=scrolled=this.scrollParent[0].scrollTop + o.scrollSpeed;
}else if(event.pageY - this.overflowOffset.top < o.scrollSensitivity){
this.scrollParent[0].scrollTop=scrolled=this.scrollParent[0].scrollTop - o.scrollSpeed;
}
if((this.overflowOffset.left + this.scrollParent[0].offsetWidth) - event.pageX < o.scrollSensitivity){
this.scrollParent[0].scrollLeft=scrolled=this.scrollParent[0].scrollLeft + o.scrollSpeed;
}else if(event.pageX - this.overflowOffset.left < o.scrollSensitivity){
this.scrollParent[0].scrollLeft=scrolled=this.scrollParent[0].scrollLeft - o.scrollSpeed;
}}else{
if(event.pageY - this.document.scrollTop() < o.scrollSensitivity){
scrolled=this.document.scrollTop(this.document.scrollTop() - o.scrollSpeed);
}else if(this.window.height() - (event.pageY - this.document.scrollTop()) < o.scrollSensitivity){
scrolled=this.document.scrollTop(this.document.scrollTop() + o.scrollSpeed);
}
if(event.pageX - this.document.scrollLeft() < o.scrollSensitivity){
scrolled=this.document.scrollLeft(this.document.scrollLeft() - o.scrollSpeed);
}else if(this.window.width() - (event.pageX - this.document.scrollLeft()) < o.scrollSensitivity){
scrolled=this.document.scrollLeft(this.document.scrollLeft() + o.scrollSpeed);
}}
if(scrolled!==false&&$.ui.ddmanager&&!o.dropBehaviour){
$.ui.ddmanager.prepareOffsets(this, event);
}}
this.positionAbs=this._convertPositionTo("absolute");
if(!this.options.axis||this.options.axis!=="y"){
this.helper[0].style.left=this.position.left+"px";
}
if(!this.options.axis||this.options.axis!=="x"){
this.helper[0].style.top=this.position.top+"px";
}
for (i=this.items.length - 1; i >=0; i--){
item=this.items[i];
itemElement=item.item[0];
intersection=this._intersectsWithPointer(item);
if(!intersection){
continue;
}
if(item.instance!==this.currentContainer){
continue;
}
if(itemElement!==this.currentItem[0] &&
this.placeholder[intersection===1 ? "next":"prev"]()[0]!==itemElement &&
!$.contains(this.placeholder[0], itemElement) &&
(this.options.type==="semi-dynamic" ? !$.contains(this.element[0], itemElement):true)
){
this.direction=intersection===1 ? "down":"up";
if(this.options.tolerance==="pointer"||this._intersectsWithSides(item)){
this._rearrange(event, item);
}else{
break;
}
this._trigger("change", event, this._uiHash());
break;
}}
this._contactContainers(event);
if($.ui.ddmanager){
$.ui.ddmanager.drag(this, event);
}
this._trigger("sort", event, this._uiHash());
this.lastPositionAbs=this.positionAbs;
return false;
},
_mouseStop: function(event, noPropagation){
if(!event){
return;
}
if($.ui.ddmanager&&!this.options.dropBehaviour){
$.ui.ddmanager.drop(this, event);
}
if(this.options.revert){
var that=this,
cur=this.placeholder.offset(),
axis=this.options.axis,
animation={};
if(!axis||axis==="x"){
animation.left=cur.left - this.offset.parent.left - this.margins.left + (this.offsetParent[0]===this.document[0].body ? 0:this.offsetParent[0].scrollLeft);
}
if(!axis||axis==="y"){
animation.top=cur.top - this.offset.parent.top - this.margins.top + (this.offsetParent[0]===this.document[0].body ? 0:this.offsetParent[0].scrollTop);
}
this.reverting=true;
$(this.helper).animate(animation, parseInt(this.options.revert, 10)||500, function(){
that._clear(event);
});
}else{
this._clear(event, noPropagation);
}
return false;
},
cancel: function(){
if(this.dragging){
this._mouseUp({ target: null });
if(this.options.helper==="original"){
this.currentItem.css(this._storedCSS).removeClass("ui-sortable-helper");
}else{
this.currentItem.show();
}
for (var i=this.containers.length - 1; i >=0; i--){
this.containers[i]._trigger("deactivate", null, this._uiHash(this));
if(this.containers[i].containerCache.over){
this.containers[i]._trigger("out", null, this._uiHash(this));
this.containers[i].containerCache.over=0;
}}
}
if(this.placeholder){
if(this.placeholder[0].parentNode){
this.placeholder[0].parentNode.removeChild(this.placeholder[0]);
}
if(this.options.helper!=="original"&&this.helper&&this.helper[0].parentNode){
this.helper.remove();
}
$.extend(this, {
helper: null,
dragging: false,
reverting: false,
_noFinalSort: null
});
if(this.domPosition.prev){
$(this.domPosition.prev).after(this.currentItem);
}else{
$(this.domPosition.parent).prepend(this.currentItem);
}}
return this;
},
serialize: function(o){
var items=this._getItemsAsjQuery(o&&o.connected),
str=[];
o=o||{};
$(items).each(function(){
var res=($(o.item||this).attr(o.attribute||"id")||"").match(o.expression||(/(.+)[\-=_](.+)/));
if(res){
str.push((o.key||res[1]+"[]")+"="+(o.key&&o.expression ? res[1]:res[2]));
}});
if(!str.length&&o.key){
str.push(o.key + "=");
}
return str.join("&");
},
toArray: function(o){
var items=this._getItemsAsjQuery(o&&o.connected),
ret=[];
o=o||{};
items.each(function(){ ret.push($(o.item||this).attr(o.attribute||"id")||""); });
return ret;
},
_intersectsWith: function(item){
var x1=this.positionAbs.left,
x2=x1 + this.helperProportions.width,
y1=this.positionAbs.top,
y2=y1 + this.helperProportions.height,
l=item.left,
r=l + item.width,
t=item.top,
b=t + item.height,
dyClick=this.offset.click.top,
dxClick=this.offset.click.left,
isOverElementHeight=(this.options.axis==="x")||(( y1 + dyClick) > t&&(y1 + dyClick) < b),
isOverElementWidth=(this.options.axis==="y")||(( x1 + dxClick) > l&&(x1 + dxClick) < r),
isOverElement=isOverElementHeight&&isOverElementWidth;
if(this.options.tolerance==="pointer" ||
this.options.forcePointerForContainers ||
(this.options.tolerance!=="pointer"&&this.helperProportions[this.floating ? "width":"height"] > item[this.floating ? "width":"height"])
){
return isOverElement;
}else{
return (l < x1 + (this.helperProportions.width / 2) &&
x2 - (this.helperProportions.width / 2) < r &&
t < y1 + (this.helperProportions.height / 2) &&
y2 - (this.helperProportions.height / 2) < b);
}},
_intersectsWithPointer: function(item){
var isOverElementHeight=(this.options.axis==="x")||this._isOverAxis(this.positionAbs.top + this.offset.click.top, item.top, item.height),
isOverElementWidth=(this.options.axis==="y")||this._isOverAxis(this.positionAbs.left + this.offset.click.left, item.left, item.width),
isOverElement=isOverElementHeight&&isOverElementWidth,
verticalDirection=this._getDragVerticalDirection(),
horizontalDirection=this._getDragHorizontalDirection();
if(!isOverElement){
return false;
}
return this.floating ?
(((horizontalDirection&&horizontalDirection==="right")||verticalDirection==="down") ? 2:1)
:(verticalDirection&&(verticalDirection==="down" ? 2:1));
},
_intersectsWithSides: function(item){
var isOverBottomHalf=this._isOverAxis(this.positionAbs.top + this.offset.click.top, item.top + (item.height/2), item.height),
isOverRightHalf=this._isOverAxis(this.positionAbs.left + this.offset.click.left, item.left + (item.width/2), item.width),
verticalDirection=this._getDragVerticalDirection(),
horizontalDirection=this._getDragHorizontalDirection();
if(this.floating&&horizontalDirection){
return ((horizontalDirection==="right"&&isOverRightHalf)||(horizontalDirection==="left"&&!isOverRightHalf));
}else{
return verticalDirection&&((verticalDirection==="down"&&isOverBottomHalf)||(verticalDirection==="up"&&!isOverBottomHalf));
}},
_getDragVerticalDirection: function(){
var delta=this.positionAbs.top - this.lastPositionAbs.top;
return delta!==0&&(delta > 0 ? "down":"up");
},
_getDragHorizontalDirection: function(){
var delta=this.positionAbs.left - this.lastPositionAbs.left;
return delta!==0&&(delta > 0 ? "right":"left");
},
refresh: function(event){
this._refreshItems(event);
this._setHandleClassName();
this.refreshPositions();
return this;
},
_connectWith: function(){
var options=this.options;
return options.connectWith.constructor===String ? [options.connectWith]:options.connectWith;
},
_getItemsAsjQuery: function(connected){
var i, j, cur, inst,
items=[],
queries=[],
connectWith=this._connectWith();
if(connectWith&&connected){
for (i=connectWith.length - 1; i >=0; i--){
cur=$(connectWith[i], this.document[0]);
for(j=cur.length - 1; j >=0; j--){
inst=$.data(cur[j], this.widgetFullName);
if(inst&&inst!==this&&!inst.options.disabled){
queries.push([$.isFunction(inst.options.items) ? inst.options.items.call(inst.element):$(inst.options.items, inst.element).not(".ui-sortable-helper").not(".ui-sortable-placeholder"), inst]);
}}
}}
queries.push([$.isFunction(this.options.items) ? this.options.items.call(this.element, null, { options: this.options, item: this.currentItem }):$(this.options.items, this.element).not(".ui-sortable-helper").not(".ui-sortable-placeholder"), this]);
function addItems(){
items.push(this);
}
for (i=queries.length - 1; i >=0; i--){
queries[i][0].each(addItems);
}
return $(items);
},
_removeCurrentsFromItems: function(){
var list=this.currentItem.find(":data(" + this.widgetName + "-item)");
this.items=$.grep(this.items, function (item){
for (var j=0; j < list.length; j++){
if(list[j]===item.item[0]){
return false;
}}
return true;
});
},
_refreshItems: function(event){
this.items=[];
this.containers=[this];
var i, j, cur, inst, targetData, _queries, item, queriesLength,
items=this.items,
queries=[[$.isFunction(this.options.items) ? this.options.items.call(this.element[0], event, { item: this.currentItem }):$(this.options.items, this.element), this]],
connectWith=this._connectWith();
if(connectWith&&this.ready){
for (i=connectWith.length - 1; i >=0; i--){
cur=$(connectWith[i], this.document[0]);
for (j=cur.length - 1; j >=0; j--){
inst=$.data(cur[j], this.widgetFullName);
if(inst&&inst!==this&&!inst.options.disabled){
queries.push([$.isFunction(inst.options.items) ? inst.options.items.call(inst.element[0], event, { item: this.currentItem }):$(inst.options.items, inst.element), inst]);
this.containers.push(inst);
}}
}}
for (i=queries.length - 1; i >=0; i--){
targetData=queries[i][1];
_queries=queries[i][0];
for (j=0, queriesLength=_queries.length; j < queriesLength; j++){
item=$(_queries[j]);
item.data(this.widgetName + "-item", targetData);
items.push({
item: item,
instance: targetData,
width: 0, height: 0,
left: 0, top: 0
});
}}
},
refreshPositions: function(fast){
this.floating=this.items.length ?
this.options.axis==="x"||this._isFloating(this.items[ 0 ].item) :
false;
if(this.offsetParent&&this.helper){
this.offset.parent=this._getParentOffset();
}
var i, item, t, p;
for (i=this.items.length - 1; i >=0; i--){
item=this.items[i];
if(item.instance!==this.currentContainer&&this.currentContainer&&item.item[0]!==this.currentItem[0]){
continue;
}
t=this.options.toleranceElement ? $(this.options.toleranceElement, item.item):item.item;
if(!fast){
item.width=t.outerWidth();
item.height=t.outerHeight();
}
p=t.offset();
item.left=p.left;
item.top=p.top;
}
if(this.options.custom&&this.options.custom.refreshContainers){
this.options.custom.refreshContainers.call(this);
}else{
for (i=this.containers.length - 1; i >=0; i--){
p=this.containers[i].element.offset();
this.containers[i].containerCache.left=p.left;
this.containers[i].containerCache.top=p.top;
this.containers[i].containerCache.width=this.containers[i].element.outerWidth();
this.containers[i].containerCache.height=this.containers[i].element.outerHeight();
}}
return this;
},
_createPlaceholder: function(that){
that=that||this;
var className,
o=that.options;
if(!o.placeholder||o.placeholder.constructor===String){
className=o.placeholder;
o.placeholder={
element: function(){
var nodeName=that.currentItem[0].nodeName.toLowerCase(),
element=$("<" + nodeName + ">", that.document[0])
.addClass(className||that.currentItem[0].className+" ui-sortable-placeholder")
.removeClass("ui-sortable-helper");
if(nodeName==="tbody"){
that._createTrPlaceholder(that.currentItem.find("tr").eq(0),
$("<tr>", that.document[ 0 ]).appendTo(element)
);
}else if(nodeName==="tr"){
that._createTrPlaceholder(that.currentItem, element);
}else if(nodeName==="img"){
element.attr("src", that.currentItem.attr("src"));
}
if(!className){
element.css("visibility", "hidden");
}
return element;
},
update: function(container, p){
if(className&&!o.forcePlaceholderSize){
return;
}
if(!p.height()){ p.height(that.currentItem.innerHeight() - parseInt(that.currentItem.css("paddingTop")||0, 10) - parseInt(that.currentItem.css("paddingBottom")||0, 10)); }
if(!p.width()){ p.width(that.currentItem.innerWidth() - parseInt(that.currentItem.css("paddingLeft")||0, 10) - parseInt(that.currentItem.css("paddingRight")||0, 10)); }}
};}
that.placeholder=$(o.placeholder.element.call(that.element, that.currentItem));
that.currentItem.after(that.placeholder);
o.placeholder.update(that, that.placeholder);
},
_createTrPlaceholder: function(sourceTr, targetTr){
var that=this;
sourceTr.children().each(function(){
$("<td>&#160;</td>", that.document[ 0 ])
.attr("colspan", $(this).attr("colspan")||1)
.appendTo(targetTr);
});
},
_contactContainers: function(event){
var i, j, dist, itemWithLeastDistance, posProperty, sizeProperty, cur, nearBottom, floating, axis,
innermostContainer=null,
innermostIndex=null;
for (i=this.containers.length - 1; i >=0; i--){
if($.contains(this.currentItem[0], this.containers[i].element[0])){
continue;
}
if(this._intersectsWith(this.containers[i].containerCache)){
if(innermostContainer&&$.contains(this.containers[i].element[0], innermostContainer.element[0])){
continue;
}
innermostContainer=this.containers[i];
innermostIndex=i;
}else{
if(this.containers[i].containerCache.over){
this.containers[i]._trigger("out", event, this._uiHash(this));
this.containers[i].containerCache.over=0;
}}
}
if(!innermostContainer){
return;
}
if(this.containers.length===1){
if(!this.containers[innermostIndex].containerCache.over){
this.containers[innermostIndex]._trigger("over", event, this._uiHash(this));
this.containers[innermostIndex].containerCache.over=1;
}}else{
dist=10000;
itemWithLeastDistance=null;
floating=innermostContainer.floating||this._isFloating(this.currentItem);
posProperty=floating ? "left":"top";
sizeProperty=floating ? "width":"height";
axis=floating ? "clientX":"clientY";
for (j=this.items.length - 1; j >=0; j--){
if(!$.contains(this.containers[innermostIndex].element[0], this.items[j].item[0])){
continue;
}
if(this.items[j].item[0]===this.currentItem[0]){
continue;
}
cur=this.items[j].item.offset()[posProperty];
nearBottom=false;
if(event[ axis ] - cur > this.items[ j ][ sizeProperty ] / 2){
nearBottom=true;
}
if(Math.abs(event[ axis ] - cur) < dist){
dist=Math.abs(event[ axis ] - cur);
itemWithLeastDistance=this.items[ j ];
this.direction=nearBottom ? "up": "down";
}}
if(!itemWithLeastDistance&&!this.options.dropOnEmpty){
return;
}
if(this.currentContainer===this.containers[innermostIndex]){
if(!this.currentContainer.containerCache.over){
this.containers[ innermostIndex ]._trigger("over", event, this._uiHash());
this.currentContainer.containerCache.over=1;
}
return;
}
itemWithLeastDistance ? this._rearrange(event, itemWithLeastDistance, null, true):this._rearrange(event, null, this.containers[innermostIndex].element, true);
this._trigger("change", event, this._uiHash());
this.containers[innermostIndex]._trigger("change", event, this._uiHash(this));
this.currentContainer=this.containers[innermostIndex];
this.options.placeholder.update(this.currentContainer, this.placeholder);
this.containers[innermostIndex]._trigger("over", event, this._uiHash(this));
this.containers[innermostIndex].containerCache.over=1;
}},
_createHelper: function(event){
var o=this.options,
helper=$.isFunction(o.helper) ? $(o.helper.apply(this.element[0], [event, this.currentItem])):(o.helper==="clone" ? this.currentItem.clone():this.currentItem);
if(!helper.parents("body").length){
$(o.appendTo!=="parent" ? o.appendTo:this.currentItem[0].parentNode)[0].appendChild(helper[0]);
}
if(helper[0]===this.currentItem[0]){
this._storedCSS={ width: this.currentItem[0].style.width, height: this.currentItem[0].style.height, position: this.currentItem.css("position"), top: this.currentItem.css("top"), left: this.currentItem.css("left") };}
if(!helper[0].style.width||o.forceHelperSize){
helper.width(this.currentItem.width());
}
if(!helper[0].style.height||o.forceHelperSize){
helper.height(this.currentItem.height());
}
return helper;
},
_adjustOffsetFromHelper: function(obj){
if(typeof obj==="string"){
obj=obj.split(" ");
}
if($.isArray(obj)){
obj={left: +obj[0], top: +obj[1]||0};}
if("left" in obj){
this.offset.click.left=obj.left + this.margins.left;
}
if("right" in obj){
this.offset.click.left=this.helperProportions.width - obj.right + this.margins.left;
}
if("top" in obj){
this.offset.click.top=obj.top + this.margins.top;
}
if("bottom" in obj){
this.offset.click.top=this.helperProportions.height - obj.bottom + this.margins.top;
}},
_getParentOffset: function(){
this.offsetParent=this.helper.offsetParent();
var po=this.offsetParent.offset();
if(this.cssPosition==="absolute"&&this.scrollParent[0]!==this.document[0]&&$.contains(this.scrollParent[0], this.offsetParent[0])){
po.left +=this.scrollParent.scrollLeft();
po.top +=this.scrollParent.scrollTop();
}
if(this.offsetParent[0]===this.document[0].body||(this.offsetParent[0].tagName&&this.offsetParent[0].tagName.toLowerCase()==="html"&&$.ui.ie)){
po={ top: 0, left: 0 };}
return {
top: po.top + (parseInt(this.offsetParent.css("borderTopWidth"),10)||0),
left: po.left + (parseInt(this.offsetParent.css("borderLeftWidth"),10)||0)
};},
_getRelativeOffset: function(){
if(this.cssPosition==="relative"){
var p=this.currentItem.position();
return {
top: p.top - (parseInt(this.helper.css("top"),10)||0) + this.scrollParent.scrollTop(),
left: p.left - (parseInt(this.helper.css("left"),10)||0) + this.scrollParent.scrollLeft()
};}else{
return { top: 0, left: 0 };}},
_cacheMargins: function(){
this.margins={
left: (parseInt(this.currentItem.css("marginLeft"),10)||0),
top: (parseInt(this.currentItem.css("marginTop"),10)||0)
};},
_cacheHelperProportions: function(){
this.helperProportions={
width: this.helper.outerWidth(),
height: this.helper.outerHeight()
};},
_setContainment: function(){
var ce, co, over,
o=this.options;
if(o.containment==="parent"){
o.containment=this.helper[0].parentNode;
}
if(o.containment==="document"||o.containment==="window"){
this.containment=[
0 - this.offset.relative.left - this.offset.parent.left,
0 - this.offset.relative.top - this.offset.parent.top,
o.containment==="document" ? this.document.width():this.window.width() - this.helperProportions.width - this.margins.left,
(o.containment==="document" ? this.document.width():this.window.height()||this.document[0].body.parentNode.scrollHeight) - this.helperProportions.height - this.margins.top
];
}
if(!(/^(document|window|parent)$/).test(o.containment)){
ce=$(o.containment)[0];
co=$(o.containment).offset();
over=($(ce).css("overflow")!=="hidden");
this.containment=[
co.left + (parseInt($(ce).css("borderLeftWidth"),10)||0) + (parseInt($(ce).css("paddingLeft"),10)||0) - this.margins.left,
co.top + (parseInt($(ce).css("borderTopWidth"),10)||0) + (parseInt($(ce).css("paddingTop"),10)||0) - this.margins.top,
co.left+(over ? Math.max(ce.scrollWidth,ce.offsetWidth):ce.offsetWidth) - (parseInt($(ce).css("borderLeftWidth"),10)||0) - (parseInt($(ce).css("paddingRight"),10)||0) - this.helperProportions.width - this.margins.left,
co.top+(over ? Math.max(ce.scrollHeight,ce.offsetHeight):ce.offsetHeight) - (parseInt($(ce).css("borderTopWidth"),10)||0) - (parseInt($(ce).css("paddingBottom"),10)||0) - this.helperProportions.height - this.margins.top
];
}},
_convertPositionTo: function(d, pos){
if(!pos){
pos=this.position;
}
var mod=d==="absolute" ? 1:-1,
scroll=this.cssPosition==="absolute"&&!(this.scrollParent[0]!==this.document[0]&&$.contains(this.scrollParent[0], this.offsetParent[0])) ? this.offsetParent:this.scrollParent,
scrollIsRootNode=(/(html|body)/i).test(scroll[0].tagName);
return {
top: (
pos.top	+
this.offset.relative.top * mod +
this.offset.parent.top * mod -
(( this.cssPosition==="fixed" ? -this.scrollParent.scrollTop():(scrollIsRootNode ? 0:scroll.scrollTop())) * mod)
),
left: (
pos.left +
this.offset.relative.left * mod +
this.offset.parent.left * mod	-
(( this.cssPosition==="fixed" ? -this.scrollParent.scrollLeft():scrollIsRootNode ? 0:scroll.scrollLeft()) * mod)
)
};},
_generatePosition: function(event){
var top, left,
o=this.options,
pageX=event.pageX,
pageY=event.pageY,
scroll=this.cssPosition==="absolute"&&!(this.scrollParent[0]!==this.document[0]&&$.contains(this.scrollParent[0], this.offsetParent[0])) ? this.offsetParent:this.scrollParent, scrollIsRootNode=(/(html|body)/i).test(scroll[0].tagName);
if(this.cssPosition==="relative"&&!(this.scrollParent[0]!==this.document[0]&&this.scrollParent[0]!==this.offsetParent[0])){
this.offset.relative=this._getRelativeOffset();
}
if(this.originalPosition){
if(this.containment){
if(event.pageX - this.offset.click.left < this.containment[0]){
pageX=this.containment[0] + this.offset.click.left;
}
if(event.pageY - this.offset.click.top < this.containment[1]){
pageY=this.containment[1] + this.offset.click.top;
}
if(event.pageX - this.offset.click.left > this.containment[2]){
pageX=this.containment[2] + this.offset.click.left;
}
if(event.pageY - this.offset.click.top > this.containment[3]){
pageY=this.containment[3] + this.offset.click.top;
}}
if(o.grid){
top=this.originalPageY + Math.round((pageY - this.originalPageY) / o.grid[1]) * o.grid[1];
pageY=this.containment ?((top - this.offset.click.top >=this.containment[1]&&top - this.offset.click.top <=this.containment[3]) ? top:((top - this.offset.click.top >=this.containment[1]) ? top - o.grid[1]:top + o.grid[1])):top;
left=this.originalPageX + Math.round((pageX - this.originalPageX) / o.grid[0]) * o.grid[0];
pageX=this.containment ?((left - this.offset.click.left >=this.containment[0]&&left - this.offset.click.left <=this.containment[2]) ? left:((left - this.offset.click.left >=this.containment[0]) ? left - o.grid[0]:left + o.grid[0])):left;
}}
return {
top: (
pageY -
this.offset.click.top -
this.offset.relative.top	-
this.offset.parent.top +
(( this.cssPosition==="fixed" ? -this.scrollParent.scrollTop():(scrollIsRootNode ? 0:scroll.scrollTop())))
),
left: (
pageX -
this.offset.click.left -
this.offset.relative.left	-
this.offset.parent.left +
(( this.cssPosition==="fixed" ? -this.scrollParent.scrollLeft():scrollIsRootNode ? 0:scroll.scrollLeft()))
)
};},
_rearrange: function(event, i, a, hardRefresh){
a ? a[0].appendChild(this.placeholder[0]):i.item[0].parentNode.insertBefore(this.placeholder[0], (this.direction==="down" ? i.item[0]:i.item[0].nextSibling));
this.counter=this.counter ? ++this.counter:1;
var counter=this.counter;
this._delay(function(){
if(counter===this.counter){
this.refreshPositions(!hardRefresh);
}});
},
_clear: function(event, noPropagation){
this.reverting=false;
var i,
delayedTriggers=[];
if(!this._noFinalSort&&this.currentItem.parent().length){
this.placeholder.before(this.currentItem);
}
this._noFinalSort=null;
if(this.helper[0]===this.currentItem[0]){
for(i in this._storedCSS){
if(this._storedCSS[i]==="auto"||this._storedCSS[i]==="static"){
this._storedCSS[i]="";
}}
this.currentItem.css(this._storedCSS).removeClass("ui-sortable-helper");
}else{
this.currentItem.show();
}
if(this.fromOutside&&!noPropagation){
delayedTriggers.push(function(event){ this._trigger("receive", event, this._uiHash(this.fromOutside)); });
}
if((this.fromOutside||this.domPosition.prev!==this.currentItem.prev().not(".ui-sortable-helper")[0]||this.domPosition.parent!==this.currentItem.parent()[0])&&!noPropagation){
delayedTriggers.push(function(event){ this._trigger("update", event, this._uiHash()); });
}
if(this!==this.currentContainer){
if(!noPropagation){
delayedTriggers.push(function(event){ this._trigger("remove", event, this._uiHash()); });
delayedTriggers.push((function(c){ return function(event){ c._trigger("receive", event, this._uiHash(this)); };}).call(this, this.currentContainer));
delayedTriggers.push((function(c){ return function(event){ c._trigger("update", event, this._uiHash(this));  };}).call(this, this.currentContainer));
}}
function delayEvent(type, instance, container){
return function(event){
container._trigger(type, event, instance._uiHash(instance));
};}
for (i=this.containers.length - 1; i >=0; i--){
if(!noPropagation){
delayedTriggers.push(delayEvent("deactivate", this, this.containers[ i ]));
}
if(this.containers[i].containerCache.over){
delayedTriggers.push(delayEvent("out", this, this.containers[ i ]));
this.containers[i].containerCache.over=0;
}}
if(this.storedCursor){
this.document.find("body").css("cursor", this.storedCursor);
this.storedStylesheet.remove();
}
if(this._storedOpacity){
this.helper.css("opacity", this._storedOpacity);
}
if(this._storedZIndex){
this.helper.css("zIndex", this._storedZIndex==="auto" ? "":this._storedZIndex);
}
this.dragging=false;
if(!noPropagation){
this._trigger("beforeStop", event, this._uiHash());
}
this.placeholder[0].parentNode.removeChild(this.placeholder[0]);
if(!this.cancelHelperRemoval){
if(this.helper[ 0 ]!==this.currentItem[ 0 ]){
this.helper.remove();
}
this.helper=null;
}
if(!noPropagation){
for (i=0; i < delayedTriggers.length; i++){
delayedTriggers[i].call(this, event);
}
this._trigger("stop", event, this._uiHash());
}
this.fromOutside=false;
return !this.cancelHelperRemoval;
},
_trigger: function(){
if($.Widget.prototype._trigger.apply(this, arguments)===false){
this.cancel();
}},
_uiHash: function(_inst){
var inst=_inst||this;
return {
helper: inst.helper,
placeholder: inst.placeholder||$([]),
position: inst.position,
originalPosition: inst.originalPosition,
offset: inst.positionAbs,
item: inst.currentItem,
sender: _inst ? _inst.element:null
};}});
var accordion=$.widget("ui.accordion", {
version: "1.11.4",
options: {
active: 0,
animate: {},
collapsible: false,
event: "click",
header: "> li > :first-child,> :not(li):even",
heightStyle: "auto",
icons: {
activeHeader: "ui-icon-triangle-1-s",
header: "ui-icon-triangle-1-e"
},
activate: null,
beforeActivate: null
},
hideProps: {
borderTopWidth: "hide",
borderBottomWidth: "hide",
paddingTop: "hide",
paddingBottom: "hide",
height: "hide"
},
showProps: {
borderTopWidth: "show",
borderBottomWidth: "show",
paddingTop: "show",
paddingBottom: "show",
height: "show"
},
_create: function(){
var options=this.options;
this.prevShow=this.prevHide=$();
this.element.addClass("ui-accordion ui-widget ui-helper-reset")
.attr("role", "tablist");
if(!options.collapsible&&(options.active===false||options.active==null)){
options.active=0;
}
this._processPanels();
if(options.active < 0){
options.active +=this.headers.length;
}
this._refresh();
},
_getCreateEventData: function(){
return {
header: this.active,
panel: !this.active.length ? $():this.active.next()
};},
_createIcons: function(){
var icons=this.options.icons;
if(icons){
$("<span>")
.addClass("ui-accordion-header-icon ui-icon " + icons.header)
.prependTo(this.headers);
this.active.children(".ui-accordion-header-icon")
.removeClass(icons.header)
.addClass(icons.activeHeader);
this.headers.addClass("ui-accordion-icons");
}},
_destroyIcons: function(){
this.headers
.removeClass("ui-accordion-icons")
.children(".ui-accordion-header-icon")
.remove();
},
_destroy: function(){
var contents;
this.element
.removeClass("ui-accordion ui-widget ui-helper-reset")
.removeAttr("role");
this.headers
.removeClass("ui-accordion-header ui-accordion-header-active ui-state-default " +
"ui-corner-all ui-state-active ui-state-disabled ui-corner-top")
.removeAttr("role")
.removeAttr("aria-expanded")
.removeAttr("aria-selected")
.removeAttr("aria-controls")
.removeAttr("tabIndex")
.removeUniqueId();
this._destroyIcons();
contents=this.headers.next()
.removeClass("ui-helper-reset ui-widget-content ui-corner-bottom " +
"ui-accordion-content ui-accordion-content-active ui-state-disabled")
.css("display", "")
.removeAttr("role")
.removeAttr("aria-hidden")
.removeAttr("aria-labelledby")
.removeUniqueId();
if(this.options.heightStyle!=="content"){
contents.css("height", "");
}},
_setOption: function(key, value){
if(key==="active"){
this._activate(value);
return;
}
if(key==="event"){
if(this.options.event){
this._off(this.headers, this.options.event);
}
this._setupEvents(value);
}
this._super(key, value);
if(key==="collapsible"&&!value&&this.options.active===false){
this._activate(0);
}
if(key==="icons"){
this._destroyIcons();
if(value){
this._createIcons();
}}
if(key==="disabled"){
this.element
.toggleClass("ui-state-disabled", !!value)
.attr("aria-disabled", value);
this.headers.add(this.headers.next())
.toggleClass("ui-state-disabled", !!value);
}},
_keydown: function(event){
if(event.altKey||event.ctrlKey){
return;
}
var keyCode=$.ui.keyCode,
length=this.headers.length,
currentIndex=this.headers.index(event.target),
toFocus=false;
switch(event.keyCode){
case keyCode.RIGHT:
case keyCode.DOWN:
toFocus=this.headers[(currentIndex + 1) % length ];
break;
case keyCode.LEFT:
case keyCode.UP:
toFocus=this.headers[(currentIndex - 1 + length) % length ];
break;
case keyCode.SPACE:
case keyCode.ENTER:
this._eventHandler(event);
break;
case keyCode.HOME:
toFocus=this.headers[ 0 ];
break;
case keyCode.END:
toFocus=this.headers[ length - 1 ];
break;
}
if(toFocus){
$(event.target).attr("tabIndex", -1);
$(toFocus).attr("tabIndex", 0);
toFocus.focus();
event.preventDefault();
}},
_panelKeyDown: function(event){
if(event.keyCode===$.ui.keyCode.UP&&event.ctrlKey){
$(event.currentTarget).prev().focus();
}},
refresh: function(){
var options=this.options;
this._processPanels();
if(( options.active===false&&options.collapsible===true)||!this.headers.length){
options.active=false;
this.active=$();
}else if(options.active===false){
this._activate(0);
}else if(this.active.length&&!$.contains(this.element[ 0 ], this.active[ 0 ])){
if(this.headers.length===this.headers.find(".ui-state-disabled").length){
options.active=false;
this.active=$();
}else{
this._activate(Math.max(0, options.active - 1));
}}else{
options.active=this.headers.index(this.active);
}
this._destroyIcons();
this._refresh();
},
_processPanels: function(){
var prevHeaders=this.headers,
prevPanels=this.panels;
this.headers=this.element.find(this.options.header)
.addClass("ui-accordion-header ui-state-default ui-corner-all");
this.panels=this.headers.next()
.addClass("ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom")
.filter(":not(.ui-accordion-content-active)")
.hide();
if(prevPanels){
this._off(prevHeaders.not(this.headers));
this._off(prevPanels.not(this.panels));
}},
_refresh: function(){
var maxHeight,
options=this.options,
heightStyle=options.heightStyle,
parent=this.element.parent();
this.active=this._findActive(options.active)
.addClass("ui-accordion-header-active ui-state-active ui-corner-top")
.removeClass("ui-corner-all");
this.active.next()
.addClass("ui-accordion-content-active")
.show();
this.headers
.attr("role", "tab")
.each(function(){
var header=$(this),
headerId=header.uniqueId().attr("id"),
panel=header.next(),
panelId=panel.uniqueId().attr("id");
header.attr("aria-controls", panelId);
panel.attr("aria-labelledby", headerId);
})
.next()
.attr("role", "tabpanel");
this.headers
.not(this.active)
.attr({
"aria-selected": "false",
"aria-expanded": "false",
tabIndex: -1
})
.next()
.attr({
"aria-hidden": "true"
})
.hide();
if(!this.active.length){
this.headers.eq(0).attr("tabIndex", 0);
}else{
this.active.attr({
"aria-selected": "true",
"aria-expanded": "true",
tabIndex: 0
})
.next()
.attr({
"aria-hidden": "false"
});
}
this._createIcons();
this._setupEvents(options.event);
if(heightStyle==="fill"){
maxHeight=parent.height();
this.element.siblings(":visible").each(function(){
var elem=$(this),
position=elem.css("position");
if(position==="absolute"||position==="fixed"){
return;
}
maxHeight -=elem.outerHeight(true);
});
this.headers.each(function(){
maxHeight -=$(this).outerHeight(true);
});
this.headers.next()
.each(function(){
$(this).height(Math.max(0, maxHeight -
$(this).innerHeight() + $(this).height()));
})
.css("overflow", "auto");
}else if(heightStyle==="auto"){
maxHeight=0;
this.headers.next()
.each(function(){
maxHeight=Math.max(maxHeight, $(this).css("height", "").height());
})
.height(maxHeight);
}},
_activate: function(index){
var active=this._findActive(index)[ 0 ];
if(active===this.active[ 0 ]){
return;
}
active=active||this.active[ 0 ];
this._eventHandler({
target: active,
currentTarget: active,
preventDefault: $.noop
});
},
_findActive: function(selector){
return typeof selector==="number" ? this.headers.eq(selector):$();
},
_setupEvents: function(event){
var events={
keydown: "_keydown"
};
if(event){
$.each(event.split(" "), function(index, eventName){
events[ eventName ]="_eventHandler";
});
}
this._off(this.headers.add(this.headers.next()));
this._on(this.headers, events);
this._on(this.headers.next(), { keydown: "_panelKeyDown" });
this._hoverable(this.headers);
this._focusable(this.headers);
},
_eventHandler: function(event){
var options=this.options,
active=this.active,
clicked=$(event.currentTarget),
clickedIsActive=clicked[ 0 ]===active[ 0 ],
collapsing=clickedIsActive&&options.collapsible,
toShow=collapsing ? $():clicked.next(),
toHide=active.next(),
eventData={
oldHeader: active,
oldPanel: toHide,
newHeader: collapsing ? $():clicked,
newPanel: toShow
};
event.preventDefault();
if((clickedIsActive&&!options.collapsible) ||
(this._trigger("beforeActivate", event, eventData)===false)){
return;
}
options.active=collapsing ? false:this.headers.index(clicked);
this.active=clickedIsActive ? $():clicked;
this._toggle(eventData);
active.removeClass("ui-accordion-header-active ui-state-active");
if(options.icons){
active.children(".ui-accordion-header-icon")
.removeClass(options.icons.activeHeader)
.addClass(options.icons.header);
}
if(!clickedIsActive){
clicked
.removeClass("ui-corner-all")
.addClass("ui-accordion-header-active ui-state-active ui-corner-top");
if(options.icons){
clicked.children(".ui-accordion-header-icon")
.removeClass(options.icons.header)
.addClass(options.icons.activeHeader);
}
clicked
.next()
.addClass("ui-accordion-content-active");
}},
_toggle: function(data){
var toShow=data.newPanel,
toHide=this.prevShow.length ? this.prevShow:data.oldPanel;
this.prevShow.add(this.prevHide).stop(true, true);
this.prevShow=toShow;
this.prevHide=toHide;
if(this.options.animate){
this._animate(toShow, toHide, data);
}else{
toHide.hide();
toShow.show();
this._toggleComplete(data);
}
toHide.attr({
"aria-hidden": "true"
});
toHide.prev().attr({
"aria-selected": "false",
"aria-expanded": "false"
});
if(toShow.length&&toHide.length){
toHide.prev().attr({
"tabIndex": -1,
"aria-expanded": "false"
});
}else if(toShow.length){
this.headers.filter(function(){
return parseInt($(this).attr("tabIndex"), 10)===0;
})
.attr("tabIndex", -1);
}
toShow
.attr("aria-hidden", "false")
.prev()
.attr({
"aria-selected": "true",
"aria-expanded": "true",
tabIndex: 0
});
},
_animate: function(toShow, toHide, data){
var total, easing, duration,
that=this,
adjust=0,
boxSizing=toShow.css("box-sizing"),
down=toShow.length &&
(!toHide.length||(toShow.index() < toHide.index())),
animate=this.options.animate||{},
options=down&&animate.down||animate,
complete=function(){
that._toggleComplete(data);
};
if(typeof options==="number"){
duration=options;
}
if(typeof options==="string"){
easing=options;
}
easing=easing||options.easing||animate.easing;
duration=duration||options.duration||animate.duration;
if(!toHide.length){
return toShow.animate(this.showProps, duration, easing, complete);
}
if(!toShow.length){
return toHide.animate(this.hideProps, duration, easing, complete);
}
total=toShow.show().outerHeight();
toHide.animate(this.hideProps, {
duration: duration,
easing: easing,
step: function(now, fx){
fx.now=Math.round(now);
}});
toShow
.hide()
.animate(this.showProps, {
duration: duration,
easing: easing,
complete: complete,
step: function(now, fx){
fx.now=Math.round(now);
if(fx.prop!=="height"){
if(boxSizing==="content-box"){
adjust +=fx.now;
}}else if(that.options.heightStyle!=="content"){
fx.now=Math.round(total - toHide.outerHeight() - adjust);
adjust=0;
}}
});
},
_toggleComplete: function(data){
var toHide=data.oldPanel;
toHide
.removeClass("ui-accordion-content-active")
.prev()
.removeClass("ui-corner-top")
.addClass("ui-corner-all");
if(toHide.length){
toHide.parent()[ 0 ].className=toHide.parent()[ 0 ].className;
}
this._trigger("activate", null, data);
}});
var menu=$.widget("ui.menu", {
version: "1.11.4",
defaultElement: "<ul>",
delay: 300,
options: {
icons: {
submenu: "ui-icon-carat-1-e"
},
items: "> *",
menus: "ul",
position: {
my: "left-1 top",
at: "right top"
},
role: "menu",
blur: null,
focus: null,
select: null
},
_create: function(){
this.activeMenu=this.element;
this.mouseHandled=false;
this.element
.uniqueId()
.addClass("ui-menu ui-widget ui-widget-content")
.toggleClass("ui-menu-icons", !!this.element.find(".ui-icon").length)
.attr({
role: this.options.role,
tabIndex: 0
});
if(this.options.disabled){
this.element
.addClass("ui-state-disabled")
.attr("aria-disabled", "true");
}
this._on({
"mousedown .ui-menu-item": function(event){
event.preventDefault();
},
"click .ui-menu-item": function(event){
var target=$(event.target);
if(!this.mouseHandled&&target.not(".ui-state-disabled").length){
this.select(event);
if(!event.isPropagationStopped()){
this.mouseHandled=true;
}
if(target.has(".ui-menu").length){
this.expand (event);
}else if(!this.element.is(":focus")&&$(this.document[ 0 ].activeElement).closest(".ui-menu").length){
this.element.trigger("focus", [ true ]);
if(this.active&&this.active.parents(".ui-menu").length===1){
clearTimeout(this.timer);
}}
}},
"mouseenter .ui-menu-item": function(event){
if(this.previousFilter){
return;
}
var target=$(event.currentTarget);
target.siblings(".ui-state-active").removeClass("ui-state-active");
this.focus(event, target);
},
mouseleave: "collapseAll",
"mouseleave .ui-menu": "collapseAll",
focus: function(event, keepActiveItem){
var item=this.active||this.element.find(this.options.items).eq(0);
if(!keepActiveItem){
this.focus(event, item);
}},
blur: function(event){
this._delay(function(){
if(!$.contains(this.element[0], this.document[0].activeElement)){
this.collapseAll(event);
}});
},
keydown: "_keydown"
});
this.refresh();
this._on(this.document, {
click: function(event){
if(this._closeOnDocumentClick(event)){
this.collapseAll(event);
}
this.mouseHandled=false;
}});
},
_destroy: function(){
this.element
.removeAttr("aria-activedescendant")
.find(".ui-menu").addBack()
.removeClass("ui-menu ui-widget ui-widget-content ui-menu-icons ui-front")
.removeAttr("role")
.removeAttr("tabIndex")
.removeAttr("aria-labelledby")
.removeAttr("aria-expanded")
.removeAttr("aria-hidden")
.removeAttr("aria-disabled")
.removeUniqueId()
.show();
this.element.find(".ui-menu-item")
.removeClass("ui-menu-item")
.removeAttr("role")
.removeAttr("aria-disabled")
.removeUniqueId()
.removeClass("ui-state-hover")
.removeAttr("tabIndex")
.removeAttr("role")
.removeAttr("aria-haspopup")
.children().each(function(){
var elem=$(this);
if(elem.data("ui-menu-submenu-carat")){
elem.remove();
}});
this.element.find(".ui-menu-divider").removeClass("ui-menu-divider ui-widget-content");
},
_keydown: function(event){
var match, prev, character, skip,
preventDefault=true;
switch(event.keyCode){
case $.ui.keyCode.PAGE_UP:
this.previousPage(event);
break;
case $.ui.keyCode.PAGE_DOWN:
this.nextPage(event);
break;
case $.ui.keyCode.HOME:
this._move("first", "first", event);
break;
case $.ui.keyCode.END:
this._move("last", "last", event);
break;
case $.ui.keyCode.UP:
this.previous(event);
break;
case $.ui.keyCode.DOWN:
this.next(event);
break;
case $.ui.keyCode.LEFT:
this.collapse(event);
break;
case $.ui.keyCode.RIGHT:
if(this.active&&!this.active.is(".ui-state-disabled")){
this.expand (event);
}
break;
case $.ui.keyCode.ENTER:
case $.ui.keyCode.SPACE:
this._activate(event);
break;
case $.ui.keyCode.ESCAPE:
this.collapse(event);
break;
default:
preventDefault=false;
prev=this.previousFilter||"";
character=String.fromCharCode(event.keyCode);
skip=false;
clearTimeout(this.filterTimer);
if(character===prev){
skip=true;
}else{
character=prev + character;
}
match=this._filterMenuItems(character);
match=skip&&match.index(this.active.next())!==-1 ?
this.active.nextAll(".ui-menu-item") :
match;
if(!match.length){
character=String.fromCharCode(event.keyCode);
match=this._filterMenuItems(character);
}
if(match.length){
this.focus(event, match);
this.previousFilter=character;
this.filterTimer=this._delay(function(){
delete this.previousFilter;
}, 1000);
}else{
delete this.previousFilter;
}}
if(preventDefault){
event.preventDefault();
}},
_activate: function(event){
if(!this.active.is(".ui-state-disabled")){
if(this.active.is("[aria-haspopup='true']")){
this.expand (event);
}else{
this.select(event);
}}
},
refresh: function(){
var menus, items,
that=this,
icon=this.options.icons.submenu,
submenus=this.element.find(this.options.menus);
this.element.toggleClass("ui-menu-icons", !!this.element.find(".ui-icon").length);
submenus.filter(":not(.ui-menu)")
.addClass("ui-menu ui-widget ui-widget-content ui-front")
.hide()
.attr({
role: this.options.role,
"aria-hidden": "true",
"aria-expanded": "false"
})
.each(function(){
var menu=$(this),
item=menu.parent(),
submenuCarat=$("<span>")
.addClass("ui-menu-icon ui-icon " + icon)
.data("ui-menu-submenu-carat", true);
item
.attr("aria-haspopup", "true")
.prepend(submenuCarat);
menu.attr("aria-labelledby", item.attr("id"));
});
menus=submenus.add(this.element);
items=menus.find(this.options.items);
items.not(".ui-menu-item").each(function(){
var item=$(this);
if(that._isDivider(item)){
item.addClass("ui-widget-content ui-menu-divider");
}});
items.not(".ui-menu-item, .ui-menu-divider")
.addClass("ui-menu-item")
.uniqueId()
.attr({
tabIndex: -1,
role: this._itemRole()
});
items.filter(".ui-state-disabled").attr("aria-disabled", "true");
if(this.active&&!$.contains(this.element[ 0 ], this.active[ 0 ])){
this.blur();
}},
_itemRole: function(){
return {
menu: "menuitem",
listbox: "option"
}[ this.options.role ];
},
_setOption: function(key, value){
if(key==="icons"){
this.element.find(".ui-menu-icon")
.removeClass(this.options.icons.submenu)
.addClass(value.submenu);
}
if(key==="disabled"){
this.element
.toggleClass("ui-state-disabled", !!value)
.attr("aria-disabled", value);
}
this._super(key, value);
},
focus: function(event, item){
var nested, focused;
this.blur(event, event&&event.type==="focus");
this._scrollIntoView(item);
this.active=item.first();
focused=this.active.addClass("ui-state-focus").removeClass("ui-state-active");
if(this.options.role){
this.element.attr("aria-activedescendant", focused.attr("id"));
}
this.active
.parent()
.closest(".ui-menu-item")
.addClass("ui-state-active");
if(event&&event.type==="keydown"){
this._close();
}else{
this.timer=this._delay(function(){
this._close();
}, this.delay);
}
nested=item.children(".ui-menu");
if(nested.length&&event&&(/^mouse/.test(event.type))){
this._startOpening(nested);
}
this.activeMenu=item.parent();
this._trigger("focus", event, { item: item });
},
_scrollIntoView: function(item){
var borderTop, paddingTop, offset, scroll, elementHeight, itemHeight;
if(this._hasScroll()){
borderTop=parseFloat($.css(this.activeMenu[0], "borderTopWidth"))||0;
paddingTop=parseFloat($.css(this.activeMenu[0], "paddingTop"))||0;
offset=item.offset().top - this.activeMenu.offset().top - borderTop - paddingTop;
scroll=this.activeMenu.scrollTop();
elementHeight=this.activeMenu.height();
itemHeight=item.outerHeight();
if(offset < 0){
this.activeMenu.scrollTop(scroll + offset);
}else if(offset + itemHeight > elementHeight){
this.activeMenu.scrollTop(scroll + offset - elementHeight + itemHeight);
}}
},
blur: function(event, fromFocus){
if(!fromFocus){
clearTimeout(this.timer);
}
if(!this.active){
return;
}
this.active.removeClass("ui-state-focus");
this.active=null;
this._trigger("blur", event, { item: this.active });
},
_startOpening: function(submenu){
clearTimeout(this.timer);
if(submenu.attr("aria-hidden")!=="true"){
return;
}
this.timer=this._delay(function(){
this._close();
this._open(submenu);
}, this.delay);
},
_open: function(submenu){
var position=$.extend({
of: this.active
}, this.options.position);
clearTimeout(this.timer);
this.element.find(".ui-menu").not(submenu.parents(".ui-menu"))
.hide()
.attr("aria-hidden", "true");
submenu
.show()
.removeAttr("aria-hidden")
.attr("aria-expanded", "true")
.position(position);
},
collapseAll: function(event, all){
clearTimeout(this.timer);
this.timer=this._delay(function(){
var currentMenu=all ? this.element :
$(event&&event.target).closest(this.element.find(".ui-menu"));
if(!currentMenu.length){
currentMenu=this.element;
}
this._close(currentMenu);
this.blur(event);
this.activeMenu=currentMenu;
}, this.delay);
},
_close: function(startMenu){
if(!startMenu){
startMenu=this.active ? this.active.parent():this.element;
}
startMenu
.find(".ui-menu")
.hide()
.attr("aria-hidden", "true")
.attr("aria-expanded", "false")
.end()
.find(".ui-state-active").not(".ui-state-focus")
.removeClass("ui-state-active");
},
_closeOnDocumentClick: function(event){
return !$(event.target).closest(".ui-menu").length;
},
_isDivider: function(item){
return !/[^\-\u2014\u2013\s]/.test(item.text());
},
collapse: function(event){
var newItem=this.active &&
this.active.parent().closest(".ui-menu-item", this.element);
if(newItem&&newItem.length){
this._close();
this.focus(event, newItem);
}},
expand: function(event){
var newItem=this.active &&
this.active
.children(".ui-menu ")
.find(this.options.items)
.first();
if(newItem&&newItem.length){
this._open(newItem.parent());
this._delay(function(){
this.focus(event, newItem);
});
}},
next: function(event){
this._move("next", "first", event);
},
previous: function(event){
this._move("prev", "last", event);
},
isFirstItem: function(){
return this.active&&!this.active.prevAll(".ui-menu-item").length;
},
isLastItem: function(){
return this.active&&!this.active.nextAll(".ui-menu-item").length;
},
_move: function(direction, filter, event){
var next;
if(this.active){
if(direction==="first"||direction==="last"){
next=this.active
[ direction==="first" ? "prevAll":"nextAll" ](".ui-menu-item")
.eq(-1);
}else{
next=this.active
[ direction + "All" ](".ui-menu-item")
.eq(0);
}}
if(!next||!next.length||!this.active){
next=this.activeMenu.find(this.options.items)[ filter ]();
}
this.focus(event, next);
},
nextPage: function(event){
var item, base, height;
if(!this.active){
this.next(event);
return;
}
if(this.isLastItem()){
return;
}
if(this._hasScroll()){
base=this.active.offset().top;
height=this.element.height();
this.active.nextAll(".ui-menu-item").each(function(){
item=$(this);
return item.offset().top - base - height < 0;
});
this.focus(event, item);
}else{
this.focus(event, this.activeMenu.find(this.options.items)
[ !this.active ? "first":"last" ]());
}},
previousPage: function(event){
var item, base, height;
if(!this.active){
this.next(event);
return;
}
if(this.isFirstItem()){
return;
}
if(this._hasScroll()){
base=this.active.offset().top;
height=this.element.height();
this.active.prevAll(".ui-menu-item").each(function(){
item=$(this);
return item.offset().top - base + height > 0;
});
this.focus(event, item);
}else{
this.focus(event, this.activeMenu.find(this.options.items).first());
}},
_hasScroll: function(){
return this.element.outerHeight() < this.element.prop("scrollHeight");
},
select: function(event){
this.active=this.active||$(event.target).closest(".ui-menu-item");
var ui={ item: this.active };
if(!this.active.has(".ui-menu").length){
this.collapseAll(event, true);
}
this._trigger("select", event, ui);
},
_filterMenuItems: function(character){
var escapedCharacter=character.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, "\\$&"),
regex=new RegExp("^" + escapedCharacter, "i");
return this.activeMenu
.find(this.options.items)
.filter(".ui-menu-item")
.filter(function(){
return regex.test($.trim($(this).text()));
});
}});
$.widget("ui.autocomplete", {
version: "1.11.4",
defaultElement: "<input>",
options: {
appendTo: null,
autoFocus: false,
delay: 300,
minLength: 1,
position: {
my: "left top",
at: "left bottom",
collision: "none"
},
source: null,
change: null,
close: null,
focus: null,
open: null,
response: null,
search: null,
select: null
},
requestIndex: 0,
pending: 0,
_create: function(){
var suppressKeyPress, suppressKeyPressRepeat, suppressInput,
nodeName=this.element[ 0 ].nodeName.toLowerCase(),
isTextarea=nodeName==="textarea",
isInput=nodeName==="input";
this.isMultiLine =
isTextarea ? true :
isInput ? false :
this.element.prop("isContentEditable");
this.valueMethod=this.element[ isTextarea||isInput ? "val":"text" ];
this.isNewMenu=true;
this.element
.addClass("ui-autocomplete-input")
.attr("autocomplete", "off");
this._on(this.element, {
keydown: function(event){
if(this.element.prop("readOnly")){
suppressKeyPress=true;
suppressInput=true;
suppressKeyPressRepeat=true;
return;
}
suppressKeyPress=false;
suppressInput=false;
suppressKeyPressRepeat=false;
var keyCode=$.ui.keyCode;
switch(event.keyCode){
case keyCode.PAGE_UP:
suppressKeyPress=true;
this._move("previousPage", event);
break;
case keyCode.PAGE_DOWN:
suppressKeyPress=true;
this._move("nextPage", event);
break;
case keyCode.UP:
suppressKeyPress=true;
this._keyEvent("previous", event);
break;
case keyCode.DOWN:
suppressKeyPress=true;
this._keyEvent("next", event);
break;
case keyCode.ENTER:
if(this.menu.active){
suppressKeyPress=true;
event.preventDefault();
this.menu.select(event);
}
break;
case keyCode.TAB:
if(this.menu.active){
this.menu.select(event);
}
break;
case keyCode.ESCAPE:
if(this.menu.element.is(":visible")){
if(!this.isMultiLine){
this._value(this.term);
}
this.close(event);
event.preventDefault();
}
break;
default:
suppressKeyPressRepeat=true;
this._searchTimeout(event);
break;
}},
keypress: function(event){
if(suppressKeyPress){
suppressKeyPress=false;
if(!this.isMultiLine||this.menu.element.is(":visible")){
event.preventDefault();
}
return;
}
if(suppressKeyPressRepeat){
return;
}
var keyCode=$.ui.keyCode;
switch(event.keyCode){
case keyCode.PAGE_UP:
this._move("previousPage", event);
break;
case keyCode.PAGE_DOWN:
this._move("nextPage", event);
break;
case keyCode.UP:
this._keyEvent("previous", event);
break;
case keyCode.DOWN:
this._keyEvent("next", event);
break;
}},
input: function(event){
if(suppressInput){
suppressInput=false;
event.preventDefault();
return;
}
this._searchTimeout(event);
},
focus: function(){
this.selectedItem=null;
this.previous=this._value();
},
blur: function(event){
if(this.cancelBlur){
delete this.cancelBlur;
return;
}
clearTimeout(this.searching);
this.close(event);
this._change(event);
}});
this._initSource();
this.menu=$("<ul>")
.addClass("ui-autocomplete ui-front")
.appendTo(this._appendTo())
.menu({
role: null
})
.hide()
.menu("instance");
this._on(this.menu.element, {
mousedown: function(event){
event.preventDefault();
this.cancelBlur=true;
this._delay(function(){
delete this.cancelBlur;
});
var menuElement=this.menu.element[ 0 ];
if(!$(event.target).closest(".ui-menu-item").length){
this._delay(function(){
var that=this;
this.document.one("mousedown", function(event){
if(event.target!==that.element[ 0 ] &&
event.target!==menuElement &&
!$.contains(menuElement, event.target)){
that.close();
}});
});
}},
menufocus: function(event, ui){
var label, item;
if(this.isNewMenu){
this.isNewMenu=false;
if(event.originalEvent&&/^mouse/.test(event.originalEvent.type)){
this.menu.blur();
this.document.one("mousemove", function(){
$(event.target).trigger(event.originalEvent);
});
return;
}}
item=ui.item.data("ui-autocomplete-item");
if(false!==this._trigger("focus", event, { item: item })){
if(event.originalEvent&&/^key/.test(event.originalEvent.type)){
this._value(item.value);
}}
label=ui.item.attr("aria-label")||item.value;
if(label&&$.trim(label).length){
this.liveRegion.children().hide();
$("<div>").text(label).appendTo(this.liveRegion);
}},
menuselect: function(event, ui){
var item=ui.item.data("ui-autocomplete-item"),
previous=this.previous;
if(this.element[ 0 ]!==this.document[ 0 ].activeElement){
this.element.focus();
this.previous=previous;
this._delay(function(){
this.previous=previous;
this.selectedItem=item;
});
}
if(false!==this._trigger("select", event, { item: item })){
this._value(item.value);
}
this.term=this._value();
this.close(event);
this.selectedItem=item;
}});
this.liveRegion=$("<span>", {
role: "status",
"aria-live": "assertive",
"aria-relevant": "additions"
})
.addClass("ui-helper-hidden-accessible")
.appendTo(this.document[ 0 ].body);
this._on(this.window, {
beforeunload: function(){
this.element.removeAttr("autocomplete");
}});
},
_destroy: function(){
clearTimeout(this.searching);
this.element
.removeClass("ui-autocomplete-input")
.removeAttr("autocomplete");
this.menu.element.remove();
this.liveRegion.remove();
},
_setOption: function(key, value){
this._super(key, value);
if(key==="source"){
this._initSource();
}
if(key==="appendTo"){
this.menu.element.appendTo(this._appendTo());
}
if(key==="disabled"&&value&&this.xhr){
this.xhr.abort();
}},
_appendTo: function(){
var element=this.options.appendTo;
if(element){
element=element.jquery||element.nodeType ?
$(element) :
this.document.find(element).eq(0);
}
if(!element||!element[ 0 ]){
element=this.element.closest(".ui-front");
}
if(!element.length){
element=this.document[ 0 ].body;
}
return element;
},
_initSource: function(){
var array, url,
that=this;
if($.isArray(this.options.source)){
array=this.options.source;
this.source=function(request, response){
response($.ui.autocomplete.filter(array, request.term));
};}else if(typeof this.options.source==="string"){
url=this.options.source;
this.source=function(request, response){
if(that.xhr){
that.xhr.abort();
}
that.xhr=$.ajax({
url: url,
data: request,
dataType: "json",
success: function(data){
response(data);
},
error: function(){
response([]);
}});
};}else{
this.source=this.options.source;
}},
_searchTimeout: function(event){
clearTimeout(this.searching);
this.searching=this._delay(function(){
var equalValues=this.term===this._value(),
menuVisible=this.menu.element.is(":visible"),
modifierKey=event.altKey||event.ctrlKey||event.metaKey||event.shiftKey;
if(!equalValues||(equalValues&&!menuVisible&&!modifierKey)){
this.selectedItem=null;
this.search(null, event);
}}, this.options.delay);
},
search: function(value, event){
value=value!=null ? value:this._value();
this.term=this._value();
if(value.length < this.options.minLength){
return this.close(event);
}
if(this._trigger("search", event)===false){
return;
}
return this._search(value);
},
_search: function(value){
this.pending++;
this.element.addClass("ui-autocomplete-loading");
this.cancelSearch=false;
this.source({ term: value }, this._response());
},
_response: function(){
var index=++this.requestIndex;
return $.proxy(function(content){
if(index===this.requestIndex){
this.__response(content);
}
this.pending--;
if(!this.pending){
this.element.removeClass("ui-autocomplete-loading");
}}, this);
},
__response: function(content){
if(content){
content=this._normalize(content);
}
this._trigger("response", null, { content: content });
if(!this.options.disabled&&content&&content.length&&!this.cancelSearch){
this._suggest(content);
this._trigger("open");
}else{
this._close();
}},
close: function(event){
this.cancelSearch=true;
this._close(event);
},
_close: function(event){
if(this.menu.element.is(":visible")){
this.menu.element.hide();
this.menu.blur();
this.isNewMenu=true;
this._trigger("close", event);
}},
_change: function(event){
if(this.previous!==this._value()){
this._trigger("change", event, { item: this.selectedItem });
}},
_normalize: function(items){
if(items.length&&items[ 0 ].label&&items[ 0 ].value){
return items;
}
return $.map(items, function(item){
if(typeof item==="string"){
return {
label: item,
value: item
};}
return $.extend({}, item, {
label: item.label||item.value,
value: item.value||item.label
});
});
},
_suggest: function(items){
var ul=this.menu.element.empty();
this._renderMenu(ul, items);
this.isNewMenu=true;
this.menu.refresh();
ul.show();
this._resizeMenu();
ul.position($.extend({
of: this.element
}, this.options.position));
if(this.options.autoFocus){
this.menu.next();
}},
_resizeMenu: function(){
var ul=this.menu.element;
ul.outerWidth(Math.max(ul.width("").outerWidth() + 1,
this.element.outerWidth()
));
},
_renderMenu: function(ul, items){
var that=this;
$.each(items, function(index, item){
that._renderItemData(ul, item);
});
},
_renderItemData: function(ul, item){
return this._renderItem(ul, item).data("ui-autocomplete-item", item);
},
_renderItem: function(ul, item){
return $("<li>").text(item.label).appendTo(ul);
},
_move: function(direction, event){
if(!this.menu.element.is(":visible")){
this.search(null, event);
return;
}
if(this.menu.isFirstItem()&&/^previous/.test(direction) ||
this.menu.isLastItem()&&/^next/.test(direction)){
if(!this.isMultiLine){
this._value(this.term);
}
this.menu.blur();
return;
}
this.menu[ direction ](event);
},
widget: function(){
return this.menu.element;
},
_value: function(){
return this.valueMethod.apply(this.element, arguments);
},
_keyEvent: function(keyEvent, event){
if(!this.isMultiLine||this.menu.element.is(":visible")){
this._move(keyEvent, event);
event.preventDefault();
}}
});
$.extend($.ui.autocomplete, {
escapeRegex: function(value){
return value.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, "\\$&");
},
filter: function(array, term){
var matcher=new RegExp($.ui.autocomplete.escapeRegex(term), "i");
return $.grep(array, function(value){
return matcher.test(value.label||value.value||value);
});
}});
$.widget("ui.autocomplete", $.ui.autocomplete, {
options: {
messages: {
noResults: "No search results.",
results: function(amount){
return amount +(amount > 1 ? " results are":" result is") +
" available, use up and down arrow keys to navigate.";
}}
},
__response: function(content){
var message;
this._superApply(arguments);
if(this.options.disabled||this.cancelSearch){
return;
}
if(content&&content.length){
message=this.options.messages.results(content.length);
}else{
message=this.options.messages.noResults;
}
this.liveRegion.children().hide();
$("<div>").text(message).appendTo(this.liveRegion);
}});
var autocomplete=$.ui.autocomplete;
var lastActive,
baseClasses="ui-button ui-widget ui-state-default ui-corner-all",
typeClasses="ui-button-icons-only ui-button-icon-only ui-button-text-icons ui-button-text-icon-primary ui-button-text-icon-secondary ui-button-text-only",
formResetHandler=function(){
var form=$(this);
setTimeout(function(){
form.find(":ui-button").button("refresh");
}, 1);
},
radioGroup=function(radio){
var name=radio.name,
form=radio.form,
radios=$([]);
if(name){
name=name.replace(/'/g, "\\'");
if(form){
radios=$(form).find("[name='" + name + "'][type=radio]");
}else{
radios=$("[name='" + name + "'][type=radio]", radio.ownerDocument)
.filter(function(){
return !this.form;
});
}}
return radios;
};
$.widget("ui.button", {
version: "1.11.4",
defaultElement: "<button>",
options: {
disabled: null,
text: true,
label: null,
icons: {
primary: null,
secondary: null
}},
_create: function(){
this.element.closest("form")
.unbind("reset" + this.eventNamespace)
.bind("reset" + this.eventNamespace, formResetHandler);
if(typeof this.options.disabled!=="boolean"){
this.options.disabled = !!this.element.prop("disabled");
}else{
this.element.prop("disabled", this.options.disabled);
}
this._determineButtonType();
this.hasTitle = !!this.buttonElement.attr("title");
var that=this,
options=this.options,
toggleButton=this.type==="checkbox"||this.type==="radio",
activeClass = !toggleButton ? "ui-state-active":"";
if(options.label===null){
options.label=(this.type==="input" ? this.buttonElement.val():this.buttonElement.html());
}
this._hoverable(this.buttonElement);
this.buttonElement
.addClass(baseClasses)
.attr("role", "button")
.bind("mouseenter" + this.eventNamespace, function(){
if(options.disabled){
return;
}
if(this===lastActive){
$(this).addClass("ui-state-active");
}})
.bind("mouseleave" + this.eventNamespace, function(){
if(options.disabled){
return;
}
$(this).removeClass(activeClass);
})
.bind("click" + this.eventNamespace, function(event){
if(options.disabled){
event.preventDefault();
event.stopImmediatePropagation();
}});
this._on({
focus: function(){
this.buttonElement.addClass("ui-state-focus");
},
blur: function(){
this.buttonElement.removeClass("ui-state-focus");
}});
if(toggleButton){
this.element.bind("change" + this.eventNamespace, function(){
that.refresh();
});
}
if(this.type==="checkbox"){
this.buttonElement.bind("click" + this.eventNamespace, function(){
if(options.disabled){
return false;
}});
}else if(this.type==="radio"){
this.buttonElement.bind("click" + this.eventNamespace, function(){
if(options.disabled){
return false;
}
$(this).addClass("ui-state-active");
that.buttonElement.attr("aria-pressed", "true");
var radio=that.element[ 0 ];
radioGroup(radio)
.not(radio)
.map(function(){
return $(this).button("widget")[ 0 ];
})
.removeClass("ui-state-active")
.attr("aria-pressed", "false");
});
}else{
this.buttonElement
.bind("mousedown" + this.eventNamespace, function(){
if(options.disabled){
return false;
}
$(this).addClass("ui-state-active");
lastActive=this;
that.document.one("mouseup", function(){
lastActive=null;
});
})
.bind("mouseup" + this.eventNamespace, function(){
if(options.disabled){
return false;
}
$(this).removeClass("ui-state-active");
})
.bind("keydown" + this.eventNamespace, function(event){
if(options.disabled){
return false;
}
if(event.keyCode===$.ui.keyCode.SPACE||event.keyCode===$.ui.keyCode.ENTER){
$(this).addClass("ui-state-active");
}})
.bind("keyup" + this.eventNamespace + " blur" + this.eventNamespace, function(){
$(this).removeClass("ui-state-active");
});
if(this.buttonElement.is("a")){
this.buttonElement.keyup(function(event){
if(event.keyCode===$.ui.keyCode.SPACE){
$(this).click();
}});
}}
this._setOption("disabled", options.disabled);
this._resetButton();
},
_determineButtonType: function(){
var ancestor, labelSelector, checked;
if(this.element.is("[type=checkbox]")){
this.type="checkbox";
}else if(this.element.is("[type=radio]")){
this.type="radio";
}else if(this.element.is("input")){
this.type="input";
}else{
this.type="button";
}
if(this.type==="checkbox"||this.type==="radio"){
ancestor=this.element.parents().last();
labelSelector="label[for='" + this.element.attr("id") + "']";
this.buttonElement=ancestor.find(labelSelector);
if(!this.buttonElement.length){
ancestor=ancestor.length ? ancestor.siblings():this.element.siblings();
this.buttonElement=ancestor.filter(labelSelector);
if(!this.buttonElement.length){
this.buttonElement=ancestor.find(labelSelector);
}}
this.element.addClass("ui-helper-hidden-accessible");
checked=this.element.is(":checked");
if(checked){
this.buttonElement.addClass("ui-state-active");
}
this.buttonElement.prop("aria-pressed", checked);
}else{
this.buttonElement=this.element;
}},
widget: function(){
return this.buttonElement;
},
_destroy: function(){
this.element
.removeClass("ui-helper-hidden-accessible");
this.buttonElement
.removeClass(baseClasses + " ui-state-active " + typeClasses)
.removeAttr("role")
.removeAttr("aria-pressed")
.html(this.buttonElement.find(".ui-button-text").html());
if(!this.hasTitle){
this.buttonElement.removeAttr("title");
}},
_setOption: function(key, value){
this._super(key, value);
if(key==="disabled"){
this.widget().toggleClass("ui-state-disabled", !!value);
this.element.prop("disabled", !!value);
if(value){
if(this.type==="checkbox"||this.type==="radio"){
this.buttonElement.removeClass("ui-state-focus");
}else{
this.buttonElement.removeClass("ui-state-focus ui-state-active");
}}
return;
}
this._resetButton();
},
refresh: function(){
var isDisabled=this.element.is("input, button") ? this.element.is(":disabled"):this.element.hasClass("ui-button-disabled");
if(isDisabled!==this.options.disabled){
this._setOption("disabled", isDisabled);
}
if(this.type==="radio"){
radioGroup(this.element[0]).each(function(){
if($(this).is(":checked")){
$(this).button("widget")
.addClass("ui-state-active")
.attr("aria-pressed", "true");
}else{
$(this).button("widget")
.removeClass("ui-state-active")
.attr("aria-pressed", "false");
}});
}else if(this.type==="checkbox"){
if(this.element.is(":checked")){
this.buttonElement
.addClass("ui-state-active")
.attr("aria-pressed", "true");
}else{
this.buttonElement
.removeClass("ui-state-active")
.attr("aria-pressed", "false");
}}
},
_resetButton: function(){
if(this.type==="input"){
if(this.options.label){
this.element.val(this.options.label);
}
return;
}
var buttonElement=this.buttonElement.removeClass(typeClasses),
buttonText=$("<span></span>", this.document[0])
.addClass("ui-button-text")
.html(this.options.label)
.appendTo(buttonElement.empty())
.text(),
icons=this.options.icons,
multipleIcons=icons.primary&&icons.secondary,
buttonClasses=[];
if(icons.primary||icons.secondary){
if(this.options.text){
buttonClasses.push("ui-button-text-icon" +(multipleIcons ? "s":(icons.primary ? "-primary":"-secondary")));
}
if(icons.primary){
buttonElement.prepend("<span class='ui-button-icon-primary ui-icon " + icons.primary + "'></span>");
}
if(icons.secondary){
buttonElement.append("<span class='ui-button-icon-secondary ui-icon " + icons.secondary + "'></span>");
}
if(!this.options.text){
buttonClasses.push(multipleIcons ? "ui-button-icons-only":"ui-button-icon-only");
if(!this.hasTitle){
buttonElement.attr("title", $.trim(buttonText));
}}
}else{
buttonClasses.push("ui-button-text-only");
}
buttonElement.addClass(buttonClasses.join(" "));
}});
$.widget("ui.buttonset", {
version: "1.11.4",
options: {
items: "button, input[type=button], input[type=submit], input[type=reset], input[type=checkbox], input[type=radio], a, :data(ui-button)"
},
_create: function(){
this.element.addClass("ui-buttonset");
},
_init: function(){
this.refresh();
},
_setOption: function(key, value){
if(key==="disabled"){
this.buttons.button("option", key, value);
}
this._super(key, value);
},
refresh: function(){
var rtl=this.element.css("direction")==="rtl",
allButtons=this.element.find(this.options.items),
existingButtons=allButtons.filter(":ui-button");
allButtons.not(":ui-button").button();
existingButtons.button("refresh");
this.buttons=allButtons
.map(function(){
return $(this).button("widget")[ 0 ];
})
.removeClass("ui-corner-all ui-corner-left ui-corner-right")
.filter(":first")
.addClass(rtl ? "ui-corner-right":"ui-corner-left")
.end()
.filter(":last")
.addClass(rtl ? "ui-corner-left":"ui-corner-right")
.end()
.end();
},
_destroy: function(){
this.element.removeClass("ui-buttonset");
this.buttons
.map(function(){
return $(this).button("widget")[ 0 ];
})
.removeClass("ui-corner-left ui-corner-right")
.end()
.button("destroy");
}});
var button=$.ui.button;
$.extend($.ui, { datepicker: { version: "1.11.4" }});
var datepicker_instActive;
function datepicker_getZindex(elem){
var position, value;
while(elem.length&&elem[ 0 ]!==document){
position=elem.css("position");
if(position==="absolute"||position==="relative"||position==="fixed"){
value=parseInt(elem.css("zIndex"), 10);
if(!isNaN(value)&&value!==0){
return value;
}}
elem=elem.parent();
}
return 0;
}
function Datepicker(){
this._curInst=null;
this._keyEvent=false;
this._disabledInputs=[];
this._datepickerShowing=false;
this._inDialog=false;
this._mainDivId="ui-datepicker-div";
this._inlineClass="ui-datepicker-inline";
this._appendClass="ui-datepicker-append";
this._triggerClass="ui-datepicker-trigger";
this._dialogClass="ui-datepicker-dialog";
this._disableClass="ui-datepicker-disabled";
this._unselectableClass="ui-datepicker-unselectable";
this._currentClass="ui-datepicker-current-day";
this._dayOverClass="ui-datepicker-days-cell-over";
this.regional=[];
this.regional[""]={
closeText: "Done",
prevText: "Prev",
nextText: "Next",
currentText: "Today",
monthNames: ["January","February","March","April","May","June",
"July","August","September","October","November","December"],
monthNamesShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
dayNames: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
dayNamesShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
dayNamesMin: ["Su","Mo","Tu","We","Th","Fr","Sa"],
weekHeader: "Wk",
dateFormat: "mm/dd/yy",
firstDay: 0,
isRTL: false,
showMonthAfterYear: false,
yearSuffix: ""
};
this._defaults={
showOn: "focus", // "focus" for popup on focus,
showAnim: "fadeIn",
showOptions: {},
defaultDate: null,
appendText: "",
buttonText: "...",
buttonImage: "",
buttonImageOnly: false,
hideIfNoPrevNext: false,
navigationAsDateFormat: false,
gotoCurrent: false,
changeMonth: false,
changeYear: false,
yearRange: "c-10:c+10",
showOtherMonths: false,
selectOtherMonths: false,
showWeek: false,
calculateWeek: this.iso8601Week,
shortYearCutoff: "+10",
minDate: null,
maxDate: null,
duration: "fast",
beforeShowDay: null,
beforeShow: null,
onSelect: null,
onChangeMonthYear: null,
onClose: null,
numberOfMonths: 1,
showCurrentAtPos: 0,
stepMonths: 1,
stepBigMonths: 12,
altField: "",
altFormat: "",
constrainInput: true,
showButtonPanel: false,
autoSize: false,
disabled: false
};
$.extend(this._defaults, this.regional[""]);
this.regional.en=$.extend(true, {}, this.regional[ "" ]);
this.regional[ "en-US" ]=$.extend(true, {}, this.regional.en);
this.dpDiv=datepicker_bindHover($("<div id='" + this._mainDivId + "' class='ui-datepicker ui-widget ui-widget-content ui-helper-clearfix ui-corner-all'></div>"));
}
$.extend(Datepicker.prototype, {
markerClassName: "hasDatepicker",
maxRows: 4,
_widgetDatepicker: function(){
return this.dpDiv;
},
setDefaults: function(settings){
datepicker_extendRemove(this._defaults, settings||{});
return this;
},
_attachDatepicker: function(target, settings){
var nodeName, inline, inst;
nodeName=target.nodeName.toLowerCase();
inline=(nodeName==="div"||nodeName==="span");
if(!target.id){
this.uuid +=1;
target.id="dp" + this.uuid;
}
inst=this._newInst($(target), inline);
inst.settings=$.extend({}, settings||{});
if(nodeName==="input"){
this._connectDatepicker(target, inst);
}else if(inline){
this._inlineDatepicker(target, inst);
}},
_newInst: function(target, inline){
var id=target[0].id.replace(/([^A-Za-z0-9_\-])/g, "\\\\$1");
return {id: id, input: target,
selectedDay: 0, selectedMonth: 0, selectedYear: 0,
drawMonth: 0, drawYear: 0,
inline: inline,
dpDiv: (!inline ? this.dpDiv :
datepicker_bindHover($("<div class='" + this._inlineClass + " ui-datepicker ui-widget ui-widget-content ui-helper-clearfix ui-corner-all'></div>")))};},
_connectDatepicker: function(target, inst){
var input=$(target);
inst.append=$([]);
inst.trigger=$([]);
if(input.hasClass(this.markerClassName)){
return;
}
this._attachments(input, inst);
input.addClass(this.markerClassName).keydown(this._doKeyDown).
keypress(this._doKeyPress).keyup(this._doKeyUp);
this._autoSize(inst);
$.data(target, "datepicker", inst);
if(inst.settings.disabled){
this._disableDatepicker(target);
}},
_attachments: function(input, inst){
var showOn, buttonText, buttonImage,
appendText=this._get(inst, "appendText"),
isRTL=this._get(inst, "isRTL");
if(inst.append){
inst.append.remove();
}
if(appendText){
inst.append=$("<span class='" + this._appendClass + "'>" + appendText + "</span>");
input[isRTL ? "before":"after"](inst.append);
}
input.unbind("focus", this._showDatepicker);
if(inst.trigger){
inst.trigger.remove();
}
showOn=this._get(inst, "showOn");
if(showOn==="focus"||showOn==="both"){
input.focus(this._showDatepicker);
}
if(showOn==="button"||showOn==="both"){
buttonText=this._get(inst, "buttonText");
buttonImage=this._get(inst, "buttonImage");
inst.trigger=$(this._get(inst, "buttonImageOnly") ?
$("<img/>").addClass(this._triggerClass).
attr({ src: buttonImage, alt: buttonText, title: buttonText }) :
$("<button type='button'></button>").addClass(this._triggerClass).
html(!buttonImage ? buttonText:$("<img/>").attr({ src:buttonImage, alt:buttonText, title:buttonText })));
input[isRTL ? "before":"after"](inst.trigger);
inst.trigger.click(function(){
if($.datepicker._datepickerShowing&&$.datepicker._lastInput===input[0]){
$.datepicker._hideDatepicker();
}else if($.datepicker._datepickerShowing&&$.datepicker._lastInput!==input[0]){
$.datepicker._hideDatepicker();
$.datepicker._showDatepicker(input[0]);
}else{
$.datepicker._showDatepicker(input[0]);
}
return false;
});
}},
_autoSize: function(inst){
if(this._get(inst, "autoSize")&&!inst.inline){
var findMax, max, maxI, i,
date=new Date(2009, 12 - 1, 20),
dateFormat=this._get(inst, "dateFormat");
if(dateFormat.match(/[DM]/)){
findMax=function(names){
max=0;
maxI=0;
for (i=0; i < names.length; i++){
if(names[i].length > max){
max=names[i].length;
maxI=i;
}}
return maxI;
};
date.setMonth(findMax(this._get(inst, (dateFormat.match(/MM/) ?
"monthNames":"monthNamesShort"))));
date.setDate(findMax(this._get(inst, (dateFormat.match(/DD/) ?
"dayNames":"dayNamesShort"))) + 20 - date.getDay());
}
inst.input.attr("size", this._formatDate(inst, date).length);
}},
_inlineDatepicker: function(target, inst){
var divSpan=$(target);
if(divSpan.hasClass(this.markerClassName)){
return;
}
divSpan.addClass(this.markerClassName).append(inst.dpDiv);
$.data(target, "datepicker", inst);
this._setDate(inst, this._getDefaultDate(inst), true);
this._updateDatepicker(inst);
this._updateAlternate(inst);
if(inst.settings.disabled){
this._disableDatepicker(target);
}
inst.dpDiv.css("display", "block");
},
_dialogDatepicker: function(input, date, onSelect, settings, pos){
var id, browserWidth, browserHeight, scrollX, scrollY,
inst=this._dialogInst;
if(!inst){
this.uuid +=1;
id="dp" + this.uuid;
this._dialogInput=$("<input type='text' id='" + id +
"' style='position: absolute; top: -100px; width: 0px;'/>");
this._dialogInput.keydown(this._doKeyDown);
$("body").append(this._dialogInput);
inst=this._dialogInst=this._newInst(this._dialogInput, false);
inst.settings={};
$.data(this._dialogInput[0], "datepicker", inst);
}
datepicker_extendRemove(inst.settings, settings||{});
date=(date&&date.constructor===Date ? this._formatDate(inst, date):date);
this._dialogInput.val(date);
this._pos=(pos ? (pos.length ? pos:[pos.pageX, pos.pageY]):null);
if(!this._pos){
browserWidth=document.documentElement.clientWidth;
browserHeight=document.documentElement.clientHeight;
scrollX=document.documentElement.scrollLeft||document.body.scrollLeft;
scrollY=document.documentElement.scrollTop||document.body.scrollTop;
this._pos =
[(browserWidth / 2) - 100 + scrollX, (browserHeight / 2) - 150 + scrollY];
}
this._dialogInput.css("left", (this._pos[0] + 20) + "px").css("top", this._pos[1] + "px");
inst.settings.onSelect=onSelect;
this._inDialog=true;
this.dpDiv.addClass(this._dialogClass);
this._showDatepicker(this._dialogInput[0]);
if($.blockUI){
$.blockUI(this.dpDiv);
}
$.data(this._dialogInput[0], "datepicker", inst);
return this;
},
_destroyDatepicker: function(target){
var nodeName,
$target=$(target),
inst=$.data(target, "datepicker");
if(!$target.hasClass(this.markerClassName)){
return;
}
nodeName=target.nodeName.toLowerCase();
$.removeData(target, "datepicker");
if(nodeName==="input"){
inst.append.remove();
inst.trigger.remove();
$target.removeClass(this.markerClassName).
unbind("focus", this._showDatepicker).
unbind("keydown", this._doKeyDown).
unbind("keypress", this._doKeyPress).
unbind("keyup", this._doKeyUp);
}else if(nodeName==="div"||nodeName==="span"){
$target.removeClass(this.markerClassName).empty();
}
if(datepicker_instActive===inst){
datepicker_instActive=null;
}},
_enableDatepicker: function(target){
var nodeName, inline,
$target=$(target),
inst=$.data(target, "datepicker");
if(!$target.hasClass(this.markerClassName)){
return;
}
nodeName=target.nodeName.toLowerCase();
if(nodeName==="input"){
target.disabled=false;
inst.trigger.filter("button").
each(function(){ this.disabled=false; }).end().
filter("img").css({opacity: "1.0", cursor: ""});
}else if(nodeName==="div"||nodeName==="span"){
inline=$target.children("." + this._inlineClass);
inline.children().removeClass("ui-state-disabled");
inline.find("select.ui-datepicker-month, select.ui-datepicker-year").
prop("disabled", false);
}
this._disabledInputs=$.map(this._disabledInputs,
function(value){ return (value===target ? null:value); });
},
_disableDatepicker: function(target){
var nodeName, inline,
$target=$(target),
inst=$.data(target, "datepicker");
if(!$target.hasClass(this.markerClassName)){
return;
}
nodeName=target.nodeName.toLowerCase();
if(nodeName==="input"){
target.disabled=true;
inst.trigger.filter("button").
each(function(){ this.disabled=true; }).end().
filter("img").css({opacity: "0.5", cursor: "default"});
}else if(nodeName==="div"||nodeName==="span"){
inline=$target.children("." + this._inlineClass);
inline.children().addClass("ui-state-disabled");
inline.find("select.ui-datepicker-month, select.ui-datepicker-year").
prop("disabled", true);
}
this._disabledInputs=$.map(this._disabledInputs,
function(value){ return (value===target ? null:value); });
this._disabledInputs[this._disabledInputs.length]=target;
},
_isDisabledDatepicker: function(target){
if(!target){
return false;
}
for (var i=0; i < this._disabledInputs.length; i++){
if(this._disabledInputs[i]===target){
return true;
}}
return false;
},
_getInst: function(target){
try {
return $.data(target, "datepicker");
}
catch (err){
throw "Missing instance data for this datepicker";
}},
_optionDatepicker: function(target, name, value){
var settings, date, minDate, maxDate,
inst=this._getInst(target);
if(arguments.length===2&&typeof name==="string"){
return (name==="defaults" ? $.extend({}, $.datepicker._defaults) :
(inst ? (name==="all" ? $.extend({}, inst.settings) :
this._get(inst, name)):null));
}
settings=name||{};
if(typeof name==="string"){
settings={};
settings[name]=value;
}
if(inst){
if(this._curInst===inst){
this._hideDatepicker();
}
date=this._getDateDatepicker(target, true);
minDate=this._getMinMaxDate(inst, "min");
maxDate=this._getMinMaxDate(inst, "max");
datepicker_extendRemove(inst.settings, settings);
if(minDate!==null&&settings.dateFormat!==undefined&&settings.minDate===undefined){
inst.settings.minDate=this._formatDate(inst, minDate);
}
if(maxDate!==null&&settings.dateFormat!==undefined&&settings.maxDate===undefined){
inst.settings.maxDate=this._formatDate(inst, maxDate);
}
if("disabled" in settings){
if(settings.disabled){
this._disableDatepicker(target);
}else{
this._enableDatepicker(target);
}}
this._attachments($(target), inst);
this._autoSize(inst);
this._setDate(inst, date);
this._updateAlternate(inst);
this._updateDatepicker(inst);
}},
_changeDatepicker: function(target, name, value){
this._optionDatepicker(target, name, value);
},
_refreshDatepicker: function(target){
var inst=this._getInst(target);
if(inst){
this._updateDatepicker(inst);
}},
_setDateDatepicker: function(target, date){
var inst=this._getInst(target);
if(inst){
this._setDate(inst, date);
this._updateDatepicker(inst);
this._updateAlternate(inst);
}},
_getDateDatepicker: function(target, noDefault){
var inst=this._getInst(target);
if(inst&&!inst.inline){
this._setDateFromField(inst, noDefault);
}
return (inst ? this._getDate(inst):null);
},
_doKeyDown: function(event){
var onSelect, dateStr, sel,
inst=$.datepicker._getInst(event.target),
handled=true,
isRTL=inst.dpDiv.is(".ui-datepicker-rtl");
inst._keyEvent=true;
if($.datepicker._datepickerShowing){
switch (event.keyCode){
case 9: $.datepicker._hideDatepicker();
handled=false;
break;
case 13: sel=$("td." + $.datepicker._dayOverClass + ":not(." +
$.datepicker._currentClass + ")", inst.dpDiv);
if(sel[0]){
$.datepicker._selectDay(event.target, inst.selectedMonth, inst.selectedYear, sel[0]);
}
onSelect=$.datepicker._get(inst, "onSelect");
if(onSelect){
dateStr=$.datepicker._formatDate(inst);
onSelect.apply((inst.input ? inst.input[0]:null), [dateStr, inst]);
}else{
$.datepicker._hideDatepicker();
}
return false;
case 27: $.datepicker._hideDatepicker();
break;
case 33: $.datepicker._adjustDate(event.target, (event.ctrlKey ?
-$.datepicker._get(inst, "stepBigMonths") :
-$.datepicker._get(inst, "stepMonths")), "M");
break;
case 34: $.datepicker._adjustDate(event.target, (event.ctrlKey ?
+$.datepicker._get(inst, "stepBigMonths") :
+$.datepicker._get(inst, "stepMonths")), "M");
break;
case 35: if(event.ctrlKey||event.metaKey){
$.datepicker._clearDate(event.target);
}
handled=event.ctrlKey||event.metaKey;
break;
case 36: if(event.ctrlKey||event.metaKey){
$.datepicker._gotoToday(event.target);
}
handled=event.ctrlKey||event.metaKey;
break;
case 37: if(event.ctrlKey||event.metaKey){
$.datepicker._adjustDate(event.target, (isRTL ? +1:-1), "D");
}
handled=event.ctrlKey||event.metaKey;
if(event.originalEvent.altKey){
$.datepicker._adjustDate(event.target, (event.ctrlKey ?
-$.datepicker._get(inst, "stepBigMonths") :
-$.datepicker._get(inst, "stepMonths")), "M");
}
break;
case 38: if(event.ctrlKey||event.metaKey){
$.datepicker._adjustDate(event.target, -7, "D");
}
handled=event.ctrlKey||event.metaKey;
break;
case 39: if(event.ctrlKey||event.metaKey){
$.datepicker._adjustDate(event.target, (isRTL ? -1:+1), "D");
}
handled=event.ctrlKey||event.metaKey;
if(event.originalEvent.altKey){
$.datepicker._adjustDate(event.target, (event.ctrlKey ?
+$.datepicker._get(inst, "stepBigMonths") :
+$.datepicker._get(inst, "stepMonths")), "M");
}
break;
case 40: if(event.ctrlKey||event.metaKey){
$.datepicker._adjustDate(event.target, +7, "D");
}
handled=event.ctrlKey||event.metaKey;
break;
default: handled=false;
}}else if(event.keyCode===36&&event.ctrlKey){
$.datepicker._showDatepicker(this);
}else{
handled=false;
}
if(handled){
event.preventDefault();
event.stopPropagation();
}},
_doKeyPress: function(event){
var chars, chr,
inst=$.datepicker._getInst(event.target);
if($.datepicker._get(inst, "constrainInput")){
chars=$.datepicker._possibleChars($.datepicker._get(inst, "dateFormat"));
chr=String.fromCharCode(event.charCode==null ? event.keyCode:event.charCode);
return event.ctrlKey||event.metaKey||(chr < " "||!chars||chars.indexOf(chr) > -1);
}},
_doKeyUp: function(event){
var date,
inst=$.datepicker._getInst(event.target);
if(inst.input.val()!==inst.lastVal){
try {
date=$.datepicker.parseDate($.datepicker._get(inst, "dateFormat"),
(inst.input ? inst.input.val():null),
$.datepicker._getFormatConfig(inst));
if(date){
$.datepicker._setDateFromField(inst);
$.datepicker._updateAlternate(inst);
$.datepicker._updateDatepicker(inst);
}}
catch (err){
}}
return true;
},
_showDatepicker: function(input){
input=input.target||input;
if(input.nodeName.toLowerCase()!=="input"){
input=$("input", input.parentNode)[0];
}
if($.datepicker._isDisabledDatepicker(input)||$.datepicker._lastInput===input){
return;
}
var inst, beforeShow, beforeShowSettings, isFixed,
offset, showAnim, duration;
inst=$.datepicker._getInst(input);
if($.datepicker._curInst&&$.datepicker._curInst!==inst){
$.datepicker._curInst.dpDiv.stop(true, true);
if(inst&&$.datepicker._datepickerShowing){
$.datepicker._hideDatepicker($.datepicker._curInst.input[0]);
}}
beforeShow=$.datepicker._get(inst, "beforeShow");
beforeShowSettings=beforeShow ? beforeShow.apply(input, [input, inst]):{};
if(beforeShowSettings===false){
return;
}
datepicker_extendRemove(inst.settings, beforeShowSettings);
inst.lastVal=null;
$.datepicker._lastInput=input;
$.datepicker._setDateFromField(inst);
if($.datepicker._inDialog){
input.value="";
}
if(!$.datepicker._pos){
$.datepicker._pos=$.datepicker._findPos(input);
$.datepicker._pos[1] +=input.offsetHeight;
}
isFixed=false;
$(input).parents().each(function(){
isFixed |=$(this).css("position")==="fixed";
return !isFixed;
});
offset={left: $.datepicker._pos[0], top: $.datepicker._pos[1]};
$.datepicker._pos=null;
inst.dpDiv.empty();
inst.dpDiv.css({position: "absolute", display: "block", top: "-1000px"});
$.datepicker._updateDatepicker(inst);
offset=$.datepicker._checkOffset(inst, offset, isFixed);
inst.dpDiv.css({position: ($.datepicker._inDialog&&$.blockUI ?
"static":(isFixed ? "fixed":"absolute")), display: "none",
left: offset.left + "px", top: offset.top + "px"});
if(!inst.inline){
showAnim=$.datepicker._get(inst, "showAnim");
duration=$.datepicker._get(inst, "duration");
inst.dpDiv.css("z-index", datepicker_getZindex($(input)) + 1);
$.datepicker._datepickerShowing=true;
if($.effects&&$.effects.effect[ showAnim ]){
inst.dpDiv.show(showAnim, $.datepicker._get(inst, "showOptions"), duration);
}else{
inst.dpDiv[showAnim||"show"](showAnim ? duration:null);
}
if($.datepicker._shouldFocusInput(inst)){
inst.input.focus();
}
$.datepicker._curInst=inst;
}},
_updateDatepicker: function(inst){
this.maxRows=4;
datepicker_instActive=inst;
inst.dpDiv.empty().append(this._generateHTML(inst));
this._attachHandlers(inst);
var origyearshtml,
numMonths=this._getNumberOfMonths(inst),
cols=numMonths[1],
width=17,
activeCell=inst.dpDiv.find("." + this._dayOverClass + " a");
if(activeCell.length > 0){
datepicker_handleMouseover.apply(activeCell.get(0));
}
inst.dpDiv.removeClass("ui-datepicker-multi-2 ui-datepicker-multi-3 ui-datepicker-multi-4").width("");
if(cols > 1){
inst.dpDiv.addClass("ui-datepicker-multi-" + cols).css("width", (width * cols) + "em");
}
inst.dpDiv[(numMonths[0]!==1||numMonths[1]!==1 ? "add":"remove") +
"Class"]("ui-datepicker-multi");
inst.dpDiv[(this._get(inst, "isRTL") ? "add":"remove") +
"Class"]("ui-datepicker-rtl");
if(inst===$.datepicker._curInst&&$.datepicker._datepickerShowing&&$.datepicker._shouldFocusInput(inst)){
inst.input.focus();
}
if(inst.yearshtml){
origyearshtml=inst.yearshtml;
setTimeout(function(){
if(origyearshtml===inst.yearshtml&&inst.yearshtml){
inst.dpDiv.find("select.ui-datepicker-year:first").replaceWith(inst.yearshtml);
}
origyearshtml=inst.yearshtml=null;
}, 0);
}},
_shouldFocusInput: function(inst){
return inst.input&&inst.input.is(":visible")&&!inst.input.is(":disabled")&&!inst.input.is(":focus");
},
_checkOffset: function(inst, offset, isFixed){
var dpWidth=inst.dpDiv.outerWidth(),
dpHeight=inst.dpDiv.outerHeight(),
inputWidth=inst.input ? inst.input.outerWidth():0,
inputHeight=inst.input ? inst.input.outerHeight():0,
viewWidth=document.documentElement.clientWidth + (isFixed ? 0:$(document).scrollLeft()),
viewHeight=document.documentElement.clientHeight + (isFixed ? 0:$(document).scrollTop());
offset.left -=(this._get(inst, "isRTL") ? (dpWidth - inputWidth):0);
offset.left -=(isFixed&&offset.left===inst.input.offset().left) ? $(document).scrollLeft():0;
offset.top -=(isFixed&&offset.top===(inst.input.offset().top + inputHeight)) ? $(document).scrollTop():0;
offset.left -=Math.min(offset.left, (offset.left + dpWidth > viewWidth&&viewWidth > dpWidth) ?
Math.abs(offset.left + dpWidth - viewWidth):0);
offset.top -=Math.min(offset.top, (offset.top + dpHeight > viewHeight&&viewHeight > dpHeight) ?
Math.abs(dpHeight + inputHeight):0);
return offset;
},
_findPos: function(obj){
var position,
inst=this._getInst(obj),
isRTL=this._get(inst, "isRTL");
while (obj&&(obj.type==="hidden"||obj.nodeType!==1||$.expr.filters.hidden(obj))){
obj=obj[isRTL ? "previousSibling":"nextSibling"];
}
position=$(obj).offset();
return [position.left, position.top];
},
_hideDatepicker: function(input){
var showAnim, duration, postProcess, onClose,
inst=this._curInst;
if(!inst||(input&&inst!==$.data(input, "datepicker"))){
return;
}
if(this._datepickerShowing){
showAnim=this._get(inst, "showAnim");
duration=this._get(inst, "duration");
postProcess=function(){
$.datepicker._tidyDialog(inst);
};
if($.effects&&($.effects.effect[ showAnim ]||$.effects[ showAnim ])){
inst.dpDiv.hide(showAnim, $.datepicker._get(inst, "showOptions"), duration, postProcess);
}else{
inst.dpDiv[(showAnim==="slideDown" ? "slideUp" :
(showAnim==="fadeIn" ? "fadeOut":"hide"))]((showAnim ? duration:null), postProcess);
}
if(!showAnim){
postProcess();
}
this._datepickerShowing=false;
onClose=this._get(inst, "onClose");
if(onClose){
onClose.apply((inst.input ? inst.input[0]:null), [(inst.input ? inst.input.val():""), inst]);
}
this._lastInput=null;
if(this._inDialog){
this._dialogInput.css({ position: "absolute", left: "0", top: "-100px" });
if($.blockUI){
$.unblockUI();
$("body").append(this.dpDiv);
}}
this._inDialog=false;
}},
_tidyDialog: function(inst){
inst.dpDiv.removeClass(this._dialogClass).unbind(".ui-datepicker-calendar");
},
_checkExternalClick: function(event){
if(!$.datepicker._curInst){
return;
}
var $target=$(event.target),
inst=$.datepicker._getInst($target[0]);
if((($target[0].id!==$.datepicker._mainDivId &&
$target.parents("#" + $.datepicker._mainDivId).length===0 &&
!$target.hasClass($.datepicker.markerClassName) &&
!$target.closest("." + $.datepicker._triggerClass).length &&
$.datepicker._datepickerShowing&&!($.datepicker._inDialog&&$.blockUI))) ||
($target.hasClass($.datepicker.markerClassName)&&$.datepicker._curInst!==inst)){
$.datepicker._hideDatepicker();
}},
_adjustDate: function(id, offset, period){
var target=$(id),
inst=this._getInst(target[0]);
if(this._isDisabledDatepicker(target[0])){
return;
}
this._adjustInstDate(inst, offset +
(period==="M" ? this._get(inst, "showCurrentAtPos"):0),
period);
this._updateDatepicker(inst);
},
_gotoToday: function(id){
var date,
target=$(id),
inst=this._getInst(target[0]);
if(this._get(inst, "gotoCurrent")&&inst.currentDay){
inst.selectedDay=inst.currentDay;
inst.drawMonth=inst.selectedMonth=inst.currentMonth;
inst.drawYear=inst.selectedYear=inst.currentYear;
}else{
date=new Date();
inst.selectedDay=date.getDate();
inst.drawMonth=inst.selectedMonth=date.getMonth();
inst.drawYear=inst.selectedYear=date.getFullYear();
}
this._notifyChange(inst);
this._adjustDate(target);
},
_selectMonthYear: function(id, select, period){
var target=$(id),
inst=this._getInst(target[0]);
inst["selected" + (period==="M" ? "Month":"Year")] =
inst["draw" + (period==="M" ? "Month":"Year")] =
parseInt(select.options[select.selectedIndex].value,10);
this._notifyChange(inst);
this._adjustDate(target);
},
_selectDay: function(id, month, year, td){
var inst,
target=$(id);
if($(td).hasClass(this._unselectableClass)||this._isDisabledDatepicker(target[0])){
return;
}
inst=this._getInst(target[0]);
inst.selectedDay=inst.currentDay=$("a", td).html();
inst.selectedMonth=inst.currentMonth=month;
inst.selectedYear=inst.currentYear=year;
this._selectDate(id, this._formatDate(inst,
inst.currentDay, inst.currentMonth, inst.currentYear));
},
_clearDate: function(id){
var target=$(id);
this._selectDate(target, "");
},
_selectDate: function(id, dateStr){
var onSelect,
target=$(id),
inst=this._getInst(target[0]);
dateStr=(dateStr!=null ? dateStr:this._formatDate(inst));
if(inst.input){
inst.input.val(dateStr);
}
this._updateAlternate(inst);
onSelect=this._get(inst, "onSelect");
if(onSelect){
onSelect.apply((inst.input ? inst.input[0]:null), [dateStr, inst]);
}else if(inst.input){
inst.input.trigger("change");
}
if(inst.inline){
this._updateDatepicker(inst);
}else{
this._hideDatepicker();
this._lastInput=inst.input[0];
if(typeof(inst.input[0])!=="object"){
inst.input.focus();
}
this._lastInput=null;
}},
_updateAlternate: function(inst){
var altFormat, date, dateStr,
altField=this._get(inst, "altField");
if(altField){
altFormat=this._get(inst, "altFormat")||this._get(inst, "dateFormat");
date=this._getDate(inst);
dateStr=this.formatDate(altFormat, date, this._getFormatConfig(inst));
$(altField).each(function(){ $(this).val(dateStr); });
}},
noWeekends: function(date){
var day=date.getDay();
return [(day > 0&&day < 6), ""];
},
iso8601Week: function(date){
var time,
checkDate=new Date(date.getTime());
checkDate.setDate(checkDate.getDate() + 4 - (checkDate.getDay()||7));
time=checkDate.getTime();
checkDate.setMonth(0);
checkDate.setDate(1);
return Math.floor(Math.round((time - checkDate) / 86400000) / 7) + 1;
},
parseDate: function (format, value, settings){
if(format==null||value==null){
throw "Invalid arguments";
}
value=(typeof value==="object" ? value.toString():value + "");
if(value===""){
return null;
}
var iFormat, dim, extra,
iValue=0,
shortYearCutoffTemp=(settings ? settings.shortYearCutoff:null)||this._defaults.shortYearCutoff,
shortYearCutoff=(typeof shortYearCutoffTemp!=="string" ? shortYearCutoffTemp :
new Date().getFullYear() % 100 + parseInt(shortYearCutoffTemp, 10)),
dayNamesShort=(settings ? settings.dayNamesShort:null)||this._defaults.dayNamesShort,
dayNames=(settings ? settings.dayNames:null)||this._defaults.dayNames,
monthNamesShort=(settings ? settings.monthNamesShort:null)||this._defaults.monthNamesShort,
monthNames=(settings ? settings.monthNames:null)||this._defaults.monthNames,
year=-1,
month=-1,
day=-1,
doy=-1,
literal=false,
date,
lookAhead=function(match){
var matches=(iFormat + 1 < format.length&&format.charAt(iFormat + 1)===match);
if(matches){
iFormat++;
}
return matches;
},
getNumber=function(match){
var isDoubled=lookAhead(match),
size=(match==="@" ? 14:(match==="!" ? 20 :
(match==="y"&&isDoubled ? 4:(match==="o" ? 3:2)))),
minSize=(match==="y" ? size:1),
digits=new RegExp("^\\d{" + minSize + "," + size + "}"),
num=value.substring(iValue).match(digits);
if(!num){
throw "Missing number at position " + iValue;
}
iValue +=num[0].length;
return parseInt(num[0], 10);
},
getName=function(match, shortNames, longNames){
var index=-1,
names=$.map(lookAhead(match) ? longNames:shortNames, function (v, k){
return [ [k, v] ];
}).sort(function (a, b){
return -(a[1].length - b[1].length);
});
$.each(names, function (i, pair){
var name=pair[1];
if(value.substr(iValue, name.length).toLowerCase()===name.toLowerCase()){
index=pair[0];
iValue +=name.length;
return false;
}});
if(index!==-1){
return index + 1;
}else{
throw "Unknown name at position " + iValue;
}},
checkLiteral=function(){
if(value.charAt(iValue)!==format.charAt(iFormat)){
throw "Unexpected literal at position " + iValue;
}
iValue++;
};
for (iFormat=0; iFormat < format.length; iFormat++){
if(literal){
if(format.charAt(iFormat)==="'"&&!lookAhead("'")){
literal=false;
}else{
checkLiteral();
}}else{
switch (format.charAt(iFormat)){
case "d":
day=getNumber("d");
break;
case "D":
getName("D", dayNamesShort, dayNames);
break;
case "o":
doy=getNumber("o");
break;
case "m":
month=getNumber("m");
break;
case "M":
month=getName("M", monthNamesShort, monthNames);
break;
case "y":
year=getNumber("y");
break;
case "@":
date=new Date(getNumber("@"));
year=date.getFullYear();
month=date.getMonth() + 1;
day=date.getDate();
break;
case "!":
date=new Date((getNumber("!") - this._ticksTo1970) / 10000);
year=date.getFullYear();
month=date.getMonth() + 1;
day=date.getDate();
break;
case "'":
if(lookAhead("'")){
checkLiteral();
}else{
literal=true;
}
break;
default:
checkLiteral();
}}
}
if(iValue < value.length){
extra=value.substr(iValue);
if(!/^\s+/.test(extra)){
throw "Extra/unparsed characters found in date: " + extra;
}}
if(year===-1){
year=new Date().getFullYear();
}else if(year < 100){
year +=new Date().getFullYear() - new Date().getFullYear() % 100 +
(year <=shortYearCutoff ? 0:-100);
}
if(doy > -1){
month=1;
day=doy;
do {
dim=this._getDaysInMonth(year, month - 1);
if(day <=dim){
break;
}
month++;
day -=dim;
} while (true);
}
date=this._daylightSavingAdjust(new Date(year, month - 1, day));
if(date.getFullYear()!==year||date.getMonth() + 1!==month||date.getDate()!==day){
throw "Invalid date";
}
return date;
},
ATOM: "yy-mm-dd",
COOKIE: "D, dd M yy",
ISO_8601: "yy-mm-dd",
RFC_822: "D, d M y",
RFC_850: "DD, dd-M-y",
RFC_1036: "D, d M y",
RFC_1123: "D, d M yy",
RFC_2822: "D, d M yy",
RSS: "D, d M y",
TICKS: "!",
TIMESTAMP: "@",
W3C: "yy-mm-dd",
_ticksTo1970: (((1970 - 1) * 365 + Math.floor(1970 / 4) - Math.floor(1970 / 100) +
Math.floor(1970 / 400)) * 24 * 60 * 60 * 10000000),
formatDate: function (format, date, settings){
if(!date){
return "";
}
var iFormat,
dayNamesShort=(settings ? settings.dayNamesShort:null)||this._defaults.dayNamesShort,
dayNames=(settings ? settings.dayNames:null)||this._defaults.dayNames,
monthNamesShort=(settings ? settings.monthNamesShort:null)||this._defaults.monthNamesShort,
monthNames=(settings ? settings.monthNames:null)||this._defaults.monthNames,
lookAhead=function(match){
var matches=(iFormat + 1 < format.length&&format.charAt(iFormat + 1)===match);
if(matches){
iFormat++;
}
return matches;
},
formatNumber=function(match, value, len){
var num="" + value;
if(lookAhead(match)){
while (num.length < len){
num="0" + num;
}}
return num;
},
formatName=function(match, value, shortNames, longNames){
return (lookAhead(match) ? longNames[value]:shortNames[value]);
},
output="",
literal=false;
if(date){
for (iFormat=0; iFormat < format.length; iFormat++){
if(literal){
if(format.charAt(iFormat)==="'"&&!lookAhead("'")){
literal=false;
}else{
output +=format.charAt(iFormat);
}}else{
switch (format.charAt(iFormat)){
case "d":
output +=formatNumber("d", date.getDate(), 2);
break;
case "D":
output +=formatName("D", date.getDay(), dayNamesShort, dayNames);
break;
case "o":
output +=formatNumber("o",
Math.round((new Date(date.getFullYear(), date.getMonth(), date.getDate()).getTime() - new Date(date.getFullYear(), 0, 0).getTime()) / 86400000), 3);
break;
case "m":
output +=formatNumber("m", date.getMonth() + 1, 2);
break;
case "M":
output +=formatName("M", date.getMonth(), monthNamesShort, monthNames);
break;
case "y":
output +=(lookAhead("y") ? date.getFullYear() :
(date.getYear() % 100 < 10 ? "0":"") + date.getYear() % 100);
break;
case "@":
output +=date.getTime();
break;
case "!":
output +=date.getTime() * 10000 + this._ticksTo1970;
break;
case "'":
if(lookAhead("'")){
output +="'";
}else{
literal=true;
}
break;
default:
output +=format.charAt(iFormat);
}}
}}
return output;
},
_possibleChars: function (format){
var iFormat,
chars="",
literal=false,
lookAhead=function(match){
var matches=(iFormat + 1 < format.length&&format.charAt(iFormat + 1)===match);
if(matches){
iFormat++;
}
return matches;
};
for (iFormat=0; iFormat < format.length; iFormat++){
if(literal){
if(format.charAt(iFormat)==="'"&&!lookAhead("'")){
literal=false;
}else{
chars +=format.charAt(iFormat);
}}else{
switch (format.charAt(iFormat)){
case "d": case "m": case "y": case "@":
chars +="0123456789";
break;
case "D": case "M":
return null;
case "'":
if(lookAhead("'")){
chars +="'";
}else{
literal=true;
}
break;
default:
chars +=format.charAt(iFormat);
}}
}
return chars;
},
_get: function(inst, name){
return inst.settings[name]!==undefined ?
inst.settings[name]:this._defaults[name];
},
_setDateFromField: function(inst, noDefault){
if(inst.input.val()===inst.lastVal){
return;
}
var dateFormat=this._get(inst, "dateFormat"),
dates=inst.lastVal=inst.input ? inst.input.val():null,
defaultDate=this._getDefaultDate(inst),
date=defaultDate,
settings=this._getFormatConfig(inst);
try {
date=this.parseDate(dateFormat, dates, settings)||defaultDate;
} catch (event){
dates=(noDefault ? "":dates);
}
inst.selectedDay=date.getDate();
inst.drawMonth=inst.selectedMonth=date.getMonth();
inst.drawYear=inst.selectedYear=date.getFullYear();
inst.currentDay=(dates ? date.getDate():0);
inst.currentMonth=(dates ? date.getMonth():0);
inst.currentYear=(dates ? date.getFullYear():0);
this._adjustInstDate(inst);
},
_getDefaultDate: function(inst){
return this._restrictMinMax(inst,
this._determineDate(inst, this._get(inst, "defaultDate"), new Date()));
},
_determineDate: function(inst, date, defaultDate){
var offsetNumeric=function(offset){
var date=new Date();
date.setDate(date.getDate() + offset);
return date;
},
offsetString=function(offset){
try {
return $.datepicker.parseDate($.datepicker._get(inst, "dateFormat"),
offset, $.datepicker._getFormatConfig(inst));
}
catch (e){
}
var date=(offset.toLowerCase().match(/^c/) ?
$.datepicker._getDate(inst):null)||new Date(),
year=date.getFullYear(),
month=date.getMonth(),
day=date.getDate(),
pattern=/([+\-]?[0-9]+)\s*(d|D|w|W|m|M|y|Y)?/g,
matches=pattern.exec(offset);
while (matches){
switch (matches[2]||"d"){
case "d":case "D" :
day +=parseInt(matches[1],10); break;
case "w":case "W" :
day +=parseInt(matches[1],10) * 7; break;
case "m":case "M" :
month +=parseInt(matches[1],10);
day=Math.min(day, $.datepicker._getDaysInMonth(year, month));
break;
case "y": case "Y" :
year +=parseInt(matches[1],10);
day=Math.min(day, $.datepicker._getDaysInMonth(year, month));
break;
}
matches=pattern.exec(offset);
}
return new Date(year, month, day);
},
newDate=(date==null||date==="" ? defaultDate:(typeof date==="string" ? offsetString(date) :
(typeof date==="number" ? (isNaN(date) ? defaultDate:offsetNumeric(date)):new Date(date.getTime()))));
newDate=(newDate&&newDate.toString()==="Invalid Date" ? defaultDate:newDate);
if(newDate){
newDate.setHours(0);
newDate.setMinutes(0);
newDate.setSeconds(0);
newDate.setMilliseconds(0);
}
return this._daylightSavingAdjust(newDate);
},
_daylightSavingAdjust: function(date){
if(!date){
return null;
}
date.setHours(date.getHours() > 12 ? date.getHours() + 2:0);
return date;
},
_setDate: function(inst, date, noChange){
var clear = !date,
origMonth=inst.selectedMonth,
origYear=inst.selectedYear,
newDate=this._restrictMinMax(inst, this._determineDate(inst, date, new Date()));
inst.selectedDay=inst.currentDay=newDate.getDate();
inst.drawMonth=inst.selectedMonth=inst.currentMonth=newDate.getMonth();
inst.drawYear=inst.selectedYear=inst.currentYear=newDate.getFullYear();
if((origMonth!==inst.selectedMonth||origYear!==inst.selectedYear)&&!noChange){
this._notifyChange(inst);
}
this._adjustInstDate(inst);
if(inst.input){
inst.input.val(clear ? "":this._formatDate(inst));
}},
_getDate: function(inst){
var startDate=(!inst.currentYear||(inst.input&&inst.input.val()==="") ? null :
this._daylightSavingAdjust(new Date(
inst.currentYear, inst.currentMonth, inst.currentDay)));
return startDate;
},
_attachHandlers: function(inst){
var stepMonths=this._get(inst, "stepMonths"),
id="#" + inst.id.replace(/\\\\/g, "\\");
inst.dpDiv.find("[data-handler]").map(function (){
var handler={
prev: function (){
$.datepicker._adjustDate(id, -stepMonths, "M");
},
next: function (){
$.datepicker._adjustDate(id, +stepMonths, "M");
},
hide: function (){
$.datepicker._hideDatepicker();
},
today: function (){
$.datepicker._gotoToday(id);
},
selectDay: function (){
$.datepicker._selectDay(id, +this.getAttribute("data-month"), +this.getAttribute("data-year"), this);
return false;
},
selectMonth: function (){
$.datepicker._selectMonthYear(id, this, "M");
return false;
},
selectYear: function (){
$.datepicker._selectMonthYear(id, this, "Y");
return false;
}};
$(this).bind(this.getAttribute("data-event"), handler[this.getAttribute("data-handler")]);
});
},
_generateHTML: function(inst){
var maxDraw, prevText, prev, nextText, next, currentText, gotoDate,
controls, buttonPanel, firstDay, showWeek, dayNames, dayNamesMin,
monthNames, monthNamesShort, beforeShowDay, showOtherMonths,
selectOtherMonths, defaultDate, html, dow, row, group, col, selectedDate,
cornerClass, calender, thead, day, daysInMonth, leadDays, curRows, numRows,
printDate, dRow, tbody, daySettings, otherMonth, unselectable,
tempDate=new Date(),
today=this._daylightSavingAdjust(new Date(tempDate.getFullYear(), tempDate.getMonth(), tempDate.getDate())),
isRTL=this._get(inst, "isRTL"),
showButtonPanel=this._get(inst, "showButtonPanel"),
hideIfNoPrevNext=this._get(inst, "hideIfNoPrevNext"),
navigationAsDateFormat=this._get(inst, "navigationAsDateFormat"),
numMonths=this._getNumberOfMonths(inst),
showCurrentAtPos=this._get(inst, "showCurrentAtPos"),
stepMonths=this._get(inst, "stepMonths"),
isMultiMonth=(numMonths[0]!==1||numMonths[1]!==1),
currentDate=this._daylightSavingAdjust((!inst.currentDay ? new Date(9999, 9, 9) :
new Date(inst.currentYear, inst.currentMonth, inst.currentDay))),
minDate=this._getMinMaxDate(inst, "min"),
maxDate=this._getMinMaxDate(inst, "max"),
drawMonth=inst.drawMonth - showCurrentAtPos,
drawYear=inst.drawYear;
if(drawMonth < 0){
drawMonth +=12;
drawYear--;
}
if(maxDate){
maxDraw=this._daylightSavingAdjust(new Date(maxDate.getFullYear(),
maxDate.getMonth() - (numMonths[0] * numMonths[1]) + 1, maxDate.getDate()));
maxDraw=(minDate&&maxDraw < minDate ? minDate:maxDraw);
while (this._daylightSavingAdjust(new Date(drawYear, drawMonth, 1)) > maxDraw){
drawMonth--;
if(drawMonth < 0){
drawMonth=11;
drawYear--;
}}
}
inst.drawMonth=drawMonth;
inst.drawYear=drawYear;
prevText=this._get(inst, "prevText");
prevText=(!navigationAsDateFormat ? prevText:this.formatDate(prevText,
this._daylightSavingAdjust(new Date(drawYear, drawMonth - stepMonths, 1)),
this._getFormatConfig(inst)));
prev=(this._canAdjustMonth(inst, -1, drawYear, drawMonth) ?
"<a class='ui-datepicker-prev ui-corner-all' data-handler='prev' data-event='click'" +
" title='" + prevText + "'><span class='ui-icon ui-icon-circle-triangle-" +(isRTL ? "e":"w") + "'>" + prevText + "</span></a>" :
(hideIfNoPrevNext ? "":"<a class='ui-datepicker-prev ui-corner-all ui-state-disabled' title='"+ prevText +"'><span class='ui-icon ui-icon-circle-triangle-" +(isRTL ? "e":"w") + "'>" + prevText + "</span></a>"));
nextText=this._get(inst, "nextText");
nextText=(!navigationAsDateFormat ? nextText:this.formatDate(nextText,
this._daylightSavingAdjust(new Date(drawYear, drawMonth + stepMonths, 1)),
this._getFormatConfig(inst)));
next=(this._canAdjustMonth(inst, +1, drawYear, drawMonth) ?
"<a class='ui-datepicker-next ui-corner-all' data-handler='next' data-event='click'" +
" title='" + nextText + "'><span class='ui-icon ui-icon-circle-triangle-" +(isRTL ? "w":"e") + "'>" + nextText + "</span></a>" :
(hideIfNoPrevNext ? "":"<a class='ui-datepicker-next ui-corner-all ui-state-disabled' title='"+ nextText + "'><span class='ui-icon ui-icon-circle-triangle-" +(isRTL ? "w":"e") + "'>" + nextText + "</span></a>"));
currentText=this._get(inst, "currentText");
gotoDate=(this._get(inst, "gotoCurrent")&&inst.currentDay ? currentDate:today);
currentText=(!navigationAsDateFormat ? currentText :
this.formatDate(currentText, gotoDate, this._getFormatConfig(inst)));
controls=(!inst.inline ? "<button type='button' class='ui-datepicker-close ui-state-default ui-priority-primary ui-corner-all' data-handler='hide' data-event='click'>" +
this._get(inst, "closeText") + "</button>":"");
buttonPanel=(showButtonPanel) ? "<div class='ui-datepicker-buttonpane ui-widget-content'>" + (isRTL ? controls:"") +
(this._isInRange(inst, gotoDate) ? "<button type='button' class='ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all' data-handler='today' data-event='click'" +
">" + currentText + "</button>":"") + (isRTL ? "":controls) + "</div>":"";
firstDay=parseInt(this._get(inst, "firstDay"),10);
firstDay=(isNaN(firstDay) ? 0:firstDay);
showWeek=this._get(inst, "showWeek");
dayNames=this._get(inst, "dayNames");
dayNamesMin=this._get(inst, "dayNamesMin");
monthNames=this._get(inst, "monthNames");
monthNamesShort=this._get(inst, "monthNamesShort");
beforeShowDay=this._get(inst, "beforeShowDay");
showOtherMonths=this._get(inst, "showOtherMonths");
selectOtherMonths=this._get(inst, "selectOtherMonths");
defaultDate=this._getDefaultDate(inst);
html="";
dow;
for (row=0; row < numMonths[0]; row++){
group="";
this.maxRows=4;
for (col=0; col < numMonths[1]; col++){
selectedDate=this._daylightSavingAdjust(new Date(drawYear, drawMonth, inst.selectedDay));
cornerClass=" ui-corner-all";
calender="";
if(isMultiMonth){
calender +="<div class='ui-datepicker-group";
if(numMonths[1] > 1){
switch (col){
case 0: calender +=" ui-datepicker-group-first";
cornerClass=" ui-corner-" + (isRTL ? "right":"left"); break;
case numMonths[1]-1: calender +=" ui-datepicker-group-last";
cornerClass=" ui-corner-" + (isRTL ? "left":"right"); break;
default: calender +=" ui-datepicker-group-middle"; cornerClass=""; break;
}}
calender +="'>";
}
calender +="<div class='ui-datepicker-header ui-widget-header ui-helper-clearfix" + cornerClass + "'>" +
(/all|left/.test(cornerClass)&&row===0 ? (isRTL ? next:prev):"") +
(/all|right/.test(cornerClass)&&row===0 ? (isRTL ? prev:next):"") +
this._generateMonthYearHeader(inst, drawMonth, drawYear, minDate, maxDate,
row > 0||col > 0, monthNames, monthNamesShort) +
"</div><table class='ui-datepicker-calendar'><thead>" +
"<tr>";
thead=(showWeek ? "<th class='ui-datepicker-week-col'>" + this._get(inst, "weekHeader") + "</th>":"");
for (dow=0; dow < 7; dow++){
day=(dow + firstDay) % 7;
thead +="<th scope='col'" + ((dow + firstDay + 6) % 7 >=5 ? " class='ui-datepicker-week-end'":"") + ">" +
"<span title='" + dayNames[day] + "'>" + dayNamesMin[day] + "</span></th>";
}
calender +=thead + "</tr></thead><tbody>";
daysInMonth=this._getDaysInMonth(drawYear, drawMonth);
if(drawYear===inst.selectedYear&&drawMonth===inst.selectedMonth){
inst.selectedDay=Math.min(inst.selectedDay, daysInMonth);
}
leadDays=(this._getFirstDayOfMonth(drawYear, drawMonth) - firstDay + 7) % 7;
curRows=Math.ceil((leadDays + daysInMonth) / 7);
numRows=(isMultiMonth ? this.maxRows > curRows ? this.maxRows:curRows:curRows);
this.maxRows=numRows;
printDate=this._daylightSavingAdjust(new Date(drawYear, drawMonth, 1 - leadDays));
for (dRow=0; dRow < numRows; dRow++){
calender +="<tr>";
tbody=(!showWeek ? "":"<td class='ui-datepicker-week-col'>" +
this._get(inst, "calculateWeek")(printDate) + "</td>");
for (dow=0; dow < 7; dow++){
daySettings=(beforeShowDay ?
beforeShowDay.apply((inst.input ? inst.input[0]:null), [printDate]):[true, ""]);
otherMonth=(printDate.getMonth()!==drawMonth);
unselectable=(otherMonth&&!selectOtherMonths)||!daySettings[0] ||
(minDate&&printDate < minDate)||(maxDate&&printDate > maxDate);
tbody +="<td class='" +
((dow + firstDay + 6) % 7 >=5 ? " ui-datepicker-week-end":"") +
(otherMonth ? " ui-datepicker-other-month":"") +
((printDate.getTime()===selectedDate.getTime()&&drawMonth===inst.selectedMonth&&inst._keyEvent) ||
(defaultDate.getTime()===printDate.getTime()&&defaultDate.getTime()===selectedDate.getTime()) ?
" " + this._dayOverClass:"") +
(unselectable ? " " + this._unselectableClass + " ui-state-disabled": "") +
(otherMonth&&!showOtherMonths ? "":" " + daySettings[1] +
(printDate.getTime()===currentDate.getTime() ? " " + this._currentClass:"") +
(printDate.getTime()===today.getTime() ? " ui-datepicker-today":"")) + "'" +
((!otherMonth||showOtherMonths)&&daySettings[2] ? " title='" + daySettings[2].replace(/'/g, "&#39;") + "'":"") +
(unselectable ? "":" data-handler='selectDay' data-event='click' data-month='" + printDate.getMonth() + "' data-year='" + printDate.getFullYear() + "'") + ">" +
(otherMonth&&!showOtherMonths ? "&#xa0;" :
(unselectable ? "<span class='ui-state-default'>" + printDate.getDate() + "</span>":"<a class='ui-state-default" +
(printDate.getTime()===today.getTime() ? " ui-state-highlight":"") +
(printDate.getTime()===currentDate.getTime() ? " ui-state-active":"") +
(otherMonth ? " ui-priority-secondary":"") +
"' href='#'>" + printDate.getDate() + "</a>")) + "</td>";
printDate.setDate(printDate.getDate() + 1);
printDate=this._daylightSavingAdjust(printDate);
}
calender +=tbody + "</tr>";
}
drawMonth++;
if(drawMonth > 11){
drawMonth=0;
drawYear++;
}
calender +="</tbody></table>" + (isMultiMonth ? "</div>" +
((numMonths[0] > 0&&col===numMonths[1]-1) ? "<div class='ui-datepicker-row-break'></div>":""):"");
group +=calender;
}
html +=group;
}
html +=buttonPanel;
inst._keyEvent=false;
return html;
},
_generateMonthYearHeader: function(inst, drawMonth, drawYear, minDate, maxDate,
secondary, monthNames, monthNamesShort){
var inMinYear, inMaxYear, month, years, thisYear, determineYear, year, endYear,
changeMonth=this._get(inst, "changeMonth"),
changeYear=this._get(inst, "changeYear"),
showMonthAfterYear=this._get(inst, "showMonthAfterYear"),
html="<div class='ui-datepicker-title'>",
monthHtml="";
if(secondary||!changeMonth){
monthHtml +="<span class='ui-datepicker-month'>" + monthNames[drawMonth] + "</span>";
}else{
inMinYear=(minDate&&minDate.getFullYear()===drawYear);
inMaxYear=(maxDate&&maxDate.getFullYear()===drawYear);
monthHtml +="<select class='ui-datepicker-month' data-handler='selectMonth' data-event='change'>";
for(month=0; month < 12; month++){
if((!inMinYear||month >=minDate.getMonth())&&(!inMaxYear||month <=maxDate.getMonth())){
monthHtml +="<option value='" + month + "'" +
(month===drawMonth ? " selected='selected'":"") +
">" + monthNamesShort[month] + "</option>";
}}
monthHtml +="</select>";
}
if(!showMonthAfterYear){
html +=monthHtml + (secondary||!(changeMonth&&changeYear) ? "&#xa0;":"");
}
if(!inst.yearshtml){
inst.yearshtml="";
if(secondary||!changeYear){
html +="<span class='ui-datepicker-year'>" + drawYear + "</span>";
}else{
years=this._get(inst, "yearRange").split(":");
thisYear=new Date().getFullYear();
determineYear=function(value){
var year=(value.match(/c[+\-].*/) ? drawYear + parseInt(value.substring(1), 10) :
(value.match(/[+\-].*/) ? thisYear + parseInt(value, 10) :
parseInt(value, 10)));
return (isNaN(year) ? thisYear:year);
};
year=determineYear(years[0]);
endYear=Math.max(year, determineYear(years[1]||""));
year=(minDate ? Math.max(year, minDate.getFullYear()):year);
endYear=(maxDate ? Math.min(endYear, maxDate.getFullYear()):endYear);
inst.yearshtml +="<select class='ui-datepicker-year' data-handler='selectYear' data-event='change'>";
for (; year <=endYear; year++){
inst.yearshtml +="<option value='" + year + "'" +
(year===drawYear ? " selected='selected'":"") +
">" + year + "</option>";
}
inst.yearshtml +="</select>";
html +=inst.yearshtml;
inst.yearshtml=null;
}}
html +=this._get(inst, "yearSuffix");
if(showMonthAfterYear){
html +=(secondary||!(changeMonth&&changeYear) ? "&#xa0;":"") + monthHtml;
}
html +="</div>";
return html;
},
_adjustInstDate: function(inst, offset, period){
var year=inst.drawYear + (period==="Y" ? offset:0),
month=inst.drawMonth + (period==="M" ? offset:0),
day=Math.min(inst.selectedDay, this._getDaysInMonth(year, month)) + (period==="D" ? offset:0),
date=this._restrictMinMax(inst, this._daylightSavingAdjust(new Date(year, month, day)));
inst.selectedDay=date.getDate();
inst.drawMonth=inst.selectedMonth=date.getMonth();
inst.drawYear=inst.selectedYear=date.getFullYear();
if(period==="M"||period==="Y"){
this._notifyChange(inst);
}},
_restrictMinMax: function(inst, date){
var minDate=this._getMinMaxDate(inst, "min"),
maxDate=this._getMinMaxDate(inst, "max"),
newDate=(minDate&&date < minDate ? minDate:date);
return (maxDate&&newDate > maxDate ? maxDate:newDate);
},
_notifyChange: function(inst){
var onChange=this._get(inst, "onChangeMonthYear");
if(onChange){
onChange.apply((inst.input ? inst.input[0]:null),
[inst.selectedYear, inst.selectedMonth + 1, inst]);
}},
_getNumberOfMonths: function(inst){
var numMonths=this._get(inst, "numberOfMonths");
return (numMonths==null ? [1, 1]:(typeof numMonths==="number" ? [1, numMonths]:numMonths));
},
_getMinMaxDate: function(inst, minMax){
return this._determineDate(inst, this._get(inst, minMax + "Date"), null);
},
_getDaysInMonth: function(year, month){
return 32 - this._daylightSavingAdjust(new Date(year, month, 32)).getDate();
},
_getFirstDayOfMonth: function(year, month){
return new Date(year, month, 1).getDay();
},
_canAdjustMonth: function(inst, offset, curYear, curMonth){
var numMonths=this._getNumberOfMonths(inst),
date=this._daylightSavingAdjust(new Date(curYear,
curMonth + (offset < 0 ? offset:numMonths[0] * numMonths[1]), 1));
if(offset < 0){
date.setDate(this._getDaysInMonth(date.getFullYear(), date.getMonth()));
}
return this._isInRange(inst, date);
},
_isInRange: function(inst, date){
var yearSplit, currentYear,
minDate=this._getMinMaxDate(inst, "min"),
maxDate=this._getMinMaxDate(inst, "max"),
minYear=null,
maxYear=null,
years=this._get(inst, "yearRange");
if(years){
yearSplit=years.split(":");
currentYear=new Date().getFullYear();
minYear=parseInt(yearSplit[0], 10);
maxYear=parseInt(yearSplit[1], 10);
if(yearSplit[0].match(/[+\-].*/)){
minYear +=currentYear;
}
if(yearSplit[1].match(/[+\-].*/)){
maxYear +=currentYear;
}}
return ((!minDate||date.getTime() >=minDate.getTime()) &&
(!maxDate||date.getTime() <=maxDate.getTime()) &&
(!minYear||date.getFullYear() >=minYear) &&
(!maxYear||date.getFullYear() <=maxYear));
},
_getFormatConfig: function(inst){
var shortYearCutoff=this._get(inst, "shortYearCutoff");
shortYearCutoff=(typeof shortYearCutoff!=="string" ? shortYearCutoff :
new Date().getFullYear() % 100 + parseInt(shortYearCutoff, 10));
return {shortYearCutoff: shortYearCutoff,
dayNamesShort: this._get(inst, "dayNamesShort"), dayNames: this._get(inst, "dayNames"),
monthNamesShort: this._get(inst, "monthNamesShort"), monthNames: this._get(inst, "monthNames")};},
_formatDate: function(inst, day, month, year){
if(!day){
inst.currentDay=inst.selectedDay;
inst.currentMonth=inst.selectedMonth;
inst.currentYear=inst.selectedYear;
}
var date=(day ? (typeof day==="object" ? day :
this._daylightSavingAdjust(new Date(year, month, day))) :
this._daylightSavingAdjust(new Date(inst.currentYear, inst.currentMonth, inst.currentDay)));
return this.formatDate(this._get(inst, "dateFormat"), date, this._getFormatConfig(inst));
}});
function datepicker_bindHover(dpDiv){
var selector="button, .ui-datepicker-prev, .ui-datepicker-next, .ui-datepicker-calendar td a";
return dpDiv.delegate(selector, "mouseout", function(){
$(this).removeClass("ui-state-hover");
if(this.className.indexOf("ui-datepicker-prev")!==-1){
$(this).removeClass("ui-datepicker-prev-hover");
}
if(this.className.indexOf("ui-datepicker-next")!==-1){
$(this).removeClass("ui-datepicker-next-hover");
}})
.delegate(selector, "mouseover", datepicker_handleMouseover);
}
function datepicker_handleMouseover(){
if(!$.datepicker._isDisabledDatepicker(datepicker_instActive.inline? datepicker_instActive.dpDiv.parent()[0]:datepicker_instActive.input[0])){
$(this).parents(".ui-datepicker-calendar").find("a").removeClass("ui-state-hover");
$(this).addClass("ui-state-hover");
if(this.className.indexOf("ui-datepicker-prev")!==-1){
$(this).addClass("ui-datepicker-prev-hover");
}
if(this.className.indexOf("ui-datepicker-next")!==-1){
$(this).addClass("ui-datepicker-next-hover");
}}
}
function datepicker_extendRemove(target, props){
$.extend(target, props);
for (var name in props){
if(props[name]==null){
target[name]=props[name];
}}
return target;
}
$.fn.datepicker=function(options){
if(!this.length){
return this;
}
if(!$.datepicker.initialized){
$(document).mousedown($.datepicker._checkExternalClick);
$.datepicker.initialized=true;
}
if($("#"+$.datepicker._mainDivId).length===0){
$("body").append($.datepicker.dpDiv);
}
var otherArgs=Array.prototype.slice.call(arguments, 1);
if(typeof options==="string"&&(options==="isDisabled"||options==="getDate"||options==="widget")){
return $.datepicker["_" + options + "Datepicker"].
apply($.datepicker, [this[0]].concat(otherArgs));
}
if(options==="option"&&arguments.length===2&&typeof arguments[1]==="string"){
return $.datepicker["_" + options + "Datepicker"].
apply($.datepicker, [this[0]].concat(otherArgs));
}
return this.each(function(){
typeof options==="string" ?
$.datepicker["_" + options + "Datepicker"].
apply($.datepicker, [this].concat(otherArgs)) :
$.datepicker._attachDatepicker(this, options);
});
};
$.datepicker=new Datepicker();
$.datepicker.initialized=false;
$.datepicker.uuid=new Date().getTime();
$.datepicker.version="1.11.4";
var datepicker=$.datepicker;
var dialog=$.widget("ui.dialog", {
version: "1.11.4",
options: {
appendTo: "body",
autoOpen: true,
buttons: [],
closeOnEscape: true,
closeText: "Close",
dialogClass: "",
draggable: true,
hide: null,
height: "auto",
maxHeight: null,
maxWidth: null,
minHeight: 150,
minWidth: 150,
modal: false,
position: {
my: "center",
at: "center",
of: window,
collision: "fit",
using: function(pos){
var topOffset=$(this).css(pos).offset().top;
if(topOffset < 0){
$(this).css("top", pos.top - topOffset);
}}
},
resizable: true,
show: null,
title: null,
width: 300,
beforeClose: null,
close: null,
drag: null,
dragStart: null,
dragStop: null,
focus: null,
open: null,
resize: null,
resizeStart: null,
resizeStop: null
},
sizeRelatedOptions: {
buttons: true,
height: true,
maxHeight: true,
maxWidth: true,
minHeight: true,
minWidth: true,
width: true
},
resizableRelatedOptions: {
maxHeight: true,
maxWidth: true,
minHeight: true,
minWidth: true
},
_create: function(){
this.originalCss={
display: this.element[ 0 ].style.display,
width: this.element[ 0 ].style.width,
minHeight: this.element[ 0 ].style.minHeight,
maxHeight: this.element[ 0 ].style.maxHeight,
height: this.element[ 0 ].style.height
};
this.originalPosition={
parent: this.element.parent(),
index: this.element.parent().children().index(this.element)
};
this.originalTitle=this.element.attr("title");
this.options.title=this.options.title||this.originalTitle;
this._createWrapper();
this.element
.show()
.removeAttr("title")
.addClass("ui-dialog-content ui-widget-content")
.appendTo(this.uiDialog);
this._createTitlebar();
this._createButtonPane();
if(this.options.draggable&&$.fn.draggable){
this._makeDraggable();
}
if(this.options.resizable&&$.fn.resizable){
this._makeResizable();
}
this._isOpen=false;
this._trackFocus();
},
_init: function(){
if(this.options.autoOpen){
this.open();
}},
_appendTo: function(){
var element=this.options.appendTo;
if(element&&(element.jquery||element.nodeType)){
return $(element);
}
return this.document.find(element||"body").eq(0);
},
_destroy: function(){
var next,
originalPosition=this.originalPosition;
this._untrackInstance();
this._destroyOverlay();
this.element
.removeUniqueId()
.removeClass("ui-dialog-content ui-widget-content")
.css(this.originalCss)
.detach();
this.uiDialog.stop(true, true).remove();
if(this.originalTitle){
this.element.attr("title", this.originalTitle);
}
next=originalPosition.parent.children().eq(originalPosition.index);
if(next.length&&next[ 0 ]!==this.element[ 0 ]){
next.before(this.element);
}else{
originalPosition.parent.append(this.element);
}},
widget: function(){
return this.uiDialog;
},
disable: $.noop,
enable: $.noop,
close: function(event){
var activeElement,
that=this;
if(!this._isOpen||this._trigger("beforeClose", event)===false){
return;
}
this._isOpen=false;
this._focusedElement=null;
this._destroyOverlay();
this._untrackInstance();
if(!this.opener.filter(":focusable").focus().length){
try {
activeElement=this.document[ 0 ].activeElement;
if(activeElement&&activeElement.nodeName.toLowerCase()!=="body"){
$(activeElement).blur();
}} catch(error){}}
this._hide(this.uiDialog, this.options.hide, function(){
that._trigger("close", event);
});
},
isOpen: function(){
return this._isOpen;
},
moveToTop: function(){
this._moveToTop();
},
_moveToTop: function(event, silent){
var moved=false,
zIndices=this.uiDialog.siblings(".ui-front:visible").map(function(){
return +$(this).css("z-index");
}).get(),
zIndexMax=Math.max.apply(null, zIndices);
if(zIndexMax >=+this.uiDialog.css("z-index")){
this.uiDialog.css("z-index", zIndexMax + 1);
moved=true;
}
if(moved&&!silent){
this._trigger("focus", event);
}
return moved;
},
open: function(){
var that=this;
if(this._isOpen){
if(this._moveToTop()){
this._focusTabbable();
}
return;
}
this._isOpen=true;
this.opener=$(this.document[ 0 ].activeElement);
this._size();
this._position();
this._createOverlay();
this._moveToTop(null, true);
if(this.overlay){
this.overlay.css("z-index", this.uiDialog.css("z-index") - 1);
}
this._show(this.uiDialog, this.options.show, function(){
that._focusTabbable();
that._trigger("focus");
});
this._makeFocusTarget();
this._trigger("open");
},
_focusTabbable: function(){
var hasFocus=this._focusedElement;
if(!hasFocus){
hasFocus=this.element.find("[autofocus]");
}
if(!hasFocus.length){
hasFocus=this.element.find(":tabbable");
}
if(!hasFocus.length){
hasFocus=this.uiDialogButtonPane.find(":tabbable");
}
if(!hasFocus.length){
hasFocus=this.uiDialogTitlebarClose.filter(":tabbable");
}
if(!hasFocus.length){
hasFocus=this.uiDialog;
}
hasFocus.eq(0).focus();
},
_keepFocus: function(event){
function checkFocus(){
var activeElement=this.document[0].activeElement,
isActive=this.uiDialog[0]===activeElement ||
$.contains(this.uiDialog[0], activeElement);
if(!isActive){
this._focusTabbable();
}}
event.preventDefault();
checkFocus.call(this);
this._delay(checkFocus);
},
_createWrapper: function(){
this.uiDialog=$("<div>")
.addClass("ui-dialog ui-widget ui-widget-content ui-corner-all ui-front " +
this.options.dialogClass)
.hide()
.attr({
tabIndex: -1,
role: "dialog"
})
.appendTo(this._appendTo());
this._on(this.uiDialog, {
keydown: function(event){
if(this.options.closeOnEscape&&!event.isDefaultPrevented()&&event.keyCode &&
event.keyCode===$.ui.keyCode.ESCAPE){
event.preventDefault();
this.close(event);
return;
}
if(event.keyCode!==$.ui.keyCode.TAB||event.isDefaultPrevented()){
return;
}
var tabbables=this.uiDialog.find(":tabbable"),
first=tabbables.filter(":first"),
last=tabbables.filter(":last");
if(( event.target===last[0]||event.target===this.uiDialog[0])&&!event.shiftKey){
this._delay(function(){
first.focus();
});
event.preventDefault();
}else if(( event.target===first[0]||event.target===this.uiDialog[0])&&event.shiftKey){
this._delay(function(){
last.focus();
});
event.preventDefault();
}},
mousedown: function(event){
if(this._moveToTop(event)){
this._focusTabbable();
}}
});
if(!this.element.find("[aria-describedby]").length){
this.uiDialog.attr({
"aria-describedby": this.element.uniqueId().attr("id")
});
}},
_createTitlebar: function(){
var uiDialogTitle;
this.uiDialogTitlebar=$("<div>")
.addClass("ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix")
.prependTo(this.uiDialog);
this._on(this.uiDialogTitlebar, {
mousedown: function(event){
if(!$(event.target).closest(".ui-dialog-titlebar-close")){
this.uiDialog.focus();
}}
});
this.uiDialogTitlebarClose=$("<button type='button'></button>")
.button({
label: this.options.closeText,
icons: {
primary: "ui-icon-closethick"
},
text: false
})
.addClass("ui-dialog-titlebar-close")
.appendTo(this.uiDialogTitlebar);
this._on(this.uiDialogTitlebarClose, {
click: function(event){
event.preventDefault();
this.close(event);
}});
uiDialogTitle=$("<span>")
.uniqueId()
.addClass("ui-dialog-title")
.prependTo(this.uiDialogTitlebar);
this._title(uiDialogTitle);
this.uiDialog.attr({
"aria-labelledby": uiDialogTitle.attr("id")
});
},
_title: function(title){
if(!this.options.title){
title.html("&#160;");
}
title.text(this.options.title);
},
_createButtonPane: function(){
this.uiDialogButtonPane=$("<div>")
.addClass("ui-dialog-buttonpane ui-widget-content ui-helper-clearfix");
this.uiButtonSet=$("<div>")
.addClass("ui-dialog-buttonset")
.appendTo(this.uiDialogButtonPane);
this._createButtons();
},
_createButtons: function(){
var that=this,
buttons=this.options.buttons;
this.uiDialogButtonPane.remove();
this.uiButtonSet.empty();
if($.isEmptyObject(buttons)||($.isArray(buttons)&&!buttons.length)){
this.uiDialog.removeClass("ui-dialog-buttons");
return;
}
$.each(buttons, function(name, props){
var click, buttonOptions;
props=$.isFunction(props) ?
{ click: props, text: name } :
props;
props=$.extend({ type: "button" }, props);
click=props.click;
props.click=function(){
click.apply(that.element[ 0 ], arguments);
};
buttonOptions={
icons: props.icons,
text: props.showText
};
delete props.icons;
delete props.showText;
$("<button></button>", props)
.button(buttonOptions)
.appendTo(that.uiButtonSet);
});
this.uiDialog.addClass("ui-dialog-buttons");
this.uiDialogButtonPane.appendTo(this.uiDialog);
},
_makeDraggable: function(){
var that=this,
options=this.options;
function filteredUi(ui){
return {
position: ui.position,
offset: ui.offset
};}
this.uiDialog.draggable({
cancel: ".ui-dialog-content, .ui-dialog-titlebar-close",
handle: ".ui-dialog-titlebar",
containment: "document",
start: function(event, ui){
$(this).addClass("ui-dialog-dragging");
that._blockFrames();
that._trigger("dragStart", event, filteredUi(ui));
},
drag: function(event, ui){
that._trigger("drag", event, filteredUi(ui));
},
stop: function(event, ui){
var left=ui.offset.left - that.document.scrollLeft(),
top=ui.offset.top - that.document.scrollTop();
options.position={
my: "left top",
at: "left" + (left >=0 ? "+":"") + left + " " +
"top" + (top >=0 ? "+":"") + top,
of: that.window
};
$(this).removeClass("ui-dialog-dragging");
that._unblockFrames();
that._trigger("dragStop", event, filteredUi(ui));
}});
},
_makeResizable: function(){
var that=this,
options=this.options,
handles=options.resizable,
position=this.uiDialog.css("position"),
resizeHandles=typeof handles==="string" ?
handles	:
"n,e,s,w,se,sw,ne,nw";
function filteredUi(ui){
return {
originalPosition: ui.originalPosition,
originalSize: ui.originalSize,
position: ui.position,
size: ui.size
};}
this.uiDialog.resizable({
cancel: ".ui-dialog-content",
containment: "document",
alsoResize: this.element,
maxWidth: options.maxWidth,
maxHeight: options.maxHeight,
minWidth: options.minWidth,
minHeight: this._minHeight(),
handles: resizeHandles,
start: function(event, ui){
$(this).addClass("ui-dialog-resizing");
that._blockFrames();
that._trigger("resizeStart", event, filteredUi(ui));
},
resize: function(event, ui){
that._trigger("resize", event, filteredUi(ui));
},
stop: function(event, ui){
var offset=that.uiDialog.offset(),
left=offset.left - that.document.scrollLeft(),
top=offset.top - that.document.scrollTop();
options.height=that.uiDialog.height();
options.width=that.uiDialog.width();
options.position={
my: "left top",
at: "left" + (left >=0 ? "+":"") + left + " " +
"top" + (top >=0 ? "+":"") + top,
of: that.window
};
$(this).removeClass("ui-dialog-resizing");
that._unblockFrames();
that._trigger("resizeStop", event, filteredUi(ui));
}})
.css("position", position);
},
_trackFocus: function(){
this._on(this.widget(), {
focusin: function(event){
this._makeFocusTarget();
this._focusedElement=$(event.target);
}});
},
_makeFocusTarget: function(){
this._untrackInstance();
this._trackingInstances().unshift(this);
},
_untrackInstance: function(){
var instances=this._trackingInstances(),
exists=$.inArray(this, instances);
if(exists!==-1){
instances.splice(exists, 1);
}},
_trackingInstances: function(){
var instances=this.document.data("ui-dialog-instances");
if(!instances){
instances=[];
this.document.data("ui-dialog-instances", instances);
}
return instances;
},
_minHeight: function(){
var options=this.options;
return options.height==="auto" ?
options.minHeight :
Math.min(options.minHeight, options.height);
},
_position: function(){
var isVisible=this.uiDialog.is(":visible");
if(!isVisible){
this.uiDialog.show();
}
this.uiDialog.position(this.options.position);
if(!isVisible){
this.uiDialog.hide();
}},
_setOptions: function(options){
var that=this,
resize=false,
resizableOptions={};
$.each(options, function(key, value){
that._setOption(key, value);
if(key in that.sizeRelatedOptions){
resize=true;
}
if(key in that.resizableRelatedOptions){
resizableOptions[ key ]=value;
}});
if(resize){
this._size();
this._position();
}
if(this.uiDialog.is(":data(ui-resizable)")){
this.uiDialog.resizable("option", resizableOptions);
}},
_setOption: function(key, value){
var isDraggable, isResizable,
uiDialog=this.uiDialog;
if(key==="dialogClass"){
uiDialog
.removeClass(this.options.dialogClass)
.addClass(value);
}
if(key==="disabled"){
return;
}
this._super(key, value);
if(key==="appendTo"){
this.uiDialog.appendTo(this._appendTo());
}
if(key==="buttons"){
this._createButtons();
}
if(key==="closeText"){
this.uiDialogTitlebarClose.button({
label: "" + value
});
}
if(key==="draggable"){
isDraggable=uiDialog.is(":data(ui-draggable)");
if(isDraggable&&!value){
uiDialog.draggable("destroy");
}
if(!isDraggable&&value){
this._makeDraggable();
}}
if(key==="position"){
this._position();
}
if(key==="resizable"){
isResizable=uiDialog.is(":data(ui-resizable)");
if(isResizable&&!value){
uiDialog.resizable("destroy");
}
if(isResizable&&typeof value==="string"){
uiDialog.resizable("option", "handles", value);
}
if(!isResizable&&value!==false){
this._makeResizable();
}}
if(key==="title"){
this._title(this.uiDialogTitlebar.find(".ui-dialog-title"));
}},
_size: function(){
var nonContentHeight, minContentHeight, maxContentHeight,
options=this.options;
this.element.show().css({
width: "auto",
minHeight: 0,
maxHeight: "none",
height: 0
});
if(options.minWidth > options.width){
options.width=options.minWidth;
}
nonContentHeight=this.uiDialog.css({
height: "auto",
width: options.width
})
.outerHeight();
minContentHeight=Math.max(0, options.minHeight - nonContentHeight);
maxContentHeight=typeof options.maxHeight==="number" ?
Math.max(0, options.maxHeight - nonContentHeight) :
"none";
if(options.height==="auto"){
this.element.css({
minHeight: minContentHeight,
maxHeight: maxContentHeight,
height: "auto"
});
}else{
this.element.height(Math.max(0, options.height - nonContentHeight));
}
if(this.uiDialog.is(":data(ui-resizable)")){
this.uiDialog.resizable("option", "minHeight", this._minHeight());
}},
_blockFrames: function(){
this.iframeBlocks=this.document.find("iframe").map(function(){
var iframe=$(this);
return $("<div>")
.css({
position: "absolute",
width: iframe.outerWidth(),
height: iframe.outerHeight()
})
.appendTo(iframe.parent())
.offset(iframe.offset())[0];
});
},
_unblockFrames: function(){
if(this.iframeBlocks){
this.iframeBlocks.remove();
delete this.iframeBlocks;
}},
_allowInteraction: function(event){
if($(event.target).closest(".ui-dialog").length){
return true;
}
return !!$(event.target).closest(".ui-datepicker").length;
},
_createOverlay: function(){
if(!this.options.modal){
return;
}
var isOpening=true;
this._delay(function(){
isOpening=false;
});
if(!this.document.data("ui-dialog-overlays")){
this._on(this.document, {
focusin: function(event){
if(isOpening){
return;
}
if(!this._allowInteraction(event)){
event.preventDefault();
this._trackingInstances()[ 0 ]._focusTabbable();
}}
});
}
this.overlay=$("<div>")
.addClass("ui-widget-overlay ui-front")
.appendTo(this._appendTo());
this._on(this.overlay, {
mousedown: "_keepFocus"
});
this.document.data("ui-dialog-overlays",
(this.document.data("ui-dialog-overlays")||0) + 1);
},
_destroyOverlay: function(){
if(!this.options.modal){
return;
}
if(this.overlay){
var overlays=this.document.data("ui-dialog-overlays") - 1;
if(!overlays){
this.document
.unbind("focusin")
.removeData("ui-dialog-overlays");
}else{
this.document.data("ui-dialog-overlays", overlays);
}
this.overlay.remove();
this.overlay=null;
}}
});
var progressbar=$.widget("ui.progressbar", {
version: "1.11.4",
options: {
max: 100,
value: 0,
change: null,
complete: null
},
min: 0,
_create: function(){
this.oldValue=this.options.value=this._constrainedValue();
this.element
.addClass("ui-progressbar ui-widget ui-widget-content ui-corner-all")
.attr({
role: "progressbar",
"aria-valuemin": this.min
});
this.valueDiv=$("<div class='ui-progressbar-value ui-widget-header ui-corner-left'></div>")
.appendTo(this.element);
this._refreshValue();
},
_destroy: function(){
this.element
.removeClass("ui-progressbar ui-widget ui-widget-content ui-corner-all")
.removeAttr("role")
.removeAttr("aria-valuemin")
.removeAttr("aria-valuemax")
.removeAttr("aria-valuenow");
this.valueDiv.remove();
},
value: function(newValue){
if(newValue===undefined){
return this.options.value;
}
this.options.value=this._constrainedValue(newValue);
this._refreshValue();
},
_constrainedValue: function(newValue){
if(newValue===undefined){
newValue=this.options.value;
}
this.indeterminate=newValue===false;
if(typeof newValue!=="number"){
newValue=0;
}
return this.indeterminate ? false :
Math.min(this.options.max, Math.max(this.min, newValue));
},
_setOptions: function(options){
var value=options.value;
delete options.value;
this._super(options);
this.options.value=this._constrainedValue(value);
this._refreshValue();
},
_setOption: function(key, value){
if(key==="max"){
value=Math.max(this.min, value);
}
if(key==="disabled"){
this.element
.toggleClass("ui-state-disabled", !!value)
.attr("aria-disabled", value);
}
this._super(key, value);
},
_percentage: function(){
return this.indeterminate ? 100:100 *(this.options.value - this.min) /(this.options.max - this.min);
},
_refreshValue: function(){
var value=this.options.value,
percentage=this._percentage();
this.valueDiv
.toggle(this.indeterminate||value > this.min)
.toggleClass("ui-corner-right", value===this.options.max)
.width(percentage.toFixed(0) + "%");
this.element.toggleClass("ui-progressbar-indeterminate", this.indeterminate);
if(this.indeterminate){
this.element.removeAttr("aria-valuenow");
if(!this.overlayDiv){
this.overlayDiv=$("<div class='ui-progressbar-overlay'></div>").appendTo(this.valueDiv);
}}else{
this.element.attr({
"aria-valuemax": this.options.max,
"aria-valuenow": value
});
if(this.overlayDiv){
this.overlayDiv.remove();
this.overlayDiv=null;
}}
if(this.oldValue!==value){
this.oldValue=value;
this._trigger("change");
}
if(value===this.options.max){
this._trigger("complete");
}}
});
var selectmenu=$.widget("ui.selectmenu", {
version: "1.11.4",
defaultElement: "<select>",
options: {
appendTo: null,
disabled: null,
icons: {
button: "ui-icon-triangle-1-s"
},
position: {
my: "left top",
at: "left bottom",
collision: "none"
},
width: null,
change: null,
close: null,
focus: null,
open: null,
select: null
},
_create: function(){
var selectmenuId=this.element.uniqueId().attr("id");
this.ids={
element: selectmenuId,
button: selectmenuId + "-button",
menu: selectmenuId + "-menu"
};
this._drawButton();
this._drawMenu();
if(this.options.disabled){
this.disable();
}},
_drawButton: function(){
var that=this;
this.label=$("label[for='" + this.ids.element + "']").attr("for", this.ids.button);
this._on(this.label, {
click: function(event){
this.button.focus();
event.preventDefault();
}});
this.element.hide();
this.button=$("<span>", {
"class": "ui-selectmenu-button ui-widget ui-state-default ui-corner-all",
tabindex: this.options.disabled ? -1:0,
id: this.ids.button,
role: "combobox",
"aria-expanded": "false",
"aria-autocomplete": "list",
"aria-owns": this.ids.menu,
"aria-haspopup": "true"
})
.insertAfter(this.element);
$("<span>", {
"class": "ui-icon " + this.options.icons.button
})
.prependTo(this.button);
this.buttonText=$("<span>", {
"class": "ui-selectmenu-text"
})
.appendTo(this.button);
this._setText(this.buttonText, this.element.find("option:selected").text());
this._resizeButton();
this._on(this.button, this._buttonEvents);
this.button.one("focusin", function(){
if(!that.menuItems){
that._refreshMenu();
}});
this._hoverable(this.button);
this._focusable(this.button);
},
_drawMenu: function(){
var that=this;
this.menu=$("<ul>", {
"aria-hidden": "true",
"aria-labelledby": this.ids.button,
id: this.ids.menu
});
this.menuWrap=$("<div>", {
"class": "ui-selectmenu-menu ui-front"
})
.append(this.menu)
.appendTo(this._appendTo());
this.menuInstance=this.menu
.menu({
role: "listbox",
select: function(event, ui){
event.preventDefault();
that._setSelection();
that._select(ui.item.data("ui-selectmenu-item"), event);
},
focus: function(event, ui){
var item=ui.item.data("ui-selectmenu-item");
if(that.focusIndex!=null&&item.index!==that.focusIndex){
that._trigger("focus", event, { item: item });
if(!that.isOpen){
that._select(item, event);
}}
that.focusIndex=item.index;
that.button.attr("aria-activedescendant",
that.menuItems.eq(item.index).attr("id"));
}})
.menu("instance");
this.menu
.addClass("ui-corner-bottom")
.removeClass("ui-corner-all");
this.menuInstance._off(this.menu, "mouseleave");
this.menuInstance._closeOnDocumentClick=function(){
return false;
};
this.menuInstance._isDivider=function(){
return false;
};},
refresh: function(){
this._refreshMenu();
this._setText(this.buttonText, this._getSelectedItem().text());
if(!this.options.width){
this._resizeButton();
}},
_refreshMenu: function(){
this.menu.empty();
var item,
options=this.element.find("option");
if(!options.length){
return;
}
this._parseOptions(options);
this._renderMenu(this.menu, this.items);
this.menuInstance.refresh();
this.menuItems=this.menu.find("li").not(".ui-selectmenu-optgroup");
item=this._getSelectedItem();
this.menuInstance.focus(null, item);
this._setAria(item.data("ui-selectmenu-item"));
this._setOption("disabled", this.element.prop("disabled"));
},
open: function(event){
if(this.options.disabled){
return;
}
if(!this.menuItems){
this._refreshMenu();
}else{
this.menu.find(".ui-state-focus").removeClass("ui-state-focus");
this.menuInstance.focus(null, this._getSelectedItem());
}
this.isOpen=true;
this._toggleAttr();
this._resizeMenu();
this._position();
this._on(this.document, this._documentClick);
this._trigger("open", event);
},
_position: function(){
this.menuWrap.position($.extend({ of: this.button }, this.options.position));
},
close: function(event){
if(!this.isOpen){
return;
}
this.isOpen=false;
this._toggleAttr();
this.range=null;
this._off(this.document);
this._trigger("close", event);
},
widget: function(){
return this.button;
},
menuWidget: function(){
return this.menu;
},
_renderMenu: function(ul, items){
var that=this,
currentOptgroup="";
$.each(items, function(index, item){
if(item.optgroup!==currentOptgroup){
$("<li>", {
"class": "ui-selectmenu-optgroup ui-menu-divider" +
(item.element.parent("optgroup").prop("disabled") ?
" ui-state-disabled" :
""),
text: item.optgroup
})
.appendTo(ul);
currentOptgroup=item.optgroup;
}
that._renderItemData(ul, item);
});
},
_renderItemData: function(ul, item){
return this._renderItem(ul, item).data("ui-selectmenu-item", item);
},
_renderItem: function(ul, item){
var li=$("<li>");
if(item.disabled){
li.addClass("ui-state-disabled");
}
this._setText(li, item.label);
return li.appendTo(ul);
},
_setText: function(element, value){
if(value){
element.text(value);
}else{
element.html("&#160;");
}},
_move: function(direction, event){
var item, next,
filter=".ui-menu-item";
if(this.isOpen){
item=this.menuItems.eq(this.focusIndex);
}else{
item=this.menuItems.eq(this.element[ 0 ].selectedIndex);
filter +=":not(.ui-state-disabled)";
}
if(direction==="first"||direction==="last"){
next=item[ direction==="first" ? "prevAll":"nextAll" ](filter).eq(-1);
}else{
next=item[ direction + "All" ](filter).eq(0);
}
if(next.length){
this.menuInstance.focus(event, next);
}},
_getSelectedItem: function(){
return this.menuItems.eq(this.element[ 0 ].selectedIndex);
},
_toggle: function(event){
this[ this.isOpen ? "close":"open" ](event);
},
_setSelection: function(){
var selection;
if(!this.range){
return;
}
if(window.getSelection){
selection=window.getSelection();
selection.removeAllRanges();
selection.addRange(this.range);
}else{
this.range.select();
}
this.button.focus();
},
_documentClick: {
mousedown: function(event){
if(!this.isOpen){
return;
}
if(!$(event.target).closest(".ui-selectmenu-menu, #" + this.ids.button).length){
this.close(event);
}}
},
_buttonEvents: {
mousedown: function(){
var selection;
if(window.getSelection){
selection=window.getSelection();
if(selection.rangeCount){
this.range=selection.getRangeAt(0);
}}else{
this.range=document.selection.createRange();
}},
click: function(event){
this._setSelection();
this._toggle(event);
},
keydown: function(event){
var preventDefault=true;
switch(event.keyCode){
case $.ui.keyCode.TAB:
case $.ui.keyCode.ESCAPE:
this.close(event);
preventDefault=false;
break;
case $.ui.keyCode.ENTER:
if(this.isOpen){
this._selectFocusedItem(event);
}
break;
case $.ui.keyCode.UP:
if(event.altKey){
this._toggle(event);
}else{
this._move("prev", event);
}
break;
case $.ui.keyCode.DOWN:
if(event.altKey){
this._toggle(event);
}else{
this._move("next", event);
}
break;
case $.ui.keyCode.SPACE:
if(this.isOpen){
this._selectFocusedItem(event);
}else{
this._toggle(event);
}
break;
case $.ui.keyCode.LEFT:
this._move("prev", event);
break;
case $.ui.keyCode.RIGHT:
this._move("next", event);
break;
case $.ui.keyCode.HOME:
case $.ui.keyCode.PAGE_UP:
this._move("first", event);
break;
case $.ui.keyCode.END:
case $.ui.keyCode.PAGE_DOWN:
this._move("last", event);
break;
default:
this.menu.trigger(event);
preventDefault=false;
}
if(preventDefault){
event.preventDefault();
}}
},
_selectFocusedItem: function(event){
var item=this.menuItems.eq(this.focusIndex);
if(!item.hasClass("ui-state-disabled")){
this._select(item.data("ui-selectmenu-item"), event);
}},
_select: function(item, event){
var oldIndex=this.element[ 0 ].selectedIndex;
this.element[ 0 ].selectedIndex=item.index;
this._setText(this.buttonText, item.label);
this._setAria(item);
this._trigger("select", event, { item: item });
if(item.index!==oldIndex){
this._trigger("change", event, { item: item });
}
this.close(event);
},
_setAria: function(item){
var id=this.menuItems.eq(item.index).attr("id");
this.button.attr({
"aria-labelledby": id,
"aria-activedescendant": id
});
this.menu.attr("aria-activedescendant", id);
},
_setOption: function(key, value){
if(key==="icons"){
this.button.find("span.ui-icon")
.removeClass(this.options.icons.button)
.addClass(value.button);
}
this._super(key, value);
if(key==="appendTo"){
this.menuWrap.appendTo(this._appendTo());
}
if(key==="disabled"){
this.menuInstance.option("disabled", value);
this.button
.toggleClass("ui-state-disabled", value)
.attr("aria-disabled", value);
this.element.prop("disabled", value);
if(value){
this.button.attr("tabindex", -1);
this.close();
}else{
this.button.attr("tabindex", 0);
}}
if(key==="width"){
this._resizeButton();
}},
_appendTo: function(){
var element=this.options.appendTo;
if(element){
element=element.jquery||element.nodeType ?
$(element) :
this.document.find(element).eq(0);
}
if(!element||!element[ 0 ]){
element=this.element.closest(".ui-front");
}
if(!element.length){
element=this.document[ 0 ].body;
}
return element;
},
_toggleAttr: function(){
this.button
.toggleClass("ui-corner-top", this.isOpen)
.toggleClass("ui-corner-all", !this.isOpen)
.attr("aria-expanded", this.isOpen);
this.menuWrap.toggleClass("ui-selectmenu-open", this.isOpen);
this.menu.attr("aria-hidden", !this.isOpen);
},
_resizeButton: function(){
var width=this.options.width;
if(!width){
width=this.element.show().outerWidth();
this.element.hide();
}
this.button.outerWidth(width);
},
_resizeMenu: function(){
this.menu.outerWidth(Math.max(this.button.outerWidth(),
this.menu.width("").outerWidth() + 1
));
},
_getCreateOptions: function(){
return { disabled: this.element.prop("disabled") };},
_parseOptions: function(options){
var data=[];
options.each(function(index, item){
var option=$(item),
optgroup=option.parent("optgroup");
data.push({
element: option,
index: index,
value: option.val(),
label: option.text(),
optgroup: optgroup.attr("label")||"",
disabled: optgroup.prop("disabled")||option.prop("disabled")
});
});
this.items=data;
},
_destroy: function(){
this.menuWrap.remove();
this.button.remove();
this.element.show();
this.element.removeUniqueId();
this.label.attr("for", this.ids.element);
}});
var slider=$.widget("ui.slider", $.ui.mouse, {
version: "1.11.4",
widgetEventPrefix: "slide",
options: {
animate: false,
distance: 0,
max: 100,
min: 0,
orientation: "horizontal",
range: false,
step: 1,
value: 0,
values: null,
change: null,
slide: null,
start: null,
stop: null
},
numPages: 5,
_create: function(){
this._keySliding=false;
this._mouseSliding=false;
this._animateOff=true;
this._handleIndex=null;
this._detectOrientation();
this._mouseInit();
this._calculateNewMax();
this.element
.addClass("ui-slider" +
" ui-slider-" + this.orientation +
" ui-widget" +
" ui-widget-content" +
" ui-corner-all");
this._refresh();
this._setOption("disabled", this.options.disabled);
this._animateOff=false;
},
_refresh: function(){
this._createRange();
this._createHandles();
this._setupEvents();
this._refreshValue();
},
_createHandles: function(){
var i, handleCount,
options=this.options,
existingHandles=this.element.find(".ui-slider-handle").addClass("ui-state-default ui-corner-all"),
handle="<span class='ui-slider-handle ui-state-default ui-corner-all' tabindex='0'></span>",
handles=[];
handleCount=(options.values&&options.values.length)||1;
if(existingHandles.length > handleCount){
existingHandles.slice(handleCount).remove();
existingHandles=existingHandles.slice(0, handleCount);
}
for(i=existingHandles.length; i < handleCount; i++){
handles.push(handle);
}
this.handles=existingHandles.add($(handles.join("")).appendTo(this.element));
this.handle=this.handles.eq(0);
this.handles.each(function(i){
$(this).data("ui-slider-handle-index", i);
});
},
_createRange: function(){
var options=this.options,
classes="";
if(options.range){
if(options.range===true){
if(!options.values){
options.values=[ this._valueMin(), this._valueMin() ];
}else if(options.values.length&&options.values.length!==2){
options.values=[ options.values[0], options.values[0] ];
}else if($.isArray(options.values)){
options.values=options.values.slice(0);
}}
if(!this.range||!this.range.length){
this.range=$("<div></div>")
.appendTo(this.element);
classes="ui-slider-range" +
" ui-widget-header ui-corner-all";
}else{
this.range.removeClass("ui-slider-range-min ui-slider-range-max")
.css({
"left": "",
"bottom": ""
});
}
this.range.addClass(classes +
(( options.range==="min"||options.range==="max") ? " ui-slider-range-" + options.range:""));
}else{
if(this.range){
this.range.remove();
}
this.range=null;
}},
_setupEvents: function(){
this._off(this.handles);
this._on(this.handles, this._handleEvents);
this._hoverable(this.handles);
this._focusable(this.handles);
},
_destroy: function(){
this.handles.remove();
if(this.range){
this.range.remove();
}
this.element
.removeClass("ui-slider" +
" ui-slider-horizontal" +
" ui-slider-vertical" +
" ui-widget" +
" ui-widget-content" +
" ui-corner-all");
this._mouseDestroy();
},
_mouseCapture: function(event){
var position, normValue, distance, closestHandle, index, allowed, offset, mouseOverHandle,
that=this,
o=this.options;
if(o.disabled){
return false;
}
this.elementSize={
width: this.element.outerWidth(),
height: this.element.outerHeight()
};
this.elementOffset=this.element.offset();
position={ x: event.pageX, y: event.pageY };
normValue=this._normValueFromMouse(position);
distance=this._valueMax() - this._valueMin() + 1;
this.handles.each(function(i){
var thisDistance=Math.abs(normValue - that.values(i));
if((distance > thisDistance) ||
(distance===thisDistance &&
(i===that._lastChangedValue||that.values(i)===o.min))){
distance=thisDistance;
closestHandle=$(this);
index=i;
}});
allowed=this._start(event, index);
if(allowed===false){
return false;
}
this._mouseSliding=true;
this._handleIndex=index;
closestHandle
.addClass("ui-state-active")
.focus();
offset=closestHandle.offset();
mouseOverHandle = !$(event.target).parents().addBack().is(".ui-slider-handle");
this._clickOffset=mouseOverHandle ? { left: 0, top: 0 }:{
left: event.pageX - offset.left -(closestHandle.width() / 2),
top: event.pageY - offset.top -
(closestHandle.height() / 2) -
(parseInt(closestHandle.css("borderTopWidth"), 10)||0) -
(parseInt(closestHandle.css("borderBottomWidth"), 10)||0) +
(parseInt(closestHandle.css("marginTop"), 10)||0)
};
if(!this.handles.hasClass("ui-state-hover")){
this._slide(event, index, normValue);
}
this._animateOff=true;
return true;
},
_mouseStart: function(){
return true;
},
_mouseDrag: function(event){
var position={ x: event.pageX, y: event.pageY },
normValue=this._normValueFromMouse(position);
this._slide(event, this._handleIndex, normValue);
return false;
},
_mouseStop: function(event){
this.handles.removeClass("ui-state-active");
this._mouseSliding=false;
this._stop(event, this._handleIndex);
this._change(event, this._handleIndex);
this._handleIndex=null;
this._clickOffset=null;
this._animateOff=false;
return false;
},
_detectOrientation: function(){
this.orientation=(this.options.orientation==="vertical") ? "vertical":"horizontal";
},
_normValueFromMouse: function(position){
var pixelTotal,
pixelMouse,
percentMouse,
valueTotal,
valueMouse;
if(this.orientation==="horizontal"){
pixelTotal=this.elementSize.width;
pixelMouse=position.x - this.elementOffset.left -(this._clickOffset ? this._clickOffset.left:0);
}else{
pixelTotal=this.elementSize.height;
pixelMouse=position.y - this.elementOffset.top -(this._clickOffset ? this._clickOffset.top:0);
}
percentMouse=(pixelMouse / pixelTotal);
if(percentMouse > 1){
percentMouse=1;
}
if(percentMouse < 0){
percentMouse=0;
}
if(this.orientation==="vertical"){
percentMouse=1 - percentMouse;
}
valueTotal=this._valueMax() - this._valueMin();
valueMouse=this._valueMin() + percentMouse * valueTotal;
return this._trimAlignValue(valueMouse);
},
_start: function(event, index){
var uiHash={
handle: this.handles[ index ],
value: this.value()
};
if(this.options.values&&this.options.values.length){
uiHash.value=this.values(index);
uiHash.values=this.values();
}
return this._trigger("start", event, uiHash);
},
_slide: function(event, index, newVal){
var otherVal,
newValues,
allowed;
if(this.options.values&&this.options.values.length){
otherVal=this.values(index ? 0:1);
if(( this.options.values.length===2&&this.options.range===true) &&
(( index===0&&newVal > otherVal)||(index===1&&newVal < otherVal))
){
newVal=otherVal;
}
if(newVal!==this.values(index)){
newValues=this.values();
newValues[ index ]=newVal;
allowed=this._trigger("slide", event, {
handle: this.handles[ index ],
value: newVal,
values: newValues
});
otherVal=this.values(index ? 0:1);
if(allowed!==false){
this.values(index, newVal);
}}
}else{
if(newVal!==this.value()){
allowed=this._trigger("slide", event, {
handle: this.handles[ index ],
value: newVal
});
if(allowed!==false){
this.value(newVal);
}}
}},
_stop: function(event, index){
var uiHash={
handle: this.handles[ index ],
value: this.value()
};
if(this.options.values&&this.options.values.length){
uiHash.value=this.values(index);
uiHash.values=this.values();
}
this._trigger("stop", event, uiHash);
},
_change: function(event, index){
if(!this._keySliding&&!this._mouseSliding){
var uiHash={
handle: this.handles[ index ],
value: this.value()
};
if(this.options.values&&this.options.values.length){
uiHash.value=this.values(index);
uiHash.values=this.values();
}
this._lastChangedValue=index;
this._trigger("change", event, uiHash);
}},
value: function(newValue){
if(arguments.length){
this.options.value=this._trimAlignValue(newValue);
this._refreshValue();
this._change(null, 0);
return;
}
return this._value();
},
values: function(index, newValue){
var vals,
newValues,
i;
if(arguments.length > 1){
this.options.values[ index ]=this._trimAlignValue(newValue);
this._refreshValue();
this._change(null, index);
return;
}
if(arguments.length){
if($.isArray(arguments[ 0 ])){
vals=this.options.values;
newValues=arguments[ 0 ];
for(i=0; i < vals.length; i +=1){
vals[ i ]=this._trimAlignValue(newValues[ i ]);
this._change(null, i);
}
this._refreshValue();
}else{
if(this.options.values&&this.options.values.length){
return this._values(index);
}else{
return this.value();
}}
}else{
return this._values();
}},
_setOption: function(key, value){
var i,
valsLength=0;
if(key==="range"&&this.options.range===true){
if(value==="min"){
this.options.value=this._values(0);
this.options.values=null;
}else if(value==="max"){
this.options.value=this._values(this.options.values.length - 1);
this.options.values=null;
}}
if($.isArray(this.options.values)){
valsLength=this.options.values.length;
}
if(key==="disabled"){
this.element.toggleClass("ui-state-disabled", !!value);
}
this._super(key, value);
switch(key){
case "orientation":
this._detectOrientation();
this.element
.removeClass("ui-slider-horizontal ui-slider-vertical")
.addClass("ui-slider-" + this.orientation);
this._refreshValue();
this.handles.css(value==="horizontal" ? "bottom":"left", "");
break;
case "value":
this._animateOff=true;
this._refreshValue();
this._change(null, 0);
this._animateOff=false;
break;
case "values":
this._animateOff=true;
this._refreshValue();
for(i=0; i < valsLength; i +=1){
this._change(null, i);
}
this._animateOff=false;
break;
case "step":
case "min":
case "max":
this._animateOff=true;
this._calculateNewMax();
this._refreshValue();
this._animateOff=false;
break;
case "range":
this._animateOff=true;
this._refresh();
this._animateOff=false;
break;
}},
_value: function(){
var val=this.options.value;
val=this._trimAlignValue(val);
return val;
},
_values: function(index){
var val,
vals,
i;
if(arguments.length){
val=this.options.values[ index ];
val=this._trimAlignValue(val);
return val;
}else if(this.options.values&&this.options.values.length){
vals=this.options.values.slice();
for(i=0; i < vals.length; i +=1){
vals[ i ]=this._trimAlignValue(vals[ i ]);
}
return vals;
}else{
return [];
}},
_trimAlignValue: function(val){
if(val <=this._valueMin()){
return this._valueMin();
}
if(val >=this._valueMax()){
return this._valueMax();
}
var step=(this.options.step > 0) ? this.options.step:1,
valModStep=(val - this._valueMin()) % step,
alignValue=val - valModStep;
if(Math.abs(valModStep) * 2 >=step){
alignValue +=(valModStep > 0) ? step:(-step);
}
return parseFloat(alignValue.toFixed(5));
},
_calculateNewMax: function(){
var max=this.options.max,
min=this._valueMin(),
step=this.options.step,
aboveMin=Math.floor(( +(max - min).toFixed(this._precision())) / step) * step;
max=aboveMin + min;
this.max=parseFloat(max.toFixed(this._precision()));
},
_precision: function(){
var precision=this._precisionOf(this.options.step);
if(this.options.min!==null){
precision=Math.max(precision, this._precisionOf(this.options.min));
}
return precision;
},
_precisionOf: function(num){
var str=num.toString(),
decimal=str.indexOf(".");
return decimal===-1 ? 0:str.length - decimal - 1;
},
_valueMin: function(){
return this.options.min;
},
_valueMax: function(){
return this.max;
},
_refreshValue: function(){
var lastValPercent, valPercent, value, valueMin, valueMax,
oRange=this.options.range,
o=this.options,
that=this,
animate=(!this._animateOff) ? o.animate:false,
_set={};
if(this.options.values&&this.options.values.length){
this.handles.each(function(i){
valPercent=(that.values(i) - that._valueMin()) /(that._valueMax() - that._valueMin()) * 100;
_set[ that.orientation==="horizontal" ? "left":"bottom" ]=valPercent + "%";
$(this).stop(1, 1)[ animate ? "animate":"css" ](_set, o.animate);
if(that.options.range===true){
if(that.orientation==="horizontal"){
if(i===0){
that.range.stop(1, 1)[ animate ? "animate":"css" ]({ left: valPercent + "%" }, o.animate);
}
if(i===1){
that.range[ animate ? "animate":"css" ]({ width:(valPercent - lastValPercent) + "%" }, { queue: false, duration: o.animate });
}}else{
if(i===0){
that.range.stop(1, 1)[ animate ? "animate":"css" ]({ bottom:(valPercent) + "%" }, o.animate);
}
if(i===1){
that.range[ animate ? "animate":"css" ]({ height:(valPercent - lastValPercent) + "%" }, { queue: false, duration: o.animate });
}}
}
lastValPercent=valPercent;
});
}else{
value=this.value();
valueMin=this._valueMin();
valueMax=this._valueMax();
valPercent=(valueMax!==valueMin) ?
(value - valueMin) /(valueMax - valueMin) * 100 :
0;
_set[ this.orientation==="horizontal" ? "left":"bottom" ]=valPercent + "%";
this.handle.stop(1, 1)[ animate ? "animate":"css" ](_set, o.animate);
if(oRange==="min"&&this.orientation==="horizontal"){
this.range.stop(1, 1)[ animate ? "animate":"css" ]({ width: valPercent + "%" }, o.animate);
}
if(oRange==="max"&&this.orientation==="horizontal"){
this.range[ animate ? "animate":"css" ]({ width:(100 - valPercent) + "%" }, { queue: false, duration: o.animate });
}
if(oRange==="min"&&this.orientation==="vertical"){
this.range.stop(1, 1)[ animate ? "animate":"css" ]({ height: valPercent + "%" }, o.animate);
}
if(oRange==="max"&&this.orientation==="vertical"){
this.range[ animate ? "animate":"css" ]({ height:(100 - valPercent) + "%" }, { queue: false, duration: o.animate });
}}
},
_handleEvents: {
keydown: function(event){
var allowed, curVal, newVal, step,
index=$(event.target).data("ui-slider-handle-index");
switch(event.keyCode){
case $.ui.keyCode.HOME:
case $.ui.keyCode.END:
case $.ui.keyCode.PAGE_UP:
case $.ui.keyCode.PAGE_DOWN:
case $.ui.keyCode.UP:
case $.ui.keyCode.RIGHT:
case $.ui.keyCode.DOWN:
case $.ui.keyCode.LEFT:
event.preventDefault();
if(!this._keySliding){
this._keySliding=true;
$(event.target).addClass("ui-state-active");
allowed=this._start(event, index);
if(allowed===false){
return;
}}
break;
}
step=this.options.step;
if(this.options.values&&this.options.values.length){
curVal=newVal=this.values(index);
}else{
curVal=newVal=this.value();
}
switch(event.keyCode){
case $.ui.keyCode.HOME:
newVal=this._valueMin();
break;
case $.ui.keyCode.END:
newVal=this._valueMax();
break;
case $.ui.keyCode.PAGE_UP:
newVal=this._trimAlignValue(curVal +(( this._valueMax() - this._valueMin()) / this.numPages)
);
break;
case $.ui.keyCode.PAGE_DOWN:
newVal=this._trimAlignValue(curVal -((this._valueMax() - this._valueMin()) / this.numPages));
break;
case $.ui.keyCode.UP:
case $.ui.keyCode.RIGHT:
if(curVal===this._valueMax()){
return;
}
newVal=this._trimAlignValue(curVal + step);
break;
case $.ui.keyCode.DOWN:
case $.ui.keyCode.LEFT:
if(curVal===this._valueMin()){
return;
}
newVal=this._trimAlignValue(curVal - step);
break;
}
this._slide(event, index, newVal);
},
keyup: function(event){
var index=$(event.target).data("ui-slider-handle-index");
if(this._keySliding){
this._keySliding=false;
this._stop(event, index);
this._change(event, index);
$(event.target).removeClass("ui-state-active");
}}
}});
function spinner_modifier(fn){
return function(){
var previous=this.element.val();
fn.apply(this, arguments);
this._refresh();
if(previous!==this.element.val()){
this._trigger("change");
}};}
var spinner=$.widget("ui.spinner", {
version: "1.11.4",
defaultElement: "<input>",
widgetEventPrefix: "spin",
options: {
culture: null,
icons: {
down: "ui-icon-triangle-1-s",
up: "ui-icon-triangle-1-n"
},
incremental: true,
max: null,
min: null,
numberFormat: null,
page: 10,
step: 1,
change: null,
spin: null,
start: null,
stop: null
},
_create: function(){
this._setOption("max", this.options.max);
this._setOption("min", this.options.min);
this._setOption("step", this.options.step);
if(this.value()!==""){
this._value(this.element.val(), true);
}
this._draw();
this._on(this._events);
this._refresh();
this._on(this.window, {
beforeunload: function(){
this.element.removeAttr("autocomplete");
}});
},
_getCreateOptions: function(){
var options={},
element=this.element;
$.each([ "min", "max", "step" ], function(i, option){
var value=element.attr(option);
if(value!==undefined&&value.length){
options[ option ]=value;
}});
return options;
},
_events: {
keydown: function(event){
if(this._start(event)&&this._keydown(event)){
event.preventDefault();
}},
keyup: "_stop",
focus: function(){
this.previous=this.element.val();
},
blur: function(event){
if(this.cancelBlur){
delete this.cancelBlur;
return;
}
this._stop();
this._refresh();
if(this.previous!==this.element.val()){
this._trigger("change", event);
}},
mousewheel: function(event, delta){
if(!delta){
return;
}
if(!this.spinning&&!this._start(event)){
return false;
}
this._spin((delta > 0 ? 1:-1) * this.options.step, event);
clearTimeout(this.mousewheelTimer);
this.mousewheelTimer=this._delay(function(){
if(this.spinning){
this._stop(event);
}}, 100);
event.preventDefault();
},
"mousedown .ui-spinner-button": function(event){
var previous;
previous=this.element[0]===this.document[0].activeElement ?
this.previous:this.element.val();
function checkFocus(){
var isActive=this.element[0]===this.document[0].activeElement;
if(!isActive){
this.element.focus();
this.previous=previous;
this._delay(function(){
this.previous=previous;
});
}}
event.preventDefault();
checkFocus.call(this);
this.cancelBlur=true;
this._delay(function(){
delete this.cancelBlur;
checkFocus.call(this);
});
if(this._start(event)===false){
return;
}
this._repeat(null, $(event.currentTarget).hasClass("ui-spinner-up") ? 1:-1, event);
},
"mouseup .ui-spinner-button": "_stop",
"mouseenter .ui-spinner-button": function(event){
if(!$(event.currentTarget).hasClass("ui-state-active")){
return;
}
if(this._start(event)===false){
return false;
}
this._repeat(null, $(event.currentTarget).hasClass("ui-spinner-up") ? 1:-1, event);
},
"mouseleave .ui-spinner-button": "_stop"
},
_draw: function(){
var uiSpinner=this.uiSpinner=this.element
.addClass("ui-spinner-input")
.attr("autocomplete", "off")
.wrap(this._uiSpinnerHtml())
.parent()
.append(this._buttonHtml());
this.element.attr("role", "spinbutton");
this.buttons=uiSpinner.find(".ui-spinner-button")
.attr("tabIndex", -1)
.button()
.removeClass("ui-corner-all");
if(this.buttons.height() > Math.ceil(uiSpinner.height() * 0.5) &&
uiSpinner.height() > 0){
uiSpinner.height(uiSpinner.height());
}
if(this.options.disabled){
this.disable();
}},
_keydown: function(event){
var options=this.options,
keyCode=$.ui.keyCode;
switch(event.keyCode){
case keyCode.UP:
this._repeat(null, 1, event);
return true;
case keyCode.DOWN:
this._repeat(null, -1, event);
return true;
case keyCode.PAGE_UP:
this._repeat(null, options.page, event);
return true;
case keyCode.PAGE_DOWN:
this._repeat(null, -options.page, event);
return true;
}
return false;
},
_uiSpinnerHtml: function(){
return "<span class='ui-spinner ui-widget ui-widget-content ui-corner-all'></span>";
},
_buttonHtml: function(){
return "" +
"<a class='ui-spinner-button ui-spinner-up ui-corner-tr'>" +
"<span class='ui-icon " + this.options.icons.up + "'>&#9650;</span>" +
"</a>" +
"<a class='ui-spinner-button ui-spinner-down ui-corner-br'>" +
"<span class='ui-icon " + this.options.icons.down + "'>&#9660;</span>" +
"</a>";
},
_start: function(event){
if(!this.spinning&&this._trigger("start", event)===false){
return false;
}
if(!this.counter){
this.counter=1;
}
this.spinning=true;
return true;
},
_repeat: function(i, steps, event){
i=i||500;
clearTimeout(this.timer);
this.timer=this._delay(function(){
this._repeat(40, steps, event);
}, i);
this._spin(steps * this.options.step, event);
},
_spin: function(step, event){
var value=this.value()||0;
if(!this.counter){
this.counter=1;
}
value=this._adjustValue(value + step * this._increment(this.counter));
if(!this.spinning||this._trigger("spin", event, { value: value })!==false){
this._value(value);
this.counter++;
}},
_increment: function(i){
var incremental=this.options.incremental;
if(incremental){
return $.isFunction(incremental) ?
incremental(i) :
Math.floor(i * i * i / 50000 - i * i / 500 + 17 * i / 200 + 1);
}
return 1;
},
_precision: function(){
var precision=this._precisionOf(this.options.step);
if(this.options.min!==null){
precision=Math.max(precision, this._precisionOf(this.options.min));
}
return precision;
},
_precisionOf: function(num){
var str=num.toString(),
decimal=str.indexOf(".");
return decimal===-1 ? 0:str.length - decimal - 1;
},
_adjustValue: function(value){
var base, aboveMin,
options=this.options;
base=options.min!==null ? options.min:0;
aboveMin=value - base;
aboveMin=Math.round(aboveMin / options.step) * options.step;
value=base + aboveMin;
value=parseFloat(value.toFixed(this._precision()));
if(options.max!==null&&value > options.max){
return options.max;
}
if(options.min!==null&&value < options.min){
return options.min;
}
return value;
},
_stop: function(event){
if(!this.spinning){
return;
}
clearTimeout(this.timer);
clearTimeout(this.mousewheelTimer);
this.counter=0;
this.spinning=false;
this._trigger("stop", event);
},
_setOption: function(key, value){
if(key==="culture"||key==="numberFormat"){
var prevValue=this._parse(this.element.val());
this.options[ key ]=value;
this.element.val(this._format(prevValue));
return;
}
if(key==="max"||key==="min"||key==="step"){
if(typeof value==="string"){
value=this._parse(value);
}}
if(key==="icons"){
this.buttons.first().find(".ui-icon")
.removeClass(this.options.icons.up)
.addClass(value.up);
this.buttons.last().find(".ui-icon")
.removeClass(this.options.icons.down)
.addClass(value.down);
}
this._super(key, value);
if(key==="disabled"){
this.widget().toggleClass("ui-state-disabled", !!value);
this.element.prop("disabled", !!value);
this.buttons.button(value ? "disable":"enable");
}},
_setOptions: spinner_modifier(function(options){
this._super(options);
}),
_parse: function(val){
if(typeof val==="string"&&val!==""){
val=window.Globalize&&this.options.numberFormat ?
Globalize.parseFloat(val, 10, this.options.culture):+val;
}
return val===""||isNaN(val) ? null:val;
},
_format: function(value){
if(value===""){
return "";
}
return window.Globalize&&this.options.numberFormat ?
Globalize.format(value, this.options.numberFormat, this.options.culture) :
value;
},
_refresh: function(){
this.element.attr({
"aria-valuemin": this.options.min,
"aria-valuemax": this.options.max,
"aria-valuenow": this._parse(this.element.val())
});
},
isValid: function(){
var value=this.value();
if(value===null){
return false;
}
return value===this._adjustValue(value);
},
_value: function(value, allowAny){
var parsed;
if(value!==""){
parsed=this._parse(value);
if(parsed!==null){
if(!allowAny){
parsed=this._adjustValue(parsed);
}
value=this._format(parsed);
}}
this.element.val(value);
this._refresh();
},
_destroy: function(){
this.element
.removeClass("ui-spinner-input")
.prop("disabled", false)
.removeAttr("autocomplete")
.removeAttr("role")
.removeAttr("aria-valuemin")
.removeAttr("aria-valuemax")
.removeAttr("aria-valuenow");
this.uiSpinner.replaceWith(this.element);
},
stepUp: spinner_modifier(function(steps){
this._stepUp(steps);
}),
_stepUp: function(steps){
if(this._start()){
this._spin((steps||1) * this.options.step);
this._stop();
}},
stepDown: spinner_modifier(function(steps){
this._stepDown(steps);
}),
_stepDown: function(steps){
if(this._start()){
this._spin((steps||1) * -this.options.step);
this._stop();
}},
pageUp: spinner_modifier(function(pages){
this._stepUp((pages||1) * this.options.page);
}),
pageDown: spinner_modifier(function(pages){
this._stepDown((pages||1) * this.options.page);
}),
value: function(newVal){
if(!arguments.length){
return this._parse(this.element.val());
}
spinner_modifier(this._value).call(this, newVal);
},
widget: function(){
return this.uiSpinner;
}});
var tabs=$.widget("ui.tabs", {
version: "1.11.4",
delay: 300,
options: {
active: null,
collapsible: false,
event: "click",
heightStyle: "content",
hide: null,
show: null,
activate: null,
beforeActivate: null,
beforeLoad: null,
load: null
},
_isLocal: (function(){
var rhash=/#.*$/;
return function(anchor){
var anchorUrl, locationUrl;
anchor=anchor.cloneNode(false);
anchorUrl=anchor.href.replace(rhash, "");
locationUrl=location.href.replace(rhash, "");
try {
anchorUrl=decodeURIComponent(anchorUrl);
} catch(error){}
try {
locationUrl=decodeURIComponent(locationUrl);
} catch(error){}
return anchor.hash.length > 1&&anchorUrl===locationUrl;
};})(),
_create: function(){
var that=this,
options=this.options;
this.running=false;
this.element
.addClass("ui-tabs ui-widget ui-widget-content ui-corner-all")
.toggleClass("ui-tabs-collapsible", options.collapsible);
this._processTabs();
options.active=this._initialActive();
if($.isArray(options.disabled)){
options.disabled=$.unique(options.disabled.concat($.map(this.tabs.filter(".ui-state-disabled"), function(li){
return that.tabs.index(li);
})
)).sort();
}
if(this.options.active!==false&&this.anchors.length){
this.active=this._findActive(options.active);
}else{
this.active=$();
}
this._refresh();
if(this.active.length){
this.load(options.active);
}},
_initialActive: function(){
var active=this.options.active,
collapsible=this.options.collapsible,
locationHash=location.hash.substring(1);
if(active===null){
if(locationHash){
this.tabs.each(function(i, tab){
if($(tab).attr("aria-controls")===locationHash){
active=i;
return false;
}});
}
if(active===null){
active=this.tabs.index(this.tabs.filter(".ui-tabs-active"));
}
if(active===null||active===-1){
active=this.tabs.length ? 0:false;
}}
if(active!==false){
active=this.tabs.index(this.tabs.eq(active));
if(active===-1){
active=collapsible ? false:0;
}}
if(!collapsible&&active===false&&this.anchors.length){
active=0;
}
return active;
},
_getCreateEventData: function(){
return {
tab: this.active,
panel: !this.active.length ? $():this._getPanelForTab(this.active)
};},
_tabKeydown: function(event){
var focusedTab=$(this.document[0].activeElement).closest("li"),
selectedIndex=this.tabs.index(focusedTab),
goingForward=true;
if(this._handlePageNav(event)){
return;
}
switch(event.keyCode){
case $.ui.keyCode.RIGHT:
case $.ui.keyCode.DOWN:
selectedIndex++;
break;
case $.ui.keyCode.UP:
case $.ui.keyCode.LEFT:
goingForward=false;
selectedIndex--;
break;
case $.ui.keyCode.END:
selectedIndex=this.anchors.length - 1;
break;
case $.ui.keyCode.HOME:
selectedIndex=0;
break;
case $.ui.keyCode.SPACE:
event.preventDefault();
clearTimeout(this.activating);
this._activate(selectedIndex);
return;
case $.ui.keyCode.ENTER:
event.preventDefault();
clearTimeout(this.activating);
this._activate(selectedIndex===this.options.active ? false:selectedIndex);
return;
default:
return;
}
event.preventDefault();
clearTimeout(this.activating);
selectedIndex=this._focusNextTab(selectedIndex, goingForward);
if(!event.ctrlKey&&!event.metaKey){
focusedTab.attr("aria-selected", "false");
this.tabs.eq(selectedIndex).attr("aria-selected", "true");
this.activating=this._delay(function(){
this.option("active", selectedIndex);
}, this.delay);
}},
_panelKeydown: function(event){
if(this._handlePageNav(event)){
return;
}
if(event.ctrlKey&&event.keyCode===$.ui.keyCode.UP){
event.preventDefault();
this.active.focus();
}},
_handlePageNav: function(event){
if(event.altKey&&event.keyCode===$.ui.keyCode.PAGE_UP){
this._activate(this._focusNextTab(this.options.active - 1, false));
return true;
}
if(event.altKey&&event.keyCode===$.ui.keyCode.PAGE_DOWN){
this._activate(this._focusNextTab(this.options.active + 1, true));
return true;
}},
_findNextTab: function(index, goingForward){
var lastTabIndex=this.tabs.length - 1;
function constrain(){
if(index > lastTabIndex){
index=0;
}
if(index < 0){
index=lastTabIndex;
}
return index;
}
while($.inArray(constrain(), this.options.disabled)!==-1){
index=goingForward ? index + 1:index - 1;
}
return index;
},
_focusNextTab: function(index, goingForward){
index=this._findNextTab(index, goingForward);
this.tabs.eq(index).focus();
return index;
},
_setOption: function(key, value){
if(key==="active"){
this._activate(value);
return;
}
if(key==="disabled"){
this._setupDisabled(value);
return;
}
this._super(key, value);
if(key==="collapsible"){
this.element.toggleClass("ui-tabs-collapsible", value);
if(!value&&this.options.active===false){
this._activate(0);
}}
if(key==="event"){
this._setupEvents(value);
}
if(key==="heightStyle"){
this._setupHeightStyle(value);
}},
_sanitizeSelector: function(hash){
return hash ? hash.replace(/[!"$%&'()*+,.\/:;<=>?@\[\]\^`{|}~]/g, "\\$&"):"";
},
refresh: function(){
var options=this.options,
lis=this.tablist.children(":has(a[href])");
options.disabled=$.map(lis.filter(".ui-state-disabled"), function(tab){
return lis.index(tab);
});
this._processTabs();
if(options.active===false||!this.anchors.length){
options.active=false;
this.active=$();
}else if(this.active.length&&!$.contains(this.tablist[ 0 ], this.active[ 0 ])){
if(this.tabs.length===options.disabled.length){
options.active=false;
this.active=$();
}else{
this._activate(this._findNextTab(Math.max(0, options.active - 1), false));
}}else{
options.active=this.tabs.index(this.active);
}
this._refresh();
},
_refresh: function(){
this._setupDisabled(this.options.disabled);
this._setupEvents(this.options.event);
this._setupHeightStyle(this.options.heightStyle);
this.tabs.not(this.active).attr({
"aria-selected": "false",
"aria-expanded": "false",
tabIndex: -1
});
this.panels.not(this._getPanelForTab(this.active))
.hide()
.attr({
"aria-hidden": "true"
});
if(!this.active.length){
this.tabs.eq(0).attr("tabIndex", 0);
}else{
this.active
.addClass("ui-tabs-active ui-state-active")
.attr({
"aria-selected": "true",
"aria-expanded": "true",
tabIndex: 0
});
this._getPanelForTab(this.active)
.show()
.attr({
"aria-hidden": "false"
});
}},
_processTabs: function(){
var that=this,
prevTabs=this.tabs,
prevAnchors=this.anchors,
prevPanels=this.panels;
this.tablist=this._getList()
.addClass("ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all")
.attr("role", "tablist")
.delegate("> li", "mousedown" + this.eventNamespace, function(event){
if($(this).is(".ui-state-disabled")){
event.preventDefault();
}})
.delegate(".ui-tabs-anchor", "focus" + this.eventNamespace, function(){
if($(this).closest("li").is(".ui-state-disabled")){
this.blur();
}});
this.tabs=this.tablist.find("> li:has(a[href])")
.addClass("ui-state-default ui-corner-top")
.attr({
role: "tab",
tabIndex: -1
});
this.anchors=this.tabs.map(function(){
return $("a", this)[ 0 ];
})
.addClass("ui-tabs-anchor")
.attr({
role: "presentation",
tabIndex: -1
});
this.panels=$();
this.anchors.each(function(i, anchor){
var selector, panel, panelId,
anchorId=$(anchor).uniqueId().attr("id"),
tab=$(anchor).closest("li"),
originalAriaControls=tab.attr("aria-controls");
if(that._isLocal(anchor)){
selector=anchor.hash;
panelId=selector.substring(1);
panel=that.element.find(that._sanitizeSelector(selector));
}else{
panelId=tab.attr("aria-controls")||$({}).uniqueId()[ 0 ].id;
selector="#" + panelId;
panel=that.element.find(selector);
if(!panel.length){
panel=that._createPanel(panelId);
panel.insertAfter(that.panels[ i - 1 ]||that.tablist);
}
panel.attr("aria-live", "polite");
}
if(panel.length){
that.panels=that.panels.add(panel);
}
if(originalAriaControls){
tab.data("ui-tabs-aria-controls", originalAriaControls);
}
tab.attr({
"aria-controls": panelId,
"aria-labelledby": anchorId
});
panel.attr("aria-labelledby", anchorId);
});
this.panels
.addClass("ui-tabs-panel ui-widget-content ui-corner-bottom")
.attr("role", "tabpanel");
if(prevTabs){
this._off(prevTabs.not(this.tabs));
this._off(prevAnchors.not(this.anchors));
this._off(prevPanels.not(this.panels));
}},
_getList: function(){
return this.tablist||this.element.find("ol,ul").eq(0);
},
_createPanel: function(id){
return $("<div>")
.attr("id", id)
.addClass("ui-tabs-panel ui-widget-content ui-corner-bottom")
.data("ui-tabs-destroy", true);
},
_setupDisabled: function(disabled){
if($.isArray(disabled)){
if(!disabled.length){
disabled=false;
}else if(disabled.length===this.anchors.length){
disabled=true;
}}
for(var i=0, li;(li=this.tabs[ i ]); i++){
if(disabled===true||$.inArray(i, disabled)!==-1){
$(li)
.addClass("ui-state-disabled")
.attr("aria-disabled", "true");
}else{
$(li)
.removeClass("ui-state-disabled")
.removeAttr("aria-disabled");
}}
this.options.disabled=disabled;
},
_setupEvents: function(event){
var events={};
if(event){
$.each(event.split(" "), function(index, eventName){
events[ eventName ]="_eventHandler";
});
}
this._off(this.anchors.add(this.tabs).add(this.panels));
this._on(true, this.anchors, {
click: function(event){
event.preventDefault();
}});
this._on(this.anchors, events);
this._on(this.tabs, { keydown: "_tabKeydown" });
this._on(this.panels, { keydown: "_panelKeydown" });
this._focusable(this.tabs);
this._hoverable(this.tabs);
},
_setupHeightStyle: function(heightStyle){
var maxHeight,
parent=this.element.parent();
if(heightStyle==="fill"){
maxHeight=parent.height();
maxHeight -=this.element.outerHeight() - this.element.height();
this.element.siblings(":visible").each(function(){
var elem=$(this),
position=elem.css("position");
if(position==="absolute"||position==="fixed"){
return;
}
maxHeight -=elem.outerHeight(true);
});
this.element.children().not(this.panels).each(function(){
maxHeight -=$(this).outerHeight(true);
});
this.panels.each(function(){
$(this).height(Math.max(0, maxHeight -
$(this).innerHeight() + $(this).height()));
})
.css("overflow", "auto");
}else if(heightStyle==="auto"){
maxHeight=0;
this.panels.each(function(){
maxHeight=Math.max(maxHeight, $(this).height("").height());
}).height(maxHeight);
}},
_eventHandler: function(event){
var options=this.options,
active=this.active,
anchor=$(event.currentTarget),
tab=anchor.closest("li"),
clickedIsActive=tab[ 0 ]===active[ 0 ],
collapsing=clickedIsActive&&options.collapsible,
toShow=collapsing ? $():this._getPanelForTab(tab),
toHide = !active.length ? $():this._getPanelForTab(active),
eventData={
oldTab: active,
oldPanel: toHide,
newTab: collapsing ? $():tab,
newPanel: toShow
};
event.preventDefault();
if(tab.hasClass("ui-state-disabled") ||
tab.hasClass("ui-tabs-loading") ||
this.running ||
(clickedIsActive&&!options.collapsible) ||
(this._trigger("beforeActivate", event, eventData)===false)){
return;
}
options.active=collapsing ? false:this.tabs.index(tab);
this.active=clickedIsActive ? $():tab;
if(this.xhr){
this.xhr.abort();
}
if(!toHide.length&&!toShow.length){
$.error("jQuery UI Tabs: Mismatching fragment identifier.");
}
if(toShow.length){
this.load(this.tabs.index(tab), event);
}
this._toggle(event, eventData);
},
_toggle: function(event, eventData){
var that=this,
toShow=eventData.newPanel,
toHide=eventData.oldPanel;
this.running=true;
function complete(){
that.running=false;
that._trigger("activate", event, eventData);
}
function show(){
eventData.newTab.closest("li").addClass("ui-tabs-active ui-state-active");
if(toShow.length&&that.options.show){
that._show(toShow, that.options.show, complete);
}else{
toShow.show();
complete();
}}
if(toHide.length&&this.options.hide){
this._hide(toHide, this.options.hide, function(){
eventData.oldTab.closest("li").removeClass("ui-tabs-active ui-state-active");
show();
});
}else{
eventData.oldTab.closest("li").removeClass("ui-tabs-active ui-state-active");
toHide.hide();
show();
}
toHide.attr("aria-hidden", "true");
eventData.oldTab.attr({
"aria-selected": "false",
"aria-expanded": "false"
});
if(toShow.length&&toHide.length){
eventData.oldTab.attr("tabIndex", -1);
}else if(toShow.length){
this.tabs.filter(function(){
return $(this).attr("tabIndex")===0;
})
.attr("tabIndex", -1);
}
toShow.attr("aria-hidden", "false");
eventData.newTab.attr({
"aria-selected": "true",
"aria-expanded": "true",
tabIndex: 0
});
},
_activate: function(index){
var anchor,
active=this._findActive(index);
if(active[ 0 ]===this.active[ 0 ]){
return;
}
if(!active.length){
active=this.active;
}
anchor=active.find(".ui-tabs-anchor")[ 0 ];
this._eventHandler({
target: anchor,
currentTarget: anchor,
preventDefault: $.noop
});
},
_findActive: function(index){
return index===false ? $():this.tabs.eq(index);
},
_getIndex: function(index){
if(typeof index==="string"){
index=this.anchors.index(this.anchors.filter("[href$='" + index + "']"));
}
return index;
},
_destroy: function(){
if(this.xhr){
this.xhr.abort();
}
this.element.removeClass("ui-tabs ui-widget ui-widget-content ui-corner-all ui-tabs-collapsible");
this.tablist
.removeClass("ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all")
.removeAttr("role");
this.anchors
.removeClass("ui-tabs-anchor")
.removeAttr("role")
.removeAttr("tabIndex")
.removeUniqueId();
this.tablist.unbind(this.eventNamespace);
this.tabs.add(this.panels).each(function(){
if($.data(this, "ui-tabs-destroy")){
$(this).remove();
}else{
$(this)
.removeClass("ui-state-default ui-state-active ui-state-disabled " +
"ui-corner-top ui-corner-bottom ui-widget-content ui-tabs-active ui-tabs-panel")
.removeAttr("tabIndex")
.removeAttr("aria-live")
.removeAttr("aria-busy")
.removeAttr("aria-selected")
.removeAttr("aria-labelledby")
.removeAttr("aria-hidden")
.removeAttr("aria-expanded")
.removeAttr("role");
}});
this.tabs.each(function(){
var li=$(this),
prev=li.data("ui-tabs-aria-controls");
if(prev){
li
.attr("aria-controls", prev)
.removeData("ui-tabs-aria-controls");
}else{
li.removeAttr("aria-controls");
}});
this.panels.show();
if(this.options.heightStyle!=="content"){
this.panels.css("height", "");
}},
enable: function(index){
var disabled=this.options.disabled;
if(disabled===false){
return;
}
if(index===undefined){
disabled=false;
}else{
index=this._getIndex(index);
if($.isArray(disabled)){
disabled=$.map(disabled, function(num){
return num!==index ? num:null;
});
}else{
disabled=$.map(this.tabs, function(li, num){
return num!==index ? num:null;
});
}}
this._setupDisabled(disabled);
},
disable: function(index){
var disabled=this.options.disabled;
if(disabled===true){
return;
}
if(index===undefined){
disabled=true;
}else{
index=this._getIndex(index);
if($.inArray(index, disabled)!==-1){
return;
}
if($.isArray(disabled)){
disabled=$.merge([ index ], disabled).sort();
}else{
disabled=[ index ];
}}
this._setupDisabled(disabled);
},
load: function(index, event){
index=this._getIndex(index);
var that=this,
tab=this.tabs.eq(index),
anchor=tab.find(".ui-tabs-anchor"),
panel=this._getPanelForTab(tab),
eventData={
tab: tab,
panel: panel
},
complete=function(jqXHR, status){
if(status==="abort"){
that.panels.stop(false, true);
}
tab.removeClass("ui-tabs-loading");
panel.removeAttr("aria-busy");
if(jqXHR===that.xhr){
delete that.xhr;
}};
if(this._isLocal(anchor[ 0 ])){
return;
}
this.xhr=$.ajax(this._ajaxSettings(anchor, event, eventData));
if(this.xhr&&this.xhr.statusText!=="canceled"){
tab.addClass("ui-tabs-loading");
panel.attr("aria-busy", "true");
this.xhr
.done(function(response, status, jqXHR){
setTimeout(function(){
panel.html(response);
that._trigger("load", event, eventData);
complete(jqXHR, status);
}, 1);
})
.fail(function(jqXHR, status){
setTimeout(function(){
complete(jqXHR, status);
}, 1);
});
}},
_ajaxSettings: function(anchor, event, eventData){
var that=this;
return {
url: anchor.attr("href"),
beforeSend: function(jqXHR, settings){
return that._trigger("beforeLoad", event,
$.extend({ jqXHR: jqXHR, ajaxSettings: settings }, eventData));
}};},
_getPanelForTab: function(tab){
var id=$(tab).attr("aria-controls");
return this.element.find(this._sanitizeSelector("#" + id));
}});
var tooltip=$.widget("ui.tooltip", {
version: "1.11.4",
options: {
content: function(){
var title=$(this).attr("title")||"";
return $("<a>").text(title).html();
},
hide: true,
items: "[title]:not([disabled])",
position: {
my: "left top+15",
at: "left bottom",
collision: "flipfit flip"
},
show: true,
tooltipClass: null,
track: false,
close: null,
open: null
},
_addDescribedBy: function(elem, id){
var describedby=(elem.attr("aria-describedby")||"").split(/\s+/);
describedby.push(id);
elem
.data("ui-tooltip-id", id)
.attr("aria-describedby", $.trim(describedby.join(" ")));
},
_removeDescribedBy: function(elem){
var id=elem.data("ui-tooltip-id"),
describedby=(elem.attr("aria-describedby")||"").split(/\s+/),
index=$.inArray(id, describedby);
if(index!==-1){
describedby.splice(index, 1);
}
elem.removeData("ui-tooltip-id");
describedby=$.trim(describedby.join(" "));
if(describedby){
elem.attr("aria-describedby", describedby);
}else{
elem.removeAttr("aria-describedby");
}},
_create: function(){
this._on({
mouseover: "open",
focusin: "open"
});
this.tooltips={};
this.parents={};
if(this.options.disabled){
this._disable();
}
this.liveRegion=$("<div>")
.attr({
role: "log",
"aria-live": "assertive",
"aria-relevant": "additions"
})
.addClass("ui-helper-hidden-accessible")
.appendTo(this.document[ 0 ].body);
},
_setOption: function(key, value){
var that=this;
if(key==="disabled"){
this[ value ? "_disable":"_enable" ]();
this.options[ key ]=value;
return;
}
this._super(key, value);
if(key==="content"){
$.each(this.tooltips, function(id, tooltipData){
that._updateContent(tooltipData.element);
});
}},
_disable: function(){
var that=this;
$.each(this.tooltips, function(id, tooltipData){
var event=$.Event("blur");
event.target=event.currentTarget=tooltipData.element[ 0 ];
that.close(event, true);
});
this.element.find(this.options.items).addBack().each(function(){
var element=$(this);
if(element.is("[title]")){
element
.data("ui-tooltip-title", element.attr("title"))
.removeAttr("title");
}});
},
_enable: function(){
this.element.find(this.options.items).addBack().each(function(){
var element=$(this);
if(element.data("ui-tooltip-title")){
element.attr("title", element.data("ui-tooltip-title"));
}});
},
open: function(event){
var that=this,
target=$(event ? event.target:this.element)
.closest(this.options.items);
if(!target.length||target.data("ui-tooltip-id")){
return;
}
if(target.attr("title")){
target.data("ui-tooltip-title", target.attr("title"));
}
target.data("ui-tooltip-open", true);
if(event&&event.type==="mouseover"){
target.parents().each(function(){
var parent=$(this),
blurEvent;
if(parent.data("ui-tooltip-open")){
blurEvent=$.Event("blur");
blurEvent.target=blurEvent.currentTarget=this;
that.close(blurEvent, true);
}
if(parent.attr("title")){
parent.uniqueId();
that.parents[ this.id ]={
element: this,
title: parent.attr("title")
};
parent.attr("title", "");
}});
}
this._registerCloseHandlers(event, target);
this._updateContent(target, event);
},
_updateContent: function(target, event){
var content,
contentOption=this.options.content,
that=this,
eventType=event ? event.type:null;
if(typeof contentOption==="string"){
return this._open(event, target, contentOption);
}
content=contentOption.call(target[0], function(response){
that._delay(function(){
if(!target.data("ui-tooltip-open")){
return;
}
if(event){
event.type=eventType;
}
this._open(event, target, response);
});
});
if(content){
this._open(event, target, content);
}},
_open: function(event, target, content){
var tooltipData, tooltip, delayedShow, a11yContent,
positionOption=$.extend({}, this.options.position);
if(!content){
return;
}
tooltipData=this._find(target);
if(tooltipData){
tooltipData.tooltip.find(".ui-tooltip-content").html(content);
return;
}
if(target.is("[title]")){
if(event&&event.type==="mouseover"){
target.attr("title", "");
}else{
target.removeAttr("title");
}}
tooltipData=this._tooltip(target);
tooltip=tooltipData.tooltip;
this._addDescribedBy(target, tooltip.attr("id"));
tooltip.find(".ui-tooltip-content").html(content);
this.liveRegion.children().hide();
if(content.clone){
a11yContent=content.clone();
a11yContent.removeAttr("id").find("[id]").removeAttr("id");
}else{
a11yContent=content;
}
$("<div>").html(a11yContent).appendTo(this.liveRegion);
function position(event){
positionOption.of=event;
if(tooltip.is(":hidden")){
return;
}
tooltip.position(positionOption);
}
if(this.options.track&&event&&/^mouse/.test(event.type)){
this._on(this.document, {
mousemove: position
});
position(event);
}else{
tooltip.position($.extend({
of: target
}, this.options.position));
}
tooltip.hide();
this._show(tooltip, this.options.show);
if(this.options.show&&this.options.show.delay){
delayedShow=this.delayedShow=setInterval(function(){
if(tooltip.is(":visible")){
position(positionOption.of);
clearInterval(delayedShow);
}}, $.fx.interval);
}
this._trigger("open", event, { tooltip: tooltip });
},
_registerCloseHandlers: function(event, target){
var events={
keyup: function(event){
if(event.keyCode===$.ui.keyCode.ESCAPE){
var fakeEvent=$.Event(event);
fakeEvent.currentTarget=target[0];
this.close(fakeEvent, true);
}}
};
if(target[ 0 ]!==this.element[ 0 ]){
events.remove=function(){
this._removeTooltip(this._find(target).tooltip);
};}
if(!event||event.type==="mouseover"){
events.mouseleave="close";
}
if(!event||event.type==="focusin"){
events.focusout="close";
}
this._on(true, target, events);
},
close: function(event){
var tooltip,
that=this,
target=$(event ? event.currentTarget:this.element),
tooltipData=this._find(target);
if(!tooltipData){
target.removeData("ui-tooltip-open");
return;
}
tooltip=tooltipData.tooltip;
if(tooltipData.closing){
return;
}
clearInterval(this.delayedShow);
if(target.data("ui-tooltip-title")&&!target.attr("title")){
target.attr("title", target.data("ui-tooltip-title"));
}
this._removeDescribedBy(target);
tooltipData.hiding=true;
tooltip.stop(true);
this._hide(tooltip, this.options.hide, function(){
that._removeTooltip($(this));
});
target.removeData("ui-tooltip-open");
this._off(target, "mouseleave focusout keyup");
if(target[ 0 ]!==this.element[ 0 ]){
this._off(target, "remove");
}
this._off(this.document, "mousemove");
if(event&&event.type==="mouseleave"){
$.each(this.parents, function(id, parent){
$(parent.element).attr("title", parent.title);
delete that.parents[ id ];
});
}
tooltipData.closing=true;
this._trigger("close", event, { tooltip: tooltip });
if(!tooltipData.hiding){
tooltipData.closing=false;
}},
_tooltip: function(element){
var tooltip=$("<div>")
.attr("role", "tooltip")
.addClass("ui-tooltip ui-widget ui-corner-all ui-widget-content " +
(this.options.tooltipClass||"")),
id=tooltip.uniqueId().attr("id");
$("<div>")
.addClass("ui-tooltip-content")
.appendTo(tooltip);
tooltip.appendTo(this.document[0].body);
return this.tooltips[ id ]={
element: element,
tooltip: tooltip
};},
_find: function(target){
var id=target.data("ui-tooltip-id");
return id ? this.tooltips[ id ]:null;
},
_removeTooltip: function(tooltip){
tooltip.remove();
delete this.tooltips[ tooltip.attr("id") ];
},
_destroy: function(){
var that=this;
$.each(this.tooltips, function(id, tooltipData){
var event=$.Event("blur"),
element=tooltipData.element;
event.target=event.currentTarget=element[ 0 ];
that.close(event, true);
$("#" + id).remove();
if(element.data("ui-tooltip-title")){
if(!element.attr("title")){
element.attr("title", element.data("ui-tooltip-title"));
}
element.removeData("ui-tooltip-title");
}});
this.liveRegion.remove();
}});
var dataSpace="ui-effects-",
jQuery=$;
$.effects={
effect: {}};
(function(jQuery, undefined){
var stepHooks="backgroundColor borderBottomColor borderLeftColor borderRightColor borderTopColor color columnRuleColor outlineColor textDecorationColor textEmphasisColor",
rplusequals=/^([\-+])=\s*(\d+\.?\d*)/,
stringParsers=[ {
re: /rgba?\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*(?:,\s*(\d?(?:\.\d+)?)\s*)?\)/,
parse: function(execResult){
return [
execResult[ 1 ],
execResult[ 2 ],
execResult[ 3 ],
execResult[ 4 ]
];
}}, {
re: /rgba?\(\s*(\d+(?:\.\d+)?)\%\s*,\s*(\d+(?:\.\d+)?)\%\s*,\s*(\d+(?:\.\d+)?)\%\s*(?:,\s*(\d?(?:\.\d+)?)\s*)?\)/,
parse: function(execResult){
return [
execResult[ 1 ] * 2.55,
execResult[ 2 ] * 2.55,
execResult[ 3 ] * 2.55,
execResult[ 4 ]
];
}}, {
re: /#([a-f0-9]{2})([a-f0-9]{2})([a-f0-9]{2})/,
parse: function(execResult){
return [
parseInt(execResult[ 1 ], 16),
parseInt(execResult[ 2 ], 16),
parseInt(execResult[ 3 ], 16)
];
}}, {
re: /#([a-f0-9])([a-f0-9])([a-f0-9])/,
parse: function(execResult){
return [
parseInt(execResult[ 1 ] + execResult[ 1 ], 16),
parseInt(execResult[ 2 ] + execResult[ 2 ], 16),
parseInt(execResult[ 3 ] + execResult[ 3 ], 16)
];
}}, {
re: /hsla?\(\s*(\d+(?:\.\d+)?)\s*,\s*(\d+(?:\.\d+)?)\%\s*,\s*(\d+(?:\.\d+)?)\%\s*(?:,\s*(\d?(?:\.\d+)?)\s*)?\)/,
space: "hsla",
parse: function(execResult){
return [
execResult[ 1 ],
execResult[ 2 ] / 100,
execResult[ 3 ] / 100,
execResult[ 4 ]
];
}} ],
color=jQuery.Color=function(color, green, blue, alpha){
return new jQuery.Color.fn.parse(color, green, blue, alpha);
},
spaces={
rgba: {
props: {
red: {
idx: 0,
type: "byte"
},
green: {
idx: 1,
type: "byte"
},
blue: {
idx: 2,
type: "byte"
}}
},
hsla: {
props: {
hue: {
idx: 0,
type: "degrees"
},
saturation: {
idx: 1,
type: "percent"
},
lightness: {
idx: 2,
type: "percent"
}}
}},
propTypes={
"byte": {
floor: true,
max: 255
},
"percent": {
max: 1
},
"degrees": {
mod: 360,
floor: true
}},
support=color.support={},
supportElem=jQuery("<p>")[ 0 ],
colors,
each=jQuery.each;
supportElem.style.cssText="background-color:rgba(1,1,1,.5)";
support.rgba=supportElem.style.backgroundColor.indexOf("rgba") > -1;
each(spaces, function(spaceName, space){
space.cache="_" + spaceName;
space.props.alpha={
idx: 3,
type: "percent",
def: 1
};});
function clamp(value, prop, allowEmpty){
var type=propTypes[ prop.type ]||{};
if(value==null){
return (allowEmpty||!prop.def) ? null:prop.def;
}
value=type.floor ? ~~value:parseFloat(value);
if(isNaN(value)){
return prop.def;
}
if(type.mod){
return (value + type.mod) % type.mod;
}
return 0 > value ? 0:type.max < value ? type.max:value;
}
function stringParse(string){
var inst=color(),
rgba=inst._rgba=[];
string=string.toLowerCase();
each(stringParsers, function(i, parser){
var parsed,
match=parser.re.exec(string),
values=match&&parser.parse(match),
spaceName=parser.space||"rgba";
if(values){
parsed=inst[ spaceName ](values);
inst[ spaces[ spaceName ].cache ]=parsed[ spaces[ spaceName ].cache ];
rgba=inst._rgba=parsed._rgba;
return false;
}});
if(rgba.length){
if(rgba.join()==="0,0,0,0"){
jQuery.extend(rgba, colors.transparent);
}
return inst;
}
return colors[ string ];
}
color.fn=jQuery.extend(color.prototype, {
parse: function(red, green, blue, alpha){
if(red===undefined){
this._rgba=[ null, null, null, null ];
return this;
}
if(red.jquery||red.nodeType){
red=jQuery(red).css(green);
green=undefined;
}
var inst=this,
type=jQuery.type(red),
rgba=this._rgba=[];
if(green!==undefined){
red=[ red, green, blue, alpha ];
type="array";
}
if(type==="string"){
return this.parse(stringParse(red)||colors._default);
}
if(type==="array"){
each(spaces.rgba.props, function(key, prop){
rgba[ prop.idx ]=clamp(red[ prop.idx ], prop);
});
return this;
}
if(type==="object"){
if(red instanceof color){
each(spaces, function(spaceName, space){
if(red[ space.cache ]){
inst[ space.cache ]=red[ space.cache ].slice();
}});
}else{
each(spaces, function(spaceName, space){
var cache=space.cache;
each(space.props, function(key, prop){
if(!inst[ cache ]&&space.to){
if(key==="alpha"||red[ key ]==null){
return;
}
inst[ cache ]=space.to(inst._rgba);
}
inst[ cache ][ prop.idx ]=clamp(red[ key ], prop, true);
});
if(inst[ cache ]&&jQuery.inArray(null, inst[ cache ].slice(0, 3)) < 0){
inst[ cache ][ 3 ]=1;
if(space.from){
inst._rgba=space.from(inst[ cache ]);
}}
});
}
return this;
}},
is: function(compare){
var is=color(compare),
same=true,
inst=this;
each(spaces, function(_, space){
var localCache,
isCache=is[ space.cache ];
if(isCache){
localCache=inst[ space.cache ]||space.to&&space.to(inst._rgba)||[];
each(space.props, function(_, prop){
if(isCache[ prop.idx ]!=null){
same=(isCache[ prop.idx ]===localCache[ prop.idx ]);
return same;
}});
}
return same;
});
return same;
},
_space: function(){
var used=[],
inst=this;
each(spaces, function(spaceName, space){
if(inst[ space.cache ]){
used.push(spaceName);
}});
return used.pop();
},
transition: function(other, distance){
var end=color(other),
spaceName=end._space(),
space=spaces[ spaceName ],
startColor=this.alpha()===0 ? color("transparent"):this,
start=startColor[ space.cache ]||space.to(startColor._rgba),
result=start.slice();
end=end[ space.cache ];
each(space.props, function(key, prop){
var index=prop.idx,
startValue=start[ index ],
endValue=end[ index ],
type=propTypes[ prop.type ]||{};
if(endValue===null){
return;
}
if(startValue===null){
result[ index ]=endValue;
}else{
if(type.mod){
if(endValue - startValue > type.mod / 2){
startValue +=type.mod;
}else if(startValue - endValue > type.mod / 2){
startValue -=type.mod;
}}
result[ index ]=clamp(( endValue - startValue) * distance + startValue, prop);
}});
return this[ spaceName ](result);
},
blend: function(opaque){
if(this._rgba[ 3 ]===1){
return this;
}
var rgb=this._rgba.slice(),
a=rgb.pop(),
blend=color(opaque)._rgba;
return color(jQuery.map(rgb, function(v, i){
return(1 - a) * blend[ i ] + a * v;
}));
},
toRgbaString: function(){
var prefix="rgba(",
rgba=jQuery.map(this._rgba, function(v, i){
return v==null ?(i > 2 ? 1:0):v;
});
if(rgba[ 3 ]===1){
rgba.pop();
prefix="rgb(";
}
return prefix + rgba.join() + ")";
},
toHslaString: function(){
var prefix="hsla(",
hsla=jQuery.map(this.hsla(), function(v, i){
if(v==null){
v=i > 2 ? 1:0;
}
if(i&&i < 3){
v=Math.round(v * 100) + "%";
}
return v;
});
if(hsla[ 3 ]===1){
hsla.pop();
prefix="hsl(";
}
return prefix + hsla.join() + ")";
},
toHexString: function(includeAlpha){
var rgba=this._rgba.slice(),
alpha=rgba.pop();
if(includeAlpha){
rgba.push(~~(alpha * 255));
}
return "#" + jQuery.map(rgba, function(v){
v=(v||0).toString(16);
return v.length===1 ? "0" + v:v;
}).join("");
},
toString: function(){
return this._rgba[ 3 ]===0 ? "transparent":this.toRgbaString();
}});
color.fn.parse.prototype=color.fn;
function hue2rgb(p, q, h){
h=(h + 1) % 1;
if(h * 6 < 1){
return p +(q - p) * h * 6;
}
if(h * 2 < 1){
return q;
}
if(h * 3 < 2){
return p +(q - p) *(( 2 / 3) - h) * 6;
}
return p;
}
spaces.hsla.to=function(rgba){
if(rgba[ 0 ]==null||rgba[ 1 ]==null||rgba[ 2 ]==null){
return [ null, null, null, rgba[ 3 ] ];
}
var r=rgba[ 0 ] / 255,
g=rgba[ 1 ] / 255,
b=rgba[ 2 ] / 255,
a=rgba[ 3 ],
max=Math.max(r, g, b),
min=Math.min(r, g, b),
diff=max - min,
add=max + min,
l=add * 0.5,
h, s;
if(min===max){
h=0;
}else if(r===max){
h=(60 *(g - b) / diff) + 360;
}else if(g===max){
h=(60 *(b - r) / diff) + 120;
}else{
h=(60 *(r - g) / diff) + 240;
}
if(diff===0){
s=0;
}else if(l <=0.5){
s=diff / add;
}else{
s=diff /(2 - add);
}
return [ Math.round(h) % 360, s, l, a==null ? 1:a ];
};
spaces.hsla.from=function(hsla){
if(hsla[ 0 ]==null||hsla[ 1 ]==null||hsla[ 2 ]==null){
return [ null, null, null, hsla[ 3 ] ];
}
var h=hsla[ 0 ] / 360,
s=hsla[ 1 ],
l=hsla[ 2 ],
a=hsla[ 3 ],
q=l <=0.5 ? l *(1 + s):l + s - l * s,
p=2 * l - q;
return [
Math.round(hue2rgb(p, q, h +(1 / 3)) * 255),
Math.round(hue2rgb(p, q, h) * 255),
Math.round(hue2rgb(p, q, h -(1 / 3)) * 255),
a
];
};
each(spaces, function(spaceName, space){
var props=space.props,
cache=space.cache,
to=space.to,
from=space.from;
color.fn[ spaceName ]=function(value){
if(to&&!this[ cache ]){
this[ cache ]=to(this._rgba);
}
if(value===undefined){
return this[ cache ].slice();
}
var ret,
type=jQuery.type(value),
arr=(type==="array"||type==="object") ? value:arguments,
local=this[ cache ].slice();
each(props, function(key, prop){
var val=arr[ type==="object" ? key:prop.idx ];
if(val==null){
val=local[ prop.idx ];
}
local[ prop.idx ]=clamp(val, prop);
});
if(from){
ret=color(from(local));
ret[ cache ]=local;
return ret;
}else{
return color(local);
}};
each(props, function(key, prop){
if(color.fn[ key ]){
return;
}
color.fn[ key ]=function(value){
var vtype=jQuery.type(value),
fn=(key==="alpha" ?(this._hsla ? "hsla":"rgba"):spaceName),
local=this[ fn ](),
cur=local[ prop.idx ],
match;
if(vtype==="undefined"){
return cur;
}
if(vtype==="function"){
value=value.call(this, cur);
vtype=jQuery.type(value);
}
if(value==null&&prop.empty){
return this;
}
if(vtype==="string"){
match=rplusequals.exec(value);
if(match){
value=cur + parseFloat(match[ 2 ]) *(match[ 1 ]==="+" ? 1:-1);
}}
local[ prop.idx ]=value;
return this[ fn ](local);
};});
});
color.hook=function(hook){
var hooks=hook.split(" ");
each(hooks, function(i, hook){
jQuery.cssHooks[ hook ]={
set: function(elem, value){
var parsed, curElem,
backgroundColor="";
if(value!=="transparent"&&(jQuery.type(value)!=="string"||(parsed=stringParse(value)))){
value=color(parsed||value);
if(!support.rgba&&value._rgba[ 3 ]!==1){
curElem=hook==="backgroundColor" ? elem.parentNode:elem;
while (
(backgroundColor===""||backgroundColor==="transparent") &&
curElem&&curElem.style
){
try {
backgroundColor=jQuery.css(curElem, "backgroundColor");
curElem=curElem.parentNode;
} catch(e){
}}
value=value.blend(backgroundColor&&backgroundColor!=="transparent" ?
backgroundColor :
"_default");
}
value=value.toRgbaString();
}
try {
elem.style[ hook ]=value;
} catch(e){
}}
};
jQuery.fx.step[ hook ]=function(fx){
if(!fx.colorInit){
fx.start=color(fx.elem, hook);
fx.end=color(fx.end);
fx.colorInit=true;
}
jQuery.cssHooks[ hook ].set(fx.elem, fx.start.transition(fx.end, fx.pos));
};});
};
color.hook(stepHooks);
jQuery.cssHooks.borderColor={
expand: function(value){
var expanded={};
each([ "Top", "Right", "Bottom", "Left" ], function(i, part){
expanded[ "border" + part + "Color" ]=value;
});
return expanded;
}};
colors=jQuery.Color.names={
aqua: "#00ffff",
black: "#000000",
blue: "#0000ff",
fuchsia: "#ff00ff",
gray: "#808080",
green: "#008000",
lime: "#00ff00",
maroon: "#800000",
navy: "#000080",
olive: "#808000",
purple: "#800080",
red: "#ff0000",
silver: "#c0c0c0",
teal: "#008080",
white: "#ffffff",
yellow: "#ffff00",
transparent: [ null, null, null, 0 ],
_default: "#ffffff"
};})(jQuery);
(function(){
var classAnimationActions=[ "add", "remove", "toggle" ],
shorthandStyles={
border: 1,
borderBottom: 1,
borderColor: 1,
borderLeft: 1,
borderRight: 1,
borderTop: 1,
borderWidth: 1,
margin: 1,
padding: 1
};
$.each([ "borderLeftStyle", "borderRightStyle", "borderBottomStyle", "borderTopStyle" ], function(_, prop){
$.fx.step[ prop ]=function(fx){
if(fx.end!=="none"&&!fx.setAttr||fx.pos===1&&!fx.setAttr){
jQuery.style(fx.elem, prop, fx.end);
fx.setAttr=true;
}};});
function getElementStyles(elem){
var key, len,
style=elem.ownerDocument.defaultView ?
elem.ownerDocument.defaultView.getComputedStyle(elem, null) :
elem.currentStyle,
styles={};
if(style&&style.length&&style[ 0 ]&&style[ style[ 0 ] ]){
len=style.length;
while(len--){
key=style[ len ];
if(typeof style[ key ]==="string"){
styles[ $.camelCase(key) ]=style[ key ];
}}
}else{
for(key in style){
if(typeof style[ key ]==="string"){
styles[ key ]=style[ key ];
}}
}
return styles;
}
function styleDifference(oldStyle, newStyle){
var diff={},
name, value;
for(name in newStyle){
value=newStyle[ name ];
if(oldStyle[ name ]!==value){
if(!shorthandStyles[ name ]){
if($.fx.step[ name ]||!isNaN(parseFloat(value))){
diff[ name ]=value;
}}
}}
return diff;
}
if(!$.fn.addBack){
$.fn.addBack=function(selector){
return this.add(selector==null ?
this.prevObject:this.prevObject.filter(selector)
);
};}
$.effects.animateClass=function(value, duration, easing, callback){
var o=$.speed(duration, easing, callback);
return this.queue(function(){
var animated=$(this),
baseClass=animated.attr("class")||"",
applyClassChange,
allAnimations=o.children ? animated.find("*").addBack():animated;
allAnimations=allAnimations.map(function(){
var el=$(this);
return {
el: el,
start: getElementStyles(this)
};});
applyClassChange=function(){
$.each(classAnimationActions, function(i, action){
if(value[ action ]){
animated[ action + "Class" ](value[ action ]);
}});
};
applyClassChange();
allAnimations=allAnimations.map(function(){
this.end=getElementStyles(this.el[ 0 ]);
this.diff=styleDifference(this.start, this.end);
return this;
});
animated.attr("class", baseClass);
allAnimations=allAnimations.map(function(){
var styleInfo=this,
dfd=$.Deferred(),
opts=$.extend({}, o, {
queue: false,
complete: function(){
dfd.resolve(styleInfo);
}});
this.el.animate(this.diff, opts);
return dfd.promise();
});
$.when.apply($, allAnimations.get()).done(function(){
applyClassChange();
$.each(arguments, function(){
var el=this.el;
$.each(this.diff, function(key){
el.css(key, "");
});
});
o.complete.call(animated[ 0 ]);
});
});
};
$.fn.extend({
addClass: (function(orig){
return function(classNames, speed, easing, callback){
return speed ?
$.effects.animateClass.call(this,
{ add: classNames }, speed, easing, callback) :
orig.apply(this, arguments);
};})($.fn.addClass),
removeClass: (function(orig){
return function(classNames, speed, easing, callback){
return arguments.length > 1 ?
$.effects.animateClass.call(this,
{ remove: classNames }, speed, easing, callback) :
orig.apply(this, arguments);
};})($.fn.removeClass),
toggleClass: (function(orig){
return function(classNames, force, speed, easing, callback){
if(typeof force==="boolean"||force===undefined){
if(!speed){
return orig.apply(this, arguments);
}else{
return $.effects.animateClass.call(this,
(force ? { add: classNames }:{ remove: classNames }),
speed, easing, callback);
}}else{
return $.effects.animateClass.call(this,
{ toggle: classNames }, force, speed, easing);
}};})($.fn.toggleClass),
switchClass: function(remove, add, speed, easing, callback){
return $.effects.animateClass.call(this, {
add: add,
remove: remove
}, speed, easing, callback);
}});
})();
(function(){
$.extend($.effects, {
version: "1.11.4",
save: function(element, set){
for(var i=0; i < set.length; i++){
if(set[ i ]!==null){
element.data(dataSpace + set[ i ], element[ 0 ].style[ set[ i ] ]);
}}
},
restore: function(element, set){
var val, i;
for(i=0; i < set.length; i++){
if(set[ i ]!==null){
val=element.data(dataSpace + set[ i ]);
if(val===undefined){
val="";
}
element.css(set[ i ], val);
}}
},
setMode: function(el, mode){
if(mode==="toggle"){
mode=el.is(":hidden") ? "show":"hide";
}
return mode;
},
getBaseline: function(origin, original){
var y, x;
switch(origin[ 0 ]){
case "top": y=0; break;
case "middle": y=0.5; break;
case "bottom": y=1; break;
default: y=origin[ 0 ] / original.height;
}
switch(origin[ 1 ]){
case "left": x=0; break;
case "center": x=0.5; break;
case "right": x=1; break;
default: x=origin[ 1 ] / original.width;
}
return {
x: x,
y: y
};},
createWrapper: function(element){
if(element.parent().is(".ui-effects-wrapper")){
return element.parent();
}
var props={
width: element.outerWidth(true),
height: element.outerHeight(true),
"float": element.css("float")
},
wrapper=$("<div></div>")
.addClass("ui-effects-wrapper")
.css({
fontSize: "100%",
background: "transparent",
border: "none",
margin: 0,
padding: 0
}),
size={
width: element.width(),
height: element.height()
},
active=document.activeElement;
try {
active.id;
} catch(e){
active=document.body;
}
element.wrap(wrapper);
if(element[ 0 ]===active||$.contains(element[ 0 ], active)){
$(active).focus();
}
wrapper=element.parent();
if(element.css("position")==="static"){
wrapper.css({ position: "relative" });
element.css({ position: "relative" });
}else{
$.extend(props, {
position: element.css("position"),
zIndex: element.css("z-index")
});
$.each([ "top", "left", "bottom", "right" ], function(i, pos){
props[ pos ]=element.css(pos);
if(isNaN(parseInt(props[ pos ], 10))){
props[ pos ]="auto";
}});
element.css({
position: "relative",
top: 0,
left: 0,
right: "auto",
bottom: "auto"
});
}
element.css(size);
return wrapper.css(props).show();
},
removeWrapper: function(element){
var active=document.activeElement;
if(element.parent().is(".ui-effects-wrapper")){
element.parent().replaceWith(element);
if(element[ 0 ]===active||$.contains(element[ 0 ], active)){
$(active).focus();
}}
return element;
},
setTransition: function(element, list, factor, value){
value=value||{};
$.each(list, function(i, x){
var unit=element.cssUnit(x);
if(unit[ 0 ] > 0){
value[ x ]=unit[ 0 ] * factor + unit[ 1 ];
}});
return value;
}});
function _normalizeArguments(effect, options, speed, callback){
if($.isPlainObject(effect)){
options=effect;
effect=effect.effect;
}
effect={ effect: effect };
if(options==null){
options={};}
if($.isFunction(options)){
callback=options;
speed=null;
options={};}
if(typeof options==="number"||$.fx.speeds[ options ]){
callback=speed;
speed=options;
options={};}
if($.isFunction(speed)){
callback=speed;
speed=null;
}
if(options){
$.extend(effect, options);
}
speed=speed||options.duration;
effect.duration=$.fx.off ? 0 :
typeof speed==="number" ? speed :
speed in $.fx.speeds ? $.fx.speeds[ speed ] :
$.fx.speeds._default;
effect.complete=callback||options.complete;
return effect;
}
function standardAnimationOption(option){
if(!option||typeof option==="number"||$.fx.speeds[ option ]){
return true;
}
if(typeof option==="string"&&!$.effects.effect[ option ]){
return true;
}
if($.isFunction(option)){
return true;
}
if(typeof option==="object"&&!option.effect){
return true;
}
return false;
}
$.fn.extend({
effect: function(){
var args=_normalizeArguments.apply(this, arguments),
mode=args.mode,
queue=args.queue,
effectMethod=$.effects.effect[ args.effect ];
if($.fx.off||!effectMethod){
if(mode){
return this[ mode ](args.duration, args.complete);
}else{
return this.each(function(){
if(args.complete){
args.complete.call(this);
}});
}}
function run(next){
var elem=$(this),
complete=args.complete,
mode=args.mode;
function done(){
if($.isFunction(complete)){
complete.call(elem[0]);
}
if($.isFunction(next)){
next();
}}
if(elem.is(":hidden") ? mode==="hide":mode==="show"){
elem[ mode ]();
done();
}else{
effectMethod.call(elem[0], args, done);
}}
return queue===false ? this.each(run):this.queue(queue||"fx", run);
},
show: (function(orig){
return function(option){
if(standardAnimationOption(option)){
return orig.apply(this, arguments);
}else{
var args=_normalizeArguments.apply(this, arguments);
args.mode="show";
return this.effect.call(this, args);
}};})($.fn.show),
hide: (function(orig){
return function(option){
if(standardAnimationOption(option)){
return orig.apply(this, arguments);
}else{
var args=_normalizeArguments.apply(this, arguments);
args.mode="hide";
return this.effect.call(this, args);
}};})($.fn.hide),
toggle: (function(orig){
return function(option){
if(standardAnimationOption(option)||typeof option==="boolean"){
return orig.apply(this, arguments);
}else{
var args=_normalizeArguments.apply(this, arguments);
args.mode="toggle";
return this.effect.call(this, args);
}};})($.fn.toggle),
cssUnit: function(key){
var style=this.css(key),
val=[];
$.each([ "em", "px", "%", "pt" ], function(i, unit){
if(style.indexOf(unit) > 0){
val=[ parseFloat(style), unit ];
}});
return val;
}});
})();
(function(){
var baseEasings={};
$.each([ "Quad", "Cubic", "Quart", "Quint", "Expo" ], function(i, name){
baseEasings[ name ]=function(p){
return Math.pow(p, i + 2);
};});
$.extend(baseEasings, {
Sine: function(p){
return 1 - Math.cos(p * Math.PI / 2);
},
Circ: function(p){
return 1 - Math.sqrt(1 - p * p);
},
Elastic: function(p){
return p===0||p===1 ? p :
-Math.pow(2, 8 * (p - 1)) * Math.sin(( (p - 1) * 80 - 7.5) * Math.PI / 15);
},
Back: function(p){
return p * p *(3 * p - 2);
},
Bounce: function(p){
var pow2,
bounce=4;
while(p <(( pow2=Math.pow(2, --bounce)) - 1) / 11){}
return 1 / Math.pow(4, 3 - bounce) - 7.5625 * Math.pow(( pow2 * 3 - 2) / 22 - p, 2);
}});
$.each(baseEasings, function(name, easeIn){
$.easing[ "easeIn" + name ]=easeIn;
$.easing[ "easeOut" + name ]=function(p){
return 1 - easeIn(1 - p);
};
$.easing[ "easeInOut" + name ]=function(p){
return p < 0.5 ?
easeIn(p * 2) / 2 :
1 - easeIn(p * -2 + 2) / 2;
};});
})();
var effect=$.effects;
var effectBlind=$.effects.effect.blind=function(o, done){
var el=$(this),
rvertical=/up|down|vertical/,
rpositivemotion=/up|left|vertical|horizontal/,
props=[ "position", "top", "bottom", "left", "right", "height", "width" ],
mode=$.effects.setMode(el, o.mode||"hide"),
direction=o.direction||"up",
vertical=rvertical.test(direction),
ref=vertical ? "height":"width",
ref2=vertical ? "top":"left",
motion=rpositivemotion.test(direction),
animation={},
show=mode==="show",
wrapper, distance, margin;
if(el.parent().is(".ui-effects-wrapper")){
$.effects.save(el.parent(), props);
}else{
$.effects.save(el, props);
}
el.show();
wrapper=$.effects.createWrapper(el).css({
overflow: "hidden"
});
distance=wrapper[ ref ]();
margin=parseFloat(wrapper.css(ref2))||0;
animation[ ref ]=show ? distance:0;
if(!motion){
el
.css(vertical ? "bottom":"right", 0)
.css(vertical ? "top":"left", "auto")
.css({ position: "absolute" });
animation[ ref2 ]=show ? margin:distance + margin;
}
if(show){
wrapper.css(ref, 0);
if(!motion){
wrapper.css(ref2, margin + distance);
}}
wrapper.animate(animation, {
duration: o.duration,
easing: o.easing,
queue: false,
complete: function(){
if(mode==="hide"){
el.hide();
}
$.effects.restore(el, props);
$.effects.removeWrapper(el);
done();
}});
};
var effectBounce=$.effects.effect.bounce=function(o, done){
var el=$(this),
props=[ "position", "top", "bottom", "left", "right", "height", "width" ],
mode=$.effects.setMode(el, o.mode||"effect"),
hide=mode==="hide",
show=mode==="show",
direction=o.direction||"up",
distance=o.distance,
times=o.times||5,
anims=times * 2 +(show||hide ? 1:0),
speed=o.duration / anims,
easing=o.easing,
ref=(direction==="up"||direction==="down") ? "top":"left",
motion=(direction==="up"||direction==="left"),
i,
upAnim,
downAnim,
queue=el.queue(),
queuelen=queue.length;
if(show||hide){
props.push("opacity");
}
$.effects.save(el, props);
el.show();
$.effects.createWrapper(el);
if(!distance){
distance=el[ ref==="top" ? "outerHeight":"outerWidth" ]() / 3;
}
if(show){
downAnim={ opacity: 1 };
downAnim[ ref ]=0;
el.css("opacity", 0)
.css(ref, motion ? -distance * 2:distance * 2)
.animate(downAnim, speed, easing);
}
if(hide){
distance=distance / Math.pow(2, times - 1);
}
downAnim={};
downAnim[ ref ]=0;
for(i=0; i < times; i++){
upAnim={};
upAnim[ ref ]=(motion ? "-=":"+=") + distance;
el.animate(upAnim, speed, easing)
.animate(downAnim, speed, easing);
distance=hide ? distance * 2:distance / 2;
}
if(hide){
upAnim={ opacity: 0 };
upAnim[ ref ]=(motion ? "-=":"+=") + distance;
el.animate(upAnim, speed, easing);
}
el.queue(function(){
if(hide){
el.hide();
}
$.effects.restore(el, props);
$.effects.removeWrapper(el);
done();
});
if(queuelen > 1){
queue.splice.apply(queue,
[ 1, 0 ].concat(queue.splice(queuelen, anims + 1)));
}
el.dequeue();
};
var effectClip=$.effects.effect.clip=function(o, done){
var el=$(this),
props=[ "position", "top", "bottom", "left", "right", "height", "width" ],
mode=$.effects.setMode(el, o.mode||"hide"),
show=mode==="show",
direction=o.direction||"vertical",
vert=direction==="vertical",
size=vert ? "height":"width",
position=vert ? "top":"left",
animation={},
wrapper, animate, distance;
$.effects.save(el, props);
el.show();
wrapper=$.effects.createWrapper(el).css({
overflow: "hidden"
});
animate=(el[0].tagName==="IMG") ? wrapper:el;
distance=animate[ size ]();
if(show){
animate.css(size, 0);
animate.css(position, distance / 2);
}
animation[ size ]=show ? distance:0;
animation[ position ]=show ? 0:distance / 2;
animate.animate(animation, {
queue: false,
duration: o.duration,
easing: o.easing,
complete: function(){
if(!show){
el.hide();
}
$.effects.restore(el, props);
$.effects.removeWrapper(el);
done();
}});
};
var effectDrop=$.effects.effect.drop=function(o, done){
var el=$(this),
props=[ "position", "top", "bottom", "left", "right", "opacity", "height", "width" ],
mode=$.effects.setMode(el, o.mode||"hide"),
show=mode==="show",
direction=o.direction||"left",
ref=(direction==="up"||direction==="down") ? "top":"left",
motion=(direction==="up"||direction==="left") ? "pos":"neg",
animation={
opacity: show ? 1:0
},
distance;
$.effects.save(el, props);
el.show();
$.effects.createWrapper(el);
distance=o.distance||el[ ref==="top" ? "outerHeight":"outerWidth" ](true) / 2;
if(show){
el
.css("opacity", 0)
.css(ref, motion==="pos" ? -distance:distance);
}
animation[ ref ]=(show ?
(motion==="pos" ? "+=":"-=") :
(motion==="pos" ? "-=":"+=")) +
distance;
el.animate(animation, {
queue: false,
duration: o.duration,
easing: o.easing,
complete: function(){
if(mode==="hide"){
el.hide();
}
$.effects.restore(el, props);
$.effects.removeWrapper(el);
done();
}});
};
var effectExplode=$.effects.effect.explode=function(o, done){
var rows=o.pieces ? Math.round(Math.sqrt(o.pieces)):3,
cells=rows,
el=$(this),
mode=$.effects.setMode(el, o.mode||"hide"),
show=mode==="show",
offset=el.show().css("visibility", "hidden").offset(),
width=Math.ceil(el.outerWidth() / cells),
height=Math.ceil(el.outerHeight() / rows),
pieces=[],
i, j, left, top, mx, my;
function childComplete(){
pieces.push(this);
if(pieces.length===rows * cells){
animComplete();
}}
for(i=0; i < rows ; i++){
top=offset.top + i * height;
my=i -(rows - 1) / 2 ;
for(j=0; j < cells ; j++){
left=offset.left + j * width;
mx=j -(cells - 1) / 2 ;
el
.clone()
.appendTo("body")
.wrap("<div></div>")
.css({
position: "absolute",
visibility: "visible",
left: -j * width,
top: -i * height
})
.parent()
.addClass("ui-effects-explode")
.css({
position: "absolute",
overflow: "hidden",
width: width,
height: height,
left: left +(show ? mx * width:0),
top: top +(show ? my * height:0),
opacity: show ? 0:1
}).animate({
left: left +(show ? 0:mx * width),
top: top +(show ? 0:my * height),
opacity: show ? 1:0
}, o.duration||500, o.easing, childComplete);
}}
function animComplete(){
el.css({
visibility: "visible"
});
$(pieces).remove();
if(!show){
el.hide();
}
done();
}};
var effectFade=$.effects.effect.fade=function(o, done){
var el=$(this),
mode=$.effects.setMode(el, o.mode||"toggle");
el.animate({
opacity: mode
}, {
queue: false,
duration: o.duration,
easing: o.easing,
complete: done
});
};
var effectFold=$.effects.effect.fold=function(o, done){
var el=$(this),
props=[ "position", "top", "bottom", "left", "right", "height", "width" ],
mode=$.effects.setMode(el, o.mode||"hide"),
show=mode==="show",
hide=mode==="hide",
size=o.size||15,
percent=/([0-9]+)%/.exec(size),
horizFirst = !!o.horizFirst,
widthFirst=show!==horizFirst,
ref=widthFirst ? [ "width", "height" ]:[ "height", "width" ],
duration=o.duration / 2,
wrapper, distance,
animation1={},
animation2={};
$.effects.save(el, props);
el.show();
wrapper=$.effects.createWrapper(el).css({
overflow: "hidden"
});
distance=widthFirst ?
[ wrapper.width(), wrapper.height() ] :
[ wrapper.height(), wrapper.width() ];
if(percent){
size=parseInt(percent[ 1 ], 10) / 100 * distance[ hide ? 0:1 ];
}
if(show){
wrapper.css(horizFirst ? {
height: 0,
width: size
}:{
height: size,
width: 0
});
}
animation1[ ref[ 0 ] ]=show ? distance[ 0 ]:size;
animation2[ ref[ 1 ] ]=show ? distance[ 1 ]:0;
wrapper
.animate(animation1, duration, o.easing)
.animate(animation2, duration, o.easing, function(){
if(hide){
el.hide();
}
$.effects.restore(el, props);
$.effects.removeWrapper(el);
done();
});
};
var effectHighlight=$.effects.effect.highlight=function(o, done){
var elem=$(this),
props=[ "backgroundImage", "backgroundColor", "opacity" ],
mode=$.effects.setMode(elem, o.mode||"show"),
animation={
backgroundColor: elem.css("backgroundColor")
};
if(mode==="hide"){
animation.opacity=0;
}
$.effects.save(elem, props);
elem
.show()
.css({
backgroundImage: "none",
backgroundColor: o.color||"#ffff99"
})
.animate(animation, {
queue: false,
duration: o.duration,
easing: o.easing,
complete: function(){
if(mode==="hide"){
elem.hide();
}
$.effects.restore(elem, props);
done();
}});
};
var effectSize=$.effects.effect.size=function(o, done){
var original, baseline, factor,
el=$(this),
props0=[ "position", "top", "bottom", "left", "right", "width", "height", "overflow", "opacity" ],
props1=[ "position", "top", "bottom", "left", "right", "overflow", "opacity" ],
props2=[ "width", "height", "overflow" ],
cProps=[ "fontSize" ],
vProps=[ "borderTopWidth", "borderBottomWidth", "paddingTop", "paddingBottom" ],
hProps=[ "borderLeftWidth", "borderRightWidth", "paddingLeft", "paddingRight" ],
mode=$.effects.setMode(el, o.mode||"effect"),
restore=o.restore||mode!=="effect",
scale=o.scale||"both",
origin=o.origin||[ "middle", "center" ],
position=el.css("position"),
props=restore ? props0:props1,
zero={
height: 0,
width: 0,
outerHeight: 0,
outerWidth: 0
};
if(mode==="show"){
el.show();
}
original={
height: el.height(),
width: el.width(),
outerHeight: el.outerHeight(),
outerWidth: el.outerWidth()
};
if(o.mode==="toggle"&&mode==="show"){
el.from=o.to||zero;
el.to=o.from||original;
}else{
el.from=o.from||(mode==="show" ? zero:original);
el.to=o.to||(mode==="hide" ? zero:original);
}
factor={
from: {
y: el.from.height / original.height,
x: el.from.width / original.width
},
to: {
y: el.to.height / original.height,
x: el.to.width / original.width
}};
if(scale==="box"||scale==="both"){
if(factor.from.y!==factor.to.y){
props=props.concat(vProps);
el.from=$.effects.setTransition(el, vProps, factor.from.y, el.from);
el.to=$.effects.setTransition(el, vProps, factor.to.y, el.to);
}
if(factor.from.x!==factor.to.x){
props=props.concat(hProps);
el.from=$.effects.setTransition(el, hProps, factor.from.x, el.from);
el.to=$.effects.setTransition(el, hProps, factor.to.x, el.to);
}}
if(scale==="content"||scale==="both"){
if(factor.from.y!==factor.to.y){
props=props.concat(cProps).concat(props2);
el.from=$.effects.setTransition(el, cProps, factor.from.y, el.from);
el.to=$.effects.setTransition(el, cProps, factor.to.y, el.to);
}}
$.effects.save(el, props);
el.show();
$.effects.createWrapper(el);
el.css("overflow", "hidden").css(el.from);
if(origin){
baseline=$.effects.getBaseline(origin, original);
el.from.top=(original.outerHeight - el.outerHeight()) * baseline.y;
el.from.left=(original.outerWidth - el.outerWidth()) * baseline.x;
el.to.top=(original.outerHeight - el.to.outerHeight) * baseline.y;
el.to.left=(original.outerWidth - el.to.outerWidth) * baseline.x;
}
el.css(el.from);
if(scale==="content"||scale==="both"){
vProps=vProps.concat([ "marginTop", "marginBottom" ]).concat(cProps);
hProps=hProps.concat([ "marginLeft", "marginRight" ]);
props2=props0.concat(vProps).concat(hProps);
el.find("*[width]").each(function(){
var child=$(this),
c_original={
height: child.height(),
width: child.width(),
outerHeight: child.outerHeight(),
outerWidth: child.outerWidth()
};
if(restore){
$.effects.save(child, props2);
}
child.from={
height: c_original.height * factor.from.y,
width: c_original.width * factor.from.x,
outerHeight: c_original.outerHeight * factor.from.y,
outerWidth: c_original.outerWidth * factor.from.x
};
child.to={
height: c_original.height * factor.to.y,
width: c_original.width * factor.to.x,
outerHeight: c_original.height * factor.to.y,
outerWidth: c_original.width * factor.to.x
};
if(factor.from.y!==factor.to.y){
child.from=$.effects.setTransition(child, vProps, factor.from.y, child.from);
child.to=$.effects.setTransition(child, vProps, factor.to.y, child.to);
}
if(factor.from.x!==factor.to.x){
child.from=$.effects.setTransition(child, hProps, factor.from.x, child.from);
child.to=$.effects.setTransition(child, hProps, factor.to.x, child.to);
}
child.css(child.from);
child.animate(child.to, o.duration, o.easing, function(){
if(restore){
$.effects.restore(child, props2);
}});
});
}
el.animate(el.to, {
queue: false,
duration: o.duration,
easing: o.easing,
complete: function(){
if(el.to.opacity===0){
el.css("opacity", el.from.opacity);
}
if(mode==="hide"){
el.hide();
}
$.effects.restore(el, props);
if(!restore){
if(position==="static"){
el.css({
position: "relative",
top: el.to.top,
left: el.to.left
});
}else{
$.each([ "top", "left" ], function(idx, pos){
el.css(pos, function(_, str){
var val=parseInt(str, 10),
toRef=idx ? el.to.left:el.to.top;
if(str==="auto"){
return toRef + "px";
}
return val + toRef + "px";
});
});
}}
$.effects.removeWrapper(el);
done();
}});
};
var effectScale=$.effects.effect.scale=function(o, done){
var el=$(this),
options=$.extend(true, {}, o),
mode=$.effects.setMode(el, o.mode||"effect"),
percent=parseInt(o.percent, 10) ||
(parseInt(o.percent, 10)===0 ? 0:(mode==="hide" ? 0:100)),
direction=o.direction||"both",
origin=o.origin,
original={
height: el.height(),
width: el.width(),
outerHeight: el.outerHeight(),
outerWidth: el.outerWidth()
},
factor={
y: direction!=="horizontal" ? (percent / 100):1,
x: direction!=="vertical" ? (percent / 100):1
};
options.effect="size";
options.queue=false;
options.complete=done;
if(mode!=="effect"){
options.origin=origin||[ "middle", "center" ];
options.restore=true;
}
options.from=o.from||(mode==="show" ? {
height: 0,
width: 0,
outerHeight: 0,
outerWidth: 0
}:original);
options.to={
height: original.height * factor.y,
width: original.width * factor.x,
outerHeight: original.outerHeight * factor.y,
outerWidth: original.outerWidth * factor.x
};
if(options.fade){
if(mode==="show"){
options.from.opacity=0;
options.to.opacity=1;
}
if(mode==="hide"){
options.from.opacity=1;
options.to.opacity=0;
}}
el.effect(options);
};
var effectPuff=$.effects.effect.puff=function(o, done){
var elem=$(this),
mode=$.effects.setMode(elem, o.mode||"hide"),
hide=mode==="hide",
percent=parseInt(o.percent, 10)||150,
factor=percent / 100,
original={
height: elem.height(),
width: elem.width(),
outerHeight: elem.outerHeight(),
outerWidth: elem.outerWidth()
};
$.extend(o, {
effect: "scale",
queue: false,
fade: true,
mode: mode,
complete: done,
percent: hide ? percent:100,
from: hide ?
original :
{
height: original.height * factor,
width: original.width * factor,
outerHeight: original.outerHeight * factor,
outerWidth: original.outerWidth * factor
}});
elem.effect(o);
};
var effectPulsate=$.effects.effect.pulsate=function(o, done){
var elem=$(this),
mode=$.effects.setMode(elem, o.mode||"show"),
show=mode==="show",
hide=mode==="hide",
showhide=(show||mode==="hide"),
anims=(( o.times||5) * 2) +(showhide ? 1:0),
duration=o.duration / anims,
animateTo=0,
queue=elem.queue(),
queuelen=queue.length,
i;
if(show||!elem.is(":visible")){
elem.css("opacity", 0).show();
animateTo=1;
}
for(i=1; i < anims; i++){
elem.animate({
opacity: animateTo
}, duration, o.easing);
animateTo=1 - animateTo;
}
elem.animate({
opacity: animateTo
}, duration, o.easing);
elem.queue(function(){
if(hide){
elem.hide();
}
done();
});
if(queuelen > 1){
queue.splice.apply(queue,
[ 1, 0 ].concat(queue.splice(queuelen, anims + 1)));
}
elem.dequeue();
};
var effectShake=$.effects.effect.shake=function(o, done){
var el=$(this),
props=[ "position", "top", "bottom", "left", "right", "height", "width" ],
mode=$.effects.setMode(el, o.mode||"effect"),
direction=o.direction||"left",
distance=o.distance||20,
times=o.times||3,
anims=times * 2 + 1,
speed=Math.round(o.duration / anims),
ref=(direction==="up"||direction==="down") ? "top":"left",
positiveMotion=(direction==="up"||direction==="left"),
animation={},
animation1={},
animation2={},
i,
queue=el.queue(),
queuelen=queue.length;
$.effects.save(el, props);
el.show();
$.effects.createWrapper(el);
animation[ ref ]=(positiveMotion ? "-=":"+=") + distance;
animation1[ ref ]=(positiveMotion ? "+=":"-=") + distance * 2;
animation2[ ref ]=(positiveMotion ? "-=":"+=") + distance * 2;
el.animate(animation, speed, o.easing);
for(i=1; i < times; i++){
el.animate(animation1, speed, o.easing).animate(animation2, speed, o.easing);
}
el
.animate(animation1, speed, o.easing)
.animate(animation, speed / 2, o.easing)
.queue(function(){
if(mode==="hide"){
el.hide();
}
$.effects.restore(el, props);
$.effects.removeWrapper(el);
done();
});
if(queuelen > 1){
queue.splice.apply(queue,
[ 1, 0 ].concat(queue.splice(queuelen, anims + 1)));
}
el.dequeue();
};
var effectSlide=$.effects.effect.slide=function(o, done){
var el=$(this),
props=[ "position", "top", "bottom", "left", "right", "width", "height" ],
mode=$.effects.setMode(el, o.mode||"show"),
show=mode==="show",
direction=o.direction||"left",
ref=(direction==="up"||direction==="down") ? "top":"left",
positiveMotion=(direction==="up"||direction==="left"),
distance,
animation={};
$.effects.save(el, props);
el.show();
distance=o.distance||el[ ref==="top" ? "outerHeight":"outerWidth" ](true);
$.effects.createWrapper(el).css({
overflow: "hidden"
});
if(show){
el.css(ref, positiveMotion ? (isNaN(distance) ? "-" + distance:-distance):distance);
}
animation[ ref ]=(show ?
(positiveMotion ? "+=":"-=") :
(positiveMotion ? "-=":"+=")) +
distance;
el.animate(animation, {
queue: false,
duration: o.duration,
easing: o.easing,
complete: function(){
if(mode==="hide"){
el.hide();
}
$.effects.restore(el, props);
$.effects.removeWrapper(el);
done();
}});
};
var effectTransfer=$.effects.effect.transfer=function(o, done){
var elem=$(this),
target=$(o.to),
targetFixed=target.css("position")==="fixed",
body=$("body"),
fixTop=targetFixed ? body.scrollTop():0,
fixLeft=targetFixed ? body.scrollLeft():0,
endPosition=target.offset(),
animation={
top: endPosition.top - fixTop,
left: endPosition.left - fixLeft,
height: target.innerHeight(),
width: target.innerWidth()
},
startPosition=elem.offset(),
transfer=$("<div class='ui-effects-transfer'></div>")
.appendTo(document.body)
.addClass(o.className)
.css({
top: startPosition.top - fixTop,
left: startPosition.left - fixLeft,
height: elem.innerHeight(),
width: elem.innerWidth(),
position: targetFixed ? "fixed":"absolute"
})
.animate(animation, o.duration, o.easing, function(){
transfer.remove();
done();
});
};}));
if(typeof Object.create!=="function"){
Object.create=function (obj){
function F(){}
F.prototype=obj;
return new F();
};}
(function ($, window, document){
var Carousel={
init:function (options, el){
var base=this;
base.$elem=$(el);
base.options=$.extend({}, $.fn.owlCarousel.options, base.$elem.data(), options);
base.userOptions=options;
base.loadContent();
},
loadContent:function (){
var base=this, url;
function getData(data){
var i, content="";
if(typeof base.options.jsonSuccess==="function"){
base.options.jsonSuccess.apply(this, [data]);
}else{
for (i in data.owl){
if(data.owl.hasOwnProperty(i)){
content +=data.owl[i].item;
}}
base.$elem.html(content);
}
base.logIn();
}
if(typeof base.options.beforeInit==="function"){
base.options.beforeInit.apply(this, [base.$elem]);
}
if(typeof base.options.jsonPath==="string"){
url=base.options.jsonPath;
$.getJSON(url, getData);
}else{
base.logIn();
}},
logIn:function (){
var base=this;
base.$elem.data("owl-originalStyles", base.$elem.attr("style"));
base.$elem.data("owl-originalClasses", base.$elem.attr("class"));
base.$elem.css({opacity: 0});
base.orignalItems=base.options.items;
base.checkBrowser();
base.wrapperWidth=0;
base.checkVisible=null;
base.setVars();
},
setVars:function (){
var base=this;
if(base.$elem.children().length===0){return false; }
base.baseClass();
base.eventTypes();
base.$userItems=base.$elem.children();
base.itemsAmount=base.$userItems.length;
base.wrapItems();
base.$owlItems=base.$elem.find(".owl-item");
base.$owlWrapper=base.$elem.find(".owl-wrapper");
base.playDirection="next";
base.prevItem=0;
base.prevArr=[0];
base.currentItem=0;
base.customEvents();
base.onStartup();
},
onStartup:function (){
var base=this;
base.updateItems();
base.calculateAll();
base.buildControls();
base.updateControls();
base.response();
base.moveEvents();
base.stopOnHover();
base.owlStatus();
if(base.options.transitionStyle!==false){
base.transitionTypes(base.options.transitionStyle);
}
if(base.options.autoPlay===true){
base.options.autoPlay=5000;
}
base.play();
base.$elem.find(".owl-wrapper").css("display", "block");
if(!base.$elem.is(":visible")){
base.watchVisibility();
}else{
base.$elem.css("opacity", 1);
}
base.onstartup=false;
base.eachMoveUpdate();
if(typeof base.options.afterInit==="function"){
base.options.afterInit.apply(this, [base.$elem]);
}},
eachMoveUpdate:function (){
var base=this;
if(base.options.lazyLoad===true){
base.lazyLoad();
}
if(base.options.autoHeight===true){
base.autoHeight();
}
base.onVisibleItems();
if(typeof base.options.afterAction==="function"){
base.options.afterAction.apply(this, [base.$elem]);
}},
updateVars:function (){
var base=this;
if(typeof base.options.beforeUpdate==="function"){
base.options.beforeUpdate.apply(this, [base.$elem]);
}
base.watchVisibility();
base.updateItems();
base.calculateAll();
base.updatePosition();
base.updateControls();
base.eachMoveUpdate();
if(typeof base.options.afterUpdate==="function"){
base.options.afterUpdate.apply(this, [base.$elem]);
}},
reload:function (){
var base=this;
window.setTimeout(function (){
base.updateVars();
}, 0);
},
watchVisibility:function (){
var base=this;
if(base.$elem.is(":visible")===false){
base.$elem.css({opacity: 0});
window.clearInterval(base.autoPlayInterval);
window.clearInterval(base.checkVisible);
}else{
return false;
}
base.checkVisible=window.setInterval(function (){
if(base.$elem.is(":visible")){
base.reload();
base.$elem.animate({opacity: 1}, 200);
window.clearInterval(base.checkVisible);
}}, 500);
},
wrapItems:function (){
var base=this;
base.$userItems.wrapAll("<div class=\"owl-wrapper\">").wrap("<div class=\"owl-item\"></div>");
base.$elem.find(".owl-wrapper").wrap("<div class=\"owl-wrapper-outer\">");
base.wrapperOuter=base.$elem.find(".owl-wrapper-outer");
base.$elem.css("display", "block");
},
baseClass:function (){
var base=this,
hasBaseClass=base.$elem.hasClass(base.options.baseClass),
hasThemeClass=base.$elem.hasClass(base.options.theme);
if(!hasBaseClass){
base.$elem.addClass(base.options.baseClass);
}
if(!hasThemeClass){
base.$elem.addClass(base.options.theme);
}},
updateItems:function (){
var base=this, width, i;
if(base.options.responsive===false){
return false;
}
if(base.options.singleItem===true){
base.options.items=base.orignalItems=1;
base.options.itemsCustom=false;
base.options.itemsDesktop=false;
base.options.itemsDesktopSmall=false;
base.options.itemsTablet=false;
base.options.itemsTabletSmall=false;
base.options.itemsMobile=false;
return false;
}
width=$(base.options.responsiveBaseWidth).width();
if(width > (base.options.itemsDesktop[0]||base.orignalItems)){
base.options.items=base.orignalItems;
}
if(base.options.itemsCustom!==false){
base.options.itemsCustom.sort(function (a, b){return a[0] - b[0]; });
for (i=0; i < base.options.itemsCustom.length; i +=1){
if(base.options.itemsCustom[i][0] <=width){
base.options.items=base.options.itemsCustom[i][1];
}}
}else{
if(width <=base.options.itemsDesktop[0]&&base.options.itemsDesktop!==false){
base.options.items=base.options.itemsDesktop[1];
}
if(width <=base.options.itemsDesktopSmall[0]&&base.options.itemsDesktopSmall!==false){
base.options.items=base.options.itemsDesktopSmall[1];
}
if(width <=base.options.itemsTablet[0]&&base.options.itemsTablet!==false){
base.options.items=base.options.itemsTablet[1];
}
if(width <=base.options.itemsTabletSmall[0]&&base.options.itemsTabletSmall!==false){
base.options.items=base.options.itemsTabletSmall[1];
}
if(width <=base.options.itemsMobile[0]&&base.options.itemsMobile!==false){
base.options.items=base.options.itemsMobile[1];
}}
if(base.options.items > base.itemsAmount&&base.options.itemsScaleUp===true){
base.options.items=base.itemsAmount;
}},
response:function (){
var base=this,
smallDelay,
lastWindowWidth;
if(base.options.responsive!==true){
return false;
}
lastWindowWidth=$(window).width();
base.resizer=function (){
if($(window).width()!==lastWindowWidth){
if(base.options.autoPlay!==false){
window.clearInterval(base.autoPlayInterval);
}
window.clearTimeout(smallDelay);
smallDelay=window.setTimeout(function (){
lastWindowWidth=$(window).width();
base.updateVars();
}, base.options.responsiveRefreshRate);
}};
$(window).resize(base.resizer);
},
updatePosition:function (){
var base=this;
base.jumpTo(base.currentItem);
if(base.options.autoPlay!==false){
base.checkAp();
}},
appendItemsSizes:function (){
var base=this,
roundPages=0,
lastItem=base.itemsAmount - base.options.items;
base.$owlItems.each(function (index){
var $this=$(this);
$this
.css({"width": base.itemWidth})
.data("owl-item", Number(index));
if(index % base.options.items===0||index===lastItem){
if(!(index > lastItem)){
roundPages +=1;
}}
$this.data("owl-roundPages", roundPages);
});
},
appendWrapperSizes:function (){
var base=this,
width=base.$owlItems.length * base.itemWidth;
base.$owlWrapper.css({
"width": width * 2,
"left": 0
});
base.appendItemsSizes();
},
calculateAll:function (){
var base=this;
base.calculateWidth();
base.appendWrapperSizes();
base.loops();
base.max();
},
calculateWidth:function (){
var base=this;
base.itemWidth=Math.round(base.$elem.width() / base.options.items);
},
max:function (){
var base=this,
maximum=((base.itemsAmount * base.itemWidth) - base.options.items * base.itemWidth) * -1;
if(base.options.items > base.itemsAmount){
base.maximumItem=0;
maximum=0;
base.maximumPixels=0;
}else{
base.maximumItem=base.itemsAmount - base.options.items;
base.maximumPixels=maximum;
}
return maximum;
},
min:function (){
return 0;
},
loops:function (){
var base=this,
prev=0,
elWidth=0,
i,
item,
roundPageNum;
base.positionsInArray=[0];
base.pagesInArray=[];
for (i=0; i < base.itemsAmount; i +=1){
elWidth +=base.itemWidth;
base.positionsInArray.push(-elWidth);
if(base.options.scrollPerPage===true){
item=$(base.$owlItems[i]);
roundPageNum=item.data("owl-roundPages");
if(roundPageNum!==prev){
base.pagesInArray[prev]=base.positionsInArray[i];
prev=roundPageNum;
}}
}},
buildControls:function (){
var base=this;
if(base.options.navigation===true||base.options.pagination===true){
base.owlControls=$("<div class=\"owl-controls\"/>").toggleClass("clickable", !base.browser.isTouch).appendTo(base.$elem);
}
if(base.options.pagination===true){
base.buildPagination();
}
if(base.options.navigation===true){
base.buildButtons();
}},
buildButtons:function (){
var base=this,
buttonsWrapper=$("<div class=\"owl-buttons\"/>");
base.owlControls.append(buttonsWrapper);
base.buttonPrev=$("<div/>", {
"class":"owl-prev",
"html":base.options.navigationText[0]||""
});
base.buttonNext=$("<div/>", {
"class":"owl-next",
"html":base.options.navigationText[1]||""
});
buttonsWrapper
.append(base.buttonPrev)
.append(base.buttonNext);
buttonsWrapper.on("touchstart.owlControls mousedown.owlControls", "div[class^=\"owl\"]", function (event){
event.preventDefault();
});
buttonsWrapper.on("touchend.owlControls mouseup.owlControls", "div[class^=\"owl\"]", function (event){
event.preventDefault();
if($(this).hasClass("owl-next")){
base.next();
}else{
base.prev();
}});
},
buildPagination:function (){
var base=this;
base.paginationWrapper=$("<div class=\"owl-pagination\"/>");
base.owlControls.append(base.paginationWrapper);
base.paginationWrapper.on("touchend.owlControls mouseup.owlControls", ".owl-page", function (event){
event.preventDefault();
if(Number($(this).data("owl-page"))!==base.currentItem){
base.goTo(Number($(this).data("owl-page")), true);
}});
},
updatePagination:function (){
var base=this,
counter,
lastPage,
lastItem,
i,
paginationButton,
paginationButtonInner;
if(base.options.pagination===false){
return false;
}
base.paginationWrapper.html("");
counter=0;
lastPage=base.itemsAmount - base.itemsAmount % base.options.items;
for (i=0; i < base.itemsAmount; i +=1){
if(i % base.options.items===0){
counter +=1;
if(lastPage===i){
lastItem=base.itemsAmount - base.options.items;
}
paginationButton=$("<div/>", {
"class":"owl-page"
});
paginationButtonInner=$("<span></span>", {
"text": base.options.paginationNumbers===true ? counter:"",
"class": base.options.paginationNumbers===true ? "owl-numbers":""
});
paginationButton.append(paginationButtonInner);
paginationButton.data("owl-page", lastPage===i ? lastItem:i);
paginationButton.data("owl-roundPages", counter);
base.paginationWrapper.append(paginationButton);
}}
base.checkPagination();
},
checkPagination:function (){
var base=this;
if(base.options.pagination===false){
return false;
}
base.paginationWrapper.find(".owl-page").each(function (){
if($(this).data("owl-roundPages")===$(base.$owlItems[base.currentItem]).data("owl-roundPages")){
base.paginationWrapper
.find(".owl-page")
.removeClass("active");
$(this).addClass("active");
}});
},
checkNavigation:function (){
var base=this;
if(base.options.navigation===false){
return false;
}
if(base.options.rewindNav===false){
if(base.currentItem===0&&base.maximumItem===0){
base.buttonPrev.addClass("disabled");
base.buttonNext.addClass("disabled");
}else if(base.currentItem===0&&base.maximumItem!==0){
base.buttonPrev.addClass("disabled");
base.buttonNext.removeClass("disabled");
}else if(base.currentItem===base.maximumItem){
base.buttonPrev.removeClass("disabled");
base.buttonNext.addClass("disabled");
}else if(base.currentItem!==0&&base.currentItem!==base.maximumItem){
base.buttonPrev.removeClass("disabled");
base.buttonNext.removeClass("disabled");
}}
},
updateControls:function (){
var base=this;
base.updatePagination();
base.checkNavigation();
if(base.owlControls){
if(base.options.items >=base.itemsAmount){
base.owlControls.hide();
}else{
base.owlControls.show();
}}
},
destroyControls:function (){
var base=this;
if(base.owlControls){
base.owlControls.remove();
}},
next:function (speed){
var base=this;
if(base.isTransition){
return false;
}
base.currentItem +=base.options.scrollPerPage===true ? base.options.items:1;
if(base.currentItem > base.maximumItem + (base.options.scrollPerPage===true ? (base.options.items - 1):0)){
if(base.options.rewindNav===true){
base.currentItem=0;
speed="rewind";
}else{
base.currentItem=base.maximumItem;
return false;
}}
base.goTo(base.currentItem, speed);
},
prev:function (speed){
var base=this;
if(base.isTransition){
return false;
}
if(base.options.scrollPerPage===true&&base.currentItem > 0&&base.currentItem < base.options.items){
base.currentItem=0;
}else{
base.currentItem -=base.options.scrollPerPage===true ? base.options.items:1;
}
if(base.currentItem < 0){
if(base.options.rewindNav===true){
base.currentItem=base.maximumItem;
speed="rewind";
}else{
base.currentItem=0;
return false;
}}
base.goTo(base.currentItem, speed);
},
goTo:function (position, speed, drag){
var base=this,
goToPixel;
if(base.isTransition){
return false;
}
if(typeof base.options.beforeMove==="function"){
base.options.beforeMove.apply(this, [base.$elem]);
}
if(position >=base.maximumItem){
position=base.maximumItem;
}else if(position <=0){
position=0;
}
base.currentItem=base.owl.currentItem=position;
if(base.options.transitionStyle!==false&&drag!=="drag"&&base.options.items===1&&base.browser.support3d===true){
base.swapSpeed(0);
if(base.browser.support3d===true){
base.transition3d(base.positionsInArray[position]);
}else{
base.css2slide(base.positionsInArray[position], 1);
}
base.afterGo();
base.singleItemTransition();
return false;
}
goToPixel=base.positionsInArray[position];
if(base.browser.support3d===true){
base.isCss3Finish=false;
if(speed===true){
base.swapSpeed("paginationSpeed");
window.setTimeout(function (){
base.isCss3Finish=true;
}, base.options.paginationSpeed);
}else if(speed==="rewind"){
base.swapSpeed(base.options.rewindSpeed);
window.setTimeout(function (){
base.isCss3Finish=true;
}, base.options.rewindSpeed);
}else{
base.swapSpeed("slideSpeed");
window.setTimeout(function (){
base.isCss3Finish=true;
}, base.options.slideSpeed);
}
base.transition3d(goToPixel);
}else{
if(speed===true){
base.css2slide(goToPixel, base.options.paginationSpeed);
}else if(speed==="rewind"){
base.css2slide(goToPixel, base.options.rewindSpeed);
}else{
base.css2slide(goToPixel, base.options.slideSpeed);
}}
base.afterGo();
},
jumpTo:function (position){
var base=this;
if(typeof base.options.beforeMove==="function"){
base.options.beforeMove.apply(this, [base.$elem]);
}
if(position >=base.maximumItem||position===-1){
position=base.maximumItem;
}else if(position <=0){
position=0;
}
base.swapSpeed(0);
if(base.browser.support3d===true){
base.transition3d(base.positionsInArray[position]);
}else{
base.css2slide(base.positionsInArray[position], 1);
}
base.currentItem=base.owl.currentItem=position;
base.afterGo();
},
afterGo:function (){
var base=this;
base.prevArr.push(base.currentItem);
base.prevItem=base.owl.prevItem=base.prevArr[base.prevArr.length - 2];
base.prevArr.shift(0);
if(base.prevItem!==base.currentItem){
base.checkPagination();
base.checkNavigation();
base.eachMoveUpdate();
if(base.options.autoPlay!==false){
base.checkAp();
}}
if(typeof base.options.afterMove==="function"&&base.prevItem!==base.currentItem){
base.options.afterMove.apply(this, [base.$elem]);
}},
stop:function (){
var base=this;
base.apStatus="stop";
window.clearInterval(base.autoPlayInterval);
},
checkAp:function (){
var base=this;
if(base.apStatus!=="stop"){
base.play();
}},
play:function (){
var base=this;
base.apStatus="play";
if(base.options.autoPlay===false){
return false;
}
window.clearInterval(base.autoPlayInterval);
base.autoPlayInterval=window.setInterval(function (){
base.next(true);
}, base.options.autoPlay);
},
swapSpeed:function (action){
var base=this;
if(action==="slideSpeed"){
base.$owlWrapper.css(base.addCssSpeed(base.options.slideSpeed));
}else if(action==="paginationSpeed"){
base.$owlWrapper.css(base.addCssSpeed(base.options.paginationSpeed));
}else if(typeof action!=="string"){
base.$owlWrapper.css(base.addCssSpeed(action));
}},
addCssSpeed:function (speed){
return {
"-webkit-transition": "all " + speed + "ms ease",
"-moz-transition": "all " + speed + "ms ease",
"-o-transition": "all " + speed + "ms ease",
"transition": "all " + speed + "ms ease"
};},
removeTransition:function (){
return {
"-webkit-transition": "",
"-moz-transition": "",
"-o-transition": "",
"transition": ""
};},
doTranslate:function (pixels){
return {
"-webkit-transform": "translate3d(" + pixels + "px, 0px, 0px)",
"-moz-transform": "translate3d(" + pixels + "px, 0px, 0px)",
"-o-transform": "translate3d(" + pixels + "px, 0px, 0px)",
"-ms-transform": "translate3d(" + pixels + "px, 0px, 0px)",
"transform": "translate3d(" + pixels + "px, 0px,0px)"
};},
transition3d:function (value){
var base=this;
base.$owlWrapper.css(base.doTranslate(value));
},
css2move:function (value){
var base=this;
base.$owlWrapper.css({"left":value});
},
css2slide:function (value, speed){
var base=this;
base.isCssFinish=false;
base.$owlWrapper.stop(true, true).animate({
"left":value
}, {
duration:speed||base.options.slideSpeed,
complete:function (){
base.isCssFinish=true;
}});
},
checkBrowser:function (){
var base=this,
translate3D="translate3d(0px, 0px, 0px)",
tempElem=document.createElement("div"),
regex,
asSupport,
support3d,
isTouch;
tempElem.style.cssText="  -moz-transform:" + translate3D +
"; -ms-transform:"     + translate3D +
"; -o-transform:"      + translate3D +
"; -webkit-transform:" + translate3D +
"; transform:"         + translate3D;
regex=/translate3d\(0px, 0px, 0px\)/g;
asSupport=tempElem.style.cssText.match(regex);
support3d=(asSupport!==null&&asSupport.length===1);
isTouch="ontouchstart" in window||window.navigator.msMaxTouchPoints;
base.browser={
"support3d":support3d,
"isTouch":isTouch
};},
moveEvents:function (){
var base=this;
if(base.options.mouseDrag!==false||base.options.touchDrag!==false){
base.gestures();
base.disabledEvents();
}},
eventTypes:function (){
var base=this,
types=["s", "e", "x"];
base.ev_types={};
if(base.options.mouseDrag===true&&base.options.touchDrag===true){
types=[
"touchstart.owl mousedown.owl",
"touchmove.owl mousemove.owl",
"touchend.owl touchcancel.owl mouseup.owl"
];
}else if(base.options.mouseDrag===false&&base.options.touchDrag===true){
types=[
"touchstart.owl",
"touchmove.owl",
"touchend.owl touchcancel.owl"
];
}else if(base.options.mouseDrag===true&&base.options.touchDrag===false){
types=[
"mousedown.owl",
"mousemove.owl",
"mouseup.owl"
];
}
base.ev_types.start=types[0];
base.ev_types.move=types[1];
base.ev_types.end=types[2];
},
disabledEvents:function (){
var base=this;
base.$elem.on("dragstart.owl", function (event){ event.preventDefault(); });
base.$elem.on("mousedown.disableTextSelect", function (e){
return $(e.target).is('input, textarea, select, option');
});
},
gestures:function (){
var base=this,
locals={
offsetX:0,
offsetY:0,
baseElWidth:0,
relativePos:0,
position: null,
minSwipe:null,
maxSwipe: null,
sliding:null,
dargging: null,
targetElement:null
};
base.isCssFinish=true;
function getTouches(event){
if(event.touches!==undefined){
return {
x:event.touches[0].pageX,
y:event.touches[0].pageY
};}
if(event.touches===undefined){
if(event.pageX!==undefined){
return {
x:event.pageX,
y:event.pageY
};}
if(event.pageX===undefined){
return {
x:event.clientX,
y:event.clientY
};}}
}
function swapEvents(type){
if(type==="on"){
$(document).on(base.ev_types.move, dragMove);
$(document).on(base.ev_types.end, dragEnd);
}else if(type==="off"){
$(document).off(base.ev_types.move);
$(document).off(base.ev_types.end);
}}
function dragStart(event){
var ev=event.originalEvent||event||window.event,
position;
if(ev.which===3){
return false;
}
if(base.itemsAmount <=base.options.items){
return;
}
if(base.isCssFinish===false&&!base.options.dragBeforeAnimFinish){
return false;
}
if(base.isCss3Finish===false&&!base.options.dragBeforeAnimFinish){
return false;
}
if(base.options.autoPlay!==false){
window.clearInterval(base.autoPlayInterval);
}
if(base.browser.isTouch!==true&&!base.$owlWrapper.hasClass("grabbing")){
base.$owlWrapper.addClass("grabbing");
}
base.newPosX=0;
base.newRelativeX=0;
$(this).css(base.removeTransition());
position=$(this).position();
locals.relativePos=position.left;
locals.offsetX=getTouches(ev).x - position.left;
locals.offsetY=getTouches(ev).y - position.top;
swapEvents("on");
locals.sliding=false;
locals.targetElement=ev.target||ev.srcElement;
}
function dragMove(event){
var ev=event.originalEvent||event||window.event,
minSwipe,
maxSwipe;
base.newPosX=getTouches(ev).x - locals.offsetX;
base.newPosY=getTouches(ev).y - locals.offsetY;
base.newRelativeX=base.newPosX - locals.relativePos;
if(typeof base.options.startDragging==="function"&&locals.dragging!==true&&base.newRelativeX!==0){
locals.dragging=true;
base.options.startDragging.apply(base, [base.$elem]);
}
if((base.newRelativeX > 8||base.newRelativeX < -8)&&(base.browser.isTouch===true)){
if(ev.preventDefault!==undefined){
ev.preventDefault();
}else{
ev.returnValue=false;
}
locals.sliding=true;
}
if((base.newPosY > 10||base.newPosY < -10)&&locals.sliding===false){
$(document).off("touchmove.owl");
}
minSwipe=function (){
return base.newRelativeX / 5;
};
maxSwipe=function (){
return base.maximumPixels + base.newRelativeX / 5;
};
base.newPosX=Math.max(Math.min(base.newPosX, minSwipe()), maxSwipe());
if(base.browser.support3d===true){
base.transition3d(base.newPosX);
}else{
base.css2move(base.newPosX);
}}
function dragEnd(event){
var ev=event.originalEvent||event||window.event,
newPosition,
handlers,
owlStopEvent;
ev.target=ev.target||ev.srcElement;
locals.dragging=false;
if(base.browser.isTouch!==true){
base.$owlWrapper.removeClass("grabbing");
}
if(base.newRelativeX < 0){
base.dragDirection=base.owl.dragDirection="left";
}else{
base.dragDirection=base.owl.dragDirection="right";
}
if(base.newRelativeX!==0){
newPosition=base.getNewPosition();
base.goTo(newPosition, false, "drag");
if(locals.targetElement===ev.target&&base.browser.isTouch!==true){
$(ev.target).on("click.disable", function (ev){
ev.stopImmediatePropagation();
ev.stopPropagation();
ev.preventDefault();
$(ev.target).off("click.disable");
});
handlers=$._data(ev.target, "events").click;
owlStopEvent=handlers.pop();
handlers.splice(0, 0, owlStopEvent);
}}
swapEvents("off");
}
base.$elem.on(base.ev_types.start, ".owl-wrapper", dragStart);
},
getNewPosition:function (){
var base=this,
newPosition=base.closestItem();
if(newPosition > base.maximumItem){
base.currentItem=base.maximumItem;
newPosition=base.maximumItem;
}else if(base.newPosX >=0){
newPosition=0;
base.currentItem=0;
}
return newPosition;
},
closestItem:function (){
var base=this,
array=base.options.scrollPerPage===true ? base.pagesInArray:base.positionsInArray,
goal=base.newPosX,
closest=null;
$.each(array, function (i, v){
if(goal - (base.itemWidth / 20) > array[i + 1]&&goal - (base.itemWidth / 20) < v&&base.moveDirection()==="left"){
closest=v;
if(base.options.scrollPerPage===true){
base.currentItem=$.inArray(closest, base.positionsInArray);
}else{
base.currentItem=i;
}}else if(goal + (base.itemWidth / 20) < v&&goal + (base.itemWidth / 20) > (array[i + 1]||array[i] - base.itemWidth)&&base.moveDirection()==="right"){
if(base.options.scrollPerPage===true){
closest=array[i + 1]||array[array.length - 1];
base.currentItem=$.inArray(closest, base.positionsInArray);
}else{
closest=array[i + 1];
base.currentItem=i + 1;
}}
});
return base.currentItem;
},
moveDirection:function (){
var base=this,
direction;
if(base.newRelativeX < 0){
direction="right";
base.playDirection="next";
}else{
direction="left";
base.playDirection="prev";
}
return direction;
},
customEvents:function (){
var base=this;
base.$elem.on("owl.next", function (){
base.next();
});
base.$elem.on("owl.prev", function (){
base.prev();
});
base.$elem.on("owl.play", function (event, speed){
base.options.autoPlay=speed;
base.play();
base.hoverStatus="play";
});
base.$elem.on("owl.stop", function (){
base.stop();
base.hoverStatus="stop";
});
base.$elem.on("owl.goTo", function (event, item){
base.goTo(item);
});
base.$elem.on("owl.jumpTo", function (event, item){
base.jumpTo(item);
});
},
stopOnHover:function (){
var base=this;
if(base.options.stopOnHover===true&&base.browser.isTouch!==true&&base.options.autoPlay!==false){
base.$elem.on("mouseover", function (){
base.stop();
});
base.$elem.on("mouseout", function (){
if(base.hoverStatus!=="stop"){
base.play();
}});
}},
lazyLoad:function (){
var base=this,
i,
$item,
itemNumber,
$lazyImg,
follow;
if(base.options.lazyLoad===false){
return false;
}
for (i=0; i < base.itemsAmount; i +=1){
$item=$(base.$owlItems[i]);
if($item.data("owl-loaded")==="loaded"){
continue;
}
itemNumber=$item.data("owl-item");
$lazyImg=$item.find(".lazyOwl");
if(typeof $lazyImg.data("src")!=="string"){
$item.data("owl-loaded", "loaded");
continue;
}
if($item.data("owl-loaded")===undefined){
$lazyImg.hide();
$item.addClass("loading").data("owl-loaded", "checked");
}
if(base.options.lazyFollow===true){
follow=itemNumber >=base.currentItem;
}else{
follow=true;
}
if(follow&&itemNumber < base.currentItem + base.options.items&&$lazyImg.length){
base.lazyPreload($item, $lazyImg);
}}
},
lazyPreload:function ($item, $lazyImg){
var base=this,
iterations=0,
isBackgroundImg;
if($lazyImg.prop("tagName")==="DIV"){
$lazyImg.css("background-image", "url(" + $lazyImg.data("src") + ")");
isBackgroundImg=true;
}else{
$lazyImg[0].src=$lazyImg.data("src");
}
function showImage(){
$item.data("owl-loaded", "loaded").removeClass("loading");
$lazyImg.removeAttr("data-src");
if(base.options.lazyEffect==="fade"){
$lazyImg.fadeIn(400);
}else{
$lazyImg.show();
}
if(typeof base.options.afterLazyLoad==="function"){
base.options.afterLazyLoad.apply(this, [base.$elem]);
}}
function checkLazyImage(){
iterations +=1;
if(base.completeImg($lazyImg.get(0))||isBackgroundImg===true){
showImage();
}else if(iterations <=100){
window.setTimeout(checkLazyImage, 100);
}else{
showImage();
}}
checkLazyImage();
},
autoHeight:function (){
var base=this,
$currentimg=$(base.$owlItems[base.currentItem]).find("img"),
iterations;
function addHeight(){
var $currentItem=$(base.$owlItems[base.currentItem]).height();
base.wrapperOuter.css("height", $currentItem + "px");
if(!base.wrapperOuter.hasClass("autoHeight")){
window.setTimeout(function (){
base.wrapperOuter.addClass("autoHeight");
}, 0);
}}
function checkImage(){
iterations +=1;
if(base.completeImg($currentimg.get(0))){
addHeight();
}else if(iterations <=100){
window.setTimeout(checkImage, 100);
}else{
base.wrapperOuter.css("height", "");
}}
if($currentimg.get(0)!==undefined){
iterations=0;
checkImage();
}else{
addHeight();
}},
completeImg:function (img){
var naturalWidthType;
if(!img.complete){
return false;
}
naturalWidthType=typeof img.naturalWidth;
if(naturalWidthType!=="undefined"&&img.naturalWidth===0){
return false;
}
return true;
},
onVisibleItems:function (){
var base=this,
i;
if(base.options.addClassActive===true){
base.$owlItems.removeClass("active");
}
base.visibleItems=[];
for (i=base.currentItem; i < base.currentItem + base.options.items; i +=1){
base.visibleItems.push(i);
if(base.options.addClassActive===true){
$(base.$owlItems[i]).addClass("active");
}}
base.owl.visibleItems=base.visibleItems;
},
transitionTypes:function (className){
var base=this;
base.outClass="owl-" + className + "-out";
base.inClass="owl-" + className + "-in";
},
singleItemTransition:function (){
var base=this,
outClass=base.outClass,
inClass=base.inClass,
$currentItem=base.$owlItems.eq(base.currentItem),
$prevItem=base.$owlItems.eq(base.prevItem),
prevPos=Math.abs(base.positionsInArray[base.currentItem]) + base.positionsInArray[base.prevItem],
origin=Math.abs(base.positionsInArray[base.currentItem]) + base.itemWidth / 2,
animEnd='webkitAnimationEnd oAnimationEnd MSAnimationEnd animationend';
base.isTransition=true;
base.$owlWrapper
.addClass('owl-origin')
.css({
"-webkit-transform-origin":origin + "px",
"-moz-perspective-origin":origin + "px",
"perspective-origin":origin + "px"
});
function transStyles(prevPos){
return {
"position":"relative",
"left":prevPos + "px"
};}
$prevItem
.css(transStyles(prevPos, 10))
.addClass(outClass)
.on(animEnd, function (){
base.endPrev=true;
$prevItem.off(animEnd);
base.clearTransStyle($prevItem, outClass);
});
$currentItem
.addClass(inClass)
.on(animEnd, function (){
base.endCurrent=true;
$currentItem.off(animEnd);
base.clearTransStyle($currentItem, inClass);
});
},
clearTransStyle:function (item, classToRemove){
var base=this;
item.css({
"position":"",
"left":""
}).removeClass(classToRemove);
if(base.endPrev&&base.endCurrent){
base.$owlWrapper.removeClass('owl-origin');
base.endPrev=false;
base.endCurrent=false;
base.isTransition=false;
}},
owlStatus:function (){
var base=this;
base.owl={
"userOptions":base.userOptions,
"baseElement":base.$elem,
"userItems":base.$userItems,
"owlItems":base.$owlItems,
"currentItem":base.currentItem,
"prevItem":base.prevItem,
"visibleItems":base.visibleItems,
"isTouch":base.browser.isTouch,
"browser":base.browser,
"dragDirection":base.dragDirection
};},
clearEvents:function (){
var base=this;
base.$elem.off(".owl owl mousedown.disableTextSelect");
$(document).off(".owl owl");
$(window).off("resize", base.resizer);
},
unWrap:function (){
var base=this;
if(base.$elem.children().length!==0){
base.$owlWrapper.unwrap();
base.$userItems.unwrap().unwrap();
if(base.owlControls){
base.owlControls.remove();
}}
base.clearEvents();
base.$elem
.attr("style", base.$elem.data("owl-originalStyles")||"")
.attr("class", base.$elem.data("owl-originalClasses"));
},
destroy:function (){
var base=this;
base.stop();
window.clearInterval(base.checkVisible);
base.unWrap();
base.$elem.removeData();
},
reinit:function (newOptions){
var base=this,
options=$.extend({}, base.userOptions, newOptions);
base.unWrap();
base.init(options, base.$elem);
},
addItem:function (htmlString, targetPosition){
var base=this,
position;
if(!htmlString){return false; }
if(base.$elem.children().length===0){
base.$elem.append(htmlString);
base.setVars();
return false;
}
base.unWrap();
if(targetPosition===undefined||targetPosition===-1){
position=-1;
}else{
position=targetPosition;
}
if(position >=base.$userItems.length||position===-1){
base.$userItems.eq(-1).after(htmlString);
}else{
base.$userItems.eq(position).before(htmlString);
}
base.setVars();
},
removeItem:function (targetPosition){
var base=this,
position;
if(base.$elem.children().length===0){
return false;
}
if(targetPosition===undefined||targetPosition===-1){
position=-1;
}else{
position=targetPosition;
}
base.unWrap();
base.$userItems.eq(position).remove();
base.setVars();
}};
$.fn.owlCarousel=function (options){
return this.each(function (){
if($(this).data("owl-init")===true){
return false;
}
$(this).data("owl-init", true);
var carousel=Object.create(Carousel);
carousel.init(options, this);
$.data(this, "owlCarousel", carousel);
});
};
$.fn.owlCarousel.options={
items:5,
itemsCustom:false,
itemsDesktop:[1199, 4],
itemsDesktopSmall:[979, 3],
itemsTablet:[768, 2],
itemsTabletSmall:false,
itemsMobile:[479, 1],
singleItem:false,
itemsScaleUp:false,
slideSpeed:200,
paginationSpeed:800,
rewindSpeed:1000,
autoPlay:false,
stopOnHover:false,
navigation:false,
navigationText:["prev", "next"],
rewindNav:true,
scrollPerPage:false,
pagination:true,
paginationNumbers:false,
responsive:true,
responsiveRefreshRate:200,
responsiveBaseWidth:window,
baseClass:"owl-carousel",
theme:"owl-theme",
lazyLoad:false,
lazyFollow:true,
lazyEffect:"fade",
autoHeight:false,
jsonPath:false,
jsonSuccess:false,
dragBeforeAnimFinish:true,
mouseDrag:true,
touchDrag:true,
addClassActive:false,
transitionStyle:false,
beforeUpdate:false,
afterUpdate:false,
beforeInit:false,
afterInit:false,
beforeMove:false,
afterMove:false,
afterAction:false,
startDragging:false,
afterLazyLoad: false
};}(jQuery, window, document));
(function($){
$.jCarouselLite={
version: '1.1'
};
$.fn.jCarouselLite=function(options){
options=$.extend({}, $.fn.jCarouselLite.options, options||{});
return this.each(function(){
var running,
animCss, sizeCss,
div=$(this), ul, initialLi, li,
liSize, ulSize, divSize,
numVisible, initialItemLength, itemLength, calculatedTo, autoTimeout;
initVariables();
initStyles();
initSizes();
attachEventHandlers();
function go(to){
if(!running){
clearTimeout(autoTimeout);
calculatedTo=to;
if(options.beforeStart){
options.beforeStart.call(this, visibleItems());
}
if(options.circular){
adjustOobForCircular(to);
}else{
adjustOobForNonCircular(to);
}
animateToPosition({
start: function(){
running=true;
},
done: function(){
if(options.afterEnd){
options.afterEnd.call(this, visibleItems());
}
if(options.auto){
setupAutoScroll();
}
running=false;
}});
if(!options.circular){
disableOrEnableButtons();
}}
return false;
}
function initVariables(){
running=false;
animCss=options.vertical ? "top":"left";
sizeCss=options.vertical ? "height":"width";
ul=div.find(">ul");
initialLi=ul.find(">li");
initialItemLength=initialLi.size();
numVisible=initialItemLength < options.visible ? initialItemLength:options.visible;
if(options.circular){
var $lastItemSet=initialLi.slice(initialItemLength-numVisible).clone();
var $firstItemSet=initialLi.slice(0,numVisible).clone();
ul.prepend($lastItemSet)
.append($firstItemSet);
options.start +=numVisible;
}
li=$("li", ul);
itemLength=li.size();
calculatedTo=options.start;
}
function initStyles(){
div.css("visibility", "visible");
li.css({
overflow: "hidden",
"float": options.vertical ? "none":"left"
});
ul.css({
margin: "0",
padding: "0",
position: "relative",
"list-style": "none",
"z-index": "1"
});
div.css({
overflow: "hidden",
position: "relative",
"z-index": "2",
left: "0px"
});
if(!options.circular&&options.btnPrev&&options.start==0){
$(options.btnPrev).addClass("disabled");
}}
function initSizes(){
liSize=options.vertical ?
li.outerHeight(true) :
li.outerWidth(true);
ulSize=liSize * itemLength;
divSize=liSize * numVisible;
li.css({
width: li.width(),
height: li.height()
});
ul.css(sizeCss, ulSize+"px")
.css(animCss, -(calculatedTo * liSize));
div.css(sizeCss, divSize+"px");
}
function attachEventHandlers(){
if(options.btnPrev){
$(options.btnPrev).click(function(){
return go(calculatedTo - options.scroll);
});
}
if(options.btnNext){
$(options.btnNext).click(function(){
return go(calculatedTo + options.scroll);
});
}
if(options.btnGo){
$.each(options.btnGo, function(i, val){
$(val).click(function(){
return go(options.circular ? numVisible + i:i);
});
});
}
if(options.mouseWheel&&div.mousewheel){
div.mousewheel(function(e, d){
return d > 0 ?
go(calculatedTo - options.scroll) :
go(calculatedTo + options.scroll);
});
}
if(options.auto){
setupAutoScroll();
}}
function setupAutoScroll(){
autoTimeout=setTimeout(function(){
go(calculatedTo + options.scroll);
}, options.auto);
}
function visibleItems(){
return li.slice(calculatedTo).slice(0,numVisible);
}
function adjustOobForCircular(to){
var newPosition;
if(to <=options.start - numVisible - 1){
newPosition=to + initialItemLength + options.scroll;
ul.css(animCss, -(newPosition * liSize) + "px");
calculatedTo=newPosition - options.scroll;
console.log("Before - Positioned at: " + newPosition + " and Moving to: " + calculatedTo);
}
else if(to >=itemLength - numVisible + 1){
newPosition=to - initialItemLength - options.scroll;
ul.css(animCss, -(newPosition * liSize) + "px");
calculatedTo=newPosition + options.scroll;
console.log("After - Positioned at: " + newPosition + " and Moving to: " + calculatedTo);
}}
function adjustOobForNonCircular(to){
if(to < 0){
calculatedTo=0;
}
else if(to > itemLength - numVisible){
calculatedTo=itemLength - numVisible;
}
console.log("Item Length: " + itemLength + "; " +
"To: " + to + "; " +
"CalculatedTo: " + calculatedTo + "; " +
"Num Visible: " + numVisible);
}
function disableOrEnableButtons(){
$(options.btnPrev + "," + options.btnNext).removeClass("disabled");
$((calculatedTo-options.scroll<0&&options.btnPrev)
||
(calculatedTo+options.scroll > itemLength-numVisible&&options.btnNext)
||
[]
).addClass("disabled");
}
function animateToPosition(animationOptions){
running=true;
ul.animate(animCss=="left" ?
{ left: -(calculatedTo*liSize) } :
{ top: -(calculatedTo*liSize) },
$.extend({
duration: options.speed,
easing: options.easing
}, animationOptions)
);
}});
};
$.fn.jCarouselLite.options={
btnPrev: null,
btnNext: null,
btnGo: null,
mouseWheel: false,
auto: null,
speed: 200,
easing: null,
vertical: false,
circular: true,
visible: 3,
start: 0,
scroll: 1,
beforeStart: null,
afterEnd: null
};})(jQuery);
(function($){
var methods={
init:function(options){
var defaults={
set_width:false, 
set_height:false, 
horizontalScroll:false, 
scrollInertia:950, 
mouseWheel:true, 
mouseWheelPixels:"auto", 
autoDraggerLength:true, 
autoHideScrollbar:false, 
alwaysShowScrollbar:false, 
snapAmount:null, 
snapOffset:0, 
scrollButtons:{ 
enable:false, 
scrollType:"continuous", 
scrollSpeed:"auto", 
scrollAmount:40 
},
advanced:{
updateOnBrowserResize:true, 
updateOnContentResize:false, 
autoExpandHorizontalScroll:false, 
autoScrollOnFocus:true, 
normalizeMouseWheelDelta:false 
},
contentTouchScroll:true, 
callbacks:{
onScrollStart:function(){}, 
onScroll:function(){}, 
onTotalScroll:function(){}, 
onTotalScrollBack:function(){}, 
onTotalScrollOffset:0, 
onTotalScrollBackOffset:0, 
whileScrolling:function(){} 
},
theme:"light" 
},
options=$.extend(true,defaults,options);
return this.each(function(){
var $this=$(this);
if(options.set_width){
$this.css("width",options.set_width);
}
if(options.set_height){
$this.css("height",options.set_height);
}
if(!$(document).data("mCustomScrollbar-index")){
$(document).data("mCustomScrollbar-index","1");
}else{
var mCustomScrollbarIndex=parseInt($(document).data("mCustomScrollbar-index"));
$(document).data("mCustomScrollbar-index",mCustomScrollbarIndex+1);
}
$this.wrapInner("<div class='mCustomScrollBox"+" mCS-"+options.theme+"' id='mCSB_"+$(document).data("mCustomScrollbar-index")+"' style='position:relative; height:100%; overflow:hidden; max-width:100%;' />").addClass("mCustomScrollbar _mCS_"+$(document).data("mCustomScrollbar-index"));
var mCustomScrollBox=$this.children(".mCustomScrollBox");
if(options.horizontalScroll){
mCustomScrollBox.addClass("mCSB_horizontal").wrapInner("<div class='mCSB_h_wrapper' style='position:relative; left:0; width:999999px;' />");
var mCSB_h_wrapper=mCustomScrollBox.children(".mCSB_h_wrapper");
mCSB_h_wrapper.wrapInner("<div class='mCSB_container' style='position:absolute; left:0;' />").children(".mCSB_container").css({"width":mCSB_h_wrapper.children().outerWidth(),"position":"relative"}).unwrap();
}else{
mCustomScrollBox.wrapInner("<div class='mCSB_container' style='position:relative; top:0;' />");
}
var mCSB_container=mCustomScrollBox.children(".mCSB_container");
if($.support.touch){
mCSB_container.addClass("mCS_touch");
}
mCSB_container.after("<div class='mCSB_scrollTools' style='position:absolute;'><div class='mCSB_draggerContainer'><div class='mCSB_dragger' style='position:absolute;' oncontextmenu='return false;'><div class='mCSB_dragger_bar' style='position:relative;'></div></div><div class='mCSB_draggerRail'></div></div></div>");
var mCSB_scrollTools=mCustomScrollBox.children(".mCSB_scrollTools"),
mCSB_draggerContainer=mCSB_scrollTools.children(".mCSB_draggerContainer"),
mCSB_dragger=mCSB_draggerContainer.children(".mCSB_dragger");
if(options.horizontalScroll){
mCSB_dragger.data("minDraggerWidth",mCSB_dragger.width());
}else{
mCSB_dragger.data("minDraggerHeight",mCSB_dragger.height());
}
if(options.scrollButtons.enable){
if(options.horizontalScroll){
mCSB_scrollTools.prepend("<a class='mCSB_buttonLeft' oncontextmenu='return false;'></a>").append("<a class='mCSB_buttonRight' oncontextmenu='return false;'></a>");
}else{
mCSB_scrollTools.prepend("<a class='mCSB_buttonUp' oncontextmenu='return false;'></a>").append("<a class='mCSB_buttonDown' oncontextmenu='return false;'></a>");
}}
mCustomScrollBox.bind("scroll",function(){
if(!$this.is(".mCS_disabled")){ 
mCustomScrollBox.scrollTop(0).scrollLeft(0);
}});
$this.data({
"mCS_Init":true,
"mCustomScrollbarIndex":$(document).data("mCustomScrollbar-index"),
"horizontalScroll":options.horizontalScroll,
"scrollInertia":options.scrollInertia,
"scrollEasing":"mcsEaseOut",
"mouseWheel":options.mouseWheel,
"mouseWheelPixels":options.mouseWheelPixels,
"autoDraggerLength":options.autoDraggerLength,
"autoHideScrollbar":options.autoHideScrollbar,
"alwaysShowScrollbar":options.alwaysShowScrollbar,
"snapAmount":options.snapAmount,
"snapOffset":options.snapOffset,
"scrollButtons_enable":options.scrollButtons.enable,
"scrollButtons_scrollType":options.scrollButtons.scrollType,
"scrollButtons_scrollSpeed":options.scrollButtons.scrollSpeed,
"scrollButtons_scrollAmount":options.scrollButtons.scrollAmount,
"autoExpandHorizontalScroll":options.advanced.autoExpandHorizontalScroll,
"autoScrollOnFocus":options.advanced.autoScrollOnFocus,
"normalizeMouseWheelDelta":options.advanced.normalizeMouseWheelDelta,
"contentTouchScroll":options.contentTouchScroll,
"onScrollStart_Callback":options.callbacks.onScrollStart,
"onScroll_Callback":options.callbacks.onScroll,
"onTotalScroll_Callback":options.callbacks.onTotalScroll,
"onTotalScrollBack_Callback":options.callbacks.onTotalScrollBack,
"onTotalScroll_Offset":options.callbacks.onTotalScrollOffset,
"onTotalScrollBack_Offset":options.callbacks.onTotalScrollBackOffset,
"whileScrolling_Callback":options.callbacks.whileScrolling,
"bindEvent_scrollbar_drag":false,
"bindEvent_content_touch":false,
"bindEvent_scrollbar_click":false,
"bindEvent_mousewheel":false,
"bindEvent_buttonsContinuous_y":false,
"bindEvent_buttonsContinuous_x":false,
"bindEvent_buttonsPixels_y":false,
"bindEvent_buttonsPixels_x":false,
"bindEvent_focusin":false,
"bindEvent_autoHideScrollbar":false,
"mCSB_buttonScrollRight":false,
"mCSB_buttonScrollLeft":false,
"mCSB_buttonScrollDown":false,
"mCSB_buttonScrollUp":false
});
if(options.horizontalScroll){
if($this.css("max-width")!=="none"){
if(!options.advanced.updateOnContentResize){ 
options.advanced.updateOnContentResize=true;
}}
}else{
if($this.css("max-height")!=="none"){
var percentage=false,maxHeight=parseInt($this.css("max-height"));
if($this.css("max-height").indexOf("%")>=0){
percentage=maxHeight,
maxHeight=$this.parent().height()*percentage/100;
}
$this.css("overflow","hidden");
mCustomScrollBox.css("max-height",maxHeight);
}}
$this.mCustomScrollbar("update");
if(options.advanced.updateOnBrowserResize){
var mCSB_resizeTimeout,currWinWidth=$(window).width(),currWinHeight=$(window).height();
$(window).bind("resize."+$this.data("mCustomScrollbarIndex"),function(){
if(mCSB_resizeTimeout){
clearTimeout(mCSB_resizeTimeout);
}
mCSB_resizeTimeout=setTimeout(function(){
if(!$this.is(".mCS_disabled")&&!$this.is(".mCS_destroyed")){
var winWidth=$(window).width(),winHeight=$(window).height();
if(currWinWidth!==winWidth||currWinHeight!==winHeight){ 
if($this.css("max-height")!=="none"&&percentage){
mCustomScrollBox.css("max-height",$this.parent().height()*percentage/100);
}
$this.mCustomScrollbar("update");
currWinWidth=winWidth; currWinHeight=winHeight;
}}
},150);
});
}
if(options.advanced.updateOnContentResize){
var mCSB_onContentResize;
if(options.horizontalScroll){
var mCSB_containerOldSize=mCSB_container.outerWidth();
}else{
var mCSB_containerOldSize=mCSB_container.outerHeight();
}
mCSB_onContentResize=setInterval(function(){
if(options.horizontalScroll){
if(options.advanced.autoExpandHorizontalScroll){
mCSB_container.css({"position":"absolute","width":"auto"}).wrap("<div class='mCSB_h_wrapper' style='position:relative; left:0; width:999999px;' />").css({"width":mCSB_container.outerWidth(),"position":"relative"}).unwrap();
}
var mCSB_containerNewSize=mCSB_container.outerWidth();
}else{
var mCSB_containerNewSize=mCSB_container.outerHeight();
}
if(mCSB_containerNewSize!=mCSB_containerOldSize){
$this.mCustomScrollbar("update");
mCSB_containerOldSize=mCSB_containerNewSize;
}},300);
}});
},
update:function(){
var $this=$(this),
mCustomScrollBox=$this.children(".mCustomScrollBox"),
mCSB_container=mCustomScrollBox.children(".mCSB_container");
mCSB_container.removeClass("mCS_no_scrollbar");
$this.removeClass("mCS_disabled mCS_destroyed");
mCustomScrollBox.scrollTop(0).scrollLeft(0); 
var mCSB_scrollTools=mCustomScrollBox.children(".mCSB_scrollTools"),
mCSB_draggerContainer=mCSB_scrollTools.children(".mCSB_draggerContainer"),
mCSB_dragger=mCSB_draggerContainer.children(".mCSB_dragger");
if($this.data("horizontalScroll")){
var mCSB_buttonLeft=mCSB_scrollTools.children(".mCSB_buttonLeft"),
mCSB_buttonRight=mCSB_scrollTools.children(".mCSB_buttonRight"),
mCustomScrollBoxW=mCustomScrollBox.width();
if($this.data("autoExpandHorizontalScroll")){
mCSB_container.css({"position":"absolute","width":"auto"}).wrap("<div class='mCSB_h_wrapper' style='position:relative; left:0; width:999999px;' />").css({"width":mCSB_container.outerWidth(),"position":"relative"}).unwrap();
}
var mCSB_containerW=mCSB_container.outerWidth();
}else{
var mCSB_buttonUp=mCSB_scrollTools.children(".mCSB_buttonUp"),
mCSB_buttonDown=mCSB_scrollTools.children(".mCSB_buttonDown"),
mCustomScrollBoxH=mCustomScrollBox.height(),
mCSB_containerH=mCSB_container.outerHeight();
}
if(mCSB_containerH>mCustomScrollBoxH&&!$this.data("horizontalScroll")){ 
mCSB_scrollTools.css("display","block");
var mCSB_draggerContainerH=mCSB_draggerContainer.height();
if($this.data("autoDraggerLength")){
var draggerH=Math.round(mCustomScrollBoxH/mCSB_containerH*mCSB_draggerContainerH),
minDraggerH=mCSB_dragger.data("minDraggerHeight");
if(draggerH<=minDraggerH){ 
mCSB_dragger.css({"height":minDraggerH});
}else if(draggerH>=mCSB_draggerContainerH-10){ 
var mCSB_draggerContainerMaxH=mCSB_draggerContainerH-10;
mCSB_dragger.css({"height":mCSB_draggerContainerMaxH});
}else{
mCSB_dragger.css({"height":draggerH});
}
mCSB_dragger.children(".mCSB_dragger_bar").css({"line-height":mCSB_dragger.height()+"px"});
}
var mCSB_draggerH=mCSB_dragger.height(),
scrollAmount=(mCSB_containerH-mCustomScrollBoxH)/(mCSB_draggerContainerH-mCSB_draggerH);
$this.data("scrollAmount",scrollAmount).mCustomScrollbar("scrolling",mCustomScrollBox,mCSB_container,mCSB_draggerContainer,mCSB_dragger,mCSB_buttonUp,mCSB_buttonDown,mCSB_buttonLeft,mCSB_buttonRight);
var mCSB_containerP=Math.abs(mCSB_container.position().top);
$this.mCustomScrollbar("scrollTo",mCSB_containerP,{scrollInertia:0,trigger:"internal"});
}else if(mCSB_containerW>mCustomScrollBoxW&&$this.data("horizontalScroll")){ 
mCSB_scrollTools.css("display","block");
var mCSB_draggerContainerW=mCSB_draggerContainer.width();
if($this.data("autoDraggerLength")){
var draggerW=Math.round(mCustomScrollBoxW/mCSB_containerW*mCSB_draggerContainerW),
minDraggerW=mCSB_dragger.data("minDraggerWidth");
if(draggerW<=minDraggerW){ 
mCSB_dragger.css({"width":minDraggerW});
}else if(draggerW>=mCSB_draggerContainerW-10){ 
var mCSB_draggerContainerMaxW=mCSB_draggerContainerW-10;
mCSB_dragger.css({"width":mCSB_draggerContainerMaxW});
}else{
mCSB_dragger.css({"width":draggerW});
}}
var mCSB_draggerW=mCSB_dragger.width(),
scrollAmount=(mCSB_containerW-mCustomScrollBoxW)/(mCSB_draggerContainerW-mCSB_draggerW);
$this.data("scrollAmount",scrollAmount).mCustomScrollbar("scrolling",mCustomScrollBox,mCSB_container,mCSB_draggerContainer,mCSB_dragger,mCSB_buttonUp,mCSB_buttonDown,mCSB_buttonLeft,mCSB_buttonRight);
var mCSB_containerP=Math.abs(mCSB_container.position().left);
$this.mCustomScrollbar("scrollTo",mCSB_containerP,{scrollInertia:0,trigger:"internal"});
}else{ 
mCustomScrollBox.unbind("mousewheel focusin");
if($this.data("horizontalScroll")){
mCSB_dragger.add(mCSB_container).css("left",0);
}else{
mCSB_dragger.add(mCSB_container).css("top",0);
}
if($this.data("alwaysShowScrollbar")){
if(!$this.data("horizontalScroll")){ 
mCSB_dragger.css({"height":mCSB_draggerContainer.height()});
}else if($this.data("horizontalScroll")){ 
mCSB_dragger.css({"width":mCSB_draggerContainer.width()});
}}else{
mCSB_scrollTools.css("display","none");
mCSB_container.addClass("mCS_no_scrollbar");
}
$this.data({"bindEvent_mousewheel":false,"bindEvent_focusin":false});
}},
scrolling:function(mCustomScrollBox,mCSB_container,mCSB_draggerContainer,mCSB_dragger,mCSB_buttonUp,mCSB_buttonDown,mCSB_buttonLeft,mCSB_buttonRight){
var $this=$(this);
if(!$this.data("bindEvent_scrollbar_drag")){
var mCSB_draggerDragY,mCSB_draggerDragX,
mCSB_dragger_downEvent,mCSB_dragger_moveEvent,mCSB_dragger_upEvent;
if($.support.pointer){ 
mCSB_dragger_downEvent="pointerdown";
mCSB_dragger_moveEvent="pointermove";
mCSB_dragger_upEvent="pointerup";
}else if($.support.msPointer){ 
mCSB_dragger_downEvent="MSPointerDown";
mCSB_dragger_moveEvent="MSPointerMove";
mCSB_dragger_upEvent="MSPointerUp";
}
if($.support.pointer||$.support.msPointer){ 
mCSB_dragger.bind(mCSB_dragger_downEvent,function(e){
e.preventDefault();
$this.data({"on_drag":true});mCSB_dragger.addClass("mCSB_dragger_onDrag");
var elem=$(this),
elemOffset=elem.offset(),
x=e.originalEvent.pageX-elemOffset.left,
y=e.originalEvent.pageY-elemOffset.top;
if(x<elem.width()&&x>0&&y<elem.height()&&y>0){
mCSB_draggerDragY=y;
mCSB_draggerDragX=x;
}});
$(document).bind(mCSB_dragger_moveEvent+"."+$this.data("mCustomScrollbarIndex"),function(e){
e.preventDefault();
if($this.data("on_drag")){
var elem=mCSB_dragger,
elemOffset=elem.offset(),
x=e.originalEvent.pageX-elemOffset.left,
y=e.originalEvent.pageY-elemOffset.top;
scrollbarDrag(mCSB_draggerDragY,mCSB_draggerDragX,y,x);
}}).bind(mCSB_dragger_upEvent+"."+$this.data("mCustomScrollbarIndex"),function(e){
$this.data({"on_drag":false});mCSB_dragger.removeClass("mCSB_dragger_onDrag");
});
}else{ 
mCSB_dragger.bind("mousedown touchstart",function(e){
e.preventDefault(); e.stopImmediatePropagation();
var	elem=$(this),elemOffset=elem.offset(),x,y;
if(e.type==="touchstart"){
var touch=e.originalEvent.touches[0]||e.originalEvent.changedTouches[0];
x=touch.pageX-elemOffset.left; y=touch.pageY-elemOffset.top;
}else{
$this.data({"on_drag":true});mCSB_dragger.addClass("mCSB_dragger_onDrag");
x=e.pageX-elemOffset.left; y=e.pageY-elemOffset.top;
}
if(x<elem.width()&&x>0&&y<elem.height()&&y>0){
mCSB_draggerDragY=y; mCSB_draggerDragX=x;
}}).bind("touchmove",function(e){
e.preventDefault(); e.stopImmediatePropagation();
var touch=e.originalEvent.touches[0]||e.originalEvent.changedTouches[0],
elem=$(this),
elemOffset=elem.offset(),
x=touch.pageX-elemOffset.left,
y=touch.pageY-elemOffset.top;
scrollbarDrag(mCSB_draggerDragY,mCSB_draggerDragX,y,x);
});
$(document).bind("mousemove."+$this.data("mCustomScrollbarIndex"),function(e){
if($this.data("on_drag")){
var elem=mCSB_dragger,
elemOffset=elem.offset(),
x=e.pageX-elemOffset.left,
y=e.pageY-elemOffset.top;
scrollbarDrag(mCSB_draggerDragY,mCSB_draggerDragX,y,x);
}}).bind("mouseup."+$this.data("mCustomScrollbarIndex"),function(e){
$this.data({"on_drag":false});mCSB_dragger.removeClass("mCSB_dragger_onDrag");
});
}
$this.data({"bindEvent_scrollbar_drag":true});
}
function scrollbarDrag(mCSB_draggerDragY,mCSB_draggerDragX,y,x){
if($this.data("horizontalScroll")){
$this.mCustomScrollbar("scrollTo",(mCSB_dragger.position().left-(mCSB_draggerDragX))+x,{moveDragger:true,trigger:"internal"});
}else{
$this.mCustomScrollbar("scrollTo",(mCSB_dragger.position().top-(mCSB_draggerDragY))+y,{moveDragger:true,trigger:"internal"});
}}
if($.support.touch&&$this.data("contentTouchScroll")){
if(!$this.data("bindEvent_content_touch")){
var touch,
elem,elemOffset,y,x,mCSB_containerTouchY,mCSB_containerTouchX;
mCSB_container.bind("touchstart",function(e){
e.stopImmediatePropagation();
touch=e.originalEvent.touches[0]||e.originalEvent.changedTouches[0];
elem=$(this);
elemOffset=elem.offset();
x=touch.pageX-elemOffset.left;
y=touch.pageY-elemOffset.top;
mCSB_containerTouchY=y;
mCSB_containerTouchX=x;
});
mCSB_container.bind("touchmove",function(e){
e.preventDefault(); e.stopImmediatePropagation();
touch=e.originalEvent.touches[0]||e.originalEvent.changedTouches[0];
elem=$(this).parent();
elemOffset=elem.offset();
x=touch.pageX-elemOffset.left;
y=touch.pageY-elemOffset.top;
if($this.data("horizontalScroll")){
$this.mCustomScrollbar("scrollTo",mCSB_containerTouchX-x,{trigger:"internal"});
}else{
$this.mCustomScrollbar("scrollTo",mCSB_containerTouchY-y,{trigger:"internal"});
}});
}}
if(!$this.data("bindEvent_scrollbar_click")){
mCSB_draggerContainer.bind("click",function(e){
var scrollToPos=(e.pageY-mCSB_draggerContainer.offset().top)*$this.data("scrollAmount"),target=$(e.target);
if($this.data("horizontalScroll")){
scrollToPos=(e.pageX-mCSB_draggerContainer.offset().left)*$this.data("scrollAmount");
}
if(target.hasClass("mCSB_draggerContainer")||target.hasClass("mCSB_draggerRail")){
$this.mCustomScrollbar("scrollTo",scrollToPos,{trigger:"internal",scrollEasing:"draggerRailEase"});
}});
$this.data({"bindEvent_scrollbar_click":true});
}
if($this.data("mouseWheel")){
if(!$this.data("bindEvent_mousewheel")){
mCustomScrollBox.bind("mousewheel",function(e,delta){
var scrollTo,mouseWheelPixels=$this.data("mouseWheelPixels"),absPos=Math.abs(mCSB_container.position().top),
draggerPos=mCSB_dragger.position().top,limit=mCSB_draggerContainer.height()-mCSB_dragger.height();
if($this.data("normalizeMouseWheelDelta")){
if(delta<0){delta=-1;}else{delta=1;}}
if(mouseWheelPixels==="auto"){
mouseWheelPixels=100+Math.round($this.data("scrollAmount")/2);
}
if($this.data("horizontalScroll")){
draggerPos=mCSB_dragger.position().left;
limit=mCSB_draggerContainer.width()-mCSB_dragger.width();
absPos=Math.abs(mCSB_container.position().left);
}
if((delta>0&&draggerPos!==0)||(delta<0&&draggerPos!==limit)){e.preventDefault(); e.stopImmediatePropagation();}
scrollTo=absPos-(delta*mouseWheelPixels);
$this.mCustomScrollbar("scrollTo",scrollTo,{trigger:"internal"});
});
$this.data({"bindEvent_mousewheel":true});
}}
if($this.data("scrollButtons_enable")){
if($this.data("scrollButtons_scrollType")==="pixels"){ 
if($this.data("horizontalScroll")){
mCSB_buttonRight.add(mCSB_buttonLeft).unbind("mousedown touchstart MSPointerDown pointerdown mouseup MSPointerUp pointerup mouseout MSPointerOut pointerout touchend",mCSB_buttonRight_stop,mCSB_buttonLeft_stop);
$this.data({"bindEvent_buttonsContinuous_x":false});
if(!$this.data("bindEvent_buttonsPixels_x")){
mCSB_buttonRight.bind("click",function(e){
e.preventDefault();
PixelsScrollTo(Math.abs(mCSB_container.position().left)+$this.data("scrollButtons_scrollAmount"));
});
mCSB_buttonLeft.bind("click",function(e){
e.preventDefault();
PixelsScrollTo(Math.abs(mCSB_container.position().left)-$this.data("scrollButtons_scrollAmount"));
});
$this.data({"bindEvent_buttonsPixels_x":true});
}}else{
mCSB_buttonDown.add(mCSB_buttonUp).unbind("mousedown touchstart MSPointerDown pointerdown mouseup MSPointerUp pointerup mouseout MSPointerOut pointerout touchend",mCSB_buttonRight_stop,mCSB_buttonLeft_stop);
$this.data({"bindEvent_buttonsContinuous_y":false});
if(!$this.data("bindEvent_buttonsPixels_y")){
mCSB_buttonDown.bind("click",function(e){
e.preventDefault();
PixelsScrollTo(Math.abs(mCSB_container.position().top)+$this.data("scrollButtons_scrollAmount"));
});
mCSB_buttonUp.bind("click",function(e){
e.preventDefault();
PixelsScrollTo(Math.abs(mCSB_container.position().top)-$this.data("scrollButtons_scrollAmount"));
});
$this.data({"bindEvent_buttonsPixels_y":true});
}}
function PixelsScrollTo(to){
if(!mCSB_dragger.data("preventAction")){
mCSB_dragger.data("preventAction",true);
$this.mCustomScrollbar("scrollTo",to,{trigger:"internal"});
}}
}else{ 
if($this.data("horizontalScroll")){
mCSB_buttonRight.add(mCSB_buttonLeft).unbind("click");
$this.data({"bindEvent_buttonsPixels_x":false});
if(!$this.data("bindEvent_buttonsContinuous_x")){
mCSB_buttonRight.bind("mousedown touchstart MSPointerDown pointerdown",function(e){
e.preventDefault();
var scrollButtonsSpeed=ScrollButtonsSpeed();
$this.data({"mCSB_buttonScrollRight":setInterval(function(){
$this.mCustomScrollbar("scrollTo",Math.abs(mCSB_container.position().left)+scrollButtonsSpeed,{trigger:"internal",scrollEasing:"easeOutCirc"});
},17)});
});
var mCSB_buttonRight_stop=function(e){
e.preventDefault(); clearInterval($this.data("mCSB_buttonScrollRight"));
}
mCSB_buttonRight.bind("mouseup touchend MSPointerUp pointerup mouseout MSPointerOut pointerout",mCSB_buttonRight_stop);
mCSB_buttonLeft.bind("mousedown touchstart MSPointerDown pointerdown",function(e){
e.preventDefault();
var scrollButtonsSpeed=ScrollButtonsSpeed();
$this.data({"mCSB_buttonScrollLeft":setInterval(function(){
$this.mCustomScrollbar("scrollTo",Math.abs(mCSB_container.position().left)-scrollButtonsSpeed,{trigger:"internal",scrollEasing:"easeOutCirc"});
},17)});
});
var mCSB_buttonLeft_stop=function(e){
e.preventDefault(); clearInterval($this.data("mCSB_buttonScrollLeft"));
}
mCSB_buttonLeft.bind("mouseup touchend MSPointerUp pointerup mouseout MSPointerOut pointerout",mCSB_buttonLeft_stop);
$this.data({"bindEvent_buttonsContinuous_x":true});
}}else{
mCSB_buttonDown.add(mCSB_buttonUp).unbind("click");
$this.data({"bindEvent_buttonsPixels_y":false});
if(!$this.data("bindEvent_buttonsContinuous_y")){
mCSB_buttonDown.bind("mousedown touchstart MSPointerDown pointerdown",function(e){
e.preventDefault();
var scrollButtonsSpeed=ScrollButtonsSpeed();
$this.data({"mCSB_buttonScrollDown":setInterval(function(){
$this.mCustomScrollbar("scrollTo",Math.abs(mCSB_container.position().top)+scrollButtonsSpeed,{trigger:"internal",scrollEasing:"easeOutCirc"});
},17)});
});
var mCSB_buttonDown_stop=function(e){
e.preventDefault(); clearInterval($this.data("mCSB_buttonScrollDown"));
}
mCSB_buttonDown.bind("mouseup touchend MSPointerUp pointerup mouseout MSPointerOut pointerout",mCSB_buttonDown_stop);
mCSB_buttonUp.bind("mousedown touchstart MSPointerDown pointerdown",function(e){
e.preventDefault();
var scrollButtonsSpeed=ScrollButtonsSpeed();
$this.data({"mCSB_buttonScrollUp":setInterval(function(){
$this.mCustomScrollbar("scrollTo",Math.abs(mCSB_container.position().top)-scrollButtonsSpeed,{trigger:"internal",scrollEasing:"easeOutCirc"});
},17)});
});
var mCSB_buttonUp_stop=function(e){
e.preventDefault(); clearInterval($this.data("mCSB_buttonScrollUp"));
}
mCSB_buttonUp.bind("mouseup touchend MSPointerUp pointerup mouseout MSPointerOut pointerout",mCSB_buttonUp_stop);
$this.data({"bindEvent_buttonsContinuous_y":true});
}}
function ScrollButtonsSpeed(){
var speed=$this.data("scrollButtons_scrollSpeed");
if($this.data("scrollButtons_scrollSpeed")==="auto"){
speed=Math.round(($this.data("scrollInertia")+100)/40);
}
return speed;
}}
}
if($this.data("autoScrollOnFocus")){
if(!$this.data("bindEvent_focusin")){
mCustomScrollBox.bind("focusin",function(){
mCustomScrollBox.scrollTop(0).scrollLeft(0);
var focusedElem=$(document.activeElement);
if(focusedElem.is("input,textarea,select,button,a[tabindex],area,object")){
var mCSB_containerPos=mCSB_container.position().top,
focusedElemPos=focusedElem.position().top,
visibleLimit=mCustomScrollBox.height()-focusedElem.outerHeight();
if($this.data("horizontalScroll")){
mCSB_containerPos=mCSB_container.position().left;
focusedElemPos=focusedElem.position().left;
visibleLimit=mCustomScrollBox.width()-focusedElem.outerWidth();
}
if(mCSB_containerPos+focusedElemPos<0||mCSB_containerPos+focusedElemPos>visibleLimit){
$this.mCustomScrollbar("scrollTo",focusedElemPos,{trigger:"internal"});
}}
});
$this.data({"bindEvent_focusin":true});
}}
if($this.data("autoHideScrollbar")&&!$this.data("alwaysShowScrollbar")){
if(!$this.data("bindEvent_autoHideScrollbar")){
mCustomScrollBox.bind("mouseenter",function(e){
mCustomScrollBox.addClass("mCS-mouse-over");
functions.showScrollbar.call(mCustomScrollBox.children(".mCSB_scrollTools"));
}).bind("mouseleave touchend",function(e){
mCustomScrollBox.removeClass("mCS-mouse-over");
if(e.type==="mouseleave"){functions.hideScrollbar.call(mCustomScrollBox.children(".mCSB_scrollTools"));}});
$this.data({"bindEvent_autoHideScrollbar":true});
}}
},
scrollTo:function(scrollTo,options){
var $this=$(this),
defaults={
moveDragger:false,
trigger:"external",
callbacks:true,
scrollInertia:$this.data("scrollInertia"),
scrollEasing:$this.data("scrollEasing")
},
options=$.extend(defaults,options),
draggerScrollTo,
mCustomScrollBox=$this.children(".mCustomScrollBox"),
mCSB_container=mCustomScrollBox.children(".mCSB_container"),
mCSB_scrollTools=mCustomScrollBox.children(".mCSB_scrollTools"),
mCSB_draggerContainer=mCSB_scrollTools.children(".mCSB_draggerContainer"),
mCSB_dragger=mCSB_draggerContainer.children(".mCSB_dragger"),
contentSpeed=draggerSpeed=options.scrollInertia,
scrollBeginning,scrollBeginningOffset,totalScroll,totalScrollOffset;
if(!mCSB_container.hasClass("mCS_no_scrollbar")){
$this.data({"mCS_trigger":options.trigger});
if($this.data("mCS_Init")){options.callbacks=false;}
if(scrollTo||scrollTo===0){
if(typeof(scrollTo)==="number"){ 
if(options.moveDragger){ 
draggerScrollTo=scrollTo;
if($this.data("horizontalScroll")){
scrollTo=mCSB_dragger.position().left*$this.data("scrollAmount");
}else{
scrollTo=mCSB_dragger.position().top*$this.data("scrollAmount");
}
draggerSpeed=0;
}else{ 
draggerScrollTo=scrollTo/$this.data("scrollAmount");
}}else if(typeof(scrollTo)==="string"){ 
var target;
if(scrollTo==="top"){ 
target=0;
}else if(scrollTo==="bottom"&&!$this.data("horizontalScroll")){ 
target=mCSB_container.outerHeight()-mCustomScrollBox.height();
}else if(scrollTo==="left"){ 
target=0;
}else if(scrollTo==="right"&&$this.data("horizontalScroll")){ 
target=mCSB_container.outerWidth()-mCustomScrollBox.width();
}else if(scrollTo==="first"){ 
target=$this.find(".mCSB_container").find(":first");
}else if(scrollTo==="last"){ 
target=$this.find(".mCSB_container").find(":last");
}else{ 
target=$this.find(scrollTo);
}
if(target.length===1){ 
if($this.data("horizontalScroll")){
scrollTo=target.position().left;
}else{
scrollTo=target.position().top;
}
draggerScrollTo=scrollTo/$this.data("scrollAmount");
}else{
draggerScrollTo=scrollTo=target;
}}
if($this.data("horizontalScroll")){
if($this.data("onTotalScrollBack_Offset")){ 
scrollBeginningOffset=-$this.data("onTotalScrollBack_Offset");
}
if($this.data("onTotalScroll_Offset")){ 
totalScrollOffset=mCustomScrollBox.width()-mCSB_container.outerWidth()+$this.data("onTotalScroll_Offset");
}
if(draggerScrollTo<0){ 
draggerScrollTo=scrollTo=0; clearInterval($this.data("mCSB_buttonScrollLeft"));
if(!scrollBeginningOffset){scrollBeginning=true;}}else if(draggerScrollTo>=mCSB_draggerContainer.width()-mCSB_dragger.width()){ 
draggerScrollTo=mCSB_draggerContainer.width()-mCSB_dragger.width();
scrollTo=mCustomScrollBox.width()-mCSB_container.outerWidth(); clearInterval($this.data("mCSB_buttonScrollRight"));
if(!totalScrollOffset){totalScroll=true;}}else{scrollTo=-scrollTo;}
var snapAmount=$this.data("snapAmount");
if(snapAmount){
scrollTo=Math.round(scrollTo / snapAmount) * snapAmount - $this.data("snapOffset");
}
functions.mTweenAxis.call(this,mCSB_dragger[0],"left",Math.round(draggerScrollTo),draggerSpeed,options.scrollEasing);
functions.mTweenAxis.call(this,mCSB_container[0],"left",Math.round(scrollTo),contentSpeed,options.scrollEasing,{
onStart:function(){
if(options.callbacks&&!$this.data("mCS_tweenRunning")){callbacks("onScrollStart");}
if($this.data("autoHideScrollbar")&&!$this.data("alwaysShowScrollbar")){functions.showScrollbar.call(mCSB_scrollTools);}},
onUpdate:function(){
if(options.callbacks){callbacks("whileScrolling");}},
onComplete:function(){
if(options.callbacks){
callbacks("onScroll");
if(scrollBeginning||(scrollBeginningOffset&&mCSB_container.position().left>=scrollBeginningOffset)){callbacks("onTotalScrollBack");}
if(totalScroll||(totalScrollOffset&&mCSB_container.position().left<=totalScrollOffset)){callbacks("onTotalScroll");}}
mCSB_dragger.data("preventAction",false); $this.data("mCS_tweenRunning",false);
if($this.data("autoHideScrollbar")&&!$this.data("alwaysShowScrollbar")){if(!mCustomScrollBox.hasClass("mCS-mouse-over")){functions.hideScrollbar.call(mCSB_scrollTools);}}}
});
}else{
if($this.data("onTotalScrollBack_Offset")){ 
scrollBeginningOffset=-$this.data("onTotalScrollBack_Offset");
}
if($this.data("onTotalScroll_Offset")){ 
totalScrollOffset=mCustomScrollBox.height()-mCSB_container.outerHeight()+$this.data("onTotalScroll_Offset");
}
if(draggerScrollTo<0){ 
draggerScrollTo=scrollTo=0; clearInterval($this.data("mCSB_buttonScrollUp"));
if(!scrollBeginningOffset){scrollBeginning=true;}}else if(draggerScrollTo>=mCSB_draggerContainer.height()-mCSB_dragger.height()){ 
draggerScrollTo=mCSB_draggerContainer.height()-mCSB_dragger.height();
scrollTo=mCustomScrollBox.height()-mCSB_container.outerHeight(); clearInterval($this.data("mCSB_buttonScrollDown"));
if(!totalScrollOffset){totalScroll=true;}}else{scrollTo=-scrollTo;}
var snapAmount=$this.data("snapAmount");
if(snapAmount){
scrollTo=Math.round(scrollTo / snapAmount) * snapAmount - $this.data("snapOffset");
}
functions.mTweenAxis.call(this,mCSB_dragger[0],"top",Math.round(draggerScrollTo),draggerSpeed,options.scrollEasing);
functions.mTweenAxis.call(this,mCSB_container[0],"top",Math.round(scrollTo),contentSpeed,options.scrollEasing,{
onStart:function(){
if(options.callbacks&&!$this.data("mCS_tweenRunning")){callbacks("onScrollStart");}
if($this.data("autoHideScrollbar")&&!$this.data("alwaysShowScrollbar")){functions.showScrollbar.call(mCSB_scrollTools);}},
onUpdate:function(){
if(options.callbacks){callbacks("whileScrolling");}},
onComplete:function(){
if(options.callbacks){
callbacks("onScroll");
if(scrollBeginning||(scrollBeginningOffset&&mCSB_container.position().top>=scrollBeginningOffset)){callbacks("onTotalScrollBack");}
if(totalScroll||(totalScrollOffset&&mCSB_container.position().top<=totalScrollOffset)){callbacks("onTotalScroll");}}
mCSB_dragger.data("preventAction",false); $this.data("mCS_tweenRunning",false);
if($this.data("autoHideScrollbar")&&!$this.data("alwaysShowScrollbar")){if(!mCustomScrollBox.hasClass("mCS-mouse-over")){functions.hideScrollbar.call(mCSB_scrollTools);}}}
});
}
if($this.data("mCS_Init")){$this.data({"mCS_Init":false});}}
}
function callbacks(cb){
if($this.data("mCustomScrollbarIndex")){
this.mcs={
top: mCSB_container.position().top, left: mCSB_container.position().left,
draggerTop: mCSB_dragger.position().top, draggerLeft: mCSB_dragger.position().left,
topPct: Math.round((100 * Math.abs(mCSB_container.position().top)) / Math.abs(mCSB_container.outerHeight() - mCustomScrollBox.height())),
leftPct: Math.round((100 * Math.abs(mCSB_container.position().left)) / Math.abs(mCSB_container.outerWidth() - mCustomScrollBox.width()))
};
switch (cb){
case "onScrollStart":
$this.data("mCS_tweenRunning", true).data("onScrollStart_Callback").call($this, this.mcs);
break;
case "whileScrolling":
$this.data("whileScrolling_Callback").call($this, this.mcs);
break;
case "onScroll":
$this.data("onScroll_Callback").call($this, this.mcs);
break;
case "onTotalScrollBack":
$this.data("onTotalScrollBack_Callback").call($this, this.mcs);
break;
case "onTotalScroll":
$this.data("onTotalScroll_Callback").call($this, this.mcs);
break;
}}
}},
stop:function(){
var $this=$(this),
mCSB_container=$this.children().children(".mCSB_container"),
mCSB_dragger=$this.children().children().children().children(".mCSB_dragger");
functions.mTweenAxisStop.call(this,mCSB_container[0]);
functions.mTweenAxisStop.call(this,mCSB_dragger[0]);
},
disable:function(resetScroll){
var $this=$(this),
mCustomScrollBox=$this.children(".mCustomScrollBox"),
mCSB_container=mCustomScrollBox.children(".mCSB_container"),
mCSB_scrollTools=mCustomScrollBox.children(".mCSB_scrollTools"),
mCSB_dragger=mCSB_scrollTools.children().children(".mCSB_dragger");
mCustomScrollBox.unbind("mousewheel focusin mouseenter mouseleave touchend");
mCSB_container.unbind("touchstart touchmove")
if(resetScroll){
if($this.data("horizontalScroll")){
mCSB_dragger.add(mCSB_container).css("left",0);
}else{
mCSB_dragger.add(mCSB_container).css("top",0);
}}
mCSB_scrollTools.css("display","none");
mCSB_container.addClass("mCS_no_scrollbar");
$this.data({"bindEvent_mousewheel":false,"bindEvent_focusin":false,"bindEvent_content_touch":false,"bindEvent_autoHideScrollbar":false}).addClass("mCS_disabled");
},
destroy:function(){
var $this=$(this);
$this.removeClass("mCustomScrollbar _mCS_"+$this.data("mCustomScrollbarIndex")).addClass("mCS_destroyed").children().children(".mCSB_container").unwrap().children().unwrap().siblings(".mCSB_scrollTools").remove();
$(document).unbind("mousemove."+$this.data("mCustomScrollbarIndex")+" mouseup."+$this.data("mCustomScrollbarIndex")+" MSPointerMove."+$this.data("mCustomScrollbarIndex")+" MSPointerUp."+$this.data("mCustomScrollbarIndex"));
$(window).unbind("resize."+$this.data("mCustomScrollbarIndex"));
}},
functions={
showScrollbar:function(){
this.stop().animate({opacity:1},"fast");
},
hideScrollbar:function(){
this.stop().animate({opacity:0},"fast");
},
mTweenAxis:function(el,prop,to,duration,easing,callbacks){
var callbacks=callbacks||{},
onStart=callbacks.onStart||function(){},onUpdate=callbacks.onUpdate||function(){},onComplete=callbacks.onComplete||function(){};
var startTime=_getTime(),_delay,progress=0,from=el.offsetTop,elStyle=el.style;
if(prop==="left"){from=el.offsetLeft;}
var diff=to-from;
_cancelTween();
_startTween();
function _getTime(){
if(window.performance&&window.performance.now){
return window.performance.now();
}else{
if(window.performance&&window.performance.webkitNow){
return window.performance.webkitNow();
}else{
if(Date.now){return Date.now();}else{return new Date().getTime();}}
}}
function _step(){
if(!progress){onStart.call();}
progress=_getTime()-startTime;
_tween();
if(progress>=el._time){
el._time=(progress>el._time) ? progress+_delay-(progress- el._time):progress+_delay-1;
if(el._time<progress+1){el._time=progress+1;}}
if(el._time<duration){el._id=_request(_step);}else{onComplete.call();}}
function _tween(){
if(duration>0){
el.currVal=_ease(el._time,from,diff,duration,easing);
elStyle[prop]=Math.round(el.currVal)+"px";
}else{
elStyle[prop]=to+"px";
}
onUpdate.call();
}
function _startTween(){
_delay=1000/60;
el._time=progress+_delay;
_request=(!window.requestAnimationFrame) ? function(f){_tween(); return setTimeout(f,0.01);}:window.requestAnimationFrame;
el._id=_request(_step);
}
function _cancelTween(){
if(el._id==null){return;}
if(!window.requestAnimationFrame){clearTimeout(el._id);
}else{window.cancelAnimationFrame(el._id);}
el._id=null;
}
function _ease(t,b,c,d,type){
switch(type){
case "linear":
return c*t/d + b;
break;
case "easeOutQuad":
t /=d; return -c * t*(t-2) + b;
break;
case "easeInOutQuad":
t /=d/2;
if(t < 1) return c/2*t*t + b;
t--;
return -c/2 * (t*(t-2) - 1) + b;
break;
case "easeOutCubic":
t /=d; t--; return c*(t*t*t + 1) + b;
break;
case "easeOutQuart":
t /=d; t--; return -c * (t*t*t*t - 1) + b;
break;
case "easeOutQuint":
t /=d; t--; return c*(t*t*t*t*t + 1) + b;
break;
case "easeOutCirc":
t /=d; t--; return c * Math.sqrt(1 - t*t) + b;
break;
case "easeOutSine":
return c * Math.sin(t/d * (Math.PI/2)) + b;
break;
case "easeOutExpo":
return c *(-Math.pow(2, -10 * t/d) + 1) + b;
break;
case "mcsEaseOut":
var ts=(t/=d)*t,tc=ts*t;
return b+c*(0.499999999999997*tc*ts + -2.5*ts*ts + 5.5*tc + -6.5*ts + 4*t);
break;
case "draggerRailEase":
t /=d/2;
if(t < 1) return c/2*t*t*t + b;
t -=2;
return c/2*(t*t*t + 2) + b;
break;
}}
},
mTweenAxisStop:function(el){
if(el._id==null){return;}
if(!window.requestAnimationFrame){clearTimeout(el._id);
}else{window.cancelAnimationFrame(el._id);}
el._id=null;
},
rafPolyfill:function(){
var pfx=["ms","moz","webkit","o"],i=pfx.length;
while(--i > -1&&!window.requestAnimationFrame){
window.requestAnimationFrame=window[pfx[i]+"RequestAnimationFrame"];
window.cancelAnimationFrame=window[pfx[i]+"CancelAnimationFrame"]||window[pfx[i]+"CancelRequestAnimationFrame"];
}}
}
functions.rafPolyfill.call(); 
$.support.touch=!!('ontouchstart' in window); 
$.support.pointer=window.navigator.pointerEnabled; 
$.support.msPointer=window.navigator.msPointerEnabled; 
var _dlp=("https:"==document.location.protocol) ? "https:":"http:";
$.event.special.mousewheel||document.write('<script src="https://vuagym.com/wp-content/uploads/jquery.mousewheel.min.js"><\/script>');
$.fn.mCustomScrollbar=function(method){
if(methods[method]){
return methods[method].apply(this,Array.prototype.slice.call(arguments,1));
}else if(typeof method==="object"||!method){
return methods.init.apply(this,arguments);
}else{
$.error("Method "+method+" does not exist");
}};})(jQuery);
if(typeof Object.create!=='function'){
Object.create=function(obj){
function F(){};
F.prototype=obj;
return new F();
};}
(function($, window, document, undefined){
var ElevateZoom={
init: function(options, elem){
var self=this;
self.elem=elem;
self.$elem=$(elem);
self.imageSrc=self.$elem.data("zoom-image") ? self.$elem.data("zoom-image"):self.$elem.attr("src");
self.options=$.extend({}, $.fn.elevateZoom.options, options);
if(self.options.tint){
self.options.lensColour="none",
self.options.lensOpacity="1"
}
if(self.options.zoomType=="inner"){self.options.showLens=false;}
self.$elem.parent().removeAttr('title').removeAttr('alt');
self.zoomImage=self.imageSrc;
self.refresh(1);
$('#'+self.options.gallery + ' a').click(function(e){
if(self.options.galleryActiveClass){
$('#'+self.options.gallery + ' a').removeClass(self.options.galleryActiveClass);
$(this).addClass(self.options.galleryActiveClass);
}
e.preventDefault();
if($(this).data("zoom-image")){self.zoomImagePre=$(this).data("zoom-image")}else{self.zoomImagePre=$(this).data("image");}
self.swaptheimage($(this).data("image"), self.zoomImagePre);
return false;
});
},
refresh: function(length){
var self=this;
setTimeout(function(){
self.fetch(self.imageSrc);
}, length||self.options.refresh);
},
fetch: function(imgsrc){
var self=this;
var newImg=new Image();
newImg.onload=function(){
self.largeWidth=newImg.width;
self.largeHeight=newImg.height;
self.startZoom();
self.currentImage=self.imageSrc;
self.options.onZoomedImageLoaded(self.$elem);
}
newImg.src=imgsrc;
return;
},
startZoom: function(){
var self=this;
self.nzWidth=self.$elem.width();
self.nzHeight=self.$elem.height();
self.isWindowActive=false;
self.isLensActive=false;
self.isTintActive=false;
self.overWindow=false;
if(self.options.imageCrossfade){
self.zoomWrap=self.$elem.wrap('<div style="height:'+self.nzHeight+'px;width:'+self.nzWidth+'px;" class="zoomWrapper" />');
self.$elem.css('position', 'absolute');
}
self.zoomLock=1;
self.scrollingLock=false;
self.changeBgSize=false;
self.currentZoomLevel=self.options.zoomLevel;
self.nzOffset=self.$elem.offset();
self.widthRatio=(self.largeWidth/self.currentZoomLevel) / self.nzWidth;
self.heightRatio=(self.largeHeight/self.currentZoomLevel) / self.nzHeight;
if(self.options.zoomType=="window"){
self.zoomWindowStyle="overflow: hidden;"
+ "background-position: 0px 0px;text-align:center;"
+ "background-color: " + String(self.options.zoomWindowBgColour)
+ ";width: " + String(self.options.zoomWindowWidth) + "px;"
+ "height: " + String(self.options.zoomWindowHeight)
+ "px;float: left;"
+ "background-size: "+ self.largeWidth/self.currentZoomLevel+ "px " +self.largeHeight/self.currentZoomLevel + "px;"
+ "display: none;z-index:100;"
+ "border: " + String(self.options.borderSize)
+ "px solid " + self.options.borderColour
+ ";background-repeat: no-repeat;"
+ "position: absolute;";
}
if(self.options.zoomType=="inner"){
var borderWidth=self.$elem.css("border-left-width");
self.zoomWindowStyle="overflow: hidden;"
+ "margin-left: " + String(borderWidth) + ";"
+ "margin-top: " + String(borderWidth) + ";"
+ "background-position: 0px 0px;"
+ "width: " + String(self.nzWidth) + "px;"
+ "height: " + String(self.nzHeight) + "px;"
+ "px;float: left;"
+ "display: none;"
+ "cursor:"+(self.options.cursor)+";"
+ "px solid " + self.options.borderColour
+ ";background-repeat: no-repeat;"
+ "position: absolute;";
}
if(self.options.zoomType=="window"){
if(self.nzHeight < self.options.zoomWindowWidth/self.widthRatio){
lensHeight=self.nzHeight;
}else{
lensHeight=String((self.options.zoomWindowHeight/self.heightRatio))
}
if(self.largeWidth < self.options.zoomWindowWidth){
lensWidth=self.nzWidth;
}else{
lensWidth=(self.options.zoomWindowWidth/self.widthRatio);
}
self.lensStyle="background-position: 0px 0px;width: " + String((self.options.zoomWindowWidth)/self.widthRatio) + "px;height: " + String((self.options.zoomWindowHeight)/self.heightRatio)
+ "px;float: right;display: none;"
+ "overflow: hidden;"
+ "z-index: 999;"
+ "-webkit-transform: translateZ(0);"
+ "opacity:"+(self.options.lensOpacity)+";filter: alpha(opacity="+(self.options.lensOpacity*100)+"); zoom:1;"
+ "width:"+lensWidth+"px;"
+ "height:"+lensHeight+"px;"
+ "background-color:"+(self.options.lensColour)+";"
+ "cursor:"+(self.options.cursor)+";"
+ "border: "+(self.options.lensBorderSize)+"px" +
" solid "+(self.options.lensBorderColour)+";background-repeat: no-repeat;position: absolute;";
}
self.tintStyle="display: block;"
+ "position: absolute;"
+ "background-color: "+self.options.tintColour+";"
+ "filter:alpha(opacity=0);"
+ "opacity: 0;"
+ "width: " + self.nzWidth + "px;"
+ "height: " + self.nzHeight + "px;"
;
self.lensRound='';
if(self.options.zoomType=="lens"){
self.lensStyle="background-position: 0px 0px;"
+ "float: left;display: none;"
+ "border: " + String(self.options.borderSize) + "px solid " + self.options.borderColour+";"
+ "width:"+ String(self.options.lensSize) +"px;"
+ "height:"+ String(self.options.lensSize)+"px;"
+ "background-repeat: no-repeat;position: absolute;";
}
if(self.options.lensShape=="round"){
self.lensRound="border-top-left-radius: " + String(self.options.lensSize / 2 + self.options.borderSize) + "px;"
+ "border-top-right-radius: " + String(self.options.lensSize / 2 + self.options.borderSize) + "px;"
+ "border-bottom-left-radius: " + String(self.options.lensSize / 2 + self.options.borderSize) + "px;"
+ "border-bottom-right-radius: " + String(self.options.lensSize / 2 + self.options.borderSize) + "px;";
}
self.zoomContainer=$('<div class="zoomContainer" style="-webkit-transform: translateZ(0);position:absolute;left:'+self.nzOffset.left+'px;top:'+self.nzOffset.top+'px;height:'+self.nzHeight+'px;width:'+self.nzWidth+'px;"></div>');
$('body').append(self.zoomContainer);
if(self.options.containLensZoom&&self.options.zoomType=="lens"){
self.zoomContainer.css("overflow", "hidden");
}
if(self.options.zoomType!="inner"){
self.zoomLens=$("<div class='zoomLens' style='" + self.lensStyle + self.lensRound +"'>&nbsp;</div>")
.appendTo(self.zoomContainer)
.click(function (){
self.$elem.trigger('click');
});
if(self.options.tint){
self.tintContainer=$('<div/>').addClass('tintContainer');
self.zoomTint=$("<div class='zoomTint' style='"+self.tintStyle+"'></div>");
self.zoomLens.wrap(self.tintContainer);
self.zoomTintcss=self.zoomLens.after(self.zoomTint);
self.zoomTintImage=$('<img style="position: absolute; left: 0px; top: 0px; max-width: none; width: '+self.nzWidth+'px; height: '+self.nzHeight+'px;" src="'+self.imageSrc+'">')
.appendTo(self.zoomLens)
.click(function (){
self.$elem.trigger('click');
});
}}
if(isNaN(self.options.zoomWindowPosition)){
self.zoomWindow=$("<div style='z-index:999;left:"+(self.windowOffsetLeft)+"px;top:"+(self.windowOffsetTop)+"px;" + self.zoomWindowStyle + "' class='zoomWindow'>&nbsp;</div>")
.appendTo('body')
.click(function (){
self.$elem.trigger('click');
});
}else{
self.zoomWindow=$("<div style='z-index:999;left:"+(self.windowOffsetLeft)+"px;top:"+(self.windowOffsetTop)+"px;" + self.zoomWindowStyle + "' class='zoomWindow'>&nbsp;</div>")
.appendTo(self.zoomContainer)
.click(function (){
self.$elem.trigger('click');
});
}
self.zoomWindowContainer=$('<div/>').addClass('zoomWindowContainer').css("width",self.options.zoomWindowWidth);
self.zoomWindow.wrap(self.zoomWindowContainer);
if(self.options.zoomType=="lens"){
self.zoomLens.css({ backgroundImage: "url('" + self.imageSrc + "')" });
}
if(self.options.zoomType=="window"){
self.zoomWindow.css({ backgroundImage: "url('" + self.imageSrc + "')" });
}
if(self.options.zoomType=="inner"){
self.zoomWindow.css({ backgroundImage: "url('" + self.imageSrc + "')" });
}
self.$elem.bind('touchmove', function(e){
e.preventDefault();
var touch=e.originalEvent.touches[0]||e.originalEvent.changedTouches[0];
self.setPosition(touch);
});
self.zoomContainer.bind('touchmove', function(e){
if(self.options.zoomType=="inner"){
self.showHideWindow("show");
}
e.preventDefault();
var touch=e.originalEvent.touches[0]||e.originalEvent.changedTouches[0];
self.setPosition(touch);
});
self.zoomContainer.bind('touchend', function(e){
self.showHideWindow("hide");
if(self.options.showLens){self.showHideLens("hide");}
if(self.options.tint&&self.options.zoomType!="inner"){self.showHideTint("hide");}});
self.$elem.bind('touchend', function(e){
self.showHideWindow("hide");
if(self.options.showLens){self.showHideLens("hide");}
if(self.options.tint&&self.options.zoomType!="inner"){self.showHideTint("hide");}});
if(self.options.showLens){
self.zoomLens.bind('touchmove', function(e){
e.preventDefault();
var touch=e.originalEvent.touches[0]||e.originalEvent.changedTouches[0];
self.setPosition(touch);
});
self.zoomLens.bind('touchend', function(e){
self.showHideWindow("hide");
if(self.options.showLens){self.showHideLens("hide");}
if(self.options.tint&&self.options.zoomType!="inner"){self.showHideTint("hide");}});
}
self.$elem.bind('mousemove', function(e){
if(self.overWindow==false){self.setElements("show");}
if(self.lastX!==e.clientX||self.lastY!==e.clientY){
self.setPosition(e);
self.currentLoc=e;
}
self.lastX=e.clientX;
self.lastY=e.clientY;
});
self.zoomContainer.bind('mousemove', function(e){
if(self.overWindow==false){self.setElements("show");}
if(self.lastX!==e.clientX||self.lastY!==e.clientY){
self.setPosition(e);
self.currentLoc=e;
}
self.lastX=e.clientX;
self.lastY=e.clientY;
});
if(self.options.zoomType!="inner"){
self.zoomLens.bind('mousemove', function(e){
if(self.lastX!==e.clientX||self.lastY!==e.clientY){
self.setPosition(e);
self.currentLoc=e;
}
self.lastX=e.clientX;
self.lastY=e.clientY;
});
}
if(self.options.tint&&self.options.zoomType!="inner"){
self.zoomTint.bind('mousemove', function(e){
if(self.lastX!==e.clientX||self.lastY!==e.clientY){
self.setPosition(e);
self.currentLoc=e;
}
self.lastX=e.clientX;
self.lastY=e.clientY;
});
}
if(self.options.zoomType=="inner"){
self.zoomWindow.bind('mousemove', function(e){
if(self.lastX!==e.clientX||self.lastY!==e.clientY){
self.setPosition(e);
self.currentLoc=e;
}
self.lastX=e.clientX;
self.lastY=e.clientY;
});
}
self.zoomContainer.add(self.$elem).mouseenter(function(){
if(self.overWindow==false){self.setElements("show");}}).mouseleave(function(){
if(!self.scrollLock){
self.setElements("hide");
self.options.onDestroy(self.$elem);
}});
if(self.options.zoomType!="inner"){
self.zoomWindow.mouseenter(function(){
self.overWindow=true;
self.setElements("hide");
}).mouseleave(function(){
self.overWindow=false;
});
}
if(self.options.zoomLevel!=1){
}
if(self.options.minZoomLevel){
self.minZoomLevel=self.options.minZoomLevel;
}else{
self.minZoomLevel=self.options.scrollZoomIncrement * 2;
}
if(self.options.scrollZoom){
self.zoomContainer.add(self.$elem).bind('mousewheel DOMMouseScroll MozMousePixelScroll', function(e){
self.scrollLock=true;
clearTimeout($.data(this, 'timer'));
$.data(this, 'timer', setTimeout(function(){
self.scrollLock=false;
}, 250));
var theEvent=e.originalEvent.wheelDelta||e.originalEvent.detail*-1
e.stopImmediatePropagation();
e.stopPropagation();
e.preventDefault();
if(theEvent /120 > 0){
if(self.currentZoomLevel >=self.minZoomLevel){
self.changeZoomLevel(self.currentZoomLevel-self.options.scrollZoomIncrement);
}}else{
if(self.options.maxZoomLevel){
if(self.currentZoomLevel <=self.options.maxZoomLevel){
self.changeZoomLevel(parseFloat(self.currentZoomLevel)+self.options.scrollZoomIncrement);
}}else{
self.changeZoomLevel(parseFloat(self.currentZoomLevel)+self.options.scrollZoomIncrement);
}}
return false;
});
}},
setElements: function(type){
var self=this;
if(!self.options.zoomEnabled){return false;}
if(type=="show"){
if(self.isWindowSet){
if(self.options.zoomType=="inner"){self.showHideWindow("show");}
if(self.options.zoomType=="window"){self.showHideWindow("show");}
if(self.options.showLens){self.showHideLens("show");}
if(self.options.tint&&self.options.zoomType!="inner"){self.showHideTint("show");
}}
}
if(type=="hide"){
if(self.options.zoomType=="window"){self.showHideWindow("hide");}
if(!self.options.tint){self.showHideWindow("hide");}
if(self.options.showLens){self.showHideLens("hide");}
if(self.options.tint){	self.showHideTint("hide");}}
},
setPosition: function(e){
var self=this;
if(!self.options.zoomEnabled){return false;}
self.nzHeight=self.$elem.height();
self.nzWidth=self.$elem.width();
self.nzOffset=self.$elem.offset();
if(self.options.tint&&self.options.zoomType!="inner"){
self.zoomTint.css({ top: 0});
self.zoomTint.css({ left: 0});
}
if(self.options.responsive&&!self.options.scrollZoom){
if(self.options.showLens){
if(self.nzHeight < self.options.zoomWindowWidth/self.widthRatio){
lensHeight=self.nzHeight;
}else{
lensHeight=String((self.options.zoomWindowHeight/self.heightRatio))
}
if(self.largeWidth < self.options.zoomWindowWidth){
lensWidth=self.nzWidth;
}else{
lensWidth=(self.options.zoomWindowWidth/self.widthRatio);
}
self.widthRatio=self.largeWidth / self.nzWidth;
self.heightRatio=self.largeHeight / self.nzHeight;
if(self.options.zoomType!="lens"){
if(self.nzHeight < self.options.zoomWindowWidth/self.widthRatio){
lensHeight=self.nzHeight;
}else{
lensHeight=String((self.options.zoomWindowHeight/self.heightRatio))
}
if(self.nzWidth < self.options.zoomWindowHeight/self.heightRatio){
lensWidth=self.nzWidth;
}else{
lensWidth=String((self.options.zoomWindowWidth/self.widthRatio));
}
self.zoomLens.css('width', lensWidth);
self.zoomLens.css('height', lensHeight);
if(self.options.tint){
self.zoomTintImage.css('width', self.nzWidth);
self.zoomTintImage.css('height', self.nzHeight);
}}
if(self.options.zoomType=="lens"){
self.zoomLens.css({ width: String(self.options.lensSize) + 'px', height: String(self.options.lensSize) + 'px' })
}}
}
self.zoomContainer.css({ top: self.nzOffset.top});
self.zoomContainer.css({ left: self.nzOffset.left});
self.mouseLeft=parseInt(e.pageX - self.nzOffset.left);
self.mouseTop=parseInt(e.pageY - self.nzOffset.top);
if(self.options.zoomType=="window"){
self.Etoppos=(self.mouseTop < (self.zoomLens.height()/2));
self.Eboppos=(self.mouseTop > self.nzHeight - (self.zoomLens.height()/2)-(self.options.lensBorderSize*2));
self.Eloppos=(self.mouseLeft < 0+((self.zoomLens.width()/2)));
self.Eroppos=(self.mouseLeft > (self.nzWidth - (self.zoomLens.width()/2)-(self.options.lensBorderSize*2)));
}
if(self.options.zoomType=="inner"){
self.Etoppos=(self.mouseTop < ((self.nzHeight/2)/self.heightRatio));
self.Eboppos=(self.mouseTop > (self.nzHeight - ((self.nzHeight/2)/self.heightRatio)));
self.Eloppos=(self.mouseLeft < 0+(((self.nzWidth/2)/self.widthRatio)));
self.Eroppos=(self.mouseLeft > (self.nzWidth - (self.nzWidth/2)/self.widthRatio-(self.options.lensBorderSize*2)));
}
if(self.mouseLeft < 0||self.mouseTop < 0||self.mouseLeft > self.nzWidth||self.mouseTop > self.nzHeight){
self.setElements("hide");
return;
}else{
if(self.options.showLens){
self.lensLeftPos=String(Math.floor(self.mouseLeft - self.zoomLens.width() / 2));
self.lensTopPos=String(Math.floor(self.mouseTop - self.zoomLens.height() / 2));
}
if(self.Etoppos){
self.lensTopPos=0;
}
if(self.Eloppos){
self.windowLeftPos=0;
self.lensLeftPos=0;
self.tintpos=0;
}
if(self.options.zoomType=="window"){
if(self.Eboppos){
self.lensTopPos=Math.max((self.nzHeight)-self.zoomLens.height()-(self.options.lensBorderSize*2), 0);
}
if(self.Eroppos){
self.lensLeftPos=(self.nzWidth-(self.zoomLens.width())-(self.options.lensBorderSize*2));
}}
if(self.options.zoomType=="inner"){
if(self.Eboppos){
self.lensTopPos=Math.max(((self.nzHeight)-(self.options.lensBorderSize*2)), 0);
}
if(self.Eroppos){
self.lensLeftPos=(self.nzWidth-(self.nzWidth)-(self.options.lensBorderSize*2));
}}
if(self.options.zoomType=="lens"){
self.windowLeftPos=String(((e.pageX - self.nzOffset.left) * self.widthRatio - self.zoomLens.width() / 2) * (-1));
self.windowTopPos=String(((e.pageY - self.nzOffset.top) * self.heightRatio - self.zoomLens.height() / 2) * (-1));
self.zoomLens.css({ backgroundPosition: self.windowLeftPos + 'px ' + self.windowTopPos + 'px' });
if(self.changeBgSize){
if(self.nzHeight>self.nzWidth){
if(self.options.zoomType=="lens"){
self.zoomLens.css({ "background-size": self.largeWidth/self.newvalueheight + 'px ' + self.largeHeight/self.newvalueheight + 'px' });
}
self.zoomWindow.css({ "background-size": self.largeWidth/self.newvalueheight + 'px ' + self.largeHeight/self.newvalueheight + 'px' });
}else{
if(self.options.zoomType=="lens"){
self.zoomLens.css({ "background-size": self.largeWidth/self.newvaluewidth + 'px ' + self.largeHeight/self.newvaluewidth + 'px' });
}
self.zoomWindow.css({ "background-size": self.largeWidth/self.newvaluewidth + 'px ' + self.largeHeight/self.newvaluewidth + 'px' });
}
self.changeBgSize=false;
}
self.setWindowPostition(e);
}
if(self.options.tint&&self.options.zoomType!="inner"){
self.setTintPosition(e);
}
if(self.options.zoomType=="window"){
self.setWindowPostition(e);
}
if(self.options.zoomType=="inner"){
self.setWindowPostition(e);
}
if(self.options.showLens){
if(self.fullwidth&&self.options.zoomType!="lens"){
self.lensLeftPos=0;
}
self.zoomLens.css({ left: self.lensLeftPos + 'px', top: self.lensTopPos + 'px' })
}}
},
showHideWindow: function(change){
var self=this;
if(change=="show"){
if(!self.isWindowActive){
if(self.options.zoomWindowFadeIn){
self.zoomWindow.stop(true, true, false).fadeIn(self.options.zoomWindowFadeIn);
}else{self.zoomWindow.show();}
self.isWindowActive=true;
}}
if(change=="hide"){
if(self.isWindowActive){
if(self.options.zoomWindowFadeOut){
self.zoomWindow.stop(true, true).fadeOut(self.options.zoomWindowFadeOut, function (){
if(self.loop){
clearInterval(self.loop);
self.loop=false;
}});
}else{self.zoomWindow.hide();}
self.isWindowActive=false;
}}
},
showHideLens: function(change){
var self=this;
if(change=="show"){
if(!self.isLensActive){
if(self.options.lensFadeIn){
self.zoomLens.stop(true, true, false).fadeIn(self.options.lensFadeIn);
}else{self.zoomLens.show();}
self.isLensActive=true;
}}
if(change=="hide"){
if(self.isLensActive){
if(self.options.lensFadeOut){
self.zoomLens.stop(true, true).fadeOut(self.options.lensFadeOut);
}else{self.zoomLens.hide();}
self.isLensActive=false;
}}
},
showHideTint: function(change){
var self=this;
if(change=="show"){
if(!self.isTintActive){
if(self.options.zoomTintFadeIn){
self.zoomTint.css({opacity:self.options.tintOpacity}).animate().stop(true, true).fadeIn("slow");
}else{
self.zoomTint.css({opacity:self.options.tintOpacity}).animate();
self.zoomTint.show();
}
self.isTintActive=true;
}}
if(change=="hide"){
if(self.isTintActive){
if(self.options.zoomTintFadeOut){
self.zoomTint.stop(true, true).fadeOut(self.options.zoomTintFadeOut);
}else{self.zoomTint.hide();}
self.isTintActive=false;
}}
},
setLensPostition: function(e){
},
setWindowPostition: function(e){
var self=this;
if(!isNaN(self.options.zoomWindowPosition)){
switch (self.options.zoomWindowPosition){
case 1:
self.windowOffsetTop=(self.options.zoomWindowOffety);
self.windowOffsetLeft=(+self.nzWidth);
break;
case 2:
if(self.options.zoomWindowHeight > self.nzHeight){
self.windowOffsetTop=((self.options.zoomWindowHeight/2)-(self.nzHeight/2))*(-1);
self.windowOffsetLeft=(self.nzWidth);
}else{
}
break;
case 3:
self.windowOffsetTop=(self.nzHeight - self.zoomWindow.height() - (self.options.borderSize*2));
self.windowOffsetLeft=(self.nzWidth);
break;
case 4:
self.windowOffsetTop=(self.nzHeight);
self.windowOffsetLeft=(self.nzWidth);
break;
case 5:
self.windowOffsetTop=(self.nzHeight);
self.windowOffsetLeft=(self.nzWidth-self.zoomWindow.width()-(self.options.borderSize*2));
break;
case 6:
if(self.options.zoomWindowHeight > self.nzHeight){
self.windowOffsetTop=(self.nzHeight);
self.windowOffsetLeft=((self.options.zoomWindowWidth/2)-(self.nzWidth/2)+(self.options.borderSize*2))*(-1);
}else{
}
break;
case 7:
self.windowOffsetTop=(self.nzHeight);
self.windowOffsetLeft=0;
break;
case 8:
self.windowOffsetTop=(self.nzHeight);
self.windowOffsetLeft=(self.zoomWindow.width()+(self.options.borderSize*2))* (-1);
break;
case 9:
self.windowOffsetTop=(self.nzHeight - self.zoomWindow.height() - (self.options.borderSize*2));
self.windowOffsetLeft=(self.zoomWindow.width()+(self.options.borderSize*2))* (-1);
break;
case 10:
if(self.options.zoomWindowHeight > self.nzHeight){
self.windowOffsetTop=((self.options.zoomWindowHeight/2)-(self.nzHeight/2))*(-1);
self.windowOffsetLeft=(self.zoomWindow.width()+(self.options.borderSize*2))* (-1);
}else{
}
break;
case 11:
self.windowOffsetTop=(self.options.zoomWindowOffety);
self.windowOffsetLeft=(self.zoomWindow.width()+(self.options.borderSize*2))* (-1);
break;
case 12:
self.windowOffsetTop=(self.zoomWindow.height()+(self.options.borderSize*2))*(-1);
self.windowOffsetLeft=(self.zoomWindow.width()+(self.options.borderSize*2))* (-1);
break;
case 13:
self.windowOffsetTop=(self.zoomWindow.height()+(self.options.borderSize*2))*(-1);
self.windowOffsetLeft=(0);
break;
case 14:
if(self.options.zoomWindowHeight > self.nzHeight){
self.windowOffsetTop=(self.zoomWindow.height()+(self.options.borderSize*2))*(-1);
self.windowOffsetLeft=((self.options.zoomWindowWidth/2)-(self.nzWidth/2)+(self.options.borderSize*2))*(-1);
}else{
}
break;
case 15:
self.windowOffsetTop=(self.zoomWindow.height()+(self.options.borderSize*2))*(-1);
self.windowOffsetLeft=(self.nzWidth-self.zoomWindow.width()-(self.options.borderSize*2));
break;
case 16:
self.windowOffsetTop=(self.zoomWindow.height()+(self.options.borderSize*2))*(-1);
self.windowOffsetLeft=(self.nzWidth);
break;
default:
self.windowOffsetTop=(self.options.zoomWindowOffety);
self.windowOffsetLeft=(self.nzWidth);
}}else{
self.externalContainer=$('#'+self.options.zoomWindowPosition);
self.externalContainerWidth=self.externalContainer.width();
self.externalContainerHeight=self.externalContainer.height();
self.externalContainerOffset=self.externalContainer.offset();
self.windowOffsetTop=self.externalContainerOffset.top;
self.windowOffsetLeft=self.externalContainerOffset.left;
}
self.isWindowSet=true;
self.windowOffsetTop=self.windowOffsetTop + self.options.zoomWindowOffety;
self.windowOffsetLeft=self.windowOffsetLeft + self.options.zoomWindowOffetx;
self.zoomWindow.css({ top: self.windowOffsetTop});
self.zoomWindow.css({ left: self.windowOffsetLeft});
if(self.options.zoomType=="inner"){
self.zoomWindow.css({ top: 0});
self.zoomWindow.css({ left: 0});
}
self.windowLeftPos=String(((e.pageX - self.nzOffset.left) * self.widthRatio - self.zoomWindow.width() / 2) * (-1));
self.windowTopPos=String(((e.pageY - self.nzOffset.top) * self.heightRatio - self.zoomWindow.height() / 2) * (-1));
if(self.Etoppos){self.windowTopPos=0;}
if(self.Eloppos){self.windowLeftPos=0;}
if(self.Eboppos){self.windowTopPos=(self.largeHeight/self.currentZoomLevel-self.zoomWindow.height())*(-1);  }
if(self.Eroppos){self.windowLeftPos=((self.largeWidth/self.currentZoomLevel-self.zoomWindow.width())*(-1));}
if(self.fullheight){
self.windowTopPos=0;
}
if(self.fullwidth){
self.windowLeftPos=0;
}
if(self.options.zoomType=="window"||self.options.zoomType=="inner"){
if(self.zoomLock==1){
if(self.widthRatio <=1){
self.windowLeftPos=0;
}
if(self.heightRatio <=1){
self.windowTopPos=0;
}}
if(self.options.zoomType=="window"){
if(self.largeHeight < self.options.zoomWindowHeight){
self.windowTopPos=0;
}
if(self.largeWidth < self.options.zoomWindowWidth){
self.windowLeftPos=0;
}}
if(self.options.easing){
if(!self.xp){self.xp=0;}
if(!self.yp){self.yp=0;}
if(!self.loop){
self.loop=setInterval(function(){
self.xp +=(self.windowLeftPos  - self.xp) / self.options.easingAmount;
self.yp +=(self.windowTopPos  - self.yp) / self.options.easingAmount;
if(self.scrollingLock){
clearInterval(self.loop);
self.xp=self.windowLeftPos;
self.yp=self.windowTopPos
self.xp=((e.pageX - self.nzOffset.left) * self.widthRatio - self.zoomWindow.width() / 2) * (-1);
self.yp=(((e.pageY - self.nzOffset.top) * self.heightRatio - self.zoomWindow.height() / 2) * (-1));
if(self.changeBgSize){
if(self.nzHeight>self.nzWidth){
if(self.options.zoomType=="lens"){
self.zoomLens.css({ "background-size": self.largeWidth/self.newvalueheight + 'px ' + self.largeHeight/self.newvalueheight + 'px' });
}
self.zoomWindow.css({ "background-size": self.largeWidth/self.newvalueheight + 'px ' + self.largeHeight/self.newvalueheight + 'px' });
}else{
if(self.options.zoomType!="lens"){
self.zoomLens.css({ "background-size": self.largeWidth/self.newvaluewidth + 'px ' + self.largeHeight/self.newvalueheight + 'px' });
}
self.zoomWindow.css({ "background-size": self.largeWidth/self.newvaluewidth + 'px ' + self.largeHeight/self.newvaluewidth + 'px' });
}
/*
if(!self.bgxp){self.bgxp=self.largeWidth/self.newvalue;}
if(!self.bgyp){self.bgyp=self.largeHeight/self.newvalue ;}
if(!self.bgloop){
self.bgloop=setInterval(function(){
self.bgxp +=(self.largeWidth/self.newvalue  - self.bgxp) / self.options.easingAmount;
self.bgyp +=(self.largeHeight/self.newvalue  - self.bgyp) / self.options.easingAmount;
self.zoomWindow.css({ "background-size": self.bgxp + 'px ' + self.bgyp + 'px' });
}, 16);
}
*/
self.changeBgSize=false;
}
self.zoomWindow.css({ backgroundPosition: self.windowLeftPos + 'px ' + self.windowTopPos + 'px' });
self.scrollingLock=false;
self.loop=false;
}
else if(Math.round(Math.abs(self.xp - self.windowLeftPos) + Math.abs(self.yp - self.windowTopPos)) < 1){
clearInterval(self.loop);
self.zoomWindow.css({ backgroundPosition: self.windowLeftPos + 'px ' + self.windowTopPos + 'px' });
self.loop=false;
}else{
if(self.changeBgSize){
if(self.nzHeight>self.nzWidth){
if(self.options.zoomType=="lens"){
self.zoomLens.css({ "background-size": self.largeWidth/self.newvalueheight + 'px ' + self.largeHeight/self.newvalueheight + 'px' });
}
self.zoomWindow.css({ "background-size": self.largeWidth/self.newvalueheight + 'px ' + self.largeHeight/self.newvalueheight + 'px' });
}else{
if(self.options.zoomType!="lens"){
self.zoomLens.css({ "background-size": self.largeWidth/self.newvaluewidth + 'px ' + self.largeHeight/self.newvaluewidth + 'px' });
}
self.zoomWindow.css({ "background-size": self.largeWidth/self.newvaluewidth + 'px ' + self.largeHeight/self.newvaluewidth + 'px' });
}
self.changeBgSize=false;
}
self.zoomWindow.css({ backgroundPosition: self.xp + 'px ' + self.yp + 'px' });
}}, 16);
}}else{
if(self.changeBgSize){
if(self.nzHeight>self.nzWidth){
if(self.options.zoomType=="lens"){
self.zoomLens.css({ "background-size": self.largeWidth/self.newvalueheight + 'px ' + self.largeHeight/self.newvalueheight + 'px' });
}
self.zoomWindow.css({ "background-size": self.largeWidth/self.newvalueheight + 'px ' + self.largeHeight/self.newvalueheight + 'px' });
}else{
if(self.options.zoomType=="lens"){
self.zoomLens.css({ "background-size": self.largeWidth/self.newvaluewidth + 'px ' + self.largeHeight/self.newvaluewidth + 'px' });
}
if((self.largeHeight/self.newvaluewidth) < self.options.zoomWindowHeight){
self.zoomWindow.css({ "background-size": self.largeWidth/self.newvaluewidth + 'px ' + self.largeHeight/self.newvaluewidth + 'px' });
}else{
self.zoomWindow.css({ "background-size": self.largeWidth/self.newvalueheight + 'px ' + self.largeHeight/self.newvalueheight + 'px' });
}}
self.changeBgSize=false;
}
self.zoomWindow.css({ backgroundPosition: self.windowLeftPos + 'px ' + self.windowTopPos + 'px' });
}}
},
setTintPosition: function(e){
var self=this;
self.nzOffset=self.$elem.offset();
self.tintpos=String(((e.pageX - self.nzOffset.left)-(self.zoomLens.width() / 2)) * (-1));
self.tintposy=String(((e.pageY - self.nzOffset.top) - self.zoomLens.height() / 2) * (-1));
if(self.Etoppos){
self.tintposy=0;
}
if(self.Eloppos){
self.tintpos=0;
}
if(self.Eboppos){
self.tintposy=(self.nzHeight-self.zoomLens.height()-(self.options.lensBorderSize*2))*(-1);
}
if(self.Eroppos){
self.tintpos=((self.nzWidth-self.zoomLens.width()-(self.options.lensBorderSize*2))*(-1));
}
if(self.options.tint){
if(self.fullheight){
self.tintposy=0;
}
if(self.fullwidth){
self.tintpos=0;
}
self.zoomTintImage.css({'left': self.tintpos+'px'});
self.zoomTintImage.css({'top': self.tintposy+'px'});
}},
swaptheimage: function(smallimage, largeimage){
var self=this;
var newImg=new Image();
if(self.options.loadingIcon){
self.spinner=$('<div style="background: url(\''+self.options.loadingIcon+'\') no-repeat center;height:'+self.nzHeight+'px;width:'+self.nzWidth+'px;z-index: 2000;position: absolute; background-position: center center;"></div>');
self.$elem.after(self.spinner);
}
self.options.onImageSwap(self.$elem);
newImg.onload=function(){
self.largeWidth=newImg.width;
self.largeHeight=newImg.height;
self.zoomImage=largeimage;
self.zoomWindow.css({ "background-size": self.largeWidth + 'px ' + self.largeHeight + 'px' });
self.swapAction(smallimage, largeimage);
return;
}
newImg.src=largeimage;
},
swapAction: function(smallimage, largeimage){
var self=this;
var newImg2=new Image();
newImg2.onload=function(){
self.nzHeight=newImg2.height;
self.nzWidth=newImg2.width;
self.options.onImageSwapComplete(self.$elem);
self.doneCallback();
return;
}
newImg2.src=smallimage;
self.currentZoomLevel=self.options.zoomLevel;
self.options.maxZoomLevel=false;
if(self.options.zoomType=="lens"){
self.zoomLens.css({ backgroundImage: "url('" + largeimage + "')" });
}
if(self.options.zoomType=="window"){
self.zoomWindow.css({ backgroundImage: "url('" + largeimage + "')" });
}
if(self.options.zoomType=="inner"){
self.zoomWindow.css({ backgroundImage: "url('" + largeimage + "')" });
}
self.currentImage=largeimage;
if(self.options.imageCrossfade){
var oldImg=self.$elem;
var newImg=oldImg.clone();
self.$elem.attr("src",smallimage)
self.$elem.after(newImg);
newImg.stop(true).fadeOut(self.options.imageCrossfade, function(){
$(this).remove();
});
self.$elem.width("auto").removeAttr("width");
self.$elem.height("auto").removeAttr("height");
oldImg.fadeIn(self.options.imageCrossfade);
if(self.options.tint&&self.options.zoomType!="inner"){
var oldImgTint=self.zoomTintImage;
var newImgTint=oldImgTint.clone();
self.zoomTintImage.attr("src",largeimage)
self.zoomTintImage.after(newImgTint);
newImgTint.stop(true).fadeOut(self.options.imageCrossfade, function(){
$(this).remove();
});
oldImgTint.fadeIn(self.options.imageCrossfade);
self.zoomTint.css({ height: self.$elem.height()});
self.zoomTint.css({ width: self.$elem.width()});
}
self.zoomContainer.css("height", self.$elem.height());
self.zoomContainer.css("width", self.$elem.width());
if(self.options.zoomType=="inner"){
if(!self.options.constrainType){
self.zoomWrap.parent().css("height", self.$elem.height());
self.zoomWrap.parent().css("width", self.$elem.width());
self.zoomWindow.css("height", self.$elem.height());
self.zoomWindow.css("width", self.$elem.width());
}}
if(self.options.imageCrossfade){
self.zoomWrap.css("height", self.$elem.height());
self.zoomWrap.css("width", self.$elem.width());
}}else{
self.$elem.attr("src",smallimage);
if(self.options.tint){
self.zoomTintImage.attr("src",largeimage);
self.zoomTintImage.attr("height",self.$elem.height());
self.zoomTintImage.css({ height: self.$elem.height()});
self.zoomTint.css({ height: self.$elem.height()});
}
self.zoomContainer.css("height", self.$elem.height());
self.zoomContainer.css("width", self.$elem.width());
if(self.options.imageCrossfade){
self.zoomWrap.css("height", self.$elem.height());
self.zoomWrap.css("width", self.$elem.width());
}}
if(self.options.constrainType){
if(self.options.constrainType=="height"){
self.zoomContainer.css("height", self.options.constrainSize);
self.zoomContainer.css("width", "auto");
if(self.options.imageCrossfade){
self.zoomWrap.css("height", self.options.constrainSize);
self.zoomWrap.css("width", "auto");
self.constwidth=self.zoomWrap.width();
}else{
self.$elem.css("height", self.options.constrainSize);
self.$elem.css("width", "auto");
self.constwidth=self.$elem.width();
}
if(self.options.zoomType=="inner"){
self.zoomWrap.parent().css("height", self.options.constrainSize);
self.zoomWrap.parent().css("width", self.constwidth);
self.zoomWindow.css("height", self.options.constrainSize);
self.zoomWindow.css("width", self.constwidth);
}
if(self.options.tint){
self.tintContainer.css("height", self.options.constrainSize);
self.tintContainer.css("width", self.constwidth);
self.zoomTint.css("height", self.options.constrainSize);
self.zoomTint.css("width", self.constwidth);
self.zoomTintImage.css("height", self.options.constrainSize);
self.zoomTintImage.css("width", self.constwidth);
}}
if(self.options.constrainType=="width"){
self.zoomContainer.css("height", "auto");
self.zoomContainer.css("width", self.options.constrainSize);
if(self.options.imageCrossfade){
self.zoomWrap.css("height", "auto");
self.zoomWrap.css("width", self.options.constrainSize);
self.constheight=self.zoomWrap.height();
}else{
self.$elem.css("height", "auto");
self.$elem.css("width", self.options.constrainSize);
self.constheight=self.$elem.height();
}
if(self.options.zoomType=="inner"){
self.zoomWrap.parent().css("height", self.constheight);
self.zoomWrap.parent().css("width", self.options.constrainSize);
self.zoomWindow.css("height", self.constheight);
self.zoomWindow.css("width", self.options.constrainSize);
}
if(self.options.tint){
self.tintContainer.css("height", self.constheight);
self.tintContainer.css("width", self.options.constrainSize);
self.zoomTint.css("height", self.constheight);
self.zoomTint.css("width", self.options.constrainSize);
self.zoomTintImage.css("height", self.constheight);
self.zoomTintImage.css("width", self.options.constrainSize);
}}
}},
doneCallback: function(){
var self=this;
if(self.options.loadingIcon){
self.spinner.hide();
}
self.nzOffset=self.$elem.offset();
self.nzWidth=self.$elem.width();
self.nzHeight=self.$elem.height();
self.currentZoomLevel=self.options.zoomLevel;
self.widthRatio=self.largeWidth / self.nzWidth;
self.heightRatio=self.largeHeight / self.nzHeight;
if(self.options.zoomType=="window"){
if(self.nzHeight < self.options.zoomWindowWidth/self.widthRatio){
lensHeight=self.nzHeight;
}else{
lensHeight=String((self.options.zoomWindowHeight/self.heightRatio))
}
if(self.options.zoomWindowWidth < self.options.zoomWindowWidth){
lensWidth=self.nzWidth;
}else{
lensWidth=(self.options.zoomWindowWidth/self.widthRatio);
}
if(self.zoomLens){
self.zoomLens.css('width', lensWidth);
self.zoomLens.css('height', lensHeight);
}}
},
getCurrentImage: function(){
var self=this;
return self.zoomImage;
},
getGalleryList: function(){
var self=this;
self.gallerylist=[];
if(self.options.gallery){
$('#'+self.options.gallery + ' a').each(function(){
var img_src='';
if($(this).data("zoom-image")){
img_src=$(this).data("zoom-image");
}
else if($(this).data("image")){
img_src=$(this).data("image");
}
if(img_src==self.zoomImage){
self.gallerylist.unshift({
href: ''+img_src+'',
title: $(this).find('img').attr("title")
});
}else{
self.gallerylist.push({
href: ''+img_src+'',
title: $(this).find('img').attr("title")
});
}});
}else{
self.gallerylist.push({
href: ''+self.zoomImage+'',
title: $(this).find('img').attr("title")
});
}
return self.gallerylist;
},
changeZoomLevel: function(value){
var self=this;
self.scrollingLock=true;
self.newvalue=parseFloat(value).toFixed(2);
newvalue=parseFloat(value).toFixed(2);
maxheightnewvalue=self.largeHeight/((self.options.zoomWindowHeight / self.nzHeight) * self.nzHeight);
maxwidthtnewvalue=self.largeWidth/((self.options.zoomWindowWidth / self.nzWidth) * self.nzWidth);
if(self.options.zoomType!="inner"){
if(maxheightnewvalue <=newvalue){
self.heightRatio=(self.largeHeight/maxheightnewvalue) / self.nzHeight;
self.newvalueheight=maxheightnewvalue;
self.fullheight=true;
}else{
self.heightRatio=(self.largeHeight/newvalue) / self.nzHeight;
self.newvalueheight=newvalue;
self.fullheight=false;
}
if(maxwidthtnewvalue <=newvalue){
self.widthRatio=(self.largeWidth/maxwidthtnewvalue) / self.nzWidth;
self.newvaluewidth=maxwidthtnewvalue;
self.fullwidth=true;
}else{
self.widthRatio=(self.largeWidth/newvalue) / self.nzWidth;
self.newvaluewidth=newvalue;
self.fullwidth=false;
}
if(self.options.zoomType=="lens"){
if(maxheightnewvalue <=newvalue){
self.fullwidth=true;
self.newvaluewidth=maxheightnewvalue;
}else{
self.widthRatio=(self.largeWidth/newvalue) / self.nzWidth;
self.newvaluewidth=newvalue;
self.fullwidth=false;
}}}
if(self.options.zoomType=="inner"){
maxheightnewvalue=parseFloat(self.largeHeight/self.nzHeight).toFixed(2);
maxwidthtnewvalue=parseFloat(self.largeWidth/self.nzWidth).toFixed(2);
if(newvalue > maxheightnewvalue){
newvalue=maxheightnewvalue;
}
if(newvalue > maxwidthtnewvalue){
newvalue=maxwidthtnewvalue;
}
if(maxheightnewvalue <=newvalue){
self.heightRatio=(self.largeHeight/newvalue) / self.nzHeight;
if(newvalue > maxheightnewvalue){
self.newvalueheight=maxheightnewvalue;
}else{
self.newvalueheight=newvalue;
}
self.fullheight=true;
}else{
self.heightRatio=(self.largeHeight/newvalue) / self.nzHeight;
if(newvalue > maxheightnewvalue){
self.newvalueheight=maxheightnewvalue;
}else{
self.newvalueheight=newvalue;
}
self.fullheight=false;
}
if(maxwidthtnewvalue <=newvalue){
self.widthRatio=(self.largeWidth/newvalue) / self.nzWidth;
if(newvalue > maxwidthtnewvalue){
self.newvaluewidth=maxwidthtnewvalue;
}else{
self.newvaluewidth=newvalue;
}
self.fullwidth=true;
}else{
self.widthRatio=(self.largeWidth/newvalue) / self.nzWidth;
self.newvaluewidth=newvalue;
self.fullwidth=false;
}}
scrcontinue=false;
if(self.options.zoomType=="inner"){
if(self.nzWidth >=self.nzHeight){
if(self.newvaluewidth <=maxwidthtnewvalue){
scrcontinue=true;
}else{
scrcontinue=false;
self.fullheight=true;
self.fullwidth=true;
}}
if(self.nzHeight > self.nzWidth){
if(self.newvaluewidth <=maxwidthtnewvalue){
scrcontinue=true;
}else{
scrcontinue=false;
self.fullheight=true;
self.fullwidth=true;
}}
}
if(self.options.zoomType!="inner"){
scrcontinue=true;
}
if(scrcontinue){
self.zoomLock=0;
self.changeZoom=true;
if(((self.options.zoomWindowHeight)/self.heightRatio) <=self.nzHeight){
self.currentZoomLevel=self.newvalueheight;
if(self.options.zoomType!="lens"&&self.options.zoomType!="inner"){
self.changeBgSize=true;
self.zoomLens.css({height: String((self.options.zoomWindowHeight)/self.heightRatio) + 'px' })
}
if(self.options.zoomType=="lens"||self.options.zoomType=="inner"){
self.changeBgSize=true;
}}
if((self.options.zoomWindowWidth/self.widthRatio) <=self.nzWidth){
if(self.options.zoomType!="inner"){
if(self.newvaluewidth > self.newvalueheight){
self.currentZoomLevel=self.newvaluewidth;
}}
if(self.options.zoomType!="lens"&&self.options.zoomType!="inner"){
self.changeBgSize=true;
self.zoomLens.css({width: String((self.options.zoomWindowWidth)/self.widthRatio) + 'px' })
}
if(self.options.zoomType=="lens"||self.options.zoomType=="inner"){
self.changeBgSize=true;
}}
if(self.options.zoomType=="inner"){
self.changeBgSize=true;
if(self.nzWidth > self.nzHeight){
self.currentZoomLevel=self.newvaluewidth;
}
if(self.nzHeight > self.nzWidth){
self.currentZoomLevel=self.newvaluewidth;
}}
}
self.setPosition(self.currentLoc);
},
closeAll: function(){
if(self.zoomWindow){self.zoomWindow.hide();}
if(self.zoomLens){self.zoomLens.hide();}
if(self.zoomTint){self.zoomTint.hide();}},
changeState: function(value){
var self=this;
if(value=='enable'){self.options.zoomEnabled=true;}
if(value=='disable'){self.options.zoomEnabled=false;}}
};
$.fn.elevateZoom=function(options){
return this.each(function(){
var elevate=Object.create(ElevateZoom);
elevate.init(options, this);
$.data(this, 'elevateZoom', elevate);
});
};
$.fn.elevateZoom.options={
zoomActivation: "hover",
zoomEnabled: true,
preloading: 1,
zoomLevel: 1,
scrollZoom: false,
scrollZoomIncrement: 0.1,
minZoomLevel: false,
maxZoomLevel: false,
easing: false,
easingAmount: 12,
lensSize: 200,
zoomWindowWidth: 400,
zoomWindowHeight: 400,
zoomWindowOffetx: 0,
zoomWindowOffety: 0,
zoomWindowPosition: 1,
zoomWindowBgColour: "#fff",
lensFadeIn: false,
lensFadeOut: false,
debug: false,
zoomWindowFadeIn: false,
zoomWindowFadeOut: false,
zoomWindowAlwaysShow: false,
zoomTintFadeIn: false,
zoomTintFadeOut: false,
borderSize: 4,
showLens: true,
borderColour: "#888",
lensBorderSize: 1,
lensBorderColour: "#000",
lensShape: "square",
zoomType: "window",
containLensZoom: false,
lensColour: "white",
lensOpacity: 0.4,
lenszoom: false,
tint: false,
tintColour: "#333",
tintOpacity: 0.4,
gallery: false,
galleryActiveClass: "zoomGalleryActive",
imageCrossfade: false,
constrainType: false,
constrainSize: false,
loadingIcon: false,
cursor:"default",
responsive:true,
onComplete: $.noop,
onDestroy: function(){},
onZoomedImageLoaded: function(){},
onImageSwap: $.noop,
onImageSwapComplete: $.noop
};})(jQuery, window, document);
!function(t){function e(t){var e=/^#?([a-f\d])([a-f\d])([a-f\d])$/i;t=t.replace(e,function(t,e,a,i){return e+e+a+a+i+i});var a=/^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(t);return a?{r:parseInt(a[1],16),g:parseInt(a[2],16),b:parseInt(a[3],16)}:null}function a(){var t=document.createElement("canvas");return!(!t.getContext||!t.getContext("2d"))}function i(){return Math.floor(65536*(1+Math.random())).toString(16).substring(1)}function n(){return i()+i()+"-"+i()+"-"+i()+"-"+i()+"-"+i()+i()+i()}function s(t){var e=t.match(/^[0-9]{4}-[0-9]{2}-[0-9]{2}\s[0-9]{1,2}:[0-9]{2}:[0-9]{2}$/);if(null!==e&&e.length>0){var a=t.split(" "),i=a[0].split("-"),n=a[1].split(":");return new Date(i[0],i[1]-1,i[2],n[0],n[1],n[2])}var s=Date.parse(t);return isNaN(s)?(s=Date.parse(t.replace(/-/g,"/").replace("T"," ")),isNaN(s)?new Date:s):s}function r(t,e,a,i,n){for(var s={},r={},o={},h={},d={},u={},l=null,f=0;f<i.length;f++){var c,p=i[f];c=null===l?a/m[p]:m[l]/m[p];var _=t/m[p],b=e/m[p];n&&(_=_>0?Math.floor(_):Math.ceil(_),b=b>0?Math.floor(b):Math.ceil(b)),"Days"!==p&&(_%=c,b%=c),s[p]=_,o[p]=Math.abs(_),r[p]=b,u[p]=Math.abs(b),h[p]=Math.abs(_)/c,d[p]=Math.abs(b)/c,l=p}return{raw_time:s,raw_old_time:r,time:o,old_time:u,pct:h,old_pct:d}}function o(){"undefined"!=typeof d.TC_Instance_List?p=d.TC_Instance_List:d.TC_Instance_List=p,h(d)}function h(t){for(var e=["webkit","moz"],a=0;a<e.length&&!t.requestAnimationFrame;++a)t.requestAnimationFrame=t[e[a]+"RequestAnimationFrame"],t.cancelAnimationFrame=t[e[a]+"CancelAnimationFrame"];t.requestAnimationFrame&&t.cancelAnimationFrame||(t.requestAnimationFrame=function(e,a,i){"undefined"==typeof i&&(i={data:{last_frame:0}});var n=(new Date).getTime(),s=Math.max(0,16-(n-i.data.last_frame)),r=t.setTimeout(function(){e(n+s)},s);return i.data.last_frame=n+s,r},t.cancelAnimationFrame=function(t){clearTimeout(t)})}var d=window;Object.keys||(Object.keys=function(){"use strict";var t=Object.prototype.hasOwnProperty,e=!{toString:null}.propertyIsEnumerable("toString"),a=["toString","toLocaleString","valueOf","hasOwnProperty","isPrototypeOf","propertyIsEnumerable","constructor"],i=a.length;return function(n){if("object"!=typeof n&&("function"!=typeof n||null===n))throw new TypeError("Object.keys called on non-object");var s,r,o=[];for(s in n)t.call(n,s)&&o.push(s);if(e)for(r=0;i>r;r++)t.call(n,a[r])&&o.push(a[r]);return o}}());var u=!1,l=200,f=("#debug"===location.hash,["Days","Hours","Minutes","Seconds"]),c={Seconds:"Minutes",Minutes:"Hours",Hours:"Days",Days:"Years"},m={Seconds:1,Minutes:60,Hours:3600,Days:86400,Months:2678400,Years:31536e3};Array.prototype.indexOf||(Array.prototype.indexOf=function(t){var e=this.length>>>0,a=Number(arguments[1])||0;for(a=0>a?Math.ceil(a):Math.floor(a),0>a&&(a+=e);e>a;a++)if(a in this&&this[a]===t)return a;return-1});var p={},_=function(t,e){this.element=t,this.container,this.listeners=null,this.data={paused:!1,last_frame:0,animation_frame:null,interval_fallback:null,timer:!1,total_duration:null,prev_time:null,drawn_units:[],text_elements:{Days:null,Hours:null,Minutes:null,Seconds:null},attributes:{canvas:null,context:null,item_size:null,line_width:null,radius:null,outer_radius:null},state:{fading:{Days:!1,Hours:!1,Minutes:!1,Seconds:!1}}},this.config=null,this.setOptions(e),this.initialize()};_.prototype.clearListeners=function(){this.listeners={all:[],visible:[]}},_.prototype.addTime=function(t){if(this.data.attributes.ref_date instanceof Date){var e=this.data.attributes.ref_date;e.setSeconds(e.getSeconds()+t)}else isNaN(this.data.attributes.ref_date)||(this.data.attributes.ref_date+=1e3*t)},_.prototype.initialize=function(e){this.data.drawn_units=[];for(var i=0;i<Object.keys(this.config.time).length;i++){var n=Object.keys(this.config.time)[i];this.config.time[n].show&&this.data.drawn_units.push(n)}t(this.element).children("div.time_circles").remove(),"undefined"==typeof e&&(e=!0),(e||null===this.listeners)&&this.clearListeners(),this.container=t("<div>"),this.container.addClass("time_circles"),this.container.appendTo(this.element);var s=this.element.offsetHeight,r=this.element.offsetWidth;0===s&&(s=t(this.element).height()),0===r&&(r=t(this.element).width()),0===s&&r>0?s=r/this.data.drawn_units.length:0===r&&s>0&&(r=s*this.data.drawn_units.length);var o=document.createElement("canvas");o.width=r,o.height=s,this.data.attributes.canvas=t(o),this.data.attributes.canvas.appendTo(this.container);var h=a();h||"undefined"==typeof G_vmlCanvasManager||(G_vmlCanvasManager.initElement(o),u=!0,h=!0),h&&(this.data.attributes.context=o.getContext("2d")),this.data.attributes.item_size=Math.min(r/this.data.drawn_units.length,s),this.data.attributes.line_width=this.data.attributes.item_size*this.config.fg_width,this.data.attributes.radius=(.8*this.data.attributes.item_size-this.data.attributes.line_width)/2,this.data.attributes.outer_radius=this.data.attributes.radius+.5*Math.max(this.data.attributes.line_width,this.data.attributes.line_width*this.config.bg_width);var i=0;for(var l in this.data.text_elements)if(this.config.time[l].show){var f=t("<div>");f.addClass("textDiv_"+l),f.css("top",Math.round(.35*this.data.attributes.item_size)),f.css("left",Math.round(i++*this.data.attributes.item_size)),f.css("width",this.data.attributes.item_size),f.appendTo(this.container);var c=t("<span class='number'>");c.appendTo(f);var m=t("<span class='text'>");m.text(this.config.time[l].text),m.appendTo(f),this.data.text_elements[l]=c}this.start(),this.config.start||(this.data.paused=!0);var p=this;this.data.interval_fallback=d.setInterval(function(){p.update.call(p,!0)},100)},_.prototype.update=function(t){if("undefined"==typeof t)t=!1;else if(t&&this.data.paused)return;u&&this.data.attributes.context.clearRect(0,0,this.data.attributes.canvas[0].width,this.data.attributes.canvas[0].hright);var e,a,i=this.data.prev_time,n=new Date;if(this.data.prev_time=n,null===i&&(i=n),!this.config.count_past_zero&&n>this.data.attributes.ref_date){for(var s=0;s<this.data.drawn_units.length;s++){var o=this.data.drawn_units[s];this.data.text_elements[o].text("0");var h=s*this.data.attributes.item_size+this.data.attributes.item_size/2,c=this.data.attributes.item_size/2,p=this.config.time[o].color;this.drawArc(h,c,p,0)}return void this.stop()}e=(this.data.attributes.ref_date-n)/1e3,a=(this.data.attributes.ref_date-i)/1e3;var _="smooth"!==this.config.animation,b=r(e,a,this.data.total_duration,this.data.drawn_units,_),g=r(e,a,m.Years,f,_),s=0,v=0,y=null,w=this.data.drawn_units.slice();for(var s in f){var o=f[s];if(Math.floor(g.raw_time[o])!==Math.floor(g.raw_old_time[o])&&this.notifyListeners(o,Math.floor(g.time[o]),Math.floor(e),"all"),!(w.indexOf(o)<0)){if(Math.floor(b.raw_time[o])!==Math.floor(b.raw_old_time[o])&&this.notifyListeners(o,Math.floor(b.time[o]),Math.floor(e),"visible"),!t){this.data.text_elements[o].text(Math.floor(Math.abs(b.time[o])));var h=v*this.data.attributes.item_size+this.data.attributes.item_size/2,c=this.data.attributes.item_size/2,p=this.config.time[o].color;"smooth"===this.config.animation?(null===y||u||(Math.floor(b.time[y])>Math.floor(b.old_time[y])?(this.radialFade(h,c,p,1,o),this.data.state.fading[o]=!0):Math.floor(b.time[y])<Math.floor(b.old_time[y])&&(this.radialFade(h,c,p,0,o),this.data.state.fading[o]=!0)),this.data.state.fading[o]||this.drawArc(h,c,p,b.pct[o])):this.animateArc(h,c,p,b.pct[o],b.old_pct[o],(new Date).getTime()+l)}y=o,v++}}if(!this.data.paused&&!t){var M=this,x=function(){M.update.call(M)};if("smooth"===this.config.animation)this.data.animation_frame=d.requestAnimationFrame(x,M.element,M);else{var A=e%1*1e3;0>A&&(A=1e3+A),A+=50,M.data.animation_frame=d.setTimeout(function(){M.data.animation_frame=d.requestAnimationFrame(x,M.element,M)},A)}}},_.prototype.animateArc=function(t,e,a,i,n,s){if(null!==this.data.attributes.context){var r=n-i;if(Math.abs(r)>.5)0===i?this.radialFade(t,e,a,1):this.radialFade(t,e,a,0);else{var o=(l-(s-(new Date).getTime()))/l;o>1&&(o=1);var h=n*(1-o)+i*o;if(this.drawArc(t,e,a,h),o>=1)return;var u=this;d.requestAnimationFrame(function(){u.animateArc(t,e,a,i,n,s)},this.element)}}},_.prototype.drawArc=function(t,e,a,i){if(null!==this.data.attributes.context){var n=Math.max(this.data.attributes.outer_radius,this.data.attributes.item_size/2);u||this.data.attributes.context.clearRect(t-n,e-n,2*n,2*n),this.config.use_background&&(this.data.attributes.context.beginPath(),this.data.attributes.context.arc(t,e,this.data.attributes.radius,0,2*Math.PI,!1),this.data.attributes.context.lineWidth=this.data.attributes.line_width*this.config.bg_width,this.data.attributes.context.strokeStyle=this.config.circle_bg_color,this.data.attributes.context.stroke());var s,r,o,h=-.5*Math.PI,d=2*Math.PI;s=h+this.config.start_angle/360*d;var l=2*i*Math.PI;"Both"===this.config.direction?(o=!1,s-=l/2,r=s+l):"Clockwise"===this.config.direction?(o=!1,r=s+l):(o=!0,r=s-l),this.data.attributes.context.beginPath(),this.data.attributes.context.arc(t,e,this.data.attributes.radius,s,r,o),this.data.attributes.context.lineWidth=this.data.attributes.line_width,this.data.attributes.context.strokeStyle=a,this.data.attributes.context.stroke()}},_.prototype.radialFade=function(t,a,i,n,s){var r,o=e(i),h=this,u=.2*(1===n?-1:1);for(r=0;1>=n&&n>=0;r++)!function(){var e=50*r,i="rgba("+o.r+", "+o.g+", "+o.b+", "+Math.round(10*n)/10+")";d.setTimeout(function(){h.drawArc(t,a,i,1)},e)}(),n+=u;void 0!==typeof s&&d.setTimeout(function(){h.data.state.fading[s]=!1},50*r)},_.prototype.timeLeft=function(){if(this.data.paused&&"number"==typeof this.data.timer)return this.data.timer;var t=new Date;return(this.data.attributes.ref_date-t)/1e3},_.prototype.start=function(){d.cancelAnimationFrame(this.data.animation_frame),d.clearTimeout(this.data.animation_frame);var e=t(this.element).data("date");if("undefined"==typeof e&&(e=t(this.element).attr("data-date")),"string"==typeof e)this.data.attributes.ref_date=s(e);else if("number"==typeof this.data.timer)this.data.paused&&(this.data.attributes.ref_date=(new Date).getTime()+1e3*this.data.timer);else{var a=t(this.element).data("timer");"undefined"==typeof a&&(a=t(this.element).attr("data-timer")),"string"==typeof a&&(a=parseFloat(a)),"number"==typeof a?(this.data.timer=a,this.data.attributes.ref_date=(new Date).getTime()+1e3*a):this.data.attributes.ref_date=this.config.ref_date}this.data.paused=!1,this.update.call(this)},_.prototype.restart=function(){this.data.timer=!1,this.start()},_.prototype.stop=function(){"number"==typeof this.data.timer&&(this.data.timer=this.timeLeft(this)),this.data.paused=!0,d.cancelAnimationFrame(this.data.animation_frame)},_.prototype.destroy=function(){this.clearListeners(),this.stop(),d.clearInterval(this.data.interval_fallback),this.data.interval_fallback=null,this.container.remove(),t(this.element).removeAttr("data-tc-id"),t(this.element).removeData("tc-id")},_.prototype.setOptions=function(e){if(null===this.config&&(this.default_options.ref_date=new Date,this.config=t.extend(!0,{},this.default_options)),t.extend(!0,this.config,e),d=this.config.use_top_frame?window.top:window,o(),this.data.total_duration=this.config.total_duration,"string"==typeof this.data.total_duration)if("undefined"!=typeof m[this.data.total_duration])this.data.total_duration=m[this.data.total_duration];else if("Auto"===this.data.total_duration)for(var a=0;a<Object.keys(this.config.time).length;a++){var i=Object.keys(this.config.time)[a];if(this.config.time[i].show){this.data.total_duration=m[c[i]];break}}else this.data.total_duration=m.Years,console.error("Valid values for TimeCircles config.total_duration are either numeric, or (string) Years, Months, Days, Hours, Minutes, Auto")},_.prototype.addListener=function(t,e,a){"function"==typeof t&&("undefined"==typeof a&&(a="visible"),this.listeners[a].push({func:t,scope:e}))},_.prototype.notifyListeners=function(t,e,a,i){for(var n=0;n<this.listeners[i].length;n++){var s=this.listeners[i][n];s.func.apply(s.scope,[t,e,a])}},_.prototype.default_options={ref_date:new Date,start:!0,animation:"smooth",count_past_zero:!0,circle_bg_color:"#fff",use_background:!0,fg_width:.1,bg_width:1.2,text_size:.07,total_duration:"Auto",direction:"Clockwise",use_top_frame:!1,start_angle:0,time:{Days:{show:!0,text:"Days",color:"#FC6"},Hours:{show:!0,text:"Hours",color:"#9CF"},Minutes:{show:!0,text:"Minutes",color:"#BFB"},Seconds:{show:!0,text:"Seconds",color:"#F99"}}};var b=function(t,e){this.elements=t,this.options=e,this.foreach()};b.prototype.getInstance=function(e){var a,i=t(e).data("tc-id");if("undefined"==typeof i&&(i=n(),t(e).attr("data-tc-id",i)),"undefined"==typeof p[i]){var s=this.options,r=t(e).data("options");"string"==typeof r&&(r=JSON.parse(r)),"object"==typeof r&&(s=t.extend(!0,{},this.options,r)),a=new _(e,s),p[i]=a}else a=p[i],"undefined"!=typeof this.options&&a.setOptions(this.options);return a},b.prototype.addTime=function(t){this.foreach(function(e){e.addTime(t)})},b.prototype.foreach=function(t){var e=this;return this.elements.each(function(){var a=e.getInstance(this);"function"==typeof t&&t(a)}),this},b.prototype.start=function(){return this.foreach(function(t){t.start()}),this},b.prototype.stop=function(){return this.foreach(function(t){t.stop()}),this},b.prototype.restart=function(){return this.foreach(function(t){t.restart()}),this},b.prototype.rebuild=function(){return this.foreach(function(t){t.initialize(!1)}),this},b.prototype.getTime=function(){return this.getInstance(this.elements[0]).timeLeft()},b.prototype.addListener=function(t,e){"undefined"==typeof e&&(e="visible");var a=this;return this.foreach(function(i){i.addListener(t,a.elements,e)}),this},b.prototype.destroy=function(){return this.foreach(function(t){t.destroy()}),this},b.prototype.end=function(){return this.elements},t.fn.TimeCircles=function(t){return new b(this,t)}}(jQuery);
(function($){
"use strict";
function randomClass(){var a=Math.ceil(Math.random()*classAmount);return type=classesArray[a]}function triggerOnce(a,b){"random"==b&&(b=randomClass()),$(a).removeClass("trigger infinite "+triggerClasses).addClass("trigger").addClass(b).one("webkitAnimationEnd oAnimationEnd MSAnimationEnd animationend",function(){$(this).removeClass("trigger infinite "+triggerClasses)})}function triggerInfinite(a,b){"random"==b&&(b=randomClass()),$(a).removeClass("trigger infinite "+triggerClasses).addClass("trigger infinite").addClass(b).one("webkitAnimationEnd oAnimationEnd MSAnimationEnd animationend",function(){$(this).removeClass("trigger infinite "+triggerClasses)})}!function(a){a.fn.visible=function(b,c,d){var e=a(this).eq(0),f=e.get(0),g=a(window),h=g.scrollTop(),i=h+g.height(),j=g.scrollLeft(),k=j+g.width(),l=e.offset().top,m=l+e.height(),n=e.offset().left,o=n+e.width(),p=b===!0?m:l,q=b===!0?l:m,r=b===!0?o:n,s=b===!0?n:o,t=c===!0?f.offsetWidth*f.offsetHeight:!0,d=d?d:"both";return"both"===d?!!t&&i>=q&&p>=h&&k>=s&&r>=j:"vertical"===d?!!t&&i>=q&&p>=h:"horizontal"===d?!!t&&k>=s&&r>=j:void 0},a.fn.fireAnimations=function(){function c(){a(window).width()>=960?a(".animate").each(function(b,c){var c=a(c),d=a(this).attr("data-anim-type"),e=a(this).attr("data-anim-delay");c.visible(!0)&&setTimeout(function(){c.addClass(d)},e)}):a(".animate").each(function(b,c){var c=a(c),d=a(this).attr("data-anim-type"),e=a(this).attr("data-anim-delay");setTimeout(function(){c.addClass(d)},e)})}a(document).ready(function(){a("html").removeClass("no-js").addClass("js"),c()}),a(window).scroll(function(){c(),a(window).scrollTop()+a(window).height()==a(document).height()&&c()})},a(".animate").fireAnimations()}(jQuery);var triggerClasses="flash strobe shake bounce tada wave spin pullback wobble pulse pulsate heartbeat panic explode",classesArray=new Array;classesArray=triggerClasses.split(" ");var classAmount=classesArray.length;$(window).resize(function(){$(".animate").fireAnimations()});
})(jQuery);
(function(){
var MutationObserver, Util, WeakMap, getComputedStyle, getComputedStyleRX,
bind=function(fn, me){ return function(){ return fn.apply(me, arguments); };},
indexOf=[].indexOf||function(item){ for (var i=0, l=this.length; i < l; i++){ if(i in this&&this[i]===item) return i; } return -1; };
Util=(function(){
function Util(){}
Util.prototype.extend=function(custom, defaults){
var key, value;
for (key in defaults){
value=defaults[key];
if(custom[key]==null){
custom[key]=value;
}}
return custom;
};
Util.prototype.isMobile=function(agent){
return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(agent);
};
Util.prototype.createEvent=function(event, bubble, cancel, detail){
var customEvent;
if(bubble==null){
bubble=false;
}
if(cancel==null){
cancel=false;
}
if(detail==null){
detail=null;
}
if(document.createEvent!=null){
customEvent=document.createEvent('CustomEvent');
customEvent.initCustomEvent(event, bubble, cancel, detail);
}else if(document.createEventObject!=null){
customEvent=document.createEventObject();
customEvent.eventType=event;
}else{
customEvent.eventName=event;
}
return customEvent;
};
Util.prototype.emitEvent=function(elem, event){
if(elem.dispatchEvent!=null){
return elem.dispatchEvent(event);
}else if(event in (elem!=null)){
return elem[event]();
}else if(("on" + event) in (elem!=null)){
return elem["on" + event]();
}};
Util.prototype.addEvent=function(elem, event, fn){
if(elem.addEventListener!=null){
return elem.addEventListener(event, fn, false);
}else if(elem.attachEvent!=null){
return elem.attachEvent("on" + event, fn);
}else{
return elem[event]=fn;
}};
Util.prototype.removeEvent=function(elem, event, fn){
if(elem.removeEventListener!=null){
return elem.removeEventListener(event, fn, false);
}else if(elem.detachEvent!=null){
return elem.detachEvent("on" + event, fn);
}else{
return delete elem[event];
}};
Util.prototype.innerHeight=function(){
if('innerHeight' in window){
return window.innerHeight;
}else{
return document.documentElement.clientHeight;
}};
return Util;
})();
WeakMap=this.WeakMap||this.MozWeakMap||(WeakMap=(function(){
function WeakMap(){
this.keys=[];
this.values=[];
}
WeakMap.prototype.get=function(key){
var i, item, j, len, ref;
ref=this.keys;
for (i=j = 0, len=ref.length; j < len; i=++j){
item=ref[i];
if(item===key){
return this.values[i];
}}
};
WeakMap.prototype.set=function(key, value){
var i, item, j, len, ref;
ref=this.keys;
for (i=j = 0, len=ref.length; j < len; i=++j){
item=ref[i];
if(item===key){
this.values[i]=value;
return;
}}
this.keys.push(key);
return this.values.push(value);
};
return WeakMap;
})());
MutationObserver=this.MutationObserver||this.WebkitMutationObserver||this.MozMutationObserver||(MutationObserver=(function(){
function MutationObserver(){
if(typeof console!=="undefined"&&console!==null){
console.warn('MutationObserver is not supported by your browser.');
}
if(typeof console!=="undefined"&&console!==null){
console.warn('WOW.js cannot detect dom mutations, please call .sync() after loading new content.');
}}
MutationObserver.notSupported=true;
MutationObserver.prototype.observe=function(){};
return MutationObserver;
})());
getComputedStyle=this.getComputedStyle||function(el, pseudo){
this.getPropertyValue=function(prop){
var ref;
if(prop==='float'){
prop='styleFloat';
}
if(getComputedStyleRX.test(prop)){
prop.replace(getComputedStyleRX, function(_, _char){
return _char.toUpperCase();
});
}
return ((ref=el.currentStyle)!=null ? ref[prop]:void 0)||null;
};
return this;
};
getComputedStyleRX=/(\-([a-z]){1})/g;
this.WOW=(function(){
WOW.prototype.defaults={
boxClass: 'wow',
animateClass: 'animated',
offset: 0,
mobile: true,
live: true,
callback: null,
scrollContainer: null
};
function WOW(options){
if(options==null){
options={};}
this.scrollCallback=bind(this.scrollCallback, this);
this.scrollHandler=bind(this.scrollHandler, this);
this.resetAnimation=bind(this.resetAnimation, this);
this.start=bind(this.start, this);
this.scrolled=true;
this.config=this.util().extend(options, this.defaults);
if(options.scrollContainer!=null){
this.config.scrollContainer=document.querySelector(options.scrollContainer);
}
this.animationNameCache=new WeakMap();
this.wowEvent=this.util().createEvent(this.config.boxClass);
}
WOW.prototype.init=function(){
var ref;
this.element=window.document.documentElement;
if((ref=document.readyState)==="interactive"||ref==="complete"){
this.start();
}else{
this.util().addEvent(document, 'DOMContentLoaded', this.start);
}
return this.finished=[];
};
WOW.prototype.start=function(){
var box, j, len, ref;
this.stopped=false;
this.boxes=(function(){
var j, len, ref, results;
ref=this.element.querySelectorAll("." + this.config.boxClass);
results=[];
for (j=0, len=ref.length; j < len; j++){
box=ref[j];
results.push(box);
}
return results;
}).call(this);
this.all=(function(){
var j, len, ref, results;
ref=this.boxes;
results=[];
for (j=0, len=ref.length; j < len; j++){
box=ref[j];
results.push(box);
}
return results;
}).call(this);
if(this.boxes.length){
if(this.disabled()){
this.resetStyle();
}else{
ref=this.boxes;
for (j=0, len=ref.length; j < len; j++){
box=ref[j];
this.applyStyle(box, true);
}}
}
if(!this.disabled()){
this.util().addEvent(this.config.scrollContainer||window, 'scroll', this.scrollHandler);
this.util().addEvent(window, 'resize', this.scrollHandler);
this.interval=setInterval(this.scrollCallback, 50);
}
if(this.config.live){
return new MutationObserver((function(_this){
return function(records){
var k, len1, node, record, results;
results=[];
for (k=0, len1=records.length; k < len1; k++){
record=records[k];
results.push((function(){
var l, len2, ref1, results1;
ref1=record.addedNodes||[];
results1=[];
for (l=0, len2=ref1.length; l < len2; l++){
node=ref1[l];
results1.push(this.doSync(node));
}
return results1;
}).call(_this));
}
return results;
};})(this)).observe(document.body, {
childList: true,
subtree: true
});
}};
WOW.prototype.stop=function(){
this.stopped=true;
this.util().removeEvent(this.config.scrollContainer||window, 'scroll', this.scrollHandler);
this.util().removeEvent(window, 'resize', this.scrollHandler);
if(this.interval!=null){
return clearInterval(this.interval);
}};
WOW.prototype.sync=function(element){
if(MutationObserver.notSupported){
return this.doSync(this.element);
}};
WOW.prototype.doSync=function(element){
var box, j, len, ref, results;
if(element==null){
element=this.element;
}
if(element.nodeType!==1){
return;
}
element=element.parentNode||element;
ref=element.querySelectorAll("." + this.config.boxClass);
results=[];
for (j=0, len=ref.length; j < len; j++){
box=ref[j];
if(indexOf.call(this.all, box) < 0){
this.boxes.push(box);
this.all.push(box);
if(this.stopped||this.disabled()){
this.resetStyle();
}else{
this.applyStyle(box, true);
}
results.push(this.scrolled=true);
}else{
results.push(void 0);
}}
return results;
};
WOW.prototype.show=function(box){
this.applyStyle(box);
box.className=box.className + " " + this.config.animateClass;
if(this.config.callback!=null){
this.config.callback(box);
}
this.util().emitEvent(box, this.wowEvent);
this.util().addEvent(box, 'animationend', this.resetAnimation);
this.util().addEvent(box, 'oanimationend', this.resetAnimation);
this.util().addEvent(box, 'webkitAnimationEnd', this.resetAnimation);
this.util().addEvent(box, 'MSAnimationEnd', this.resetAnimation);
return box;
};
WOW.prototype.applyStyle=function(box, hidden){
var delay, duration, iteration;
duration=box.getAttribute('data-wow-duration');
delay=box.getAttribute('data-wow-delay');
iteration=box.getAttribute('data-wow-iteration');
return this.animate((function(_this){
return function(){
return _this.customStyle(box, hidden, duration, delay, iteration);
};})(this));
};
WOW.prototype.animate=(function(){
if('requestAnimationFrame' in window){
return function(callback){
return window.requestAnimationFrame(callback);
};}else{
return function(callback){
return callback();
};}})();
WOW.prototype.resetStyle=function(){
var box, j, len, ref, results;
ref=this.boxes;
results=[];
for (j=0, len=ref.length; j < len; j++){
box=ref[j];
results.push(box.style.visibility='visible');
}
return results;
};
WOW.prototype.resetAnimation=function(event){
var target;
if(event.type.toLowerCase().indexOf('animationend') >=0){
target=event.target||event.srcElement;
return target.className=target.className.replace(this.config.animateClass, '').trim();
}};
WOW.prototype.customStyle=function(box, hidden, duration, delay, iteration){
if(hidden){
this.cacheAnimationName(box);
}
box.style.visibility=hidden ? 'hidden':'visible';
if(duration){
this.vendorSet(box.style, {
animationDuration: duration
});
}
if(delay){
this.vendorSet(box.style, {
animationDelay: delay
});
}
if(iteration){
this.vendorSet(box.style, {
animationIterationCount: iteration
});
}
this.vendorSet(box.style, {
animationName: hidden ? 'none':this.cachedAnimationName(box)
});
return box;
};
WOW.prototype.vendors=["moz", "webkit"];
WOW.prototype.vendorSet=function(elem, properties){
var name, results, value, vendor;
results=[];
for (name in properties){
value=properties[name];
elem["" + name]=value;
results.push((function(){
var j, len, ref, results1;
ref=this.vendors;
results1=[];
for (j=0, len=ref.length; j < len; j++){
vendor=ref[j];
results1.push(elem["" + vendor + (name.charAt(0).toUpperCase()) + (name.substr(1))]=value);
}
return results1;
}).call(this));
}
return results;
};
WOW.prototype.vendorCSS=function(elem, property){
var j, len, ref, result, style, vendor;
style=getComputedStyle(elem);
result=style.getPropertyCSSValue(property);
ref=this.vendors;
for (j=0, len=ref.length; j < len; j++){
vendor=ref[j];
result=result||style.getPropertyCSSValue("-" + vendor + "-" + property);
}
return result;
};
WOW.prototype.animationName=function(box){
var animationName, error;
try {
animationName=this.vendorCSS(box, 'animation-name').cssText;
} catch (error){
animationName=getComputedStyle(box).getPropertyValue('animation-name');
}
if(animationName==='none'){
return '';
}else{
return animationName;
}};
WOW.prototype.cacheAnimationName=function(box){
return this.animationNameCache.set(box, this.animationName(box));
};
WOW.prototype.cachedAnimationName=function(box){
return this.animationNameCache.get(box);
};
WOW.prototype.scrollHandler=function(){
return this.scrolled=true;
};
WOW.prototype.scrollCallback=function(){
var box;
if(this.scrolled){
this.scrolled=false;
this.boxes=(function(){
var j, len, ref, results;
ref=this.boxes;
results=[];
for (j=0, len=ref.length; j < len; j++){
box=ref[j];
if(!(box)){
continue;
}
if(this.isVisible(box)){
this.show(box);
continue;
}
results.push(box);
}
return results;
}).call(this);
if(!(this.boxes.length||this.config.live)){
return this.stop();
}}
};
WOW.prototype.offsetTop=function(element){
var top;
while (element.offsetTop===void 0){
element=element.parentNode;
}
top=element.offsetTop;
while (element=element.offsetParent){
top +=element.offsetTop;
}
return top;
};
WOW.prototype.isVisible=function(box){
var bottom, offset, top, viewBottom, viewTop;
offset=box.getAttribute('data-wow-offset')||this.config.offset;
viewTop=(this.config.scrollContainer&&this.config.scrollContainer.scrollTop)||window.pageYOffset;
viewBottom=viewTop + Math.min(this.element.clientHeight, this.util().innerHeight()) - offset;
top=this.offsetTop(box);
bottom=top + box.clientHeight;
return top <=viewBottom&&bottom >=viewTop;
};
WOW.prototype.util=function(){
return this._util!=null ? this._util:this._util=new Util();
};
WOW.prototype.disabled=function(){
return !this.config.mobile&&this.util().isMobile(navigator.userAgent);
};
return WOW;
})();
}).call(this);
var Base=function(){
};
Base.extend=function(_instance, _static){
"use strict";
var extend=Base.prototype.extend;
Base._prototyping=true;
var proto=new this();
extend.call(proto, _instance);
proto.base=function(){
};
delete Base._prototyping;
var constructor=proto.constructor;
var klass=proto.constructor=function(){
if(!Base._prototyping){
if(this._constructing||this.constructor==klass){
this._constructing=true;
constructor.apply(this, arguments);
delete this._constructing;
}else if(arguments[0]!==null){
return (arguments[0].extend||extend).call(arguments[0], proto);
}}
};
klass.ancestor=this;
klass.extend=this.extend;
klass.forEach=this.forEach;
klass.implement=this.implement;
klass.prototype=proto;
klass.toString=this.toString;
klass.valueOf=function(type){
return (type=="object") ? klass:constructor.valueOf();
};
extend.call(klass, _static);
if(typeof klass.init=="function") klass.init();
return klass;
};
Base.prototype={
extend: function(source, value){
if(arguments.length > 1){
var ancestor=this[source];
if(ancestor&&(typeof value=="function") &&
(!ancestor.valueOf||ancestor.valueOf()!=value.valueOf()) &&
/\bbase\b/.test(value)){
var method=value.valueOf();
value=function(){
var previous=this.base||Base.prototype.base;
this.base=ancestor;
var returnValue=method.apply(this, arguments);
this.base=previous;
return returnValue;
};
value.valueOf=function(type){
return (type=="object") ? value:method;
};
value.toString=Base.toString;
}
this[source]=value;
}else if(source){
var extend=Base.prototype.extend;
if(!Base._prototyping&&typeof this!="function"){
extend=this.extend||extend;
}
var proto={toSource: null};
var hidden=["constructor", "toString", "valueOf"];
var i=Base._prototyping ? 0:1;
while (key=hidden[i++]){
if(source[key]!=proto[key]){
extend.call(this, key, source[key]);
}}
for (var key in source){
if(!proto[key]) extend.call(this, key, source[key]);
}}
return this;
}};
Base=Base.extend({
constructor: function(){
this.extend(arguments[0]);
}}, {
ancestor: Object,
version: "1.1",
forEach: function(object, block, context){
for (var key in object){
if(this.prototype[key]===undefined){
block.call(context, object[key], key, object);
}}
},
implement: function(){
for (var i=0; i < arguments.length; i++){
if(typeof arguments[i]=="function"){
arguments[i](this.prototype);
}else{
this.prototype.extend(arguments[i]);
}}
return this;
},
toString: function(){
return String(this.valueOf());
}});
var FlipClock;
(function($){
"use strict";
FlipClock=function(obj, digit, options){
if(digit instanceof Object&&digit instanceof Date===false){
options=digit;
digit=0;
}
return new FlipClock.Factory(obj, digit, options);
};
FlipClock.Lang={};
FlipClock.Base=Base.extend({
buildDate: '2014-12-12',
version: '0.7.7',
constructor: function(_default, options){
if(typeof _default!=="object"){
_default={};}
if(typeof options!=="object"){
options={};}
this.setOptions($.extend(true, {}, _default, options));
},
callback: function(method){
if(typeof method==="function"){
var args=[];
for(var x=1; x <=arguments.length; x++){
if(arguments[x]){
args.push(arguments[x]);
}}
method.apply(this, args);
}},
log: function(str){
if(window.console&&console.log){
console.log(str);
}},
getOption: function(index){
if(this[index]){
return this[index];
}
return false;
},
getOptions: function(){
return this;
},
setOption: function(index, value){
this[index]=value;
},
setOptions: function(options){
for(var key in options){
if(typeof options[key]!=="undefined"){
this.setOption(key, options[key]);
}}
}});
}(jQuery));
(function($){
"use strict";
FlipClock.Face=FlipClock.Base.extend({
autoStart: true,
dividers: [],
factory: false,
lists: [],
constructor: function(factory, options){
this.dividers=[];
this.lists=[];
this.base(options);
this.factory=factory;
},
build: function(){
if(this.autoStart){
this.start();
}},
createDivider: function(label, css, excludeDots){
if(typeof css=="boolean"||!css){
excludeDots=css;
css=label;
}
var dots=[
'<span class="'+this.factory.classes.dot+' top"></span>',
'<span class="'+this.factory.classes.dot+' bottom"></span>'
].join('');
if(excludeDots){
dots='';
}
label=this.factory.localize(label);
var html=[
'<span class="'+this.factory.classes.divider+' '+(css ? css:'').toLowerCase()+'">',
'<span class="'+this.factory.classes.label+'">'+(label ? label:'')+'</span>',
dots,
'</span>'
];
var $html=$(html.join(''));
this.dividers.push($html);
return $html;
},
createList: function(digit, options){
if(typeof digit==="object"){
options=digit;
digit=0;
}
var obj=new FlipClock.List(this.factory, digit, options);
this.lists.push(obj);
return obj;
},
reset: function(){
this.factory.time=new FlipClock.Time(this.factory,
this.factory.original ? Math.round(this.factory.original):0,
{
minimumDigits: this.factory.minimumDigits
}
);
this.flip(this.factory.original, false);
},
appendDigitToClock: function(obj){
obj.$el.append(false);
},
addDigit: function(digit){
var obj=this.createList(digit, {
classes: {
active: this.factory.classes.active,
before: this.factory.classes.before,
flip: this.factory.classes.flip
}});
this.appendDigitToClock(obj);
},
start: function(){},
stop: function(){},
autoIncrement: function(){
if(!this.factory.countdown){
this.increment();
}else{
this.decrement();
}},
increment: function(){
this.factory.time.addSecond();
},
decrement: function(){
if(this.factory.time.getTimeSeconds()==0){
this.factory.stop()
}else{
this.factory.time.subSecond();
}},
flip: function(time, doNotAddPlayClass){
var t=this;
$.each(time, function(i, digit){
var list=t.lists[i];
if(list){
if(!doNotAddPlayClass&&digit!=list.digit){
list.play();
}
list.select(digit);
}else{
t.addDigit(digit);
}});
}});
}(jQuery));
(function($){
"use strict";
FlipClock.Factory=FlipClock.Base.extend({
animationRate: 1000,
autoStart: true,
callbacks: {
destroy: false,
create: false,
init: false,
interval: false,
start: false,
stop: false,
reset: false
},
classes: {
active: 'flip-clock-active',
before: 'flip-clock-before',
divider: 'flip-clock-divider',
dot: 'flip-clock-dot',
label: 'flip-clock-label',
flip: 'flip',
play: 'play',
wrapper: 'flip-clock-wrapper'
},
clockFace: 'HourlyCounter',
countdown: false,
defaultClockFace: 'HourlyCounter',
defaultLanguage: 'english',
$el: false,
face: true,
lang: false,
language: 'english',
minimumDigits: 0,
original: false,
running: false,
time: false,
timer: false,
$wrapper: false,
constructor: function(obj, digit, options){
if(!options){
options={};}
this.lists=[];
this.running=false;
this.base(options);
this.$el=$(obj).addClass(this.classes.wrapper);
this.$wrapper=this.$el;
this.original=(digit instanceof Date) ? digit:(digit ? Math.round(digit):0);
this.time=new FlipClock.Time(this, this.original, {
minimumDigits: this.minimumDigits,
animationRate: this.animationRate
});
this.timer=new FlipClock.Timer(this, options);
this.loadLanguage(this.language);
this.loadClockFace(this.clockFace, options);
if(this.autoStart){
this.start();
}},
loadClockFace: function(name, options){
var face, suffix='Face', hasStopped=false;
name=name.ucfirst()+suffix;
if(this.face.stop){
this.stop();
hasStopped=true;
}
this.$el.html('');
this.time.minimumDigits=this.minimumDigits;
if(FlipClock[name]){
face=new FlipClock[name](this, options);
}else{
face=new FlipClock[this.defaultClockFace+suffix](this, options);
}
face.build();
this.face=face
if(hasStopped){
this.start();
}
return this.face;
},
loadLanguage: function(name){
var lang;
if(FlipClock.Lang[name.ucfirst()]){
lang=FlipClock.Lang[name.ucfirst()];
}
else if(FlipClock.Lang[name]){
lang=FlipClock.Lang[name];
}else{
lang=FlipClock.Lang[this.defaultLanguage];
}
return this.lang=lang;
},
localize: function(index, obj){
var lang=this.lang;
if(!index){
return null;
}
var lindex=index.toLowerCase();
if(typeof obj=="object"){
lang=obj;
}
if(lang&&lang[lindex]){
return lang[lindex];
}
return index;
},
start: function(callback){
var t=this;
if(!t.running&&(!t.countdown||t.countdown&&t.time.time > 0)){
t.face.start(t.time);
t.timer.start(function(){
t.flip();
if(typeof callback==="function"){
callback();
}});
}else{
t.log('Trying to start timer when countdown already at 0');
}},
stop: function(callback){
this.face.stop();
this.timer.stop(callback);
for(var x in this.lists){
if(this.lists.hasOwnProperty(x)){
this.lists[x].stop();
}}
},
reset: function(callback){
this.timer.reset(callback);
this.face.reset();
},
setTime: function(time){
this.time.time=time;
this.flip(true);
},
getTime: function(time){
return this.time;
},
setCountdown: function(value){
var running=this.running;
this.countdown=value ? true:false;
if(running){
this.stop();
this.start();
}},
flip: function(doNotAddPlayClass){
this.face.flip(false, doNotAddPlayClass);
}});
}(jQuery));
(function($){
"use strict";
FlipClock.List=FlipClock.Base.extend({
digit: 0,
classes: {
active: 'flip-clock-active',
before: 'flip-clock-before',
flip: 'flip'
},
factory: false,
$el: false,
$obj: false,
items: [],
lastDigit: 0,
constructor: function(factory, digit, options){
this.factory=factory;
this.digit=digit;
this.lastDigit=digit;
this.$el=this.createList();
this.$obj=this.$el;
if(digit > 0){
this.select(digit);
}
this.factory.$el.append(this.$el);
},
select: function(digit){
if(typeof digit==="undefined"){
digit=this.digit;
}else{
this.digit=digit;
}
if(this.digit!=this.lastDigit){
var $delete=this.$el.find('.'+this.classes.before).removeClass(this.classes.before);
this.$el.find('.'+this.classes.active).removeClass(this.classes.active)
.addClass(this.classes.before);
this.appendListItem(this.classes.active, this.digit);
$delete.remove();
this.lastDigit=this.digit;
}},
play: function(){
this.$el.addClass(this.factory.classes.play);
},
stop: function(){
var t=this;
setTimeout(function(){
t.$el.removeClass(t.factory.classes.play);
}, this.factory.timer.interval);
},
createListItem: function(css, value){
return [
'<li class="'+(css ? css:'')+'">',
'<a href="#">',
'<div class="up">',
'<div class="shadow"></div>',
'<div class="inn">'+(value ? value:'')+'</div>',
'</div>',
'<div class="down">',
'<div class="shadow"></div>',
'<div class="inn">'+(value ? value:'')+'</div>',
'</div>',
'</a>',
'</li>'
].join('');
},
appendListItem: function(css, value){
var html=this.createListItem(css, value);
this.$el.append(html);
},
createList: function(){
var lastDigit=this.getPrevDigit() ? this.getPrevDigit():this.digit;
var html=$([
'<ul class="'+this.classes.flip+' '+(this.factory.running ? this.factory.classes.play:'')+'">',
this.createListItem(this.classes.before, lastDigit),
this.createListItem(this.classes.active, this.digit),
'</ul>'
].join(''));
return html;
},
getNextDigit: function(){
return this.digit==9 ? 0:this.digit + 1;
},
getPrevDigit: function(){
return this.digit==0 ? 9:this.digit - 1;
}});
}(jQuery));
(function($){
"use strict";
String.prototype.ucfirst=function(){
return this.substr(0, 1).toUpperCase() + this.substr(1);
};
$.fn.FlipClock=function(digit, options){
return new FlipClock($(this), digit, options);
};
$.fn.flipClock=function(digit, options){
return $.fn.FlipClock(digit, options);
};}(jQuery));
(function($){
"use strict";
FlipClock.Time=FlipClock.Base.extend({
time: 0,
factory: false,
minimumDigits: 0,
constructor: function(factory, time, options){
if(typeof options!="object"){
options={};}
if(!options.minimumDigits){
options.minimumDigits=factory.minimumDigits;
}
this.base(options);
this.factory=factory;
if(time){
this.time=time;
}},
convertDigitsToArray: function(str){
var data=[];
str=str.toString();
for(var x=0;x < str.length; x++){
if(str[x].match(/^\d*$/g)){
data.push(str[x]);
}}
return data;
},
digit: function(i){
var timeStr=this.toString();
var length=timeStr.length;
if(timeStr[length - i]){
return timeStr[length - i];
}
return false;
},
digitize: function(obj){
var data=[];
$.each(obj, function(i, value){
value=value.toString();
if(value.length==1){
value='0'+value;
}
for(var x=0; x < value.length; x++){
data.push(value.charAt(x));
}});
if(data.length > this.minimumDigits){
this.minimumDigits=data.length;
}
if(this.minimumDigits > data.length){
for(var x=data.length; x < this.minimumDigits; x++){
data.unshift('0');
}}
return data;
},
getDateObject: function(){
if(this.time instanceof Date){
return this.time;
}
return new Date((new Date()).getTime() + this.getTimeSeconds() * 1000);
},
getDayCounter: function(includeSeconds){
var digits=[
this.getDays(),
this.getHours(true),
this.getMinutes(true)
];
if(includeSeconds){
digits.push(this.getSeconds(true));
}
return this.digitize(digits);
},
getDays: function(mod){
var days=this.getTimeSeconds() / 60 / 60 / 24;
if(mod){
days=days % 7;
}
return Math.floor(days);
},
getHourCounter: function(){
var obj=this.digitize([
this.getHours(),
this.getMinutes(true),
this.getSeconds(true)
]);
return obj;
},
getHourly: function(){
return this.getHourCounter();
},
getHours: function(mod){
var hours=this.getTimeSeconds() / 60 / 60;
if(mod){
hours=hours % 24;
}
return Math.floor(hours);
},
getMilitaryTime: function(date, showSeconds){
if(typeof showSeconds==="undefined"){
showSeconds=true;
}
if(!date){
date=this.getDateObject();
}
var data=[
date.getHours(),
date.getMinutes()
];
if(showSeconds===true){
data.push(date.getSeconds());
}
return this.digitize(data);
},
getMinutes: function(mod){
var minutes=this.getTimeSeconds() / 60;
if(mod){
minutes=minutes % 60;
}
return Math.floor(minutes);
},
getMinuteCounter: function(){
var obj=this.digitize([
this.getMinutes(),
this.getSeconds(true)
]);
return obj;
},
getTimeSeconds: function(date){
if(!date){
date=new Date();
}
if(this.time instanceof Date){
if(this.factory.countdown){
return Math.max(this.time.getTime()/1000 - date.getTime()/1000,0);
}else{
return date.getTime()/1000 - this.time.getTime()/1000 ;
}}else{
return this.time;
}},
getTime: function(date, showSeconds){
if(typeof showSeconds==="undefined"){
showSeconds=true;
}
if(!date){
date=this.getDateObject();
}
console.log(date);
var hours=date.getHours();
var merid=hours > 12 ? 'PM':'AM';
var data=[
hours > 12 ? hours - 12:(hours===0 ? 12:hours),
date.getMinutes()
];
if(showSeconds===true){
data.push(date.getSeconds());
}
return this.digitize(data);
},
getSeconds: function(mod){
var seconds=this.getTimeSeconds();
if(mod){
if(seconds==60){
seconds=0;
}else{
seconds=seconds % 60;
}}
return Math.ceil(seconds);
},
getWeeks: function(mod){
var weeks=this.getTimeSeconds() / 60 / 60 / 24 / 7;
if(mod){
weeks=weeks % 52;
}
return Math.floor(weeks);
},
removeLeadingZeros: function(totalDigits, digits){
var total=0;
var newArray=[];
$.each(digits, function(i, digit){
if(i < totalDigits){
total +=parseInt(digits[i], 10);
}else{
newArray.push(digits[i]);
}});
if(total===0){
return newArray;
}
return digits;
},
addSeconds: function(x){
if(this.time instanceof Date){
this.time.setSeconds(this.time.getSeconds() + x);
}else{
this.time +=x;
}},
addSecond: function(){
this.addSeconds(1);
},
subSeconds: function(x){
if(this.time instanceof Date){
this.time.setSeconds(this.time.getSeconds() - x);
}else{
this.time -=x;
}},
subSecond: function(){
this.subSeconds(1);
},
toString: function(){
return this.getTimeSeconds().toString();
}
/*
getYears: function(){
return Math.floor(this.time / 60 / 60 / 24 / 7 / 52);
},
getDecades: function(){
return Math.floor(this.getWeeks() / 10);
}*/
});
}(jQuery));
(function($){
"use strict";
FlipClock.Timer=FlipClock.Base.extend({
callbacks: {
destroy: false,
create: false,
init: false,
interval: false,
start: false,
stop: false,
reset: false
},
count: 0,
factory: false,
interval: 1000,
animationRate: 1000,
constructor: function(factory, options){
this.base(options);
this.factory=factory;
this.callback(this.callbacks.init);
this.callback(this.callbacks.create);
},
getElapsed: function(){
return this.count * this.interval;
},
getElapsedTime: function(){
return new Date(this.time + this.getElapsed());
},
reset: function(callback){
clearInterval(this.timer);
this.count=0;
this._setInterval(callback);
this.callback(this.callbacks.reset);
},
start: function(callback){
this.factory.running=true;
this._createTimer(callback);
this.callback(this.callbacks.start);
},
stop: function(callback){
this.factory.running=false;
this._clearInterval(callback);
this.callback(this.callbacks.stop);
this.callback(callback);
},
_clearInterval: function(){
clearInterval(this.timer);
},
_createTimer: function(callback){
this._setInterval(callback);
},
_destroyTimer: function(callback){
this._clearInterval();
this.timer=false;
this.callback(callback);
this.callback(this.callbacks.destroy);
},
_interval: function(callback){
this.callback(this.callbacks.interval);
this.callback(callback);
this.count++;
},
_setInterval: function(callback){
var t=this;
t._interval(callback);
t.timer=setInterval(function(){
t._interval(callback);
}, this.interval);
}});
}(jQuery));
(function($){
FlipClock.TwentyFourHourClockFace=FlipClock.Face.extend({
constructor: function(factory, options){
this.base(factory, options);
},
build: function(time){
var t=this;
var children=this.factory.$el.find('ul');
if(!this.factory.time.time){
this.factory.original=new Date();
this.factory.time=new FlipClock.Time(this.factory, this.factory.original);
}
var time=time ? time:this.factory.time.getMilitaryTime(false, this.showSeconds);
if(time.length > children.length){
$.each(time, function(i, digit){
t.createList(digit);
});
}
this.createDivider();
this.createDivider();
$(this.dividers[0]).insertBefore(this.lists[this.lists.length - 2].$el);
$(this.dividers[1]).insertBefore(this.lists[this.lists.length - 4].$el);
this.base();
},
flip: function(time, doNotAddPlayClass){
this.autoIncrement();
time=time ? time:this.factory.time.getMilitaryTime(false, this.showSeconds);
this.base(time, doNotAddPlayClass);
}});
}(jQuery));
(function($){
FlipClock.CounterFace=FlipClock.Face.extend({
shouldAutoIncrement: false,
constructor: function(factory, options){
if(typeof options!="object"){
options={};}
factory.autoStart=options.autoStart ? true:false;
if(options.autoStart){
this.shouldAutoIncrement=true;
}
factory.increment=function(){
factory.countdown=false;
factory.setTime(factory.getTime().getTimeSeconds() + 1);
};
factory.decrement=function(){
factory.countdown=true;
var time=factory.getTime().getTimeSeconds();
if(time > 0){
factory.setTime(time - 1);
}};
factory.setValue=function(digits){
factory.setTime(digits);
};
factory.setCounter=function(digits){
factory.setTime(digits);
};
this.base(factory, options);
},
build: function(){
var t=this;
var children=this.factory.$el.find('ul');
var time=this.factory.getTime().digitize([this.factory.getTime().time]);
if(time.length > children.length){
$.each(time, function(i, digit){
var list=t.createList(digit);
list.select(digit);
});
}
$.each(this.lists, function(i, list){
list.play();
});
this.base();
},
flip: function(time, doNotAddPlayClass){
if(this.shouldAutoIncrement){
this.autoIncrement();
}
if(!time){
time=this.factory.getTime().digitize([this.factory.getTime().time]);
}
this.base(time, doNotAddPlayClass);
},
reset: function(){
this.factory.time=new FlipClock.Time(this.factory,
this.factory.original ? Math.round(this.factory.original):0
);
this.flip();
}});
}(jQuery));
(function($){
FlipClock.DailyCounterFace=FlipClock.Face.extend({
showSeconds: true,
constructor: function(factory, options){
this.base(factory, options);
},
build: function(time){
var t=this;
var children=this.factory.$el.find('ul');
var offset=0;
time=time ? time:this.factory.time.getDayCounter(this.showSeconds);
if(time.length > children.length){
$.each(time, function(i, digit){
t.createList(digit);
});
}
if(this.showSeconds){
$(this.createDivider('Seconds')).insertBefore(this.lists[this.lists.length - 2].$el);
}else{
offset=2;
}
$(this.createDivider('Minutes')).insertBefore(this.lists[this.lists.length - 4 + offset].$el);
$(this.createDivider('Hours')).insertBefore(this.lists[this.lists.length - 6 + offset].$el);
$(this.createDivider('Days', true)).insertBefore(this.lists[0].$el);
this.base();
},
flip: function(time, doNotAddPlayClass){
if(!time){
time=this.factory.time.getDayCounter(this.showSeconds);
}
this.autoIncrement();
this.base(time, doNotAddPlayClass);
}});
}(jQuery));
(function($){
FlipClock.HourlyCounterFace=FlipClock.Face.extend({
constructor: function(factory, options){
this.base(factory, options);
},
build: function(excludeHours, time){
var t=this;
var children=this.factory.$el.find('ul');
time=time ? time:this.factory.time.getHourCounter();
if(time.length > children.length){
$.each(time, function(i, digit){
t.createList(digit);
});
}
$(this.createDivider('Seconds')).insertBefore(this.lists[this.lists.length - 2].$el);
$(this.createDivider('Minutes')).insertBefore(this.lists[this.lists.length - 4].$el);
if(!excludeHours){
$(this.createDivider('Hours', true)).insertBefore(this.lists[0].$el);
}
this.base();
},
flip: function(time, doNotAddPlayClass){
if(!time){
time=this.factory.time.getHourCounter();
}
this.autoIncrement();
this.base(time, doNotAddPlayClass);
},
appendDigitToClock: function(obj){
this.base(obj);
this.dividers[0].insertAfter(this.dividers[0].next());
}});
}(jQuery));
(function($){
FlipClock.MinuteCounterFace=FlipClock.HourlyCounterFace.extend({
clearExcessDigits: false,
constructor: function(factory, options){
this.base(factory, options);
},
build: function(){
this.base(true, this.factory.time.getMinuteCounter());
},
flip: function(time, doNotAddPlayClass){
if(!time){
time=this.factory.time.getMinuteCounter();
}
this.base(time, doNotAddPlayClass);
}});
}(jQuery));
(function($){
FlipClock.TwelveHourClockFace=FlipClock.TwentyFourHourClockFace.extend({
meridium: false,
meridiumText: 'AM',
build: function(){
var t=this;
var time=this.factory.time.getTime(false, this.showSeconds);
this.base(time);
this.meridiumText=this.getMeridium();
this.meridium=$([
'<ul class="flip-clock-meridium">',
'<li>',
'<a href="#">'+this.meridiumText+'</a>',
'</li>',
'</ul>'
].join(''));
this.meridium.insertAfter(this.lists[this.lists.length-1].$el);
},
flip: function(time, doNotAddPlayClass){
if(this.meridiumText!=this.getMeridium()){
this.meridiumText=this.getMeridium();
this.meridium.find('a').html(this.meridiumText);
}
this.base(this.factory.time.getTime(false, this.showSeconds), doNotAddPlayClass);
},
getMeridium: function(){
return new Date().getHours() >=12 ? 'PM':'AM';
},
isPM: function(){
return this.getMeridium()=='PM' ? true:false;
},
isAM: function(){
return this.getMeridium()=='AM' ? true:false;
}});
}(jQuery));
(function($){
FlipClock.Lang.Arabic={
'years':'سنوات',
'months':'شهور',
'days':'أيام',
'hours':'ساعات',
'minutes':'دقائق',
'seconds':'ثواني'
};
FlipClock.Lang['ar']=FlipClock.Lang.Arabic;
FlipClock.Lang['ar-ar']=FlipClock.Lang.Arabic;
FlipClock.Lang['arabic']=FlipClock.Lang.Arabic;
}(jQuery));
(function($){
FlipClock.Lang.Danish={
'years':'År',
'months':'Måneder',
'days':'Dage',
'hours':'Timer',
'minutes':'Minutter',
'seconds':'Sekunder'
};
FlipClock.Lang['da']=FlipClock.Lang.Danish;
FlipClock.Lang['da-dk']=FlipClock.Lang.Danish;
FlipClock.Lang['danish']=FlipClock.Lang.Danish;
}(jQuery));
(function($){
FlipClock.Lang.German={
'years':'Jahre',
'months':'Monate',
'days':'Tage',
'hours':'Stunden',
'minutes':'Minuten',
'seconds':'Sekunden'
};
FlipClock.Lang['de']=FlipClock.Lang.German;
FlipClock.Lang['de-de']=FlipClock.Lang.German;
FlipClock.Lang['german']=FlipClock.Lang.German;
}(jQuery));
(function($){
FlipClock.Lang.English={
'years':'Years',
'months':'Months',
'days':'Days',
'hours':'Hours',
'minutes':'Minutes',
'seconds':'Seconds'
};
FlipClock.Lang['en']=FlipClock.Lang.English;
FlipClock.Lang['en-us']=FlipClock.Lang.English;
FlipClock.Lang['english']=FlipClock.Lang.English;
}(jQuery));
(function($){
FlipClock.Lang.Spanish={
'years':'Años',
'months':'Meses',
'days':'Días',
'hours':'Horas',
'minutes':'Minutos',
'seconds':'Segundos'
};
FlipClock.Lang['es']=FlipClock.Lang.Spanish;
FlipClock.Lang['es-es']=FlipClock.Lang.Spanish;
FlipClock.Lang['spanish']=FlipClock.Lang.Spanish;
}(jQuery));
(function($){
FlipClock.Lang.Finnish={
'years':'Vuotta',
'months':'Kuukautta',
'days':'Päivää',
'hours':'Tuntia',
'minutes':'Minuuttia',
'seconds':'Sekuntia'
};
FlipClock.Lang['fi']=FlipClock.Lang.Finnish;
FlipClock.Lang['fi-fi']=FlipClock.Lang.Finnish;
FlipClock.Lang['finnish']=FlipClock.Lang.Finnish;
}(jQuery));
(function($){
FlipClock.Lang.French={
'years':'Ans',
'months':'Mois',
'days':'Jours',
'hours':'Heures',
'minutes':'Minutes',
'seconds':'Secondes'
};
FlipClock.Lang['fr']=FlipClock.Lang.French;
FlipClock.Lang['fr-ca']=FlipClock.Lang.French;
FlipClock.Lang['french']=FlipClock.Lang.French;
}(jQuery));
(function($){
FlipClock.Lang.Italian={
'years':'Anni',
'months':'Mesi',
'days':'Giorni',
'hours':'Ore',
'minutes':'Minuti',
'seconds':'Secondi'
};
FlipClock.Lang['it']=FlipClock.Lang.Italian;
FlipClock.Lang['it-it']=FlipClock.Lang.Italian;
FlipClock.Lang['italian']=FlipClock.Lang.Italian;
}(jQuery));
(function($){
FlipClock.Lang.Latvian={
'years':'Gadi',
'months':'Mēneši',
'days':'Dienas',
'hours':'Stundas',
'minutes':'Minūtes',
'seconds':'Sekundes'
};
FlipClock.Lang['lv']=FlipClock.Lang.Latvian;
FlipClock.Lang['lv-lv']=FlipClock.Lang.Latvian;
FlipClock.Lang['latvian']=FlipClock.Lang.Latvian;
}(jQuery));
(function($){
FlipClock.Lang.Dutch={
'years':'Jaren',
'months':'Maanden',
'days':'Dagen',
'hours':'Uren',
'minutes':'Minuten',
'seconds':'Seconden'
};
FlipClock.Lang['nl']=FlipClock.Lang.Dutch;
FlipClock.Lang['nl-be']=FlipClock.Lang.Dutch;
FlipClock.Lang['dutch']=FlipClock.Lang.Dutch;
}(jQuery));
(function($){
FlipClock.Lang.Norwegian={
'years':'År',
'months':'Måneder',
'days':'Dager',
'hours':'Timer',
'minutes':'Minutter',
'seconds':'Sekunder'
};
FlipClock.Lang['no']=FlipClock.Lang.Norwegian;
FlipClock.Lang['nb']=FlipClock.Lang.Norwegian;
FlipClock.Lang['no-nb']=FlipClock.Lang.Norwegian;
FlipClock.Lang['norwegian']=FlipClock.Lang.Norwegian;
}(jQuery));
(function($){
FlipClock.Lang.Portuguese={
'years':'Anos',
'months':'Meses',
'days':'Dias',
'hours':'Horas',
'minutes':'Minutos',
'seconds':'Segundos'
};
FlipClock.Lang['pt']=FlipClock.Lang.Portuguese;
FlipClock.Lang['pt-br']=FlipClock.Lang.Portuguese;
FlipClock.Lang['portuguese']=FlipClock.Lang.Portuguese;
}(jQuery));
(function($){
FlipClock.Lang.Russian={
'years':'лет',
'months':'месяцев',
'days':'дней',
'hours':'часов',
'minutes':'минут',
'seconds':'секунд'
};
FlipClock.Lang['ru']=FlipClock.Lang.Russian;
FlipClock.Lang['ru-ru']=FlipClock.Lang.Russian;
FlipClock.Lang['russian']=FlipClock.Lang.Russian;
}(jQuery));
(function($){
FlipClock.Lang.Swedish={
'years':'År',
'months':'Månader',
'days':'Dagar',
'hours':'Timmar',
'minutes':'Minuter',
'seconds':'Sekunder'
};
FlipClock.Lang['sv']=FlipClock.Lang.Swedish;
FlipClock.Lang['sv-se']=FlipClock.Lang.Swedish;
FlipClock.Lang['swedish']=FlipClock.Lang.Swedish;
}(jQuery));
(function($){
FlipClock.Lang.Chinese={
'years':'年',
'months':'月',
'days':'日',
'hours':'时',
'minutes':'分',
'seconds':'秒'
};
FlipClock.Lang['zh']=FlipClock.Lang.Chinese;
FlipClock.Lang['zh-cn']=FlipClock.Lang.Chinese;
FlipClock.Lang['chinese']=FlipClock.Lang.Chinese;
}(jQuery));
(function(){"use strict";function a(){}function b(a,b){for(var c=a.length;c--;)if(a[c].listener===b)return c;return-1}function c(a){return function(){return this[a].apply(this,arguments)}}var d=a.prototype,e=this,f=e.EventEmitter;d.getListeners=function(a){var b,c,d=this._getEvents();if("object"==typeof a){b={};for(c in d)d.hasOwnProperty(c)&&a.test(c)&&(b[c]=d[c])}else b=d[a]||(d[a]=[]);return b},d.flattenListeners=function(a){var b,c=[];for(b=0;b<a.length;b+=1)c.push(a[b].listener);return c},d.getListenersAsObject=function(a){var b,c=this.getListeners(a);return c instanceof Array&&(b={},b[a]=c),b||c},d.addListener=function(a,c){var d,e=this.getListenersAsObject(a),f="object"==typeof c;for(d in e)e.hasOwnProperty(d)&&-1===b(e[d],c)&&e[d].push(f?c:{listener:c,once:!1});return this},d.on=c("addListener"),d.addOnceListener=function(a,b){return this.addListener(a,{listener:b,once:!0})},d.once=c("addOnceListener"),d.defineEvent=function(a){return this.getListeners(a),this},d.defineEvents=function(a){for(var b=0;b<a.length;b+=1)this.defineEvent(a[b]);return this},d.removeListener=function(a,c){var d,e,f=this.getListenersAsObject(a);for(e in f)f.hasOwnProperty(e)&&(d=b(f[e],c),-1!==d&&f[e].splice(d,1));return this},d.off=c("removeListener"),d.addListeners=function(a,b){return this.manipulateListeners(!1,a,b)},d.removeListeners=function(a,b){return this.manipulateListeners(!0,a,b)},d.manipulateListeners=function(a,b,c){var d,e,f=a?this.removeListener:this.addListener,g=a?this.removeListeners:this.addListeners;if("object"!=typeof b||b instanceof RegExp)for(d=c.length;d--;)f.call(this,b,c[d]);else for(d in b)b.hasOwnProperty(d)&&(e=b[d])&&("function"==typeof e?f.call(this,d,e):g.call(this,d,e));return this},d.removeEvent=function(a){var b,c=typeof a,d=this._getEvents();if("string"===c)delete d[a];else if("object"===c)for(b in d)d.hasOwnProperty(b)&&a.test(b)&&delete d[b];else delete this._events;return this},d.removeAllListeners=c("removeEvent"),d.emitEvent=function(a,b){var c,d,e,f,g=this.getListenersAsObject(a);for(e in g)if(g.hasOwnProperty(e))for(d=g[e].length;d--;)c=g[e][d],c.once===!0&&this.removeListener(a,c.listener),f=c.listener.apply(this,b||[]),f===this._getOnceReturnValue()&&this.removeListener(a,c.listener);return this},d.trigger=c("emitEvent"),d.emit=function(a){var b=Array.prototype.slice.call(arguments,1);return this.emitEvent(a,b)},d.setOnceReturnValue=function(a){return this._onceReturnValue=a,this},d._getOnceReturnValue=function(){return!this.hasOwnProperty("_onceReturnValue")||this._onceReturnValue},d._getEvents=function(){return this._events||(this._events={})},a.noConflict=function(){return e.EventEmitter=f,a},"function"==typeof define&&define.amd?define("eventEmitter/EventEmitter",[],function(){return a}):"object"==typeof module&&module.exports?module.exports=a:this.EventEmitter=a}).call(this),function(a){function b(b){var c=a.event;return c.target=c.target||c.srcElement||b,c}var c=document.documentElement,d=function(){};c.addEventListener?d=function(a,b,c){a.addEventListener(b,c,!1)}:c.attachEvent&&(d=function(a,c,d){a[c+d]=d.handleEvent?function(){var c=b(a);d.handleEvent.call(d,c)}:function(){var c=b(a);d.call(a,c)},a.attachEvent("on"+c,a[c+d])});var e=function(){};c.removeEventListener?e=function(a,b,c){a.removeEventListener(b,c,!1)}:c.detachEvent&&(e=function(a,b,c){a.detachEvent("on"+b,a[b+c]);try{delete a[b+c]}catch(d){a[b+c]=void 0}});var f={bind:d,unbind:e};"function"==typeof define&&define.amd?define("eventie/eventie",f):a.eventie=f}(this),function(a,b){"use strict";"function"==typeof define&&define.amd?define(["eventEmitter/EventEmitter","eventie/eventie"],function(c,d){return b(a,c,d)}):"object"==typeof module&&module.exports?module.exports=b(a,require("wolfy87-eventemitter"),require("eventie")):a.imagesLoaded=b(a,a.EventEmitter,a.eventie)}(window,function(a,b,c){function d(a,b){for(var c in b)a[c]=b[c];return a}function e(a){return"[object Array]"==l.call(a)}function f(a){var b=[];if(e(a))b=a;else if("number"==typeof a.length)for(var c=0;c<a.length;c++)b.push(a[c]);else b.push(a);return b}function g(a,b,c){if(!(this instanceof g))return new g(a,b,c);"string"==typeof a&&(a=document.querySelectorAll(a)),this.elements=f(a),this.options=d({},this.options),"function"==typeof b?c=b:d(this.options,b),c&&this.on("always",c),this.getImages(),j&&(this.jqDeferred=new j.Deferred);var e=this;setTimeout(function(){e.check()})}function h(a){this.img=a}function i(a,b){this.url=a,this.element=b,this.img=new Image}var j=a.jQuery,k=a.console,l=Object.prototype.toString;g.prototype=new b,g.prototype.options={},g.prototype.getImages=function(){this.images=[];for(var a=0;a<this.elements.length;a++){var b=this.elements[a];this.addElementImages(b)}},g.prototype.addElementImages=function(a){"IMG"==a.nodeName&&this.addImage(a),this.options.background===!0&&this.addElementBackgroundImages(a);var b=a.nodeType;if(b&&m[b]){for(var c=a.querySelectorAll("img"),d=0;d<c.length;d++){var e=c[d];this.addImage(e)}if("string"==typeof this.options.background){var f=a.querySelectorAll(this.options.background);for(d=0;d<f.length;d++){var g=f[d];this.addElementBackgroundImages(g)}}}};var m={1:!0,9:!0,11:!0};g.prototype.addElementBackgroundImages=function(a){for(var b=n(a),c=/url\(['"]*([^'"\)]+)['"]*\)/gi,d=c.exec(b.backgroundImage);null!==d;){var e=d&&d[1];e&&this.addBackground(e,a),d=c.exec(b.backgroundImage)}};var n=a.getComputedStyle||function(a){return a.currentStyle};return g.prototype.addImage=function(a){var b=new h(a);this.images.push(b)},g.prototype.addBackground=function(a,b){var c=new i(a,b);this.images.push(c)},g.prototype.check=function(){function a(a,c,d){setTimeout(function(){b.progress(a,c,d)})}var b=this;if(this.progressedCount=0,this.hasAnyBroken=!1,!this.images.length)return void this.complete();for(var c=0;c<this.images.length;c++){var d=this.images[c];d.once("progress",a),d.check()}},g.prototype.progress=function(a,b,c){this.progressedCount++,this.hasAnyBroken=this.hasAnyBroken||!a.isLoaded,this.emit("progress",this,a,b),this.jqDeferred&&this.jqDeferred.notify&&this.jqDeferred.notify(this,a),this.progressedCount==this.images.length&&this.complete(),this.options.debug&&k&&k.log("progress: "+c,a,b)},g.prototype.complete=function(){var a=this.hasAnyBroken?"fail":"done";if(this.isComplete=!0,this.emit(a,this),this.emit("always",this),this.jqDeferred){var b=this.hasAnyBroken?"reject":"resolve";this.jqDeferred[b](this)}},h.prototype=new b,h.prototype.check=function(){var a=this.getIsImageComplete();return a?void this.confirm(0!==this.img.naturalWidth,"naturalWidth"):(this.proxyImage=new Image,c.bind(this.proxyImage,"load",this),c.bind(this.proxyImage,"error",this),c.bind(this.img,"load",this),c.bind(this.img,"error",this),void(this.proxyImage.src=this.img.src))},h.prototype.getIsImageComplete=function(){return this.img.complete&&void 0!==this.img.naturalWidth},h.prototype.confirm=function(a,b){this.isLoaded=a,this.emit("progress",this,this.img,b)},h.prototype.handleEvent=function(a){var b="on"+a.type;this[b]&&this[b](a)},h.prototype.onload=function(){this.confirm(!0,"onload"),this.unbindEvents()},h.prototype.onerror=function(){this.confirm(!1,"onerror"),this.unbindEvents()},h.prototype.unbindEvents=function(){c.unbind(this.proxyImage,"load",this),c.unbind(this.proxyImage,"error",this),c.unbind(this.img,"load",this),c.unbind(this.img,"error",this)},i.prototype=new h,i.prototype.check=function(){c.bind(this.img,"load",this),c.bind(this.img,"error",this),this.img.src=this.url;var a=this.getIsImageComplete();a&&(this.confirm(0!==this.img.naturalWidth,"naturalWidth"),this.unbindEvents())},i.prototype.unbindEvents=function(){c.unbind(this.img,"load",this),c.unbind(this.img,"error",this)},i.prototype.confirm=function(a,b){this.isLoaded=a,this.emit("progress",this,this.element,b)},g.makeJQueryPlugin=function(b){b=b||a.jQuery,b&&(j=b,j.fn.imagesLoaded=function(a,b){var c=new g(this,a,b);return c.jqDeferred.promise(j(this))})},g.makeJQueryPlugin(),g});
!function(a){function b(){}function c(a){function c(b){b.prototype.option||(b.prototype.option=function(b){a.isPlainObject(b)&&(this.options=a.extend(!0,this.options,b))})}function e(b,c){a.fn[b]=function(e){if("string"==typeof e){for(var g=d.call(arguments,1),h=0,i=this.length;i>h;h++){var j=this[h],k=a.data(j,b);if(k)if(a.isFunction(k[e])&&"_"!==e.charAt(0)){var l=k[e].apply(k,g);if(void 0!==l)return l}else f("no such method '"+e+"' for "+b+" instance");else f("cannot call methods on "+b+" prior to initialization; attempted to call '"+e+"'")}return this}return this.each(function(){var d=a.data(this,b);d?(d.option(e),d._init()):(d=new c(this,e),a.data(this,b,d))})}}if(a){var f="undefined"==typeof console?b:function(a){console.error(a)};return a.bridget=function(a,b){c(b),e(a,b)},a.bridget}}var d=Array.prototype.slice;"function"==typeof define&&define.amd?define("jquery-bridget/jquery.bridget",["jquery"],c):c("object"==typeof exports?require("jquery"):a.jQuery)}(window),function(a){function b(b){var c=a.event;return c.target=c.target||c.srcElement||b,c}var c=document.documentElement,d=function(){};c.addEventListener?d=function(a,b,c){a.addEventListener(b,c,!1)}:c.attachEvent&&(d=function(a,c,d){a[c+d]=d.handleEvent?function(){var c=b(a);d.handleEvent.call(d,c)}:function(){var c=b(a);d.call(a,c)},a.attachEvent("on"+c,a[c+d])});var e=function(){};c.removeEventListener?e=function(a,b,c){a.removeEventListener(b,c,!1)}:c.detachEvent&&(e=function(a,b,c){a.detachEvent("on"+b,a[b+c]);try{delete a[b+c]}catch(d){a[b+c]=void 0}});var f={bind:d,unbind:e};"function"==typeof define&&define.amd?define("eventie/eventie",f):"object"==typeof exports?module.exports=f:a.eventie=f}(window),function(){function a(){}function b(a,b){for(var c=a.length;c--;)if(a[c].listener===b)return c;return-1}function c(a){return function(){return this[a].apply(this,arguments)}}var d=a.prototype,e=this,f=e.EventEmitter;d.getListeners=function(a){var b,c,d=this._getEvents();if(a instanceof RegExp){b={};for(c in d)d.hasOwnProperty(c)&&a.test(c)&&(b[c]=d[c])}else b=d[a]||(d[a]=[]);return b},d.flattenListeners=function(a){var b,c=[];for(b=0;b<a.length;b+=1)c.push(a[b].listener);return c},d.getListenersAsObject=function(a){var b,c=this.getListeners(a);return c instanceof Array&&(b={},b[a]=c),b||c},d.addListener=function(a,c){var d,e=this.getListenersAsObject(a),f="object"==typeof c;for(d in e)e.hasOwnProperty(d)&&-1===b(e[d],c)&&e[d].push(f?c:{listener:c,once:!1});return this},d.on=c("addListener"),d.addOnceListener=function(a,b){return this.addListener(a,{listener:b,once:!0})},d.once=c("addOnceListener"),d.defineEvent=function(a){return this.getListeners(a),this},d.defineEvents=function(a){for(var b=0;b<a.length;b+=1)this.defineEvent(a[b]);return this},d.removeListener=function(a,c){var d,e,f=this.getListenersAsObject(a);for(e in f)f.hasOwnProperty(e)&&(d=b(f[e],c),-1!==d&&f[e].splice(d,1));return this},d.off=c("removeListener"),d.addListeners=function(a,b){return this.manipulateListeners(!1,a,b)},d.removeListeners=function(a,b){return this.manipulateListeners(!0,a,b)},d.manipulateListeners=function(a,b,c){var d,e,f=a?this.removeListener:this.addListener,g=a?this.removeListeners:this.addListeners;if("object"!=typeof b||b instanceof RegExp)for(d=c.length;d--;)f.call(this,b,c[d]);else for(d in b)b.hasOwnProperty(d)&&(e=b[d])&&("function"==typeof e?f.call(this,d,e):g.call(this,d,e));return this},d.removeEvent=function(a){var b,c=typeof a,d=this._getEvents();if("string"===c)delete d[a];else if(a instanceof RegExp)for(b in d)d.hasOwnProperty(b)&&a.test(b)&&delete d[b];else delete this._events;return this},d.removeAllListeners=c("removeEvent"),d.emitEvent=function(a,b){var c,d,e,f,g=this.getListenersAsObject(a);for(e in g)if(g.hasOwnProperty(e))for(d=g[e].length;d--;)c=g[e][d],c.once===!0&&this.removeListener(a,c.listener),f=c.listener.apply(this,b||[]),f===this._getOnceReturnValue()&&this.removeListener(a,c.listener);return this},d.trigger=c("emitEvent"),d.emit=function(a){var b=Array.prototype.slice.call(arguments,1);return this.emitEvent(a,b)},d.setOnceReturnValue=function(a){return this._onceReturnValue=a,this},d._getOnceReturnValue=function(){return this.hasOwnProperty("_onceReturnValue")?this._onceReturnValue:!0},d._getEvents=function(){return this._events||(this._events={})},a.noConflict=function(){return e.EventEmitter=f,a},"function"==typeof define&&define.amd?define("eventEmitter/EventEmitter",[],function(){return a}):"object"==typeof module&&module.exports?module.exports=a:e.EventEmitter=a}.call(this),function(a){function b(a){if(a){if("string"==typeof d[a])return a;a=a.charAt(0).toUpperCase()+a.slice(1);for(var b,e=0,f=c.length;f>e;e++)if(b=c[e]+a,"string"==typeof d[b])return b}}var c="Webkit Moz ms Ms O".split(" "),d=document.documentElement.style;"function"==typeof define&&define.amd?define("get-style-property/get-style-property",[],function(){return b}):"object"==typeof exports?module.exports=b:a.getStyleProperty=b}(window),function(a){function b(a){var b=parseFloat(a),c=-1===a.indexOf("%")&&!isNaN(b);return c&&b}function c(){}function d(){for(var a={width:0,height:0,innerWidth:0,innerHeight:0,outerWidth:0,outerHeight:0},b=0,c=g.length;c>b;b++){var d=g[b];a[d]=0}return a}function e(c){function e(){if(!m){m=!0;var d=a.getComputedStyle;if(j=function(){var a=d?function(a){return d(a,null)}:function(a){return a.currentStyle};return function(b){var c=a(b);return c||f("Style returned "+c+". Are you running this code in a hidden iframe on Firefox? See http://bit.ly/getsizebug1"),c}}(),k=c("boxSizing")){var e=document.createElement("div");e.style.width="200px",e.style.padding="1px 2px 3px 4px",e.style.borderStyle="solid",e.style.borderWidth="1px 2px 3px 4px",e.style[k]="border-box";var g=document.body||document.documentElement;g.appendChild(e);var h=j(e);l=200===b(h.width),g.removeChild(e)}}}function h(a){if(e(),"string"==typeof a&&(a=document.querySelector(a)),a&&"object"==typeof a&&a.nodeType){var c=j(a);if("none"===c.display)return d();var f={};f.width=a.offsetWidth,f.height=a.offsetHeight;for(var h=f.isBorderBox=!(!k||!c[k]||"border-box"!==c[k]),m=0,n=g.length;n>m;m++){var o=g[m],p=c[o];p=i(a,p);var q=parseFloat(p);f[o]=isNaN(q)?0:q}var r=f.paddingLeft+f.paddingRight,s=f.paddingTop+f.paddingBottom,t=f.marginLeft+f.marginRight,u=f.marginTop+f.marginBottom,v=f.borderLeftWidth+f.borderRightWidth,w=f.borderTopWidth+f.borderBottomWidth,x=h&&l,y=b(c.width);y!==!1&&(f.width=y+(x?0:r+v));var z=b(c.height);return z!==!1&&(f.height=z+(x?0:s+w)),f.innerWidth=f.width-(r+v),f.innerHeight=f.height-(s+w),f.outerWidth=f.width+t,f.outerHeight=f.height+u,f}}function i(b,c){if(a.getComputedStyle||-1===c.indexOf("%"))return c;var d=b.style,e=d.left,f=b.runtimeStyle,g=f&&f.left;return g&&(f.left=b.currentStyle.left),d.left=c,c=d.pixelLeft,d.left=e,g&&(f.left=g),c}var j,k,l,m=!1;return h}var f="undefined"==typeof console?c:function(a){console.error(a)},g=["paddingLeft","paddingRight","paddingTop","paddingBottom","marginLeft","marginRight","marginTop","marginBottom","borderLeftWidth","borderRightWidth","borderTopWidth","borderBottomWidth"];"function"==typeof define&&define.amd?define("get-size/get-size",["get-style-property/get-style-property"],e):"object"==typeof exports?module.exports=e(require("desandro-get-style-property")):a.getSize=e(a.getStyleProperty)}(window),function(a){function b(a){"function"==typeof a&&(b.isReady?a():g.push(a))}function c(a){var c="readystatechange"===a.type&&"complete"!==f.readyState;b.isReady||c||d()}function d(){b.isReady=!0;for(var a=0,c=g.length;c>a;a++){var d=g[a];d()}}function e(e){return"complete"===f.readyState?d():(e.bind(f,"DOMContentLoaded",c),e.bind(f,"readystatechange",c),e.bind(a,"load",c)),b}var f=a.document,g=[];b.isReady=!1,"function"==typeof define&&define.amd?define("doc-ready/doc-ready",["eventie/eventie"],e):"object"==typeof exports?module.exports=e(require("eventie")):a.docReady=e(a.eventie)}(window),function(a){function b(a,b){return a[g](b)}function c(a){if(!a.parentNode){var b=document.createDocumentFragment();b.appendChild(a)}}function d(a,b){c(a);for(var d=a.parentNode.querySelectorAll(b),e=0,f=d.length;f>e;e++)if(d[e]===a)return!0;return!1}function e(a,d){return c(a),b(a,d)}var f,g=function(){if(a.matches)return"matches";if(a.matchesSelector)return"matchesSelector";for(var b=["webkit","moz","ms","o"],c=0,d=b.length;d>c;c++){var e=b[c],f=e+"MatchesSelector";if(a[f])return f}}();if(g){var h=document.createElement("div"),i=b(h,"div");f=i?b:e}else f=d;"function"==typeof define&&define.amd?define("matches-selector/matches-selector",[],function(){return f}):"object"==typeof exports?module.exports=f:window.matchesSelector=f}(Element.prototype),function(a,b){"function"==typeof define&&define.amd?define("fizzy-ui-utils/utils",["doc-ready/doc-ready","matches-selector/matches-selector"],function(c,d){return b(a,c,d)}):"object"==typeof exports?module.exports=b(a,require("doc-ready"),require("desandro-matches-selector")):a.fizzyUIUtils=b(a,a.docReady,a.matchesSelector)}(window,function(a,b,c){var d={};d.extend=function(a,b){for(var c in b)a[c]=b[c];return a},d.modulo=function(a,b){return(a%b+b)%b};var e=Object.prototype.toString;d.isArray=function(a){return"[object Array]"==e.call(a)},d.makeArray=function(a){var b=[];if(d.isArray(a))b=a;else if(a&&"number"==typeof a.length)for(var c=0,e=a.length;e>c;c++)b.push(a[c]);else b.push(a);return b},d.indexOf=Array.prototype.indexOf?function(a,b){return a.indexOf(b)}:function(a,b){for(var c=0,d=a.length;d>c;c++)if(a[c]===b)return c;return-1},d.removeFrom=function(a,b){var c=d.indexOf(a,b);-1!=c&&a.splice(c,1)},d.isElement="function"==typeof HTMLElement||"object"==typeof HTMLElement?function(a){return a instanceof HTMLElement}:function(a){return a&&"object"==typeof a&&1==a.nodeType&&"string"==typeof a.nodeName},d.setText=function(){function a(a,c){b=b||(void 0!==document.documentElement.textContent?"textContent":"innerText"),a[b]=c}var b;return a}(),d.getParent=function(a,b){for(;a!=document.body;)if(a=a.parentNode,c(a,b))return a},d.getQueryElement=function(a){return"string"==typeof a?document.querySelector(a):a},d.handleEvent=function(a){var b="on"+a.type;this[b]&&this[b](a)},d.filterFindElements=function(a,b){a=d.makeArray(a);for(var e=[],f=0,g=a.length;g>f;f++){var h=a[f];if(d.isElement(h))if(b){c(h,b)&&e.push(h);for(var i=h.querySelectorAll(b),j=0,k=i.length;k>j;j++)e.push(i[j])}else e.push(h)}return e},d.debounceMethod=function(a,b,c){var d=a.prototype[b],e=b+"Timeout";a.prototype[b]=function(){var a=this[e];a&&clearTimeout(a);var b=arguments,f=this;this[e]=setTimeout(function(){d.apply(f,b),delete f[e]},c||100)}},d.toDashed=function(a){return a.replace(/(.)([A-Z])/g,function(a,b,c){return b+"-"+c}).toLowerCase()};var f=a.console;return d.htmlInit=function(c,e){b(function(){for(var b=d.toDashed(e),g=document.querySelectorAll(".js-"+b),h="data-"+b+"-options",i=0,j=g.length;j>i;i++){var k,l=g[i],m=l.getAttribute(h);try{k=m&&JSON.parse(m)}catch(n){f&&f.error("Error parsing "+h+" on "+l.nodeName.toLowerCase()+(l.id?"#"+l.id:"")+": "+n);continue}var o=new c(l,k),p=a.jQuery;p&&p.data(l,e,o)}})},d}),function(a,b){"function"==typeof define&&define.amd?define("outlayer/item",["eventEmitter/EventEmitter","get-size/get-size","get-style-property/get-style-property","fizzy-ui-utils/utils"],function(c,d,e,f){return b(a,c,d,e,f)}):"object"==typeof exports?module.exports=b(a,require("wolfy87-eventemitter"),require("get-size"),require("desandro-get-style-property"),require("fizzy-ui-utils")):(a.Outlayer={},a.Outlayer.Item=b(a,a.EventEmitter,a.getSize,a.getStyleProperty,a.fizzyUIUtils))}(window,function(a,b,c,d,e){function f(a){for(var b in a)return!1;return b=null,!0}function g(a,b){a&&(this.element=a,this.layout=b,this.position={x:0,y:0},this._create())}function h(a){return a.replace(/([A-Z])/g,function(a){return"-"+a.toLowerCase()})}var i=a.getComputedStyle,j=i?function(a){return i(a,null)}:function(a){return a.currentStyle},k=d("transition"),l=d("transform"),m=k&&l,n=!!d("perspective"),o={WebkitTransition:"webkitTransitionEnd",MozTransition:"transitionend",OTransition:"otransitionend",transition:"transitionend"}[k],p=["transform","transition","transitionDuration","transitionProperty"],q=function(){for(var a={},b=0,c=p.length;c>b;b++){var e=p[b],f=d(e);f&&f!==e&&(a[e]=f)}return a}();e.extend(g.prototype,b.prototype),g.prototype._create=function(){this._transn={ingProperties:{},clean:{},onEnd:{}},this.css({position:"absolute"})},g.prototype.handleEvent=function(a){var b="on"+a.type;this[b]&&this[b](a)},g.prototype.getSize=function(){this.size=c(this.element)},g.prototype.css=function(a){var b=this.element.style;for(var c in a){var d=q[c]||c;b[d]=a[c]}},g.prototype.getPosition=function(){var a=j(this.element),b=this.layout.options,c=b.isOriginLeft,d=b.isOriginTop,e=a[c?"left":"right"],f=a[d?"top":"bottom"],g=this.layout.size,h=-1!=e.indexOf("%")?parseFloat(e)/100*g.width:parseInt(e,10),i=-1!=f.indexOf("%")?parseFloat(f)/100*g.height:parseInt(f,10);h=isNaN(h)?0:h,i=isNaN(i)?0:i,h-=c?g.paddingLeft:g.paddingRight,i-=d?g.paddingTop:g.paddingBottom,this.position.x=h,this.position.y=i},g.prototype.layoutPosition=function(){var a=this.layout.size,b=this.layout.options,c={},d=b.isOriginLeft?"paddingLeft":"paddingRight",e=b.isOriginLeft?"left":"right",f=b.isOriginLeft?"right":"left",g=this.position.x+a[d];c[e]=this.getXValue(g),c[f]="";var h=b.isOriginTop?"paddingTop":"paddingBottom",i=b.isOriginTop?"top":"bottom",j=b.isOriginTop?"bottom":"top",k=this.position.y+a[h];c[i]=this.getYValue(k),c[j]="",this.css(c),this.emitEvent("layout",[this])},g.prototype.getXValue=function(a){var b=this.layout.options;return b.percentPosition&&!b.isHorizontal?a/this.layout.size.width*100+"%":a+"px"},g.prototype.getYValue=function(a){var b=this.layout.options;return b.percentPosition&&b.isHorizontal?a/this.layout.size.height*100+"%":a+"px"},g.prototype._transitionTo=function(a,b){this.getPosition();var c=this.position.x,d=this.position.y,e=parseInt(a,10),f=parseInt(b,10),g=e===this.position.x&&f===this.position.y;if(this.setPosition(a,b),g&&!this.isTransitioning)return void this.layoutPosition();var h=a-c,i=b-d,j={};j.transform=this.getTranslate(h,i),this.transition({to:j,onTransitionEnd:{transform:this.layoutPosition},isCleaning:!0})},g.prototype.getTranslate=function(a,b){var c=this.layout.options;return a=c.isOriginLeft?a:-a,b=c.isOriginTop?b:-b,n?"translate3d("+a+"px, "+b+"px, 0)":"translate("+a+"px, "+b+"px)"},g.prototype.goTo=function(a,b){this.setPosition(a,b),this.layoutPosition()},g.prototype.moveTo=m?g.prototype._transitionTo:g.prototype.goTo,g.prototype.setPosition=function(a,b){this.position.x=parseInt(a,10),this.position.y=parseInt(b,10)},g.prototype._nonTransition=function(a){this.css(a.to),a.isCleaning&&this._removeStyles(a.to);for(var b in a.onTransitionEnd)a.onTransitionEnd[b].call(this)},g.prototype._transition=function(a){if(!parseFloat(this.layout.options.transitionDuration))return void this._nonTransition(a);var b=this._transn;for(var c in a.onTransitionEnd)b.onEnd[c]=a.onTransitionEnd[c];for(c in a.to)b.ingProperties[c]=!0,a.isCleaning&&(b.clean[c]=!0);if(a.from){this.css(a.from);var d=this.element.offsetHeight;d=null}this.enableTransition(a.to),this.css(a.to),this.isTransitioning=!0};var r="opacity,"+h(q.transform||"transform");g.prototype.enableTransition=function(){this.isTransitioning||(this.css({transitionProperty:r,transitionDuration:this.layout.options.transitionDuration}),this.element.addEventListener(o,this,!1))},g.prototype.transition=g.prototype[k?"_transition":"_nonTransition"],g.prototype.onwebkitTransitionEnd=function(a){this.ontransitionend(a)},g.prototype.onotransitionend=function(a){this.ontransitionend(a)};var s={"-webkit-transform":"transform","-moz-transform":"transform","-o-transform":"transform"};g.prototype.ontransitionend=function(a){if(a.target===this.element){var b=this._transn,c=s[a.propertyName]||a.propertyName;if(delete b.ingProperties[c],f(b.ingProperties)&&this.disableTransition(),c in b.clean&&(this.element.style[a.propertyName]="",delete b.clean[c]),c in b.onEnd){var d=b.onEnd[c];d.call(this),delete b.onEnd[c]}this.emitEvent("transitionEnd",[this])}},g.prototype.disableTransition=function(){this.removeTransitionStyles(),this.element.removeEventListener(o,this,!1),this.isTransitioning=!1},g.prototype._removeStyles=function(a){var b={};for(var c in a)b[c]="";this.css(b)};var t={transitionProperty:"",transitionDuration:""};return g.prototype.removeTransitionStyles=function(){this.css(t)},g.prototype.removeElem=function(){this.element.parentNode.removeChild(this.element),this.css({display:""}),this.emitEvent("remove",[this])},g.prototype.remove=function(){if(!k||!parseFloat(this.layout.options.transitionDuration))return void this.removeElem();var a=this;this.once("transitionEnd",function(){a.removeElem()}),this.hide()},g.prototype.reveal=function(){delete this.isHidden,this.css({display:""});var a=this.layout.options,b={},c=this.getHideRevealTransitionEndProperty("visibleStyle");b[c]=this.onRevealTransitionEnd,this.transition({from:a.hiddenStyle,to:a.visibleStyle,isCleaning:!0,onTransitionEnd:b})},g.prototype.onRevealTransitionEnd=function(){this.isHidden||this.emitEvent("reveal")},g.prototype.getHideRevealTransitionEndProperty=function(a){var b=this.layout.options[a];if(b.opacity)return"opacity";for(var c in b)return c},g.prototype.hide=function(){this.isHidden=!0,this.css({display:""});var a=this.layout.options,b={},c=this.getHideRevealTransitionEndProperty("hiddenStyle");b[c]=this.onHideTransitionEnd,this.transition({from:a.visibleStyle,to:a.hiddenStyle,isCleaning:!0,onTransitionEnd:b})},g.prototype.onHideTransitionEnd=function(){this.isHidden&&(this.css({display:"none"}),this.emitEvent("hide"))},g.prototype.destroy=function(){this.css({position:"",left:"",right:"",top:"",bottom:"",transition:"",transform:""})},g}),function(a,b){"function"==typeof define&&define.amd?define("outlayer/outlayer",["eventie/eventie","eventEmitter/EventEmitter","get-size/get-size","fizzy-ui-utils/utils","./item"],function(c,d,e,f,g){return b(a,c,d,e,f,g)}):"object"==typeof exports?module.exports=b(a,require("eventie"),require("wolfy87-eventemitter"),require("get-size"),require("fizzy-ui-utils"),require("./item")):a.Outlayer=b(a,a.eventie,a.EventEmitter,a.getSize,a.fizzyUIUtils,a.Outlayer.Item)}(window,function(a,b,c,d,e,f){function g(a,b){var c=e.getQueryElement(a);if(!c)return void(h&&h.error("Bad element for "+this.constructor.namespace+": "+(c||a)));this.element=c,i&&(this.$element=i(this.element)),this.options=e.extend({},this.constructor.defaults),this.option(b);var d=++k;this.element.outlayerGUID=d,l[d]=this,this._create(),this.options.isInitLayout&&this.layout()}var h=a.console,i=a.jQuery,j=function(){},k=0,l={};return g.namespace="outlayer",g.Item=f,g.defaults={containerStyle:{position:"relative"},isInitLayout:!0,isOriginLeft:!0,isOriginTop:!0,isResizeBound:!0,isResizingContainer:!0,transitionDuration:"0.4s",hiddenStyle:{opacity:0,transform:"scale(0.001)"},visibleStyle:{opacity:1,transform:"scale(1)"}},e.extend(g.prototype,c.prototype),g.prototype.option=function(a){e.extend(this.options,a)},g.prototype._create=function(){this.reloadItems(),this.stamps=[],this.stamp(this.options.stamp),e.extend(this.element.style,this.options.containerStyle),this.options.isResizeBound&&this.bindResize()},g.prototype.reloadItems=function(){this.items=this._itemize(this.element.children)},g.prototype._itemize=function(a){for(var b=this._filterFindItemElements(a),c=this.constructor.Item,d=[],e=0,f=b.length;f>e;e++){var g=b[e],h=new c(g,this);d.push(h)}return d},g.prototype._filterFindItemElements=function(a){return e.filterFindElements(a,this.options.itemSelector)},g.prototype.getItemElements=function(){for(var a=[],b=0,c=this.items.length;c>b;b++)a.push(this.items[b].element);return a},g.prototype.layout=function(){this._resetLayout(),this._manageStamps();var a=void 0!==this.options.isLayoutInstant?this.options.isLayoutInstant:!this._isLayoutInited;this.layoutItems(this.items,a),this._isLayoutInited=!0},g.prototype._init=g.prototype.layout,g.prototype._resetLayout=function(){this.getSize()},g.prototype.getSize=function(){this.size=d(this.element)},g.prototype._getMeasurement=function(a,b){var c,f=this.options[a];f?("string"==typeof f?c=this.element.querySelector(f):e.isElement(f)&&(c=f),this[a]=c?d(c)[b]:f):this[a]=0},g.prototype.layoutItems=function(a,b){a=this._getItemsForLayout(a),this._layoutItems(a,b),this._postLayout()},g.prototype._getItemsForLayout=function(a){for(var b=[],c=0,d=a.length;d>c;c++){var e=a[c];e.isIgnored||b.push(e)}return b},g.prototype._layoutItems=function(a,b){if(this._emitCompleteOnItems("layout",a),a&&a.length){for(var c=[],d=0,e=a.length;e>d;d++){var f=a[d],g=this._getItemLayoutPosition(f);g.item=f,g.isInstant=b||f.isLayoutInstant,c.push(g)}this._processLayoutQueue(c)}},g.prototype._getItemLayoutPosition=function(){return{x:0,y:0}},g.prototype._processLayoutQueue=function(a){for(var b=0,c=a.length;c>b;b++){var d=a[b];this._positionItem(d.item,d.x,d.y,d.isInstant)}},g.prototype._positionItem=function(a,b,c,d){d?a.goTo(b,c):a.moveTo(b,c)},g.prototype._postLayout=function(){this.resizeContainer()},g.prototype.resizeContainer=function(){if(this.options.isResizingContainer){var a=this._getContainerSize();a&&(this._setContainerMeasure(a.width,!0),this._setContainerMeasure(a.height,!1))}},g.prototype._getContainerSize=j,g.prototype._setContainerMeasure=function(a,b){if(void 0!==a){var c=this.size;c.isBorderBox&&(a+=b?c.paddingLeft+c.paddingRight+c.borderLeftWidth+c.borderRightWidth:c.paddingBottom+c.paddingTop+c.borderTopWidth+c.borderBottomWidth),a=Math.max(a,0),this.element.style[b?"width":"height"]=a+"px"}},g.prototype._emitCompleteOnItems=function(a,b){function c(){e.dispatchEvent(a+"Complete",null,[b])}function d(){g++,g===f&&c()}var e=this,f=b.length;if(!b||!f)return void c();for(var g=0,h=0,i=b.length;i>h;h++){var j=b[h];j.once(a,d)}},g.prototype.dispatchEvent=function(a,b,c){var d=b?[b].concat(c):c;if(this.emitEvent(a,d),i)if(this.$element=this.$element||i(this.element),b){var e=i.Event(b);e.type=a,this.$element.trigger(e,c)}else this.$element.trigger(a,c)},g.prototype.ignore=function(a){var b=this.getItem(a);b&&(b.isIgnored=!0)},g.prototype.unignore=function(a){var b=this.getItem(a);b&&delete b.isIgnored},g.prototype.stamp=function(a){if(a=this._find(a)){this.stamps=this.stamps.concat(a);for(var b=0,c=a.length;c>b;b++){var d=a[b];this.ignore(d)}}},g.prototype.unstamp=function(a){if(a=this._find(a))for(var b=0,c=a.length;c>b;b++){var d=a[b];e.removeFrom(this.stamps,d),this.unignore(d)}},g.prototype._find=function(a){return a?("string"==typeof a&&(a=this.element.querySelectorAll(a)),a=e.makeArray(a)):void 0},g.prototype._manageStamps=function(){if(this.stamps&&this.stamps.length){this._getBoundingRect();for(var a=0,b=this.stamps.length;b>a;a++){var c=this.stamps[a];this._manageStamp(c)}}},g.prototype._getBoundingRect=function(){var a=this.element.getBoundingClientRect(),b=this.size;this._boundingRect={left:a.left+b.paddingLeft+b.borderLeftWidth,top:a.top+b.paddingTop+b.borderTopWidth,right:a.right-(b.paddingRight+b.borderRightWidth),bottom:a.bottom-(b.paddingBottom+b.borderBottomWidth)}},g.prototype._manageStamp=j,g.prototype._getElementOffset=function(a){var b=a.getBoundingClientRect(),c=this._boundingRect,e=d(a),f={left:b.left-c.left-e.marginLeft,top:b.top-c.top-e.marginTop,right:c.right-b.right-e.marginRight,bottom:c.bottom-b.bottom-e.marginBottom};return f},g.prototype.handleEvent=function(a){var b="on"+a.type;this[b]&&this[b](a)},g.prototype.bindResize=function(){this.isResizeBound||(b.bind(a,"resize",this),this.isResizeBound=!0)},g.prototype.unbindResize=function(){this.isResizeBound&&b.unbind(a,"resize",this),this.isResizeBound=!1},g.prototype.onresize=function(){function a(){b.resize(),delete b.resizeTimeout}this.resizeTimeout&&clearTimeout(this.resizeTimeout);var b=this;this.resizeTimeout=setTimeout(a,100)},g.prototype.resize=function(){this.isResizeBound&&this.needsResizeLayout()&&this.layout()},g.prototype.needsResizeLayout=function(){var a=d(this.element),b=this.size&&a;return b&&a.innerWidth!==this.size.innerWidth},g.prototype.addItems=function(a){var b=this._itemize(a);return b.length&&(this.items=this.items.concat(b)),b},g.prototype.appended=function(a){var b=this.addItems(a);b.length&&(this.layoutItems(b,!0),this.reveal(b))},g.prototype.prepended=function(a){var b=this._itemize(a);if(b.length){var c=this.items.slice(0);this.items=b.concat(c),this._resetLayout(),this._manageStamps(),this.layoutItems(b,!0),this.reveal(b),this.layoutItems(c)}},g.prototype.reveal=function(a){this._emitCompleteOnItems("reveal",a);for(var b=a&&a.length,c=0;b&&b>c;c++){var d=a[c];d.reveal()}},g.prototype.hide=function(a){this._emitCompleteOnItems("hide",a);for(var b=a&&a.length,c=0;b&&b>c;c++){var d=a[c];d.hide()}},g.prototype.revealItemElements=function(a){var b=this.getItems(a);this.reveal(b)},g.prototype.hideItemElements=function(a){var b=this.getItems(a);this.hide(b)},g.prototype.getItem=function(a){for(var b=0,c=this.items.length;c>b;b++){var d=this.items[b];if(d.element===a)return d}},g.prototype.getItems=function(a){a=e.makeArray(a);for(var b=[],c=0,d=a.length;d>c;c++){var f=a[c],g=this.getItem(f);g&&b.push(g)}return b},g.prototype.remove=function(a){var b=this.getItems(a);if(this._emitCompleteOnItems("remove",b),b&&b.length)for(var c=0,d=b.length;d>c;c++){var f=b[c];f.remove(),e.removeFrom(this.items,f)}},g.prototype.destroy=function(){var a=this.element.style;a.height="",a.position="",a.width="";for(var b=0,c=this.items.length;c>b;b++){var d=this.items[b];d.destroy()}this.unbindResize();var e=this.element.outlayerGUID;delete l[e],delete this.element.outlayerGUID,i&&i.removeData(this.element,this.constructor.namespace)},g.data=function(a){a=e.getQueryElement(a);var b=a&&a.outlayerGUID;return b&&l[b]},g.create=function(a,b){function c(){g.apply(this,arguments)}return Object.create?c.prototype=Object.create(g.prototype):e.extend(c.prototype,g.prototype),c.prototype.constructor=c,c.defaults=e.extend({},g.defaults),e.extend(c.defaults,b),c.prototype.settings={},c.namespace=a,c.data=g.data,c.Item=function(){f.apply(this,arguments)},c.Item.prototype=new f,e.htmlInit(c,a),i&&i.bridget&&i.bridget(a,c),c},g.Item=f,g}),function(a,b){"function"==typeof define&&define.amd?define(["outlayer/outlayer","get-size/get-size","fizzy-ui-utils/utils"],b):"object"==typeof exports?module.exports=b(require("outlayer"),require("get-size"),require("fizzy-ui-utils")):a.Masonry=b(a.Outlayer,a.getSize,a.fizzyUIUtils)}(window,function(a,b,c){var d=a.create("masonry");return d.prototype._resetLayout=function(){this.getSize(),this._getMeasurement("columnWidth","outerWidth"),this._getMeasurement("gutter","outerWidth"),this.measureColumns();var a=this.cols;for(this.colYs=[];a--;)this.colYs.push(0);this.maxY=0},d.prototype.measureColumns=function(){if(this.getContainerWidth(),!this.columnWidth){var a=this.items[0],c=a&&a.element;this.columnWidth=c&&b(c).outerWidth||this.containerWidth}var d=this.columnWidth+=this.gutter,e=this.containerWidth+this.gutter,f=e/d,g=d-e%d,h=g&&1>g?"round":"floor";f=Math[h](f),this.cols=Math.max(f,1)},d.prototype.getContainerWidth=function(){var a=this.options.isFitWidth?this.element.parentNode:this.element,c=b(a);this.containerWidth=c&&c.innerWidth},d.prototype._getItemLayoutPosition=function(a){a.getSize();var b=a.size.outerWidth%this.columnWidth,d=b&&1>b?"round":"ceil",e=Math[d](a.size.outerWidth/this.columnWidth);e=Math.min(e,this.cols);for(var f=this._getColGroup(e),g=Math.min.apply(Math,f),h=c.indexOf(f,g),i={x:this.columnWidth*h,y:g},j=g+a.size.outerHeight,k=this.cols+1-f.length,l=0;k>l;l++)this.colYs[h+l]=j;return i},d.prototype._getColGroup=function(a){if(2>a)return this.colYs;for(var b=[],c=this.cols+1-a,d=0;c>d;d++){var e=this.colYs.slice(d,d+a);b[d]=Math.max.apply(Math,e)}return b},d.prototype._manageStamp=function(a){var c=b(a),d=this._getElementOffset(a),e=this.options.isOriginLeft?d.left:d.right,f=e+c.outerWidth,g=Math.floor(e/this.columnWidth);g=Math.max(0,g);var h=Math.floor(f/this.columnWidth);h-=f%this.columnWidth?0:1,h=Math.min(this.cols-1,h);for(var i=(this.options.isOriginTop?d.top:d.bottom)+c.outerHeight,j=g;h>=j;j++)this.colYs[j]=Math.max(i,this.colYs[j])},d.prototype._getContainerSize=function(){this.maxY=Math.max.apply(Math,this.colYs);var a={height:this.maxY};return this.options.isFitWidth&&(a.width=this._getContainerFitWidth()),a},d.prototype._getContainerFitWidth=function(){for(var a=0,b=this.cols;--b&&0===this.colYs[b];)a++;return(this.cols-a)*this.columnWidth-this.gutter},d.prototype.needsResizeLayout=function(){var a=this.containerWidth;return this.getContainerWidth(),a!==this.containerWidth},d});
!function(t){var e={},s={mode:"horizontal",slideSelector:"",infiniteLoop:!0,hideControlOnEnd:!1,speed:500,easing:null,slideMargin:0,startSlide:0,randomStart:!1,captions:!1,ticker:!1,tickerHover:!1,adaptiveHeight:!1,adaptiveHeightSpeed:500,video:!1,useCSS:!0,preloadImages:"visible",responsive:!0,slideZIndex:50,touchEnabled:!0,swipeThreshold:50,oneToOneTouch:!0,preventDefaultSwipeX:!0,preventDefaultSwipeY:!1,pager:!0,pagerType:"full",pagerShortSeparator:" / ",pagerSelector:null,buildPager:null,pagerCustom:null,controls:!0,nextText:"Next",prevText:"Prev",nextSelector:null,prevSelector:null,autoControls:!1,startText:"Start",stopText:"Stop",autoControlsCombine:!1,autoControlsSelector:null,auto:!1,pause:4e3,autoStart:!0,autoDirection:"next",autoHover:!1,autoDelay:0,minSlides:1,maxSlides:1,moveSlides:0,slideWidth:0,onSliderLoad:function(){},onSlideBefore:function(){},onSlideAfter:function(){},onSlideNext:function(){},onSlidePrev:function(){},onSliderResize:function(){}};t.fn.bxSlider=function(n){if(0==this.length)return this;if(this.length>1)return this.each(function(){t(this).bxSlider(n)}),this;var o={},r=this;e.el=this;var a=t(window).width(),l=t(window).height(),d=function(){o.settings=t.extend({},s,n),o.settings.slideWidth=parseInt(o.settings.slideWidth),o.children=r.children(o.settings.slideSelector),o.children.length<o.settings.minSlides&&(o.settings.minSlides=o.children.length),o.children.length<o.settings.maxSlides&&(o.settings.maxSlides=o.children.length),o.settings.randomStart&&(o.settings.startSlide=Math.floor(Math.random()*o.children.length)),o.active={index:o.settings.startSlide},o.carousel=o.settings.minSlides>1||o.settings.maxSlides>1,o.carousel&&(o.settings.preloadImages="all"),o.minThreshold=o.settings.minSlides*o.settings.slideWidth+(o.settings.minSlides-1)*o.settings.slideMargin,o.maxThreshold=o.settings.maxSlides*o.settings.slideWidth+(o.settings.maxSlides-1)*o.settings.slideMargin,o.working=!1,o.controls={},o.interval=null,o.animProp="vertical"==o.settings.mode?"top":"left",o.usingCSS=o.settings.useCSS&&"fade"!=o.settings.mode&&function(){var t=document.createElement("div"),e=["WebkitPerspective","MozPerspective","OPerspective","msPerspective"];for(var i in e)if(void 0!==t.style[e[i]])return o.cssPrefix=e[i].replace("Perspective","").toLowerCase(),o.animProp="-"+o.cssPrefix+"-transform",!0;return!1}(),"vertical"==o.settings.mode&&(o.settings.maxSlides=o.settings.minSlides),r.data("origStyle",r.attr("style")),r.children(o.settings.slideSelector).each(function(){t(this).data("origStyle",t(this).attr("style"))}),c()},c=function(){r.wrap('<div class="bx-wrapper"><div class="bx-viewport"></div></div>'),o.viewport=r.parent(),o.loader=t('<div class="bx-loading" />'),o.viewport.prepend(o.loader),r.css({width:"horizontal"==o.settings.mode?100*o.children.length+215+"%":"auto",position:"relative"}),o.usingCSS&&o.settings.easing?r.css("-"+o.cssPrefix+"-transition-timing-function",o.settings.easing):o.settings.easing||(o.settings.easing="swing"),f(),o.viewport.css({width:"100%",overflow:"hidden",position:"relative"}),o.viewport.parent().css({maxWidth:p()}),o.settings.pager||o.viewport.parent().css({margin:"0 auto 0px"}),o.children.css({"float":"horizontal"==o.settings.mode?"left":"none",listStyle:"none",position:"relative"}),o.children.css("width",u()),"horizontal"==o.settings.mode&&o.settings.slideMargin>0&&o.children.css("marginRight",o.settings.slideMargin),"vertical"==o.settings.mode&&o.settings.slideMargin>0&&o.children.css("marginBottom",o.settings.slideMargin),"fade"==o.settings.mode&&(o.children.css({position:"absolute",zIndex:0,display:"none"}),o.children.eq(o.settings.startSlide).css({zIndex:o.settings.slideZIndex,display:"block"})),o.controls.el=t('<div class="bx-controls" />'),o.settings.captions&&P(),o.active.last=o.settings.startSlide==x()-1,o.settings.video&&r.fitVids();var e=o.children.eq(o.settings.startSlide);"all"==o.settings.preloadImages&&(e=o.children),o.settings.ticker?o.settings.pager=!1:(o.settings.pager&&T(),o.settings.controls&&C(),o.settings.auto&&o.settings.autoControls&&E(),(o.settings.controls||o.settings.autoControls||o.settings.pager)&&o.viewport.after(o.controls.el)),g(e,h)},g=function(e,i){var s=e.find("img, iframe").length;if(0==s)return i(),void 0;var n=0;e.find("img, iframe").each(function(){t(this).one("load",function(){++n==s&&i()}).each(function(){this.complete&&t(this).load()})})},h=function(){if(o.settings.infiniteLoop&&"fade"!=o.settings.mode&&!o.settings.ticker){var e="vertical"==o.settings.mode?o.settings.minSlides:o.settings.maxSlides,i=o.children.slice(0,e).clone().addClass("bx-clone"),s=o.children.slice(-e).clone().addClass("bx-clone");r.append(i).prepend(s)}o.loader.remove(),S(),"vertical"==o.settings.mode&&(o.settings.adaptiveHeight=!0),o.viewport.height(v()),r.redrawSlider(),o.settings.onSliderLoad(o.active.index),o.initialized=!0,o.settings.responsive&&t(window).bind("resize",Z),o.settings.auto&&o.settings.autoStart&&H(),o.settings.ticker&&L(),o.settings.pager&&q(o.settings.startSlide),o.settings.controls&&W(),o.settings.touchEnabled&&!o.settings.ticker&&O()},v=function(){var e=0,s=t();if("vertical"==o.settings.mode||o.settings.adaptiveHeight)if(o.carousel){var n=1==o.settings.moveSlides?o.active.index:o.active.index*m();for(s=o.children.eq(n),i=1;i<=o.settings.maxSlides-1;i++)s=n+i>=o.children.length?s.add(o.children.eq(i-1)):s.add(o.children.eq(n+i))}else s=o.children.eq(o.active.index);else s=o.children;return"vertical"==o.settings.mode?(s.each(function(){e+=t(this).outerHeight()}),o.settings.slideMargin>0&&(e+=o.settings.slideMargin*(o.settings.minSlides-1))):e=Math.max.apply(Math,s.map(function(){return t(this).outerHeight(!1)}).get()),e},p=function(){var t="100%";return o.settings.slideWidth>0&&(t="horizontal"==o.settings.mode?o.settings.maxSlides*o.settings.slideWidth+(o.settings.maxSlides-1)*o.settings.slideMargin:o.settings.slideWidth),t},u=function(){var t=o.settings.slideWidth,e=o.viewport.width();return 0==o.settings.slideWidth||o.settings.slideWidth>e&&!o.carousel||"vertical"==o.settings.mode?t=e:o.settings.maxSlides>1&&"horizontal"==o.settings.mode&&(e>o.maxThreshold||e<o.minThreshold&&(t=(e-o.settings.slideMargin*(o.settings.minSlides-1))/o.settings.minSlides)),t},f=function(){var t=1;if("horizontal"==o.settings.mode&&o.settings.slideWidth>0)if(o.viewport.width()<o.minThreshold)t=o.settings.minSlides;else if(o.viewport.width()>o.maxThreshold)t=o.settings.maxSlides;else{var e=o.children.first().width();t=Math.floor(o.viewport.width()/e)}else"vertical"==o.settings.mode&&(t=o.settings.minSlides);return t},x=function(){var t=0;if(o.settings.moveSlides>0)if(o.settings.infiniteLoop)t=o.children.length/m();else for(var e=0,i=0;e<o.children.length;)++t,e=i+f(),i+=o.settings.moveSlides<=f()?o.settings.moveSlides:f();else t=Math.ceil(o.children.length/f());return t},m=function(){return o.settings.moveSlides>0&&o.settings.moveSlides<=f()?o.settings.moveSlides:f()},S=function(){if(o.children.length>o.settings.maxSlides&&o.active.last&&!o.settings.infiniteLoop){if("horizontal"==o.settings.mode){var t=o.children.last(),e=t.position();b(-(e.left-(o.viewport.width()-t.width())),"reset",0)}else if("vertical"==o.settings.mode){var i=o.children.length-o.settings.minSlides,e=o.children.eq(i).position();b(-e.top,"reset",0)}}else{var e=o.children.eq(o.active.index*m()).position();o.active.index==x()-1&&(o.active.last=!0),void 0!=e&&("horizontal"==o.settings.mode?b(-e.left,"reset",0):"vertical"==o.settings.mode&&b(-e.top,"reset",0))}},b=function(t,e,i,s){if(o.usingCSS){var n="vertical"==o.settings.mode?"translate3d(0, "+t+"px, 0)":"translate3d("+t+"px, 0, 0)";r.css("-"+o.cssPrefix+"-transition-duration",i/1e3+"s"),"slide"==e?(r.css(o.animProp,n),r.bind("transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd",function(){r.unbind("transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd"),D()})):"reset"==e?r.css(o.animProp,n):"ticker"==e&&(r.css("-"+o.cssPrefix+"-transition-timing-function","linear"),r.css(o.animProp,n),r.bind("transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd",function(){r.unbind("transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd"),b(s.resetValue,"reset",0),N()}))}else{var a={};a[o.animProp]=t,"slide"==e?r.animate(a,i,o.settings.easing,function(){D()}):"reset"==e?r.css(o.animProp,t):"ticker"==e&&r.animate(a,speed,"linear",function(){b(s.resetValue,"reset",0),N()})}},w=function(){for(var e="",i=x(),s=0;i>s;s++){var n="";o.settings.buildPager&&t.isFunction(o.settings.buildPager)?(n=o.settings.buildPager(s),o.pagerEl.addClass("bx-custom-pager")):(n=s+1,o.pagerEl.addClass("bx-default-pager")),e+='<div class="bx-pager-item"><a href="" data-slide-index="'+s+'" class="bx-pager-link">'+n+"</a></div>"}o.pagerEl.html(e)},T=function(){o.settings.pagerCustom?o.pagerEl=t(o.settings.pagerCustom):(o.pagerEl=t('<div class="bx-pager" />'),o.settings.pagerSelector?t(o.settings.pagerSelector).html(o.pagerEl):o.controls.el.addClass("bx-has-pager").append(o.pagerEl),w()),o.pagerEl.on("click","a",I)},C=function(){o.controls.next=t('<a class="bx-next" href="">'+o.settings.nextText+"</a>"),o.controls.prev=t('<a class="bx-prev" href="">'+o.settings.prevText+"</a>"),o.controls.next.bind("click",y),o.controls.prev.bind("click",z),o.settings.nextSelector&&t(o.settings.nextSelector).append(o.controls.next),o.settings.prevSelector&&t(o.settings.prevSelector).append(o.controls.prev),o.settings.nextSelector||o.settings.prevSelector||(o.controls.directionEl=t('<div class="bx-controls-direction" />'),o.controls.directionEl.append(o.controls.prev).append(o.controls.next),o.controls.el.addClass("bx-has-controls-direction").append(o.controls.directionEl))},E=function(){o.controls.start=t('<div class="bx-controls-auto-item"><a class="bx-start" href="">'+o.settings.startText+"</a></div>"),o.controls.stop=t('<div class="bx-controls-auto-item"><a class="bx-stop" href="">'+o.settings.stopText+"</a></div>"),o.controls.autoEl=t('<div class="bx-controls-auto" />'),o.controls.autoEl.on("click",".bx-start",k),o.controls.autoEl.on("click",".bx-stop",M),o.settings.autoControlsCombine?o.controls.autoEl.append(o.controls.start):o.controls.autoEl.append(o.controls.start).append(o.controls.stop),o.settings.autoControlsSelector?t(o.settings.autoControlsSelector).html(o.controls.autoEl):o.controls.el.addClass("bx-has-controls-auto").append(o.controls.autoEl),A(o.settings.autoStart?"stop":"start")},P=function(){o.children.each(function(){var e=t(this).find("img:first").attr("title");void 0!=e&&(""+e).length&&t(this).append('<div class="bx-caption"><span>'+e+"</span></div>")})},y=function(t){o.settings.auto&&r.stopAuto(),r.goToNextSlide(),t.preventDefault()},z=function(t){o.settings.auto&&r.stopAuto(),r.goToPrevSlide(),t.preventDefault()},k=function(t){r.startAuto(),t.preventDefault()},M=function(t){r.stopAuto(),t.preventDefault()},I=function(e){o.settings.auto&&r.stopAuto();var i=t(e.currentTarget),s=parseInt(i.attr("data-slide-index"));s!=o.active.index&&r.goToSlide(s),e.preventDefault()},q=function(e){var i=o.children.length;return"short"==o.settings.pagerType?(o.settings.maxSlides>1&&(i=Math.ceil(o.children.length/o.settings.maxSlides)),o.pagerEl.html(e+1+o.settings.pagerShortSeparator+i),void 0):(o.pagerEl.find("a").removeClass("active"),o.pagerEl.each(function(i,s){t(s).find("a").eq(e).addClass("active")}),void 0)},D=function(){if(o.settings.infiniteLoop){var t="";0==o.active.index?t=o.children.eq(0).position():o.active.index==x()-1&&o.carousel?t=o.children.eq((x()-1)*m()).position():o.active.index==o.children.length-1&&(t=o.children.eq(o.children.length-1).position()),t&&("horizontal"==o.settings.mode?b(-t.left,"reset",0):"vertical"==o.settings.mode&&b(-t.top,"reset",0))}o.working=!1,o.settings.onSlideAfter(o.children.eq(o.active.index),o.oldIndex,o.active.index)},A=function(t){o.settings.autoControlsCombine?o.controls.autoEl.html(o.controls[t]):(o.controls.autoEl.find("a").removeClass("active"),o.controls.autoEl.find("a:not(.bx-"+t+")").addClass("active"))},W=function(){1==x()?(o.controls.prev.addClass("disabled"),o.controls.next.addClass("disabled")):!o.settings.infiniteLoop&&o.settings.hideControlOnEnd&&(0==o.active.index?(o.controls.prev.addClass("disabled"),o.controls.next.removeClass("disabled")):o.active.index==x()-1?(o.controls.next.addClass("disabled"),o.controls.prev.removeClass("disabled")):(o.controls.prev.removeClass("disabled"),o.controls.next.removeClass("disabled")))},H=function(){o.settings.autoDelay>0?setTimeout(r.startAuto,o.settings.autoDelay):r.startAuto(),o.settings.autoHover&&r.hover(function(){o.interval&&(r.stopAuto(!0),o.autoPaused=!0)},function(){o.autoPaused&&(r.startAuto(!0),o.autoPaused=null)})},L=function(){var e=0;if("next"==o.settings.autoDirection)r.append(o.children.clone().addClass("bx-clone"));else{r.prepend(o.children.clone().addClass("bx-clone"));var i=o.children.first().position();e="horizontal"==o.settings.mode?-i.left:-i.top}b(e,"reset",0),o.settings.pager=!1,o.settings.controls=!1,o.settings.autoControls=!1,o.settings.tickerHover&&!o.usingCSS&&o.viewport.hover(function(){r.stop()},function(){var e=0;o.children.each(function(){e+="horizontal"==o.settings.mode?t(this).outerWidth(!0):t(this).outerHeight(!0)});var i=o.settings.speed/e,s="horizontal"==o.settings.mode?"left":"top",n=i*(e-Math.abs(parseInt(r.css(s))));N(n)}),N()},N=function(t){speed=t?t:o.settings.speed;var e={left:0,top:0},i={left:0,top:0};"next"==o.settings.autoDirection?e=r.find(".bx-clone").first().position():i=o.children.first().position();var s="horizontal"==o.settings.mode?-e.left:-e.top,n="horizontal"==o.settings.mode?-i.left:-i.top,a={resetValue:n};b(s,"ticker",speed,a)},O=function(){o.touch={start:{x:0,y:0},end:{x:0,y:0}},o.viewport.bind("touchstart",X)},X=function(t){if(o.working)t.preventDefault();else{o.touch.originalPos=r.position();var e=t.originalEvent;o.touch.start.x=e.changedTouches[0].pageX,o.touch.start.y=e.changedTouches[0].pageY,o.viewport.bind("touchmove",Y),o.viewport.bind("touchend",V)}},Y=function(t){var e=t.originalEvent,i=Math.abs(e.changedTouches[0].pageX-o.touch.start.x),s=Math.abs(e.changedTouches[0].pageY-o.touch.start.y);if(3*i>s&&o.settings.preventDefaultSwipeX?t.preventDefault():3*s>i&&o.settings.preventDefaultSwipeY&&t.preventDefault(),"fade"!=o.settings.mode&&o.settings.oneToOneTouch){var n=0;if("horizontal"==o.settings.mode){var r=e.changedTouches[0].pageX-o.touch.start.x;n=o.touch.originalPos.left+r}else{var r=e.changedTouches[0].pageY-o.touch.start.y;n=o.touch.originalPos.top+r}b(n,"reset",0)}},V=function(t){o.viewport.unbind("touchmove",Y);var e=t.originalEvent,i=0;if(o.touch.end.x=e.changedTouches[0].pageX,o.touch.end.y=e.changedTouches[0].pageY,"fade"==o.settings.mode){var s=Math.abs(o.touch.start.x-o.touch.end.x);s>=o.settings.swipeThreshold&&(o.touch.start.x>o.touch.end.x?r.goToNextSlide():r.goToPrevSlide(),r.stopAuto())}else{var s=0;"horizontal"==o.settings.mode?(s=o.touch.end.x-o.touch.start.x,i=o.touch.originalPos.left):(s=o.touch.end.y-o.touch.start.y,i=o.touch.originalPos.top),!o.settings.infiniteLoop&&(0==o.active.index&&s>0||o.active.last&&0>s)?b(i,"reset",200):Math.abs(s)>=o.settings.swipeThreshold?(0>s?r.goToNextSlide():r.goToPrevSlide(),r.stopAuto()):b(i,"reset",200)}o.viewport.unbind("touchend",V)},Z=function(){var e=t(window).width(),i=t(window).height();(a!=e||l!=i)&&(a=e,l=i,r.redrawSlider(),o.settings.onSliderResize.call(r,o.active.index))};return r.goToSlide=function(e,i){if(!o.working&&o.active.index!=e)if(o.working=!0,o.oldIndex=o.active.index,o.active.index=0>e?x()-1:e>=x()?0:e,o.settings.onSlideBefore(o.children.eq(o.active.index),o.oldIndex,o.active.index),"next"==i?o.settings.onSlideNext(o.children.eq(o.active.index),o.oldIndex,o.active.index):"prev"==i&&o.settings.onSlidePrev(o.children.eq(o.active.index),o.oldIndex,o.active.index),o.active.last=o.active.index>=x()-1,o.settings.pager&&q(o.active.index),o.settings.controls&&W(),"fade"==o.settings.mode)o.settings.adaptiveHeight&&o.viewport.height()!=v()&&o.viewport.animate({height:v()},o.settings.adaptiveHeightSpeed),o.children.filter(":visible").fadeOut(o.settings.speed).css({zIndex:0}),o.children.eq(o.active.index).css("zIndex",o.settings.slideZIndex+1).fadeIn(o.settings.speed,function(){t(this).css("zIndex",o.settings.slideZIndex),D()});else{o.settings.adaptiveHeight&&o.viewport.height()!=v()&&o.viewport.animate({height:v()},o.settings.adaptiveHeightSpeed);var s=0,n={left:0,top:0};if(!o.settings.infiniteLoop&&o.carousel&&o.active.last)if("horizontal"==o.settings.mode){var a=o.children.eq(o.children.length-1);n=a.position(),s=o.viewport.width()-a.outerWidth()}else{var l=o.children.length-o.settings.minSlides;n=o.children.eq(l).position()}else if(o.carousel&&o.active.last&&"prev"==i){var d=1==o.settings.moveSlides?o.settings.maxSlides-m():(x()-1)*m()-(o.children.length-o.settings.maxSlides),a=r.children(".bx-clone").eq(d);n=a.position()}else if("next"==i&&0==o.active.index)n=r.find("> .bx-clone").eq(o.settings.maxSlides).position(),o.active.last=!1;else if(e>=0){var c=e*m();n=o.children.eq(c).position()}if("undefined"!=typeof n){var g="horizontal"==o.settings.mode?-(n.left-s):-n.top;b(g,"slide",o.settings.speed)}}},r.goToNextSlide=function(){if(o.settings.infiniteLoop||!o.active.last){var t=parseInt(o.active.index)+1;r.goToSlide(t,"next")}},r.goToPrevSlide=function(){if(o.settings.infiniteLoop||0!=o.active.index){var t=parseInt(o.active.index)-1;r.goToSlide(t,"prev")}},r.startAuto=function(t){o.interval||(o.interval=setInterval(function(){"next"==o.settings.autoDirection?r.goToNextSlide():r.goToPrevSlide()},o.settings.pause),o.settings.autoControls&&1!=t&&A("stop"))},r.stopAuto=function(t){o.interval&&(clearInterval(o.interval),o.interval=null,o.settings.autoControls&&1!=t&&A("start"))},r.getCurrentSlide=function(){return o.active.index},r.getCurrentSlideElement=function(){return o.children.eq(o.active.index)},r.getSlideCount=function(){return o.children.length},r.redrawSlider=function(){o.children.add(r.find(".bx-clone")).outerWidth(u()),o.viewport.css("height",v()),o.settings.ticker||S(),o.active.last&&(o.active.index=x()-1),o.active.index>=x()&&(o.active.last=!0),o.settings.pager&&!o.settings.pagerCustom&&(w(),q(o.active.index))},r.destroySlider=function(){o.initialized&&(o.initialized=!1,t(".bx-clone",this).remove(),o.children.each(function(){void 0!=t(this).data("origStyle")?t(this).attr("style",t(this).data("origStyle")):t(this).removeAttr("style")}),void 0!=t(this).data("origStyle")?this.attr("style",t(this).data("origStyle")):t(this).removeAttr("style"),t(this).unwrap().unwrap(),o.controls.el&&o.controls.el.remove(),o.controls.next&&o.controls.next.remove(),o.controls.prev&&o.controls.prev.remove(),o.pagerEl&&o.settings.controls&&o.pagerEl.remove(),t(".bx-caption",this).remove(),o.controls.autoEl&&o.controls.autoEl.remove(),clearInterval(o.interval),o.settings.responsive&&t(window).unbind("resize",Z))},r.reloadSlider=function(t){void 0!=t&&(n=t),r.destroySlider(),d()},d(),this}}(jQuery);
!function(e){"function"==typeof define&&define.amd?define(["jquery"],e):e("object"==typeof exports?require("jquery"):jQuery)}(function(e){function n(e){return c.raw?e:encodeURIComponent(e)}function i(e){return c.raw?e:decodeURIComponent(e)}function o(e){return n(c.json?JSON.stringify(e):String(e))}function r(e){0===e.indexOf('"')&&(e=e.slice(1,-1).replace(/\\"/g,'"').replace(/\\\\/g,"\\"));try{return e=decodeURIComponent(e.replace(u," ")),c.json?JSON.parse(e):e}catch(n){}}function t(n,i){var o=c.raw?n:r(n);return e.isFunction(i)?i(o):o}var u=/\+/g,c=e.cookie=function(r,u,f){if(u!==undefined&&!e.isFunction(u)){if("number"==typeof(f=e.extend({},c.defaults,f)).expires){var d=f.expires,a=f.expires=new Date;a.setTime(+a+864e5*d)}return document.cookie=[n(r),"=",o(u),f.expires?"; expires="+f.expires.toUTCString():"",f.path?"; path="+f.path:"",f.domain?"; domain="+f.domain:"",f.secure?"; secure":""].join("")}for(var p=r?undefined:{},s=document.cookie?document.cookie.split("; "):[],m=0,x=s.length;m<x;m++){var k=s[m].split("="),l=i(k.shift()),j=k.join("=");if(r&&r===l){p=t(j,u);break}r||(j=t(j))===undefined||(p[l]=j)}return p};c.defaults={},e.removeCookie=function(n,i){return e.cookie(n)!==undefined&&(e.cookie(n,"",e.extend({},i,{expires:-1})),!e.cookie(n))}});
(function($){
"use strict";
$(document).ready(function($){
$('.sv-ggmaps').each(function(){
var id=$(this).attr('id');
var seff=$('#'+id);
var zoom=seff.data('zoom'),
style=seff.data('style'),
control=seff.data('control')=='yes' ? true:false,
scrollwheel=seff.data('scrollwheel')=='yes' ? true:false,
disable_ui=seff.data('disable_ui')=='yes' ? true:false,
draggable=seff.data('draggable')=='yes' ? true:false,
locations=seff.data('location').split('|'),
location=locations[1].split(','),
lat=location[0],
lon=location[1],
marker=seff.data('market');
var latlng=new google.maps.LatLng(lat, lon);
var stylez;
switch(style){
case 'grayscale' :
stylez=[ {featureType: 'all',  stylers: [{saturation: -100},{gamma: 0.50}]} ];
break;
case 'blue' :
stylez=[ {featureType: 'all',  stylers: [{hue: '#0000b0'},{invert_lightness: 'true'},{saturation: -30}]} ];
break;
case 'dark' :
stylez=[ {featureType: 'all',  stylers: [{ hue: '#ff1a00' },{ invert_lightness: true },{ saturation: -100  },{ lightness: 33 },{ gamma: 0.5 }]} ];
break;
case 'pink' :
stylez=[ {"stylers": [{ "hue": "#ff61a6" },{ "visibility": "on" },{ "invert_lightness": true },{ "saturation": 40 },{ "lightness": 10 }]} ];
break;
case 'light' :
stylez=[ {"featureType": "water","elementType": "all","stylers": [{"hue": "#e9ebed"},{"saturation": -78},{"lightness": 67},{"visibility": "simplified"}]
},{"featureType": "landscape","elementType": "all","stylers": [{"hue": "#ffffff"},{"saturation": -100},{"lightness": 100},{"visibility": "simplified"}]
},{"featureType": "road","elementType": "geometry","stylers": [{"hue": "#bbc0c4"},{"saturation": -93},{"lightness": 31},{"visibility": "simplified"}]
},{"featureType": "poi","elementType": "all","stylers": [{"hue": "#ffffff"},{"saturation": -100},{"lightness": 100},{"visibility": "off"}]
},{"featureType": "road.local","elementType": "geometry","stylers": [{"hue": "#e9ebed"},{"saturation": -90},{"lightness": -8},{"visibility": "simplified"}]
},{"featureType": "transit","elementType": "all","stylers": [{"hue": "#e9ebed"},{"saturation": 10},{"lightness": 69},{"visibility": "on"}]
},{"featureType": "administrative.locality","elementType": "all","stylers": [ {"hue": "#2c2e33"},{"saturation": 7},{"lightness": 19},{"visibility": "on"}]
},{"featureType": "road","elementType": "labels","stylers": [{"hue": "#bbc0c4"},{"saturation": -93},{"lightness": 31},{"visibility": "on"}]
},{"featureType": "road.arterial","elementType": "labels","stylers": [{"hue": "#bbc0c4"},{"saturation": -93},{"lightness": -2},{"visibility": "simplified"}]} ];
break;
case 'blue-essence' :
stylez=[ {featureType: "landscape.natural",elementType: "geometry.fill",stylers: [{ "visibility": "on" },{ "color": "#e0efef" }]
},{featureType: "poi",elementType: "geometry.fill",stylers: [{ "visibility": "on" },{ "hue": "#1900ff" },{ "color": "#c0e8e8" }]
},{featureType: "landscape.man_made",elementType: "geometry.fill"
},{featureType: "road",elementType: "geometry",stylers: [{ lightness: 100 },{ visibility: "simplified" }]
},{featureType: "road",elementType: "labels",stylers: [{ visibility: "off" }]
},{featureType: 'water',stylers: [{ color: '#7dcdcd' }]
},{featureType: 'transit.line',elementType: 'geometry',stylers: [{ visibility: 'on' },{ lightness: 700 }]} ];
break;
case 'bentley' :
stylez=[ {featureType: "landscape",stylers: [{hue: "#F1FF00"},{saturation: -27.4},{lightness: 9.4},{gamma: 1}]
},{featureType: "road.highway",stylers: [{hue: "#0099FF"},{saturation: -20},{lightness: 36.4},{gamma: 1}]
},{featureType: "road.arterial",stylers: [{hue: "#00FF4F"},{saturation: 0},{lightness: 0},{gamma: 1}]
},{featureType: "road.local",stylers: [{hue: "#FFB300"},{saturation: -38},{lightness: 11.2},{gamma: 1}]
},{featureType: "water",stylers: [{hue: "#00B6FF"},{saturation: 4.2},{lightness: -63.4},{gamma: 1}]
},{featureType: "poi",stylers: [{hue: "#9FFF00"},{saturation: 0},{lightness: 0},{gamma: 1}]} ];
break;
case 'retro' :
stylez=[ {featureType:"administrative",stylers:[{visibility:"off"}]
},{featureType:"poi",stylers:[{visibility:"simplified"}]},{featureType:"road",elementType:"labels",stylers:[{visibility:"simplified"}]
},{featureType:"water",stylers:[{visibility:"simplified"}]},{featureType:"transit",stylers:[{visibility:"simplified"}]},{featureType:"landscape",stylers:[{visibility:"simplified"}]
},{featureType:"road.highway",stylers:[{visibility:"off"}]},{featureType:"road.local",stylers:[{visibility:"on"}]
},{featureType:"road.highway",elementType:"geometry",stylers:[{visibility:"on"}]},{featureType:"water",stylers:[{color:"#84afa3"},{lightness:52}]},{stylers:[{saturation:-17},{gamma:0.36}]
},{featureType:"transit.line",elementType:"geometry",stylers:[{color:"#3f518c"}]} ];
break;
case 'cobalt' :
stylez=[ {featureType: "all",elementType: "all",stylers: [{invert_lightness: true},{saturation: 10},{lightness: 30},{gamma: 0.5},{hue: "#435158"}]} ];
break;
case 'brownie' :
stylez=[ {"stylers": [{ "hue": "#ff8800" },{ "gamma": 0.4 }]} ];
break;
default :
stylez='';
};
var settings={
zoom: Number(zoom),
center: latlng,
mapTypeControl: control,
mapTypeControlOptions: {
mapTypeIds: [google.maps.MapTypeId.ROADMAP, 'tehgrayz']
},
scrollwheel: scrollwheel,
disableDefaultUI: disable_ui,
draggable: draggable,
};
var map=new google.maps.Map(document.getElementById(id), settings);
var mapType=new google.maps.StyledMapType(stylez, { name:style.charAt(0).toUpperCase() + style.slice(1) });
map.mapTypes.set('tehgrayz', mapType);
map.setMapTypeId('tehgrayz');
var contentString=["content"];
for (var i=0; i < locations.length; i++){
if(locations[i]!=''){
lat=locations[i].split(',')[0];
lon=locations[i].split(',')[1];
var label=locations[i].split(',')[2];
var info_content=locations[i].split(',')[3];
var companyPos=new google.maps.LatLng(lat, lon);
var companyMarker=new google.maps.Marker({
position: companyPos,
map: map,
icon: marker,
title: label,
zIndex: 3
});
contentString.push('<div class="wrap-content">'+info_content+'</div>');
var infowindow=new google.maps.InfoWindow({
maxWidth: 360
});
google.maps.event.addListener(companyMarker, 'click', (function(companyMarker, i){
return function(){
infowindow.setContent(contentString[i]);
infowindow.open(map, companyMarker);
}})(companyMarker, i));
}};})
});
})(jQuery);
(function($){
"use strict";
function fix_custom_section_item_height(){
if($(window).width() > 667){
var item_height=$('.content-from-cat .col-md-3').height();
$('.content-from-cat .col-md-3').each(function(){
var current_height=$(this).height();
if(current_height > item_height) item_height=current_height;
})
$('.content-from-cat .custom-item-large > div').css('height',item_height);
$('.content-from-cat .custom-list-small > div').css('height',item_height/3);
}}
function letter_popup(){
var content=$('#boxes-content').html();
$('#boxes-content').html('');
$('#boxes').html(content);
if($('#boxes').html()!=''){
var id='#dialog';
var maskHeight=$(document).height();
var maskWidth=$(window).width();
$('#mask').css({'width':maskWidth,'height':maskHeight});
$('#mask').fadeIn(500);
$('#mask').fadeTo("slow",0.9);
var winH=$(window).height();
var winW=$(window).width();
$(id).css('top',  winH/2-$(id).height()/2);
$(id).css('left', winW/2-$(id).width()/2);
$(id).fadeIn(2000);
$('.window .close-popup').click(function (e){
e.preventDefault();
$('#mask').hide();
$('.window').hide();
});
$('#mask').click(function (){
$(this).hide();
$('.window').hide();
});
}}
function fixed_header(){
var menu_element;
menu_element=$('.main-nav:not(.menu-fixed-content)').closest('.vc_row');
var column_element=$('.main-nav:not(.menu-fixed-content)').closest('.col-sm-12');
if(column_element.length > 0&&!column_element.hasClass('col-md-9')&&!column_element.hasClass('col-md-6')&&!column_element.hasClass('col-md-4'))  menu_element=$('.main-nav:not(.menu-fixed-content)').closest('.col-sm-12');
if($('.menu-fixed-enable').length > 0&&$(window).width()>1024){
var menu_class=$('.main-nav').attr('class');
var header_height=$("#header").height()+100;
var ht=header_height + 150;
var st=$(window).scrollTop();
if(!menu_element.hasClass('header-fixed')&&menu_element.attr('data-vc-full-width')=='true') menu_element.addClass('header-fixed');
if(st>header_height){
if(menu_element.attr('data-vc-full-width')=='true'){
if(st > ht) menu_element.addClass('active');
else menu_element.removeClass('active');
menu_element.addClass('fixed-header');
}else{
if(st > ht) menu_element.parent().parent().addClass('active');
else menu_element.parent().parent().removeClass('active');
if(!menu_element.parent().parent().hasClass('fixed-header')){
menu_element.wrap("<div class='menu-fixed-content fixed-header "+menu_class+"'><div class='container'></div></div>");
}}
}else{
menu_element.removeClass('active');
if(menu_element.attr('data-vc-full-width')=='true') menu_element.removeClass('fixed-header');
else{
if(menu_element.parent().parent().hasClass('fixed-header')){
menu_element.unwrap();
menu_element.unwrap();
}}
}}else{
menu_element.removeClass('active');
if(menu_element.attr('data-vc-full-width')=='true') menu_element.removeClass('fixed-header');
else{
if(menu_element.parent().parent().hasClass('fixed-header')){
menu_element.unwrap();
menu_element.unwrap();
}}
}}
function background(){
$('.bg-slider .item-bn,.bg-slider .item-banner').each(function(){
var src=$(this).find('.banner-thumb a img').attr('src');
$(this).find('.banner-thumb a img').css('height',$(this).find('.banner-thumb a img').attr('height'));
$(this).css('background-image','url("'+src+'")');
});
}
function detail_gallery(){
if($('.detail-gallery').length>0){
$('.detail-gallery').each(function(){
$(this).find(".carousel").jCarouselLite({
btnNext: $(this).find(".gallery-control .next"),
btnPrev: $(this).find(".gallery-control .prev"),
speed: 800,
visible:3,
});
$(this).find('.mid img').elevateZoom({
zoomType: "inner",
cursor: "crosshair",
zoomWindowFadeIn: 500,
zoomWindowFadeOut: 750
});
$(this).find(".carousel a").on('click',function(event){
event.preventDefault();
$(this).parents('.detail-gallery').find(".carousel a").removeClass('active');
$(this).addClass('active');
$(this).parents('.detail-gallery').find(".mid img").attr("src", $(this).find('img').attr("src"));
$(this).parents('.detail-gallery').find(".mid img").attr("alt", $(this).find('img').attr("alt"));
$(this).parents('.detail-gallery').find(".mid img").attr("title", $(this).find('img').attr("title"));
$(this).parents('.detail-gallery').find(".mid img").attr("srcset", $(this).find('img').attr("srcset"));
var z_url=$(this).parents('.detail-gallery').find('.mid img').attr('src');
$('.zoomWindow').css('background-image','url("'+z_url+'")');
$.removeData($('.detail-gallery .mid img'), 'elevateZoom');
$('.zoomContainer').remove();
$('.detail-gallery .mid img').elevateZoom({
zoomType: "inner",
cursor: "crosshair",
zoomWindowFadeIn: 500,
zoomWindowFadeOut: 750
});
});
});
}}
function menu_responsive(){
$('.toggle-mobile-menu').on('click',function(event){
event.preventDefault();
$(this).parents('.main-nav').toggleClass('active');
});
if($(window).width()<768){
$('.main-nav li.menu-item-has-children>a').on('click',function(event){
if($(window).width()<768){
event.preventDefault();
$(this).next().stop(true,false).slideToggle();
}});
}}
function fix_variable_product(){
$('body input[name="variation_id"]').on('change',function(){
var id=$(this).val();
var data=$('.variations_form').attr('data-product_variations');
var curent_data={};
data=$.parseJSON(data);
if(id){
for (var i=data.length - 1; i >=0; i--){
if(data[i].variation_id==id) curent_data=data[i];
};
if('image_id' in curent_data){
$('.detail-gallery .gallery-control').find('li[data-image_id="'+curent_data.image_id+'"] a').trigger('click');
}
if($('.product-supper11').length > 0){
var slider_owl=$(this).parents('.product-supper11').find('.product-detail11 .wrap-item');
var index=slider_owl.find('.item[data-variation_id="'+id+'"]').attr('data-index');
slider_owl.trigger('owl.goTo', index);
}
if($('.trend-box18').length > 0){
$(this).parents('.item-detail18').find('.trend-thumb18').find('img').removeClass('active');
$(this).parents('.item-detail18').find('.trend-thumb18').find('div[data-variation_id="'+id+'"]').find('img').addClass('active');
}
$('.total-current').attr('data-re_price',curent_data.display_price);
$('.total-current').attr('data-price',curent_data.display_regular_price);
$('.total-current').html(curent_data.display_price);
$('.addcart-special').removeClass("disabled");
}
else $('.addcart-special').addClass("disabled");
})
$('body .variations_form select').live('change',function(){
var text=$(this).val();
$(this).parents('.attr-product').find('.current-color').html(text);
})
if($('.wrap-attr-product.special').length > 0){
$('.attr-filter ul li a').live('click',function(event){
event.preventDefault();
var text=$(this).html();
$(this).parents('.attr-product').find('.current-color').html(text);
$(this).parents('ul').find('li').removeClass('active');
$(this).parents('ul').find('li a').removeClass('active');
$(this).parent().addClass('active');
$(this).addClass('active');
var attribute=$(this).parent().attr('data-attribute');
var id=$(this).parents('ul').attr('data-attribute-id');
$('#'+id).val(attribute);
$('#'+id).trigger('change');
$('#'+id).trigger('focusin');
return false;
})
$('.attr-hover-box').hover(function(){
var seff=$(this);
var old_html=$(this).find('ul').html();
var current_val=$(this).find('ul li.active').attr('data-attribute');
$(this).next().find('select').trigger('focusin');
var content='';
$(this).next().find('select').find('option').each(function(){
var val=$(this).attr('value');
var title=$(this).html();
var el_class='';
var in_class='';
if(current_val==val){
el_class=' class="active"';
in_class='active';
}
if(val!=''){
content +='<li'+el_class+' data-attribute="'+val+'"><a href="#" class="bgcolor-'+val+' '+in_class+'"><span></span>'+title+'</a></li>';
}})
if(old_html!=content) $(this).find('ul').html(content);
})
$('body .reset_variations').live('click',function(){
$('.attr-hover-box').each(function(){
var seff=$(this);
var old_html=$(this).find('ul').html();
var current_val=$(this).find('ul li.active').attr('data-attribute');
$(this).next().find('select').trigger('focusin');
var content='';
$(this).next().find('select').find('option').each(function(){
var val=$(this).attr('value');
var title=$(this).html();
var el_class='';
var in_class='';
if(current_val==val){
el_class=' class="active"';
in_class='active';
}
if(val!=''){
content +='<li'+el_class+' data-attribute="'+val+'"><a href="#" class="bgcolor-'+val+' '+in_class+'"><span></span>'+title+'</a></li>';
}})
if(old_html!=content) $(this).find('ul').html(content);
$(this).find('ul li').removeClass('active');
})
})
}}
function afterAction(){
this.$elem.find('.owl-item').removeClass('active');
this.$elem.find('.owl-item').eq(this.owl.currentItem).addClass('active');
this.$elem.find('.owl-item').each(function(){
var check=$(this).hasClass('active');
if(check==true){
$(this).find('.animated').each(function(){
var anime=$(this).attr('data-animated');
$(this).addClass(anime);
});
}else{
$(this).find('.animated').each(function(){
var anime=$(this).attr('data-animated');
$(this).removeClass(anime);
});
}})
}
function s7upf_qty_click(){
$("body").on("click",".quantity .qty-up",function(){
var min=$(this).prev().attr("min");
var max=$(this).prev().attr("max");
var step=$(this).prev().attr("step");
if(step===undefined) step=1;
if(max!==undefined&&Number($(this).prev().val())< Number(max)||max===undefined||max===''){
if(step!='') $(this).prev().val(Number($(this).prev().val())+Number(step));
}
$('div.woocommerce > form input[name="update_cart"]').prop('disabled', false);
return false;
})
$("body").on("click",".quantity .qty-down",function(){
var min=$(this).next().attr("min");
var max=$(this).next().attr("max");
var step=$(this).next().attr("step");
if(step===undefined) step=1;
if(Number($(this).next().val()) > 1){
if(min!==undefined&&$(this).next().val()>min||min===undefined||min===''){
if(step!='') $(this).next().val(Number($(this).next().val())-Number(step));
}}
$('div.woocommerce > form input[name="update_cart"]').prop('disabled', false);
return false;
})
$("body").on("keyup change","input.qty-val",function(){
$('div.woocommerce > form input[name="update_cart"]').prop('disabled', false);
})
}
function s7upf_owl_slider(){
if($('.sv-slider').length>0){
$('.sv-slider').each(function(){
var seff=$(this);
var item=seff.attr('data-item');
var speed=seff.attr('data-speed');
var itemres=seff.attr('data-itemres');
var animation=seff.attr('data-animation');
var nav=seff.attr('data-nav');
var text_prev=seff.attr('data-prev');
var text_next=seff.attr('data-next');
var pagination=false, navigation=true, singleItem=false;
var autoplay;
if(speed!='') autoplay=speed;
else autoplay=false;
if(nav=='nav-hidden'){
pagination=false;
navigation=false;
}
if(nav=='superdeal-slider11'||nav=='testimo-slider'||nav=='testimo-slider14'){
pagination=true;
navigation=false;
}
if(nav=='banner-slider banner-slider13'){
pagination=true;
navigation=true;
}
if(animation!=''){
singleItem=true;
item='1';
}
var prev_text='<i class="fa fa-angle-left" aria-hidden="true"></i>';
var next_text='<i class="fa fa-angle-right" aria-hidden="true"></i>';
if(nav=='nav-text-data'){
var prev_text=text_prev;
var next_text=text_next;
}
if(itemres==''||itemres===undefined){
if(item=='1') itemres='0:1,480:1,768:1,1200:1';
if(item=='2') itemres='0:1,480:1,768:2,1200:2';
if(item=='3') itemres='0:1,480:2,768:2,992:3';
if(item=='4') itemres='0:1,480:2,840:3,1200:4';
if(item >='5') itemres='0:1,480:2,768:3,1024:4,1200:'+item;
}
itemres=itemres.split(',');
var i;
for (i=0; i < itemres.length; i++){
itemres[i]=itemres[i].split(':');
}
seff.owlCarousel({
items: item,
itemsCustom: itemres,
autoPlay:autoplay,
pagination: pagination,
navigation: navigation,
navigationText:[prev_text,next_text],
singleItem:singleItem,
beforeInit:background,
afterAction: afterAction,
touchDrag: true,
transitionStyle:animation
});
});
}}
function s7upf_all_slider(seff,number){
if(!seff) seff=$('.smart-slider');
if(!number) number='';
if(seff.length>0){
seff.each(function(){
var seff=$(this);
var item=seff.attr('data-item'+number);
var speed=seff.attr('data-speed');
var itemres=seff.attr('data-itemres'+number);
var text_prev=seff.attr('data-prev');
var text_next=seff.attr('data-next');
var pagination=seff.attr('data-pagination');
var navigation=seff.attr('data-navigation');
var autoplay;
if(speed===undefined) speed='';
if(speed!='') autoplay=speed;
else autoplay=false;
if(item==''||item===undefined) item=1;
if(itemres===undefined) itemres='';
if(text_prev=='false') text_prev='';
else{
if(text_prev==''||text_prev===undefined) text_prev='<i class="fa fa-angle-left" aria-hidden="true"></i>';
else text_prev='<i class="fa '+text_prev+'" aria-hidden="true"></i>';
}
if(text_next=='false') text_next='';
else{
if(text_next==''||text_next===undefined) text_next='<i class="fa fa-angle-right" aria-hidden="true"></i>';
else text_next='<i class="fa '+text_next+' aria-hidden="true"></i>';
}
if(pagination=='true') pagination=true;
else pagination=false;
if(navigation=='true') navigation=true;
else navigation=false;
if(itemres==''||itemres===undefined){
if(item=='1') itemres='0:1,480:1,768:1,1200:1';
if(item=='2') itemres='0:1,480:1,768:2,1200:2';
if(item=='3') itemres='0:1,480:2,768:2,992:3';
if(item=='4') itemres='0:1,480:2,840:3,1200:4';
if(item >='5') itemres='0:1,480:2,768:3,1200:'+item;
}
itemres=itemres.split(',');
var i;
for (i=0; i < itemres.length; i++){
itemres[i]=itemres[i].split(':');
}
seff.owlCarousel({
items: item,
itemsCustom: itemres,
autoPlay:autoplay,
pagination: pagination,
navigation: navigation,
navigationText:[text_prev,text_next],
addClassActive:true,
touchDrag: true,
});
});
}}
$(document).ready(function(){
if($('.variations_form').length > 0){
var id=$('input[name="variation_id"]').val();
if(id==0) $('.addcart-special').addClass("disabled");
}
$('.special-total-cart .qty-down').on('click',function(){
$('.detail-extralink .qty-down').trigger('click');
var price=$('.total-current').attr('data-price');
var qty=$('.detail-qty input[name="quantity"]').val();
var total=Number(price)*Number(qty);
$('.total-current').html(total);
})
$('.special-total-cart .qty-up').on('click',function(){
$('.detail-extralink .qty-up').trigger('click');
var price=$('.total-current').attr('data-price');
var qty=$('.detail-qty input[name="quantity"]').val();
var total=Number(price)*Number(qty);
$('.total-current').html(total);
})
$('.btn-get-coupon').fancybox({
'closeBtn':false
});
$('.btn-get-coupon').fancybox({
'closeBtn':false
});
$('.close-light-box').on('click',function(event){
event.preventDefault();
$.fancybox.close();
})
letter_popup();
menu_responsive();
s7upf_qty_click();
fix_variable_product();
$('.title-cat-icon').on('click',function(){
if($(this).closest('.vc_row').hasClass('fixed-header')) $(this).next().slideToggle('slow');
})
$('.scroll-top').on('click',function(event){
event.preventDefault();
$('html, body').animate({scrollTop:0}, 'slow');
});
new WOW().init();
if($("#count-cart-item").length){
var count_cart_item=$("#count-cart-item").val();
$(".cart-item-count").html(count_cart_item);
}
$('.widget .product-categories li.cat-parent').first().addClass('active');
$('.widget .product-categories li.cat-parent').first().find('ul').show();
$('.widget .product-categories li.cat-parent > a').on('click',function(event){
event.preventDefault();
$(this).parent().toggleClass('active');
$(this).next().slideToggle();
});
$('.widget-title').on('click',function(){
$(this).toggleClass('active');
$(this).next().slideToggle();
});
$('.sv-mailchimp-form').each(function(){
var placeholder=$(this).attr('data-placeholder');
var submit=$(this).attr('data-submit');
if(placeholder) $(this).find('input[name="EMAIL"]').attr('placeholder',placeholder);
if(submit) $(this).find('input[type="submit"]').val(submit);
})
$('.select-category .list-category-toggle li a').click(function(event){
event.preventDefault();
$(this).parents('.list-category-toggle').find('li').removeClass('active');
$(this).parent().addClass('active');
var x=$(this).attr('data-filter');
if(x){
x=x.replace('.','');
$('.cat-value').val(x);
}
else $('.cat-value').val('');
$('.category-toggle-link span').text($(this).text());
});
$('.live-search-on input[name="s"]').on('click',function(event){
event.preventDefault();
event.stopPropagation();
$(this).parents('.live-search-on').addClass('active');
})
$('body').on('click',function(event){
$('.live-search-on.active').removeClass('active');
})
if($('.flash-countdown').length>0){
$(".flash-countdown").TimeCircles({
fg_width: 0.01,
bg_width: 1.2,
text_size: 0.07,
circle_bg_color: "#ffffff",
time: {
Days: {
show: true,
text: "",
color: "#f9bc02"
},
Hours: {
show: true,
text: "",
color: "#f9bc02"
},
Minutes: {
show: true,
text: "",
color: "#f9bc02"
},
Seconds: {
show: true,
text: "",
color: "#f9bc02"
}}
});
}
if($('.deals-cowndown').length>0){
$(".deals-cowndown").TimeCircles({
fg_width: 0.01,
bg_width: 1.2,
text_size: 0.07,
circle_bg_color: "#ffffff",
time: {
Days: {
show: true,
text: "d",
color: "#f9bc02"
},
Hours: {
show: true,
text: "h",
color: "#f9bc02"
},
Minutes: {
show: true,
text: "m",
color: "#f9bc02"
},
Seconds: {
show: true,
text: "s",
color: "#f9bc02"
}}
});
}
if($('.toggle-tab').length>0){
$('.toggle-tab').each(function(){
$(this).find('.item-toggle-tab').first().addClass('active');
$(this).find('.item-toggle-tab').first().find('.toggle-tab-content').show();
});
}
$('.toggle-tab-title').on('click',function(){
$(this).parent().siblings().removeClass('active');
$(this).parent().toggleClass('active');
$(this).parents('.toggle-tab').find('.toggle-tab-content').not($(this).next()).slideUp();
$(this).next().slideToggle();
});
});
$(window).load(function(){
detail_gallery();
s7upf_owl_slider();
s7upf_all_slider();
$('.top-banner19').each(function(){
var src=$(this).find('img').attr('src');
$(this).css('background-image','url('+src+')');
})
$('.top-banner19').on('click',function(){
console.log('hjshn');
window.location.href=$(this).find('a').attr('href');
})
$('.fix-slider-nav8').each(function(){
var title_width=$(this).find('.title18 > span').width();
$(this).find('.owl-controls').css('left',title_width + 20);
})
$('.post-zoom-link').fancybox();
$('.price-sale').each(function(){
var sale_html=$(this).find('.sale-content').html();
$(this).find('.sale-content').remove();
$(sale_html).insertAfter($(this).find('.product-price').find('del'));
})
if($('.cat-pro3').length>0){
$('.cat-pro3').each(function(){
$(this).find('.hide-cat-banner').on('click',function(event){
event.preventDefault();
$(this).parents('.cat-pro3').addClass('hidden-banner');
});
$(this).find('.show-cat-banner').on('click',function(event){
event.preventDefault();
$(this).parents('.cat-pro3').removeClass('hidden-banner');
});
});
}
if($('.countdown-master').length>0){
$('.countdown-master').each(function(){
var seconds=Number($(this).attr('data-time'));
$(this).FlipClock(seconds,{
clockFace: 'HourlyCounter',
countdown: true,
autoStart: true,
});
});
}
$("#header").css('min-height','');
if($(window).width()>1024){
$("#header").css('min-height',$("#header").height());
fixed_header();
}else{
$("#header").css('min-height','');
}
if($('.rtl-enable').length > 0){
$('*[data-vc-full-width="true"]').each(function(){
var style=$(this).attr('style');
style=style.replace("left","right");
$(this).attr('style',style);
})
$('*[data-vc-full-width="true"] > *[data-vc-full-width="true"]').each(function(){
var style=$(this).parent().attr('style');
$(this).attr('style',style);
})
}
if($('.content-blog-masonry').length>0){
$('.content-blog-masonry').masonry({
itemSelector: '.item-post-masonry',
});
}
if($('.bxslider-banner').length>0){
$('.bxslider-banner').each(function(){
$(this).find('.bxslider').bxSlider({
controls:false,
pagerCustom: $(this).find('.bx-pager')
});
});
}
if($('.detail-countdown').length>0){
$(".detail-countdown").TimeCircles({
fg_width: 0.01,
bg_width: 1.2,
text_size: 0.07,
circle_bg_color: "#ffffff",
time: {
Days: {
show: true,
text: "",
color: "#f9bc02"
},
Hours: {
show: true,
text: "",
color: "#f9bc02"
},
Minutes: {
show: true,
text: "",
color: "#f9bc02"
},
Seconds: {
show: true,
text: "",
color: "#f9bc02"
}}
});
}});
var w_width=$(window).width();
$(window).resize(function(){
var c_width=$(window).width();
setTimeout(function(){
if($('.rtl-enable').length > 0&&c_width!=w_width){
$('*[data-vc-full-width="true"]').each(function(){
var style=$(this).attr('style');
style=style.replace(" left:"," right:");
$(this).attr('style',style);
})
$('*[data-vc-full-width="true"] > *[data-vc-full-width="true"]').each(function(){
var style=$(this).parent().attr('style');
$(this).attr('style',style);
})
w_width=c_width;
}}, 3000);
$.removeData($('.detail-gallery .mid img'), 'elevateZoom');
$('.zoomContainer').remove();
$('.detail-gallery .mid img').elevateZoom({
zoomType: "inner",
cursor: "crosshair",
zoomWindowFadeIn: 500,
zoomWindowFadeOut: 750
});
});
$(window).scroll(function(){
if($(window).width()>1024){
$("#header").css('min-height',$("#header").height());
fixed_header();
}else{
$("#header").css('min-height','');
}
if($(this).scrollTop()>$(this).height()){
$('.scroll-top').addClass('active');
}else{
$('.scroll-top').removeClass('active');
}
var count=1;
if($('#coupon-light-box').length > 0){
if($(window).scrollTop() > ($('body').height() / 2 - 250)&&$(window).scrollTop() < ($('body').height() / 2 + 250)){
if(count==1){
if(!$.cookie("first_visitor")){
$('.btn-get-coupon').trigger('click');
count++;
}
$.cookie("first_visitor", "visited");
}}
}});
$('.counter').each(function(){
var $this=$(this),
countTo=$this.attr('data-count');
$({ countNum: $this.text()}).animate({
countNum: countTo
},
{
duration: 2000,
easing:'linear',
step: function(){
$this.text(Math.floor(this.countNum));
},
complete: function(){
$this.text(this.countNum);
}});
});
})(jQuery);
(function($){
"use strict";
function get_shop_filter(seff){
var filter={};
filter['price']={};
filter['cats']=[];
filter['attributes']={};
var terms=[];
var min_price=$('#min_price').attr('data-min');
var max_price=$('#max_price').attr('data-max');
filter['min_price']=min_price;
filter['max_price']=max_price;
seff.toggleClass('active');
if(seff.parents('.pagi-bar').hasClass('pagi-bar')){
seff.parents('.pagi-bar').find('.page-numbers').not(seff).removeClass('current');
seff.parents('.pagi-bar').find('.page-numbers').not(seff).removeClass('active');
seff.addClass('current');
seff.addClass('active');
}else{
$('.page-numbers').removeClass('current');
$('.page-numbers').removeClass('active');
$('.pagi-bar').find('.page-numbers').first().addClass('current active');
}
if(seff.attr('data-type')) seff.parents('.view-type').find('a.load-shop-ajax').not(seff).removeClass('active');
if($('.price_label .from')) filter['price']['min']=$('#min_price').val();
if($('.price_label .to')) filter['price']['max']=$('#max_price').val();
if($('.woocommerce-ordering')) filter['orderby']=$('select[name="orderby"]').val();
if(seff.hasClass('page-numbers')){
if(seff.parent().find('.page-numbers.current')) filter['page']=seff.parent().find('.page-numbers.current').html();
}else{
if($('.page-numbers.current')) filter['page']=$('.page-numbers.current').html();
}
var data_element=$('.shop-get-data');
if(seff.attr('data-number')) data_element.attr('data-number',seff.attr('data-number'));
if(seff.attr('data-column')) data_element.attr('data-column',seff.attr('data-column'));
if(data_element.attr('data-number')) filter['number']=data_element.attr('data-number');
if(data_element.attr('data-column')) filter['column']=data_element.attr('data-column');
if(data_element.attr('data-item_style')) filter['item_style']=data_element.attr('data-item_style');
if(data_element.attr('data-size')) filter['size']=data_element.attr('data-size');
if(data_element.attr('data-quickview')) filter['quickview']=data_element.attr('data-quickview');
if(data_element.attr('data-quickview_pos')) filter['quickview_pos']=data_element.attr('data-quickview_pos');
if(data_element.attr('data-quickview_style')) filter['quickview_style']=data_element.attr('data-quickview_style');
if(data_element.attr('data-extra_link')) filter['extra_link']=data_element.attr('data-extra_link');
if(data_element.attr('data-extra_style')) filter['extra_style']=data_element.attr('data-extra_style');
if(data_element.attr('data-label')) filter['label']=data_element.attr('data-label');
if(data_element.attr('data-shop_style')) filter['shop_style']=data_element.attr('data-shop_style');
if(data_element.attr('data-block_style')) filter['block_style']=data_element.attr('data-block_style');
var i=1;
$('.load-shop-ajax.active').each(function(){
var seff2=$(this);
if(seff2.attr('data-type')){
if(i==1) filter['type']=seff2.attr('data-type');
i++;
}
if(seff2.attr('data-attribute')&&seff2.attr('data-term')){
if(!filter['attributes'][seff2.attr('data-attribute')]) filter['attributes'][seff2.attr('data-attribute')]=[];
if($.inArray(seff2.attr('data-term'),filter['attributes'][seff2.attr('data-attribute')])) filter['attributes'][seff2.attr('data-attribute')].push(seff2.attr('data-term'));
}
if(seff2.attr('data-cat')&&$.inArray(seff2.attr('data-cat'),filter['cats'])) filter['cats'].push(seff2.attr('data-cat'));
})
if($('.shop-page').attr('data-cats')) filter['cats'].push($('.shop-page').attr('data-cats'));
var $_GET={};
if(document.location.toString().indexOf('?')!==-1){
var query=document.location
.toString()
.replace(/^.*?\?/, '')
.replace(/#.*$/, '')
.split('&');
for(var i=0, l=query.length; i<l; i++){
var aux=decodeURIComponent(query[i]).split('=');
$_GET[aux[0]]=aux[1];
}}
if($_GET['s']) filter['s']=$_GET['s'];
if($_GET['product_cat']) filter['cats']=$_GET['product_cat'].split(',');
return filter;
}
function load_ajax_shop(e){
e.preventDefault();
var filter=get_shop_filter($(this));
console.log(filter);
var content=$('.main-shop-load');
content.addClass('loadding');
content.append('<div class="shop-loading"><i class="fa fa-spinner fa-spin"></i></div>');
$.ajax({
type:"post",
url:ajax_process.ajaxurl,
crossDomain: true,
data: {
action: "load_shop",
filter_data: filter,
},
success: function(data){
if(data[data.length-1]=='0'){
data=data.split('');
data[data.length-1]='';
data=data.join('');
}
content.find(".shop-loading").remove();
content.removeClass('loadding');
content.html(data);
},
error: function(MLHttpRequest, textStatus, errorThrown){
console.log(errorThrown);
}});
return false;
}
$(document).ready(function(){
$('.wishlist-close').on('click',function(){
$('.wishlist-mask').fadeOut();
})
$('.add_to_wishlist').live('click',function(){
$('.wishlist-countdown').html('3');
$(this).addClass('added');
var product_id=$(this).attr("data-product-id");
var product_title=$(this).attr("data-product-title");
$('.wishlist-title').html(product_title);
$('.wishlist-mask').fadeIn();
var counter=3;
var popup;
popup=setInterval(function(){
counter--;
if(counter < 0){
clearInterval(popup);
$('.wishlist-mask').hide();
}else{
$(".wishlist-countdown").text(counter.toString());
}}, 1000);
})
$('.shop-ajax-enable').on('click','.load-shop-ajax,.page-numbers,.price_slider_amount .button',load_ajax_shop);
$('.shop-ajax-enable').on('change','select[name="orderby"]',load_ajax_shop);
$('.shop-ajax-enable .woocommerce-ordering').on('submit', function(e){
e.preventDefault();
});
$('.main-shop-load').on('click','.load-more-shop',function(e){
e.preventDefault();
var filter=get_shop_filter($(this));
var content=$('.main-shop-load .shop-get-data .row');
var paged=$(this).attr('data-page');
var max_page=$(this).attr('data-maxpage');
$(this).find('i').addClass('fa-spin');
var seff=$(this);
var $_GET={};
if(document.location.toString().indexOf('?')!==-1){
var query=document.location
.toString()
.replace(/^.*?\?/, '')
.replace(/#.*$/, '')
.split('&');
for(var i=0, l=query.length; i<l; i++){
var aux=decodeURIComponent(query[i]).split('=');
$_GET[aux[0]]=aux[1];
}}
var s_cat,s_posttype,s_s;
if($_GET['s']) s_s=$_GET['s'];
if($_GET['product_cat']) s_cat=$_GET['product_cat'];
if($_GET['s_posttype']) s_posttype=$_GET['post_type'];
$.ajax({
type:"post",
url:ajax_process.ajaxurl,
crossDomain: true,
data: {
action: "load_more_shop",
filter_data: filter,
paged: paged,
s: s_s,
cats: s_cat,
post_type: s_posttype,
},
success: function(data){
if(data[data.length-1]=='0'){
data=data.split('');
data[data.length-1]='';
data=data.join('');
}
content.append(data);
seff.find('i').removeClass('fa-spin');
paged=Number(paged) +1;
seff.attr('data-page',paged);
if(paged >=Number(max_page)) seff.fadeOut();
},
error: function(MLHttpRequest, textStatus, errorThrown){
console.log(errorThrown);
}});
})
$('.live-search-on input[name="s"]').on('change keyup',function(){
var key=$(this).val();
var trim_key=key.trim();
var cat=$(this).parents('.live-search-on').find('.cat-value').val();
var taxonomy=$(this).parents('.live-search-on').find('.cat-value').attr("name");
var post_type=$(this).parents('.live-search-on').find('input[name="post_type"]').val();
var seff=$(this);
var content=seff.parent().find('.list-product-search');
content.html('<i class="fa fa-spinner fa-spin"></i>');
content.addClass('ajax-loading');
$.ajax({
type:"post",
url:ajax_process.ajaxurl,
crossDomain: true,
data: {
action: "live_search",
key: key,
cat: cat,
post_type: post_type,
taxonomy: taxonomy,
},
success: function(data){
content.removeClass('ajax-loading');
if(data[data.length-1]=='0'){
data=data.split('');
data[data.length-1]='';
data=data.join('');
}
content.html(data);
},
error: function(MLHttpRequest, textStatus, errorThrown){
console.log(errorThrown);
}});
})
$("body").on("click",".add_to_cart_button:not(.product_type_variable)",function(e){
e.preventDefault();
var product_id=$(this).attr("data-product_id");
var seff=$(this);
seff.append('<i class="fa fa-spinner fa-spin"></i>');
$.ajax({
type:"post",
url:ajax_process.ajaxurl,
crossDomain: true,
data: {
action: "add_to_cart",
product_id: product_id
},
success: function(data){
seff.find('.fa-spinner').remove();
var cart_content=data.fragments['div.widget_shopping_cart_content'];
$('.mini-cart-main-content').html(cart_content);
$('.widget_shopping_cart_content').html(cart_content);
var count_item=cart_content.split("<li").length;
$('.cart-item-count').html(count_item-1);
var price=$('.content-mini-cart').find('.mini-cart-total').find('.total-price').html();
$('.total-mini-cart-price').html(price);
},
error: function(MLHttpRequest, textStatus, errorThrown){
console.log(errorThrown);
}});
});
$('body').on('click', '.btn-remove', function(e){
e.preventDefault();
var cart_item_key=$(this).parents('.item-info-cart').attr("data-key");
var element=$(this).parents('.item-info-cart');
var currency=["د.إ","лв.","kr.","Kr.","Rs.","руб."];
var decimal=$(".num-decimal").val();
function get_currency(pricehtml){
var check,index,price,i;
for(i=0;i<6;i++){
if(pricehtml.search(currency[i])!=-1){
check=true;
index=i;
}}
if(check) price=pricehtml.replace(currency[index],"");
else price=pricehtml.replace(/[^0-9\.]+/g,"");
return price;
}
$.ajax({
type: 'POST',
url: ajax_process.ajaxurl,
crossDomain: true,
data: {
action: 'product_remove',
cart_item_key: cart_item_key
},
success: function(data){
var price_html=element.find('span.amount').html();
var price=get_currency(price_html);
var qty=element.find('.qty-product').find('span').html();
var price_remove=price*qty;
var current_total_html=$(".total-price").find(".amount").html();
console.log(price);
var current_total=get_currency(current_total_html);
var new_total=current_total-price_remove;
new_total=parseFloat(new_total).toFixed(decimal);
current_total_html=current_total_html.replace(',','');
var new_total_html=current_total_html.replace(current_total,new_total);
element.slideUp().remove();
$(".total-price").find(".amount").html(new_total_html);
$(".total-mini-cart-price").html(new_total_html);
var current_html=$('.cart-item-count').html();
$('.cart-item-count').html(current_html-1);
$('.item-info-cart[data-key="'+cart_item_key+'"]').remove();
},
error: function(MLHttpRequest, textStatus, errorThrown){
console.log(errorThrown);
}});
return false;
});
$('body').on('click','.product-quick-view', function(e){
$.fancybox.showLoading();
var product_id=$(this).attr('data-product-id');
$.ajax({
type: 'POST',
url: ajax_process.ajaxurl,
crossDomain: true,
data: {
action: 'product_popup_content',
product_id: product_id
},
success: function(res){
if(res[res.length-1]=='0'){
res=res.split('');
res[res.length-1]='';
res=res.join('');
}
$.fancybox.hideLoading();
$.fancybox(res, {
width: 1000,
height: 600,
autoSize: false,
onStart: function(opener){
if($(opener).attr('id')=='login'){
$.get('/hicommon/authenticated', function(res){
if('yes'==res){
console.log('this user must have already authenticated in another browser tab, SO I want to avoid opening the fancybox.');
return false;
}else{
console.log('the user is not authenticated');
return true;
}});
}},
});
!function(t,a,i,r){var e=function(t){this.$form=t,this.$attributeFields=t.find(".variations select"),this.$singleVariation=t.find(".single_variation"),this.$singleVariationWrap=t.find(".single_variation_wrap"),this.$resetVariations=t.find(".reset_variations"),this.$product=t.closest(".product"),this.variationData=t.data("product_variations"),this.useAjax=!1===this.variationData,this.xhr=!1,this.$singleVariationWrap.show(),this.$form.off(".wc-variation-form"),this.getChosenAttributes=this.getChosenAttributes.bind(this),this.findMatchingVariations=this.findMatchingVariations.bind(this),this.isMatch=this.isMatch.bind(this),this.toggleResetLink=this.toggleResetLink.bind(this),t.on("click.wc-variation-form",".reset_variations",{variationForm:this},this.onReset),t.on("reload_product_variations",{variationForm:this},this.onReload),t.on("hide_variation",{variationForm:this},this.onHide),t.on("show_variation",{variationForm:this},this.onShow),t.on("click",".single_add_to_cart_button",{variationForm:this},this.onAddToCart),t.on("reset_data",{variationForm:this},this.onResetDisplayedVariation),t.on("reset_image",{variationForm:this},this.onResetImage),t.on("change.wc-variation-form",".variations select",{variationForm:this},this.onChange),t.on("found_variation.wc-variation-form",{variationForm:this},this.onFoundVariation),t.on("check_variations.wc-variation-form",{variationForm:this},this.onFindVariation),t.on("update_variation_values.wc-variation-form",{variationForm:this},this.onUpdateAttributes),setTimeout(function(){t.trigger("check_variations"),t.trigger("wc_variation_form")},100)};e.prototype.onReset=function(t){t.preventDefault(),t.data.variationForm.$attributeFields.val("").change(),t.data.variationForm.$form.trigger("reset_data")},e.prototype.onReload=function(t){var a=t.data.variationForm;a.variationData=a.$form.data("product_variations"),a.useAjax=!1===a.variationData,a.$form.trigger("check_variations")},e.prototype.onHide=function(t){t.preventDefault(),t.data.variationForm.$form.find(".single_add_to_cart_button").removeClass("wc-variation-is-unavailable").addClass("disabled wc-variation-selection-needed"),t.data.variationForm.$form.find(".woocommerce-variation-add-to-cart").removeClass("woocommerce-variation-add-to-cart-enabled").addClass("woocommerce-variation-add-to-cart-disabled")},e.prototype.onShow=function(t,a,i){t.preventDefault(),i?(t.data.variationForm.$form.find(".single_add_to_cart_button").removeClass("disabled wc-variation-selection-needed wc-variation-is-unavailable"),t.data.variationForm.$form.find(".woocommerce-variation-add-to-cart").removeClass("woocommerce-variation-add-to-cart-disabled").addClass("woocommerce-variation-add-to-cart-enabled")):(t.data.variationForm.$form.find(".single_add_to_cart_button").removeClass("wc-variation-selection-needed").addClass("disabled wc-variation-is-unavailable"),t.data.variationForm.$form.find(".woocommerce-variation-add-to-cart").removeClass("woocommerce-variation-add-to-cart-enabled").addClass("woocommerce-variation-add-to-cart-disabled"))},e.prototype.onAddToCart=function(i){t(this).is(".disabled")&&(i.preventDefault(),t(this).is(".wc-variation-is-unavailable")?a.alert(wc_add_to_cart_variation_params.i18n_unavailable_text):t(this).is(".wc-variation-selection-needed")&&a.alert(wc_add_to_cart_variation_params.i18n_make_a_selection_text))},e.prototype.onResetDisplayedVariation=function(t){var a=t.data.variationForm;a.$product.find(".product_meta").find(".sku").wc_reset_content(),a.$product.find(".product_weight").wc_reset_content(),a.$product.find(".product_dimensions").wc_reset_content(),a.$form.trigger("reset_image"),a.$singleVariation.slideUp(200).trigger("hide_variation")},e.prototype.onResetImage=function(t){t.data.variationForm.$form.wc_variations_image_update(!1)},e.prototype.onFindVariation=function(a){var i=a.data.variationForm,r=i.getChosenAttributes(),e=r.data;if(r.count===r.chosenCount)if(i.useAjax)i.xhr&&i.xhr.abort(),i.$form.block({message:null,overlayCSS:{background:"#fff",opacity:.6}}),e.product_id=parseInt(i.$form.data("product_id"),10),e.custom_data=i.$form.data("custom_data"),i.xhr=t.ajax({url:wc_add_to_cart_variation_params.wc_ajax_url.toString().replace("%%endpoint%%","get_variation"),type:"POST",data:e,success:function(t){t?i.$form.trigger("found_variation",[t]):(i.$form.trigger("reset_data"),i.$form.find(".single_variation").after('<p class="wc-no-matching-variations woocommerce-info">'+wc_add_to_cart_variation_params.i18n_no_matching_variations_text+"</p>"),i.$form.find(".wc-no-matching-variations").slideDown(200))},complete:function(){i.$form.unblock()}});else{i.$form.trigger("update_variation_values");var o=i.findMatchingVariations(i.variationData,e).shift();o?i.$form.trigger("found_variation",[o]):(i.$form.trigger("reset_data"),i.$form.find(".single_variation").after('<p class="wc-no-matching-variations woocommerce-info">'+wc_add_to_cart_variation_params.i18n_no_matching_variations_text+"</p>"),i.$form.find(".wc-no-matching-variations").slideDown(200))}else i.$form.trigger("update_variation_values"),i.$form.trigger("reset_data");i.toggleResetLink(r.chosenCount>0)},e.prototype.onFoundVariation=function(a,i){var r=a.data.variationForm,e=r.$product.find(".product_meta").find(".sku"),o=r.$product.find(".product_weight"),n=r.$product.find(".product_dimensions"),s=r.$singleVariationWrap.find(".quantity"),_=!0,c=!1,d="";i.sku?e.wc_set_content(i.sku):e.wc_reset_content(),i.weight?o.wc_set_content(i.weight_html):o.wc_reset_content(),i.dimensions?n.wc_set_content(i.dimensions_html):n.wc_reset_content(),r.$form.wc_variations_image_update(i),i.variation_is_visible?(c=wp.template("variation-template"),i.variation_id):c=wp.template("unavailable-variation-template"),d=(d=(d=c({variation:i})).replace("","")).replace("",""),r.$singleVariation.html(d),r.$form.find('input[name="variation_id"], input.variation_id').val(i.variation_id).change(),"yes"===i.is_sold_individually?(s.find("input.qty").val("1").attr("min","1").attr("max",""),s.hide()):(s.find("input.qty").attr("min",i.min_qty).attr("max",i.max_qty),s.show()),i.is_purchasable&&i.is_in_stock&&i.variation_is_visible||(_=!1),t.trim(r.$singleVariation.text())?r.$singleVariation.slideDown(200).trigger("show_variation",[i,_]):r.$singleVariation.show().trigger("show_variation",[i,_])},e.prototype.onChange=function(a){var i=a.data.variationForm;i.$form.find('input[name="variation_id"], input.variation_id').val("").change(),i.$form.find(".wc-no-matching-variations").remove(),i.useAjax?i.$form.trigger("check_variations"):(i.$form.trigger("woocommerce_variation_select_change"),i.$form.trigger("check_variations"),t(this).blur()),i.$form.trigger("woocommerce_variation_has_changed")},e.prototype.addSlashes=function(t){return t=t.replace(/'/g,"\\'"),t=t.replace(/"/g,'\\"')},e.prototype.onUpdateAttributes=function(a){var i=a.data.variationForm,r=i.getChosenAttributes().data;i.useAjax||(i.$attributeFields.each(function(a,e){var o=t(e),n=o.data("attribute_name")||o.attr("name"),s=t(e).data("show_option_none"),_=":gt(0)",c=0,d=t("<select/>"),m=o.val()||"",v=!0;if(!o.data("attribute_html")){var l=o.clone();l.find("option").removeAttr("disabled attached").removeAttr("selected"),o.data("attribute_options",l.find("option"+_).get()),o.data("attribute_html",l.html())}d.html(o.data("attribute_html"));var h=t.extend(!0,{},r);h[n]="";var g=i.findMatchingVariations(i.variationData,h);for(var f in g)if("undefined"!=typeof g[f]){var u=g[f].attributes;for(var p in u)if(u.hasOwnProperty(p)){var w=u[p],b="";p===n&&(g[f].variation_is_active&&(b="enabled"),w?(w=t("<div/>").html(w).text(),d.find('option[value="'+i.addSlashes(w)+'"]').addClass("attached "+b)):d.find("option:gt(0)").addClass("attached "+b))}}c=d.find("option.attached").length,!m||0!==c&&0!==d.find('option.attached.enabled[value="'+i.addSlashes(m)+'"]').length||(v=!1),c>0&&m&&v&&"no"===s&&(d.find("option:first").remove(),_=""),d.find("option"+_+":not(.attached)").remove(),o.html(d.html()),o.find("option"+_+":not(.enabled)").prop("disabled",!0),m?v?o.val(m):o.val("").change():o.val("")}),i.$form.trigger("woocommerce_update_variation_values"))},e.prototype.getChosenAttributes=function(){var a={},i=0,r=0;return this.$attributeFields.each(function(){var e=t(this).data("attribute_name")||t(this).attr("name"),o=t(this).val()||"";o.length>0&&r++,i++,a[e]=o}),{count:i,chosenCount:r,data:a}},e.prototype.findMatchingVariations=function(t,a){for(var i=[],r=0;r<t.length;r++){var e=t[r];this.isMatch(e.attributes,a)&&i.push(e)}return i},e.prototype.isMatch=function(t,a){var i=!0;for(var r in t)if(t.hasOwnProperty(r)){var e=t[r],o=a[r];void 0!==e&&void 0!==o&&0!==e.length&&0!==o.length&&e!==o&&(i=!1)}return i},e.prototype.toggleResetLink=function(t){t?"hidden"===this.$resetVariations.css("visibility")&&this.$resetVariations.css("visibility","visible").hide().fadeIn():this.$resetVariations.css("visibility","hidden")},t.fn.wc_variation_form=function(){return new e(this),this},t.fn.wc_set_content=function(t){void 0===this.attr("data-o_content")&&this.attr("data-o_content",this.text()),this.text(t)},t.fn.wc_reset_content=function(){void 0!==this.attr("data-o_content")&&this.text(this.attr("data-o_content"))},t.fn.wc_set_variation_attr=function(t,a){void 0===this.attr("data-o_"+t)&&this.attr("data-o_"+t,this.attr(t)?this.attr(t):""),!1===a?this.removeAttr(t):this.attr(t,a)},t.fn.wc_reset_variation_attr=function(t){void 0!==this.attr("data-o_"+t)&&this.attr(t,this.attr("data-o_"+t))},t.fn.wc_maybe_trigger_slide_position_reset=function(a){var i=t(this),r=i.closest(".product").find(".images"),e=!1,o=a&&a.image_id?a.image_id:"";i.attr("current-image")!==o&&(e=!0),i.attr("current-image",o),e&&r.trigger("woocommerce_gallery_reset_slide_position")},t.fn.wc_variations_image_update=function(i){var r=this,e=r.closest(".product"),o=e.find(".images"),n=e.find(".flex-control-nav li:eq(0) img"),s=o.find(".woocommerce-product-gallery__image, .woocommerce-product-gallery__image--placeholder").eq(0),_=s.find(".wp-post-image"),c=s.find("a").eq(0);if(i&&i.image&&i.image.src&&i.image.src.length>1){if(t('.flex-control-nav li img[src="'+i.image.thumb_src+'"]').length>0)return t('.flex-control-nav li img[src="'+i.image.thumb_src+'"]').trigger("click"),void r.attr("current-image",i.image_id);_.wc_set_variation_attr("src",i.image.src),_.wc_set_variation_attr("height",i.image.src_h),_.wc_set_variation_attr("width",i.image.src_w),_.wc_set_variation_attr("srcset",i.image.srcset),_.wc_set_variation_attr("sizes",i.image.sizes),_.wc_set_variation_attr("title",i.image.title),_.wc_set_variation_attr("alt",i.image.alt),_.wc_set_variation_attr("data-src",i.image.full_src),_.wc_set_variation_attr("data-large_image",i.image.full_src),_.wc_set_variation_attr("data-large_image_width",i.image.full_src_w),_.wc_set_variation_attr("data-large_image_height",i.image.full_src_h),s.wc_set_variation_attr("data-thumb",i.image.src),n.wc_set_variation_attr("src",i.image.thumb_src),c.wc_set_variation_attr("href",i.image.full_src)}else _.wc_reset_variation_attr("src"),_.wc_reset_variation_attr("width"),_.wc_reset_variation_attr("height"),_.wc_reset_variation_attr("srcset"),_.wc_reset_variation_attr("sizes"),_.wc_reset_variation_attr("title"),_.wc_reset_variation_attr("alt"),_.wc_reset_variation_attr("data-src"),_.wc_reset_variation_attr("data-large_image"),_.wc_reset_variation_attr("data-large_image_width"),_.wc_reset_variation_attr("data-large_image_height"),s.wc_reset_variation_attr("data-thumb"),n.wc_reset_variation_attr("src"),c.wc_reset_variation_attr("href");a.setTimeout(function(){t(a).trigger("resize"),r.wc_maybe_trigger_slide_position_reset(i),o.trigger("woocommerce_gallery_init_zoom")},20)},t(function(){"undefined"!=typeof wc_add_to_cart_variation_params&&t(".variations_form").each(function(){t(this).wc_variation_form()})})}(jQuery,window,document);
$('.detail-gallery').each(function(){
$(this).find(".carousel").jCarouselLite({
btnNext: $(this).find(".gallery-control .next"),
btnPrev: $(this).find(".gallery-control .prev"),
speed: 800,
visible:3,
});
$(this).find('.mid img').elevateZoom({
zoomType: "inner",
cursor: "crosshair",
zoomWindowFadeIn: 500,
zoomWindowFadeOut: 750
});
$(this).find(".carousel a").on('click',function(event){
event.preventDefault();
$(this).parents('.detail-gallery').find(".carousel a").removeClass('active');
$(this).addClass('active');
$(this).parents('.detail-gallery').find(".mid img").attr("src", $(this).find('img').attr("src"));
$(this).parents('.detail-gallery').find(".mid img").attr("alt", $(this).find('img').attr("alt"));
$(this).parents('.detail-gallery').find(".mid img").attr("title", $(this).find('img').attr("title"));
$(this).parents('.detail-gallery').find(".mid img").attr("srcset", $(this).find('img').attr("srcset"));
var z_url=$(this).parents('.detail-gallery').find('.mid img').attr('src');
$('.zoomWindow').css('background-image','url("'+z_url+'")');
$.removeData($('.detail-gallery .mid img'), 'elevateZoom');
$('.zoomContainer').remove();
$('.detail-gallery .mid img').elevateZoom({
zoomType: "inner",
cursor: "crosshair",
zoomWindowFadeIn: 500,
zoomWindowFadeOut: 750
});
});
});
$('body input[name="variation_id"]').on('change',function(){
var id=$(this).val();
var data=$('.variations_form').attr('data-product_variations');
var curent_data={};
data=$.parseJSON(data);
if(id){
for (var i=data.length - 1; i >=0; i--){
if(data[i].variation_id==id) curent_data=data[i];
};
if('image_id' in curent_data){
$('.detail-gallery .gallery-control').find('li[data-image_id="'+curent_data.image_id+'"] a').trigger('click');
}
if($('.product-supper11').length > 0){
var slider_owl=$(this).parents('.product-supper11').find('.product-detail11 .wrap-item');
var index=slider_owl.find('.item[data-variation_id="'+id+'"]').attr('data-index');
slider_owl.trigger('owl.goTo', index);
}
if($('.trend-box18').length > 0){
$(this).parents('.item-detail18').find('.trend-thumb18').find('img').removeClass('active');
$(this).parents('.item-detail18').find('.trend-thumb18').find('div[data-variation_id="'+id+'"]').find('img').addClass('active');
}
$('.total-current').attr('data-re_price',curent_data.display_price);
$('.total-current').attr('data-price',curent_data.display_regular_price);
$('.total-current').html(curent_data.display_price);
$('.addcart-special').removeClass("disabled");
}
else $('.addcart-special').addClass("disabled");
})
$('body .variations_form select').live('change',function(){
var text=$(this).val();
$(this).parents('.attr-product').find('.current-color').html(text);
})
if($('.wrap-attr-product.special').length > 0){
$('.attr-filter ul li a').live('click',function(){
event.preventDefault();
var text=$(this).html();
$(this).parents('.attr-product').find('.current-color').html(text);
$(this).parents('ul').find('li').removeClass('active');
$(this).parents('ul').find('li a').removeClass('active');
$(this).parent().addClass('active');
$(this).addClass('active');
var attribute=$(this).parent().attr('data-attribute');
var id=$(this).parents('ul').attr('data-attribute-id');
$('#'+id).val(attribute);
$('#'+id).trigger('change');
$('#'+id).trigger('focusin');
return false;
})
$('.attr-hover-box').hover(function(){
var seff=$(this);
var old_html=$(this).find('ul').html();
var current_val=$(this).find('ul li.active').attr('data-attribute');
$(this).next().find('select').trigger('focusin');
var content='';
$(this).next().find('select').find('option').each(function(){
var val=$(this).attr('value');
var title=$(this).html();
var el_class='';
var in_class='';
if(current_val==val){
el_class=' class="active"';
in_class='active';
}
if(val!=''){
content +='<li'+el_class+' data-attribute="'+val+'"><a href="#" class="bgcolor-'+val+' '+in_class+'"><span></span>'+title+'</a></li>';
}})
if(old_html!=content) $(this).find('ul').html(content);
})
$('body .reset_variations').live('click',function(){
$('.attr-hover-box').each(function(){
var seff=$(this);
var old_html=$(this).find('ul').html();
var current_val=$(this).find('ul li.active').attr('data-attribute');
$(this).next().find('select').trigger('focusin');
var content='';
$(this).next().find('select').find('option').each(function(){
var val=$(this).attr('value');
var title=$(this).html();
var el_class='';
var in_class='';
if(current_val==val){
el_class=' class="active"';
in_class='active';
}
if(val!=''){
content +='<li'+el_class+' data-attribute="'+val+'"><a href="#" class="bgcolor-'+val+' '+in_class+'"><span></span>'+title+'</a></li>';
}})
if(old_html!=content) $(this).find('ul').html(content);
$(this).find('ul li').removeClass('active');
})
})
}
$("body").on("click",".quantity .qty-up",function(){
var min=$(this).prev().attr("min");
var max=$(this).prev().attr("max");
var step=$(this).prev().attr("step");
if(step===undefined) step=1;
if(max!==undefined&&Number($(this).prev().val())< Number(max)||max===undefined||max===''){
if(step!='') $(this).prev().val(Number($(this).prev().val())+Number(step));
}
$('div.woocommerce > form input[name="update_cart"]').prop('disabled', false);
return false;
})
$("body").on("click",".quantity .qty-down",function(){
var min=$(this).next().attr("min");
var max=$(this).next().attr("max");
var step=$(this).next().attr("step");
if(step===undefined) step=1;
if(Number($(this).next().val()) > 1){
if(min!==undefined&&$(this).next().val()>min||min===undefined||min===''){
if(step!='') $(this).next().val(Number($(this).next().val())-Number(step));
}}
$('div.woocommerce > form input[name="update_cart"]').prop('disabled', false);
return false;
})
$("body").on("keyup change","input.qty-val",function(){
$('div.woocommerce > form input[name="update_cart"]').prop('disabled', false);
})
},
error: function(MLHttpRequest, textStatus, errorThrown){
console.log(errorThrown);
}});
return false;
})
$("body").on("click",".ajax-loadmore-show .load-ajax-btn",function(e){
e.preventDefault();
var page=$(this).attr("data-page");
var max_page=$(this).attr("data-max_page");
var load_data=$(this).attr("data-load_data");
var seff=$(this);
var content=seff.parents('.content-load-wrap').find('.content-load-ajax');
seff.find('i').addClass('fa-spin');
$.ajax({
type:"post",
url:ajax_process.ajaxurl,
crossDomain: true,
data: {
action: "loadmore_product",
load_data: load_data,
page: page,
},
success: function(data){
if(data[data.length-1]=='0'){
data=data.split('');
data[data.length-1]='';
data=data.join('');
}
console.log(data);
content.append(data);
seff.find('i').removeClass('fa-spin');
page=Number(page) +1;
seff.attr('data-page',page);
if(page >=Number(max_page)) seff.fadeOut();
},
error: function(MLHttpRequest, textStatus, errorThrown){
console.log(errorThrown);
}});
});
$('body').on('click', '.masonry-ajax', function(e){
e.preventDefault();
$(this).find('i').addClass('fa-spin');
var current=$(this).parents('.blog-wrap-masonry');
var data_load=$(this);
var content=current.find('.content-blog-masonry');
var number=data_load.attr('data-number');
var orderby=data_load.attr('data-orderby');
var order=data_load.attr('data-order');
var cats=data_load.attr('data-cat');
var paged=data_load.attr('data-paged');
var maxpage=data_load.attr('data-maxpage');
$.ajax({
type: 'POST',
url: ajax_process.ajaxurl,
crossDomain: true,
data: {
action: 'load_more_post_masonry',
number: number,
orderby: orderby,
order: order,
cats: cats,
paged: paged,
},
success: function(data){
if(data[data.length-1]=='0'){
data=data.split('');
data[data.length-1]='';
data=data.join('');
}
var $newItem=$(data);
content.append($newItem).masonry('appended', $newItem, true);
content.imagesLoaded(function(){
content.masonry('layout');
});
paged=Number(paged) + 1;
data_load.attr('data-paged',paged);
data_load.find('i').removeClass('fa-spin');
if(Number(paged)>=Number(maxpage)){
data_load.parent().fadeOut();
}},
error: function(MLHttpRequest, textStatus, errorThrown){
console.log(errorThrown);
}});
return false;
});
$('.coupon-light-box').on('click','.get-coupon-button',function(e){
e.preventDefault();
var seff=$(this);
var default_code=seff.attr('data-code');
seff.append('<i class="fa fa-spinner fa-spin"></i>');
$.ajax({
type:"post",
url:ajax_process.ajaxurl,
crossDomain: true,
data: {
action: "get_coupon",
default_code: default_code,
},
success: function(data){
if(data[data.length-1]=='0'){
data=data.split('');
data[data.length-1]='';
data=data.join('');
}
seff.find('.fa-spinner').remove();
seff.parent().append("<p>Your code: "+data+"</p>");
$('.btn-get-coupon').remove();
},
error: function(MLHttpRequest, textStatus, errorThrown){
console.log(errorThrown);
}});
})
$('body').on('click','.addcart-special.disabled', function(e){
return false;
})
$('body').on('click','.addcart-special:not(.disabled)', function(e){
$.fancybox.showLoading();
var product_id=$(this).attr('data-product-id');
var qty=$('.detail-qty input[name="quantity"]').val();
var price=$('.total-current').attr("data-price");
var re_price=$('.total-current').attr("data-re_price");
var variation_id=$('input[name="variation_id"]').val();
var variations='';
if($('.variations_form').length > 0){
variations='{';
var i=1;
$('.default-attribute select').each(function(){
if(i > 1) variations +=",";
variations +='"' + $(this).attr('name') + '":"' + $(this).val() + '"';
i++;
})
variations +='}';
}
variations=$.parseJSON(variations);
$.ajax({
type: 'POST',
url: ajax_process.ajaxurl,
crossDomain: true,
data: {
action: 'cart_popup_content',
product_id: product_id,
variation_id: variation_id,
variations: variations,
qty: qty,
price: price,
re_price: re_price,
},
success: function(res){
if(res[res.length-1]=='0'){
res=res.split('');
res[res.length-1]='';
res=res.join('');
}
$.fancybox.hideLoading();
$.fancybox(res, {
width: 1000,
height: 500,
autoSize: false,
closeBtn:false,
onStart: function(opener){
if($(opener).attr('id')=='login'){
$.get('/hicommon/authenticated', function(res){
if('yes'==res){
console.log('this user must have already authenticated in another browser tab, SO I want to avoid opening the fancybox.');
return false;
}else{
console.log('the user is not authenticated');
return true;
}});
}},
});
$('.close-light-box').on('click',function(event){
event.preventDefault();
$.fancybox.close();
})
var cart_content=$('.new-content-cart').html();
$('.new-content-cart').remove();
$('.mini-cart-main-content').html(cart_content);
$('.widget_shopping_cart_content').html(cart_content);
var count_item=cart_content.split("<li").length;
$('.cart-item-count').html(count_item-1);
var price_total=$('.content-mini-cart').find('.mini-cart-total').find('.total-price').html();
$('.total-mini-cart-price').html(price_total);
var nqty=$(res).find('.detail-qty input[name="quantity"]').val();
var ins_price=nqty*price;
var symbol=$(res).find('.total-cart-popup').attr('data-symbol');
ins_price='<span class="woocommerce-Price-currencySymbol">'+symbol+'</span>'+ins_price;
$(res).find('.product-price > span').html(ins_price);
$(res).find('div.product-price').html(ins_price);
$('.content-cart-light-box .product-remove .remove').on('click',function(){
var key=$('.box-addcart-special').attr('data-key');
$('.mini-cart-main-content .item-info-cart[data-key="'+key+'"] .btn-remove').trigger('click');
$(this).parents('.cart_item').remove();
$.fancybox.close();
})
},
error: function(MLHttpRequest, textStatus, errorThrown){
console.log(errorThrown);
}});
return false;
})
$('body').on('click keyup change','.box-addcart-special .qty-up,.box-addcart-special .qty-down,.box-addcart-special input[name="quantity"]', function(e){
console.log("run");
var key=$('.box-addcart-special').attr('data-key');
var variation_id=$('.box-addcart-special').attr('data-variation_id');
var qty=$('.box-addcart-special input[name="quantity"]').val();
var price=$('.box-addcart-special .pay-price').attr('data-price');
var re_price=$('.box-addcart-special .re-price').attr('data-re_price');
$('.box-addcart-special .re-price').html(re_price*qty);
$('.box-addcart-special .pay-price').html(price*qty);
var variations='';
if($('.variations_form').length > 0){
variations='{';
var i=1;
$('.default-attribute select').each(function(){
if(i > 1) variations +=",";
variations +='"' + $(this).attr('name') + '":"' + $(this).val() + '"';
i++;
})
variations +='}';
}
variations=$.parseJSON(variations);
$.ajax({
type: 'POST',
url: ajax_process.ajaxurl,
crossDomain: true,
data: {
action: 'update_cart_popup',
key: key,
qty: qty,
variation_id: variation_id,
variations: variations,
},
success: function(res){
if(res[res.length-1]=='0'){
res=res.split('');
res[res.length-1]='';
res=res.join('');
}
var cart_content=res;
$('.mini-cart-main-content').html(cart_content);
$('.widget_shopping_cart_content').html(cart_content);
var count_item=cart_content.split("<li").length;
$('.cart-item-count').html(count_item-1);
var price_total=$('.content-mini-cart').find('.mini-cart-total').find('.total-price').html();
$('.total-mini-cart-price').html(price_total);
$('.box-addcart-special .total-cart-popup').html(price_total);
var items=$('.new-cart-item').html();
$('.new-cart-item').remove();
$('.total-item-in-cart').html(items);
},
error: function(MLHttpRequest, textStatus, errorThrown){
console.log(errorThrown);
}});
return false;
})
});
})(jQuery);
var TVE_Dash=TVE_Dash||{},ThriveGlobal=ThriveGlobal||{$j:jQuery.noConflict()};!function(a){TVE_Dash.ajax_sent=!1;var b={},c={};TVE_Dash.add_load_item=function(d,e,f){if("function"!=typeof f&&(f=a.noop),TVE_Dash.ajax_sent){var g={},h={};return g[d]=e,h[d]=f,this.send_ajax(g,h),!0}return e?(b[d]&&console.error&&console.error(d+" ajax action already defined"),b[d]=e,c[d]=f,!0):(console.error&&console.error("missing ajax data"),!1)},TVE_Dash.ajax_load_css=function(b){a.each(b,function(b,c){b+="-css",a("link#"+b).length||a('<link rel="stylesheet" id="'+b+'" type="text/css" href="'+c+'"/>').appendTo("head")})},TVE_Dash.ajax_load_js=function(b){var c=document.body;a.each(b,function(d,e){if(-1!==d.indexOf("_before"))return!0;var f=document.createElement("script");if(b[d+"_before"]){a('<script type="text/javascript">'+b[d+"_before"]+"</script>").after(c.lastChild)}d&&(f.id=d+"-script"),f.src=e,c.appendChild(f)})},TVE_Dash.send_ajax=function(b,c){a.ajax({url:tve_dash_front.ajaxurl,data:{action:"tve_dash_front_ajax",tve_dash_data:b},dataType:"json",type:"post"}).done(function(b){b&&a.isPlainObject(b)&&(b.__resources&&(b.__resources.css&&TVE_Dash.ajax_load_css(b.__resources.css),b.__resources.js&&TVE_Dash.ajax_load_js(b.__resources.js),delete b.__resources),a.each(b,function(a,b){if("function"!=typeof c[a])return!0;c[a].call(null,b)}))})},a(function(){setTimeout(function(){var d=new a.Event("tve-dash.load");return a(document).trigger(d),!a.isEmptyObject(b)&&(!tve_dash_front.is_crawler&&(TVE_Dash.send_ajax(b,c),void(TVE_Dash.ajax_sent=!0)))})})}(ThriveGlobal.$j);
!function(a,b){"use strict";function c(){if(!e){e=!0;var a,c,d,f,g=-1!==navigator.appVersion.indexOf("MSIE 10"),h=!!navigator.userAgent.match(/Trident.*rv:11\./),i=b.querySelectorAll("iframe.wp-embedded-content");for(c=0;c<i.length;c++){if(d=i[c],!d.getAttribute("data-secret"))f=Math.random().toString(36).substr(2,10),d.src+="#?secret="+f,d.setAttribute("data-secret",f);if(g||h)a=d.cloneNode(!0),a.removeAttribute("security"),d.parentNode.replaceChild(a,d)}}}var d=!1,e=!1;if(b.querySelector)if(a.addEventListener)d=!0;if(a.wp=a.wp||{},!a.wp.receiveEmbedMessage)if(a.wp.receiveEmbedMessage=function(c){var d=c.data;if(d.secret||d.message||d.value)if(!/[^a-zA-Z0-9]/.test(d.secret)){var e,f,g,h,i,j=b.querySelectorAll('iframe[data-secret="'+d.secret+'"]'),k=b.querySelectorAll('blockquote[data-secret="'+d.secret+'"]');for(e=0;e<k.length;e++)k[e].style.display="none";for(e=0;e<j.length;e++)if(f=j[e],c.source===f.contentWindow){if(f.removeAttribute("style"),"height"===d.message){if(g=parseInt(d.value,10),g>1e3)g=1e3;else if(~~g<200)g=200;f.height=g}if("link"===d.message)if(h=b.createElement("a"),i=b.createElement("a"),h.href=f.getAttribute("src"),i.href=d.value,i.host===h.host)if(b.activeElement===f)a.top.location.href=d.value}else;}},d)a.addEventListener("message",a.wp.receiveEmbedMessage,!1),b.addEventListener("DOMContentLoaded",c,!1),a.addEventListener("load",c,!1)}(window,document);
function vc_js(){vc_toggleBehaviour(),vc_tabsBehaviour(),vc_accordionBehaviour(),vc_teaserGrid(),vc_carouselBehaviour(),vc_slidersBehaviour(),vc_prettyPhoto(),vc_googleplus(),vc_pinterest(),vc_progress_bar(),vc_plugin_flexslider(),vc_google_fonts(),vc_gridBehaviour(),vc_rowBehaviour(),vc_prepareHoverBox(),vc_googleMapsPointer(),vc_ttaActivation(),jQuery(document).trigger("vc_js"),window.setTimeout(vc_waypoints,500)}function getSizeName(){var screen_w=jQuery(window).width();return 1170<screen_w?"desktop_wide":960<screen_w&&1169>screen_w?"desktop":768<screen_w&&959>screen_w?"tablet":300<screen_w&&767>screen_w?"mobile":300>screen_w?"mobile_portrait":""}function loadScript(url,$obj,callback){var script=document.createElement("script");script.type="text/javascript",script.readyState&&(script.onreadystatechange=function(){"loaded"!==script.readyState&&"complete"!==script.readyState||(script.onreadystatechange=null,callback())}),script.src=url,$obj.get(0).appendChild(script)}function vc_ttaActivation(){jQuery("[data-vc-accordion]").on("show.vc.accordion",function(e){var $=window.jQuery,ui={};ui.newPanel=$(this).data("vc.accordion").getTarget(),window.wpb_prepare_tab_content(e,ui)})}function vc_accordionActivate(event,ui){if(ui.newPanel.length&&ui.newHeader.length){var $pie_charts=ui.newPanel.find(".vc_pie_chart:not(.vc_ready)"),$round_charts=ui.newPanel.find(".vc_round-chart"),$line_charts=ui.newPanel.find(".vc_line-chart"),$carousel=ui.newPanel.find('[data-ride="vc_carousel"]');void 0!==jQuery.fn.isotope&&ui.newPanel.find(".isotope, .wpb_image_grid_ul").isotope("layout"),ui.newPanel.find(".vc_masonry_media_grid, .vc_masonry_grid").length&&ui.newPanel.find(".vc_masonry_media_grid, .vc_masonry_grid").each(function(){var grid=jQuery(this).data("vcGrid");grid&&grid.gridBuilder&&grid.gridBuilder.setMasonry&&grid.gridBuilder.setMasonry()}),vc_carouselBehaviour(ui.newPanel),vc_plugin_flexslider(ui.newPanel),$pie_charts.length&&jQuery.fn.vcChat&&$pie_charts.vcChat(),$round_charts.length&&jQuery.fn.vcRoundChart&&$round_charts.vcRoundChart({reload:!1}),$line_charts.length&&jQuery.fn.vcLineChart&&$line_charts.vcLineChart({reload:!1}),$carousel.length&&jQuery.fn.carousel&&$carousel.carousel("resizeAction"),ui.newPanel.parents(".isotope").length&&ui.newPanel.parents(".isotope").each(function(){jQuery(this).isotope("layout")})}}function initVideoBackgrounds(){return window.console&&window.console.warn&&window.console.warn("this function is deprecated use vc_initVideoBackgrounds"),vc_initVideoBackgrounds()}function vc_initVideoBackgrounds(){jQuery("[data-vc-video-bg]").each(function(){var youtubeUrl,youtubeId,$element=jQuery(this);$element.data("vcVideoBg")?(youtubeUrl=$element.data("vcVideoBg"),youtubeId=vcExtractYoutubeId(youtubeUrl),youtubeId&&($element.find(".vc_video-bg").remove(),insertYoutubeVideoAsBackground($element,youtubeId)),jQuery(window).on("grid:items:added",function(event,$grid){$element.has($grid).length&&vcResizeVideoBackground($element)})):$element.find(".vc_video-bg").remove()})}function insertYoutubeVideoAsBackground($element,youtubeId,counter){if("undefined"==typeof YT||void 0===YT.Player)return 100<(counter=void 0===counter?0:counter)?void console.warn("Too many attempts to load YouTube api"):void setTimeout(function(){insertYoutubeVideoAsBackground($element,youtubeId,counter++)},100);var $container=$element.prepend('<div class="vc_video-bg vc_hidden-xs"><div class="inner"></div></div>').find(".inner");new YT.Player($container[0],{width:"100%",height:"100%",videoId:youtubeId,playerVars:{playlist:youtubeId,iv_load_policy:3,enablejsapi:1,disablekb:1,autoplay:1,controls:0,showinfo:0,rel:0,loop:1,wmode:"transparent"},events:{onReady:function(event){event.target.mute().setLoop(!0)}}}),vcResizeVideoBackground($element),jQuery(window).bind("resize",function(){vcResizeVideoBackground($element)})}function vcResizeVideoBackground($element){var iframeW,iframeH,marginLeft,marginTop,containerW=$element.innerWidth(),containerH=$element.innerHeight();containerW/containerH<16/9?(iframeW=containerH*(16/9),iframeH=containerH,marginLeft=-Math.round((iframeW-containerW)/2)+"px",marginTop=-Math.round((iframeH-containerH)/2)+"px",iframeW+="px",iframeH+="px"):(iframeW=containerW,iframeH=containerW*(9/16),marginTop=-Math.round((iframeH-containerH)/2)+"px",marginLeft=-Math.round((iframeW-containerW)/2)+"px",iframeW+="px",iframeH+="px"),$element.find(".vc_video-bg iframe").css({maxWidth:"1000%",marginLeft:marginLeft,marginTop:marginTop,width:iframeW,height:iframeH})}function vcExtractYoutubeId(url){if(void 0===url)return!1;var id=url.match(/(?:https?:\/{2})?(?:w{3}\.)?youtu(?:be)?\.(?:com|be)(?:\/watch\?v=|\/)([^\s&]+)/);return null!==id&&id[1]}function vc_googleMapsPointer(){var $=window.jQuery,$wpbGmapsWidget=$(".wpb_gmaps_widget");$wpbGmapsWidget.click(function(){$("iframe",this).css("pointer-events","auto")}),$wpbGmapsWidget.mouseleave(function(){$("iframe",this).css("pointer-events","none")}),$(".wpb_gmaps_widget iframe").css("pointer-events","none")}function vc_setHoverBoxPerspective(hoverBox){hoverBox.each(function(){var $this=jQuery(this),width=$this.width(),perspective=4*width+"px";$this.css("perspective",perspective)})}function vc_setHoverBoxHeight(hoverBox){hoverBox.each(function(){var $this=jQuery(this),hoverBoxInner=$this.find(".vc-hoverbox-inner");hoverBoxInner.css("min-height",0);var frontHeight=$this.find(".vc-hoverbox-front-inner").outerHeight(),backHeight=$this.find(".vc-hoverbox-back-inner").outerHeight(),hoverBoxHeight=frontHeight>backHeight?frontHeight:backHeight;hoverBoxHeight<250&&(hoverBoxHeight=250),hoverBoxInner.css("min-height",hoverBoxHeight+"px")})}function vc_prepareHoverBox(){var hoverBox=jQuery(".vc-hoverbox");vc_setHoverBoxHeight(hoverBox),vc_setHoverBoxPerspective(hoverBox)}document.documentElement.className+=" js_active ",document.documentElement.className+="ontouchstart"in document.documentElement?" vc_mobile ":" vc_desktop ",function(){for(var prefix=["-webkit-","-moz-","-ms-","-o-",""],i=0;i<prefix.length;i++)prefix[i]+"transform"in document.documentElement.style&&(document.documentElement.className+=" vc_transform ")}(),"function"!=typeof window.vc_plugin_flexslider&&(window.vc_plugin_flexslider=function($parent){($parent?$parent.find(".wpb_flexslider"):jQuery(".wpb_flexslider")).each(function(){var this_element=jQuery(this),sliderTimeout=1e3*parseInt(this_element.attr("data-interval")),sliderFx=this_element.attr("data-flex_fx"),slideshow=!0;0===sliderTimeout&&(slideshow=!1),this_element.is(":visible")&&this_element.flexslider({animation:sliderFx,slideshow:slideshow,slideshowSpeed:sliderTimeout,sliderSpeed:800,smoothHeight:!0})})}),"function"!=typeof window.vc_googleplus&&(window.vc_googleplus=function(){0<jQuery(".wpb_googleplus").length&&function(){var po=document.createElement("script");po.type="text/javascript",po.async=!0,po.src="//apis.google.com/js/plusone.js";var s=document.getElementsByTagName("script")[0];s.parentNode.insertBefore(po,s)}()}),"function"!=typeof window.vc_pinterest&&(window.vc_pinterest=function(){0<jQuery(".wpb_pinterest").length&&function(){var po=document.createElement("script");po.type="text/javascript",po.async=!0,po.src="//assets.pinterest.com/js/pinit.js";var s=document.getElementsByTagName("script")[0];s.parentNode.insertBefore(po,s)}()}),"function"!=typeof window.vc_progress_bar&&(window.vc_progress_bar=function(){void 0!==jQuery.fn.waypoint&&jQuery(".vc_progress_bar").waypoint(function(){jQuery(this).find(".vc_single_bar").each(function(index){var $this=jQuery(this),bar=$this.find(".vc_bar"),val=bar.data("percentage-value");setTimeout(function(){bar.css({width:val+"%"})},200*index)})},{offset:"85%"})}),"function"!=typeof window.vc_waypoints&&(window.vc_waypoints=function(){void 0!==jQuery.fn.waypoint&&jQuery(".wpb_animate_when_almost_visible:not(.wpb_start_animation)").waypoint(function(){jQuery(this).addClass("wpb_start_animation animated")},{offset:"85%"})}),"function"!=typeof window.vc_toggleBehaviour&&(window.vc_toggleBehaviour=function($el){function event(e){e&&e.preventDefault&&e.preventDefault();var title=jQuery(this),element=title.closest(".vc_toggle"),content=element.find(".vc_toggle_content");element.hasClass("vc_toggle_active")?content.slideUp({duration:300,complete:function(){element.removeClass("vc_toggle_active")}}):content.slideDown({duration:300,complete:function(){element.addClass("vc_toggle_active")}})}$el?$el.hasClass("vc_toggle_title")?$el.unbind("click").click(event):$el.find(".vc_toggle_title").unbind("click").click(event):jQuery(".vc_toggle_title").unbind("click").on("click",event)}),"function"!=typeof window.vc_tabsBehaviour&&(window.vc_tabsBehaviour=function($tab){if(jQuery.ui){var $call=$tab||jQuery(".wpb_tabs, .wpb_tour"),ver=jQuery.ui&&jQuery.ui.version?jQuery.ui.version.split("."):"1.10",old_version=1===parseInt(ver[0])&&9>parseInt(ver[1]);$call.each(function(index){var $tabs,interval=jQuery(this).attr("data-interval"),tabs_array=[];if($tabs=jQuery(this).find(".wpb_tour_tabs_wrapper").tabs({show:function(event,ui){wpb_prepare_tab_content(event,ui)},beforeActivate:function(event,ui){1!==ui.newPanel.index()&&ui.newPanel.find(".vc_pie_chart:not(.vc_ready)")},activate:function(event,ui){wpb_prepare_tab_content(event,ui)}}),interval&&0<interval)try{$tabs.tabs("rotate",1e3*interval)}catch(e){window.console&&window.console.log&&console.log(e)}jQuery(this).find(".wpb_tab").each(function(){tabs_array.push(this.id)}),jQuery(this).find(".wpb_tabs_nav li").click(function(e){return e.preventDefault(),old_version?$tabs.tabs("select",jQuery("a",this).attr("href")):$tabs.tabs("option","active",jQuery(this).index()),!1}),jQuery(this).find(".wpb_prev_slide a, .wpb_next_slide a").click(function(e){if(e.preventDefault(),old_version){var index=$tabs.tabs("option","selected");jQuery(this).parent().hasClass("wpb_next_slide")?index++:index--,0>index?index=$tabs.tabs("length")-1:index>=$tabs.tabs("length")&&(index=0),$tabs.tabs("select",index)}else{var index=$tabs.tabs("option","active"),length=$tabs.find(".wpb_tab").length;index=jQuery(this).parent().hasClass("wpb_next_slide")?index+1>=length?0:index+1:0>index-1?length-1:index-1,$tabs.tabs("option","active",index)}})})}}),"function"!=typeof window.vc_accordionBehaviour&&(window.vc_accordionBehaviour=function(){jQuery(".wpb_accordion").each(function(index){var $tabs,$this=jQuery(this),active_tab=($this.attr("data-interval"),!isNaN(jQuery(this).data("active-tab"))&&0<parseInt($this.data("active-tab"))&&parseInt($this.data("active-tab"))-1),collapsible=!1===active_tab||"yes"===$this.data("collapsible");$tabs=$this.find(".wpb_accordion_wrapper").accordion({header:"> div > h3",autoHeight:!1,heightStyle:"content",active:active_tab,collapsible:collapsible,navigation:!0,activate:vc_accordionActivate,change:function(event,ui){void 0!==jQuery.fn.isotope&&ui.newContent.find(".isotope").isotope("layout"),vc_carouselBehaviour(ui.newPanel)}}),!0===$this.data("vcDisableKeydown")&&($tabs.data("uiAccordion")._keydown=function(){})})}),"function"!=typeof window.vc_teaserGrid&&(window.vc_teaserGrid=function(){var layout_modes={fitrows:"fitRows",masonry:"masonry"};jQuery(".wpb_grid .teaser_grid_container:not(.wpb_carousel), .wpb_filtered_grid .teaser_grid_container:not(.wpb_carousel)").each(function(){var $container=jQuery(this),$thumbs=$container.find(".wpb_thumbnails"),layout_mode=$thumbs.attr("data-layout-mode");$thumbs.isotope({itemSelector:".isotope-item",layoutMode:void 0===layout_modes[layout_mode]?"fitRows":layout_modes[layout_mode]}),$container.find(".categories_filter a").data("isotope",$thumbs).click(function(e){e.preventDefault();var $thumbs=jQuery(this).data("isotope");jQuery(this).parent().parent().find(".active").removeClass("active"),jQuery(this).parent().addClass("active"),$thumbs.isotope({filter:jQuery(this).attr("data-filter")})}),jQuery(window).bind("load resize",function(){$thumbs.isotope("layout")})})}),"function"!=typeof window.vc_carouselBehaviour&&(window.vc_carouselBehaviour=function($parent){($parent?$parent.find(".wpb_carousel"):jQuery(".wpb_carousel")).each(function(){var $this=jQuery(this);if(!0!==$this.data("carousel_enabled")&&$this.is(":visible")){$this.data("carousel_enabled",!0),getColumnsCount(jQuery(this)),jQuery(this).hasClass("columns_count_1");var carousele_li=jQuery(this).find(".wpb_thumbnails-fluid li");carousele_li.css({"margin-right":carousele_li.css("margin-left"),"margin-left":0});var fluid_ul=jQuery(this).find("ul.wpb_thumbnails-fluid");fluid_ul.width(fluid_ul.width()+300),jQuery(window).resize(function(){var before_resize=screen_size;screen_size=getSizeName(),before_resize!=screen_size&&window.setTimeout("location.reload()",20)})}})}),"function"!=typeof window.vc_slidersBehaviour&&(window.vc_slidersBehaviour=function(){jQuery(".wpb_gallery_slides").each(function(index){var $imagesGrid,this_element=jQuery(this);if(this_element.hasClass("wpb_slider_nivo")){var sliderTimeout=1e3*this_element.attr("data-interval");0===sliderTimeout&&(sliderTimeout=9999999999),this_element.find(".nivoSlider").nivoSlider({effect:"boxRainGrow,boxRain,boxRainReverse,boxRainGrowReverse",slices:15,boxCols:8,boxRows:4,animSpeed:800,pauseTime:sliderTimeout,startSlide:0,directionNav:!0,directionNavHide:!0,controlNav:!0,keyboardNav:!1,pauseOnHover:!0,manualAdvance:!1,prevText:"Prev",nextText:"Next"})}else this_element.hasClass("wpb_image_grid")&&(jQuery.fn.imagesLoaded?$imagesGrid=this_element.find(".wpb_image_grid_ul").imagesLoaded(function(){$imagesGrid.isotope({itemSelector:".isotope-item",layoutMode:"fitRows"})}):this_element.find(".wpb_image_grid_ul").isotope({itemSelector:".isotope-item",layoutMode:"fitRows"}))})}),"function"!=typeof window.vc_prettyPhoto&&(window.vc_prettyPhoto=function(){try{jQuery&&jQuery.fn&&jQuery.fn.prettyPhoto&&jQuery('a.prettyphoto, .gallery-icon a[href*=".jpg"]').prettyPhoto({animationSpeed:"normal",hook:"data-rel",padding:15,opacity:.7,showTitle:!0,allowresize:!0,counter_separator_label:"/",hideflash:!1,deeplinking:!1,modal:!1,callback:function(){location.href.indexOf("#!prettyPhoto")>-1&&(location.hash="")},social_tools:""})}catch(err){window.console&&window.console.log&&console.log(err)}}),"function"!=typeof window.vc_google_fonts&&(window.vc_google_fonts=function(){return!1}),window.vcParallaxSkroll=!1,"function"!=typeof window.vc_rowBehaviour&&(window.vc_rowBehaviour=function(){function fullWidthRow(){var $elements=$('[data-vc-full-width="true"]');$.each($elements,function(key,item){var $el=$(this);$el.addClass("vc_hidden");var $el_full=$el.next(".vc_row-full-width");if($el_full.length||($el_full=$el.parent().next(".vc_row-full-width")),$el_full.length){var el_margin_left=parseInt($el.css("margin-left"),10),el_margin_right=parseInt($el.css("margin-right"),10),offset=0-$el_full.offset().left-el_margin_left,width=$(window).width();if($el.css({position:"relative",left:offset,"box-sizing":"border-box",width:$(window).width()}),!$el.data("vcStretchContent")){var padding=-1*offset;0>padding&&(padding=0);var paddingRight=width-padding-$el_full.width()+el_margin_left+el_margin_right;0>paddingRight&&(paddingRight=0),$el.css({"padding-left":padding+"px","padding-right":paddingRight+"px"})}$el.attr("data-vc-full-width-init","true"),$el.removeClass("vc_hidden"),$(document).trigger("vc-full-width-row-single",{el:$el,offset:offset,marginLeft:el_margin_left,marginRight:el_margin_right,elFull:$el_full,width:width})}}),$(document).trigger("vc-full-width-row",$elements)}function fullHeightRow(){var $element=$(".vc_row-o-full-height:first");if($element.length){var $window,windowHeight,offsetTop,fullHeight;$window=$(window),windowHeight=$window.height(),offsetTop=$element.offset().top,offsetTop<windowHeight&&(fullHeight=100-offsetTop/(windowHeight/100),$element.css("min-height",fullHeight+"vh"))}$(document).trigger("vc-full-height-row",$element)}var $=window.jQuery;$(window).off("resize.vcRowBehaviour").on("resize.vcRowBehaviour",fullWidthRow).on("resize.vcRowBehaviour",fullHeightRow),fullWidthRow(),fullHeightRow(),function(){(window.navigator.userAgent.indexOf("MSIE ")>0||navigator.userAgent.match(/Trident.*rv\:11\./))&&$(".vc_row-o-full-height").each(function(){"flex"===$(this).css("display")&&$(this).wrap('<div class="vc_ie-flexbox-fixer"></div>')})}(),vc_initVideoBackgrounds(),function(){var vcSkrollrOptions,callSkrollInit=!1;window.vcParallaxSkroll&&window.vcParallaxSkroll.destroy(),$(".vc_parallax-inner").remove(),$("[data-5p-top-bottom]").removeAttr("data-5p-top-bottom data-30p-top-bottom"),$("[data-vc-parallax]").each(function(){var skrollrSpeed,skrollrSize,skrollrStart,skrollrEnd,$parallaxElement,parallaxImage,youtubeId;callSkrollInit=!0,"on"===$(this).data("vcParallaxOFade")&&$(this).children().attr("data-5p-top-bottom","opacity:0;").attr("data-30p-top-bottom","opacity:1;"),skrollrSize=100*$(this).data("vcParallax"),$parallaxElement=$("<div />").addClass("vc_parallax-inner").appendTo($(this)),$parallaxElement.height(skrollrSize+"%"),parallaxImage=$(this).data("vcParallaxImage"),youtubeId=vcExtractYoutubeId(parallaxImage),youtubeId?insertYoutubeVideoAsBackground($parallaxElement,youtubeId):void 0!==parallaxImage&&$parallaxElement.css("background-image","url("+parallaxImage+")"),skrollrSpeed=skrollrSize-100,skrollrStart=-skrollrSpeed,skrollrEnd=0,$parallaxElement.attr("data-bottom-top","top: "+skrollrStart+"%;").attr("data-top-bottom","top: "+skrollrEnd+"%;")}),!(!callSkrollInit||!window.skrollr)&&(vcSkrollrOptions={forceHeight:!1,smoothScrolling:!1,mobileCheck:function(){return!1}},window.vcParallaxSkroll=skrollr.init(vcSkrollrOptions),window.vcParallaxSkroll)}()}),"function"!=typeof window.vc_gridBehaviour&&(window.vc_gridBehaviour=function(){jQuery.fn.vcGrid&&jQuery("[data-vc-grid]").vcGrid()}),"function"!=typeof window.getColumnsCount&&(window.getColumnsCount=function(el){for(var find=!1,i=1;!1===find;){if(el.hasClass("columns_count_"+i))return find=!0,i;i++}});var screen_size=getSizeName();"function"!=typeof window.wpb_prepare_tab_content&&(window.wpb_prepare_tab_content=function(event,ui){var $ui_panel,$google_maps,panel=ui.panel||ui.newPanel,$pie_charts=panel.find(".vc_pie_chart:not(.vc_ready)"),$round_charts=panel.find(".vc_round-chart"),$line_charts=panel.find(".vc_line-chart"),$carousel=panel.find('[data-ride="vc_carousel"]');if(vc_carouselBehaviour(),vc_plugin_flexslider(panel),ui.newPanel.find(".vc_masonry_media_grid, .vc_masonry_grid").length&&ui.newPanel.find(".vc_masonry_media_grid, .vc_masonry_grid").each(function(){var grid=jQuery(this).data("vcGrid");grid&&grid.gridBuilder&&grid.gridBuilder.setMasonry&&grid.gridBuilder.setMasonry()}),panel.find(".vc_masonry_media_grid, .vc_masonry_grid").length&&panel.find(".vc_masonry_media_grid, .vc_masonry_grid").each(function(){var grid=jQuery(this).data("vcGrid");grid&&grid.gridBuilder&&grid.gridBuilder.setMasonry&&grid.gridBuilder.setMasonry()}),$pie_charts.length&&jQuery.fn.vcChat&&$pie_charts.vcChat(),$round_charts.length&&jQuery.fn.vcRoundChart&&$round_charts.vcRoundChart({reload:!1}),$line_charts.length&&jQuery.fn.vcLineChart&&$line_charts.vcLineChart({reload:!1}),$carousel.length&&jQuery.fn.carousel&&$carousel.carousel("resizeAction"),$ui_panel=panel.find(".isotope, .wpb_image_grid_ul"),$google_maps=panel.find(".wpb_gmaps_widget"),0<$ui_panel.length&&$ui_panel.isotope("layout"),$google_maps.length&&!$google_maps.is(".map_ready")){var $frame=$google_maps.find("iframe");$frame.attr("src",$frame.attr("src")),$google_maps.addClass("map_ready")}panel.parents(".isotope").length&&panel.parents(".isotope").each(function(){jQuery(this).isotope("layout")})}),window.vc_googleMapsPointer,jQuery(document).ready(vc_prepareHoverBox),jQuery(window).resize(vc_prepareHoverBox),jQuery(document).ready(function($){window.vc_js()});
!function(a,b,c){"use strict";function d(c){if(e=b.documentElement,f=b.body,T(),ha=this,c=c||{},ma=c.constants||{},c.easing)for(var d in c.easing)W[d]=c.easing[d];ta=c.edgeStrategy||"set",ka={beforerender:c.beforerender,render:c.render,keyframe:c.keyframe},la=c.forceHeight!==!1,la&&(Ka=c.scale||1),na=c.mobileDeceleration||y,pa=c.smoothScrolling!==!1,qa=c.smoothScrollingDuration||A,ra={targetTop:ha.getScrollTop()},Sa=(c.mobileCheck||function(){return/Android|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent||navigator.vendor||a.opera)})(),Sa?(ja=b.getElementById(c.skrollrBody||z),ja&&ga(),X(),Ea(e,[s,v],[t])):Ea(e,[s,u],[t]),ha.refresh(),wa(a,"resize orientationchange",function(){var a=e.clientWidth,b=e.clientHeight;(b!==Pa||a!==Oa)&&(Pa=b,Oa=a,Qa=!0)});var g=U();return function h(){$(),va=g(h)}(),ha}var e,f,g={get:function(){return ha},init:function(a){return ha||new d(a)},VERSION:"0.6.29"},h=Object.prototype.hasOwnProperty,i=a.Math,j=a.getComputedStyle,k="touchstart",l="touchmove",m="touchcancel",n="touchend",o="skrollable",p=o+"-before",q=o+"-between",r=o+"-after",s="skrollr",t="no-"+s,u=s+"-desktop",v=s+"-mobile",w="linear",x=1e3,y=.004,z="skrollr-body",A=200,B="start",C="end",D="center",E="bottom",F="___skrollable_id",G=/^(?:input|textarea|button|select)$/i,H=/^\s+|\s+$/g,I=/^data(?:-(_\w+))?(?:-?(-?\d*\.?\d+p?))?(?:-?(start|end|top|center|bottom))?(?:-?(top|center|bottom))?$/,J=/\s*(@?[\w\-\[\]]+)\s*:\s*(.+?)\s*(?:;|$)/gi,K=/^(@?[a-z\-]+)\[(\w+)\]$/,L=/-([a-z0-9_])/g,M=function(a,b){return b.toUpperCase()},N=/[\-+]?[\d]*\.?[\d]+/g,O=/\{\?\}/g,P=/rgba?\(\s*-?\d+\s*,\s*-?\d+\s*,\s*-?\d+/g,Q=/[a-z\-]+-gradient/g,R="",S="",T=function(){var a=/^(?:O|Moz|webkit|ms)|(?:-(?:o|moz|webkit|ms)-)/;if(j){var b=j(f,null);for(var c in b)if(R=c.match(a)||+c==c&&b[c].match(a))break;if(!R)return void(R=S="");R=R[0],"-"===R.slice(0,1)?(S=R,R={"-webkit-":"webkit","-moz-":"Moz","-ms-":"ms","-o-":"O"}[R]):S="-"+R.toLowerCase()+"-"}},U=function(){var b=a.requestAnimationFrame||a[R.toLowerCase()+"RequestAnimationFrame"],c=Ha();return(Sa||!b)&&(b=function(b){var d=Ha()-c,e=i.max(0,1e3/60-d);return a.setTimeout(function(){c=Ha(),b()},e)}),b},V=function(){var b=a.cancelAnimationFrame||a[R.toLowerCase()+"CancelAnimationFrame"];return(Sa||!b)&&(b=function(b){return a.clearTimeout(b)}),b},W={begin:function(){return 0},end:function(){return 1},linear:function(a){return a},quadratic:function(a){return a*a},cubic:function(a){return a*a*a},swing:function(a){return-i.cos(a*i.PI)/2+.5},sqrt:function(a){return i.sqrt(a)},outCubic:function(a){return i.pow(a-1,3)+1},bounce:function(a){var b;if(.5083>=a)b=3;else if(.8489>=a)b=9;else if(.96208>=a)b=27;else{if(!(.99981>=a))return 1;b=91}return 1-i.abs(3*i.cos(a*b*1.028)/b)}};d.prototype.refresh=function(a){var d,e,f=!1;for(a===c?(f=!0,ia=[],Ra=0,a=b.getElementsByTagName("*")):a.length===c&&(a=[a]),d=0,e=a.length;e>d;d++){var g=a[d],h=g,i=[],j=pa,k=ta,l=!1;if(f&&F in g&&delete g[F],g.attributes){for(var m=0,n=g.attributes.length;n>m;m++){var p=g.attributes[m];if("data-anchor-target"!==p.name)if("data-smooth-scrolling"!==p.name)if("data-edge-strategy"!==p.name)if("data-emit-events"!==p.name){var q=p.name.match(I);if(null!==q){var r={props:p.value,element:g,eventType:p.name.replace(L,M)};i.push(r);var s=q[1];s&&(r.constant=s.substr(1));var t=q[2];/p$/.test(t)?(r.isPercentage=!0,r.offset=(0|t.slice(0,-1))/100):r.offset=0|t;var u=q[3],v=q[4]||u;u&&u!==B&&u!==C?(r.mode="relative",r.anchors=[u,v]):(r.mode="absolute",u===C?r.isEnd=!0:r.isPercentage||(r.offset=r.offset*Ka))}}else l=!0;else k=p.value;else j="off"!==p.value;else if(h=b.querySelector(p.value),null===h)throw'Unable to find anchor target "'+p.value+'"'}if(i.length){var w,x,y;!f&&F in g?(y=g[F],w=ia[y].styleAttr,x=ia[y].classAttr):(y=g[F]=Ra++,w=g.style.cssText,x=Da(g)),ia[y]={element:g,styleAttr:w,classAttr:x,anchorTarget:h,keyFrames:i,smoothScrolling:j,edgeStrategy:k,emitEvents:l,lastFrameIndex:-1},Ea(g,[o],[])}}}for(Aa(),d=0,e=a.length;e>d;d++){var z=ia[a[d][F]];z!==c&&(_(z),ba(z))}return ha},d.prototype.relativeToAbsolute=function(a,b,c){var d=e.clientHeight,f=a.getBoundingClientRect(),g=f.top,h=f.bottom-f.top;return b===E?g-=d:b===D&&(g-=d/2),c===E?g+=h:c===D&&(g+=h/2),g+=ha.getScrollTop(),g+.5|0},d.prototype.animateTo=function(a,b){b=b||{};var d=Ha(),e=ha.getScrollTop(),f=b.duration===c?x:b.duration;return oa={startTop:e,topDiff:a-e,targetTop:a,duration:f,startTime:d,endTime:d+f,easing:W[b.easing||w],done:b.done},oa.topDiff||(oa.done&&oa.done.call(ha,!1),oa=c),ha},d.prototype.stopAnimateTo=function(){oa&&oa.done&&oa.done.call(ha,!0),oa=c},d.prototype.isAnimatingTo=function(){return!!oa},d.prototype.isMobile=function(){return Sa},d.prototype.setScrollTop=function(b,c){return sa=c===!0,Sa?Ta=i.min(i.max(b,0),Ja):a.scrollTo(0,b),ha},d.prototype.getScrollTop=function(){return Sa?Ta:a.pageYOffset||e.scrollTop||f.scrollTop||0},d.prototype.getMaxScrollTop=function(){return Ja},d.prototype.on=function(a,b){return ka[a]=b,ha},d.prototype.off=function(a){return delete ka[a],ha},d.prototype.destroy=function(){var a=V();a(va),ya(),Ea(e,[t],[s,u,v]);for(var b=0,d=ia.length;d>b;b++)fa(ia[b].element);e.style.overflow=f.style.overflow="",e.style.height=f.style.height="",ja&&g.setStyle(ja,"transform","none"),ha=c,ja=c,ka=c,la=c,Ja=0,Ka=1,ma=c,na=c,La="down",Ma=-1,Oa=0,Pa=0,Qa=!1,oa=c,pa=c,qa=c,ra=c,sa=c,Ra=0,ta=c,Sa=!1,Ta=0,ua=c};var X=function(){var d,g,h,j,o,p,q,r,s,t,u,v;wa(e,[k,l,m,n].join(" "),function(a){var e=a.changedTouches[0];for(j=a.target;3===j.nodeType;)j=j.parentNode;switch(o=e.clientY,p=e.clientX,t=a.timeStamp,G.test(j.tagName)||a.preventDefault(),a.type){case k:d&&d.blur(),ha.stopAnimateTo(),d=j,g=q=o,h=p,s=t;break;case l:G.test(j.tagName)&&b.activeElement!==j&&a.preventDefault(),r=o-q,v=t-u,ha.setScrollTop(Ta-r,!0),q=o,u=t;break;default:case m:case n:var f=g-o,w=h-p,x=w*w+f*f;if(49>x){if(!G.test(d.tagName)){d.focus();var y=b.createEvent("MouseEvents");y.initMouseEvent("click",!0,!0,a.view,1,e.screenX,e.screenY,e.clientX,e.clientY,a.ctrlKey,a.altKey,a.shiftKey,a.metaKey,0,null),d.dispatchEvent(y)}return}d=c;var z=r/v;z=i.max(i.min(z,3),-3);var A=i.abs(z/na),B=z*A+.5*na*A*A,C=ha.getScrollTop()-B,D=0;C>Ja?(D=(Ja-C)/B,C=Ja):0>C&&(D=-C/B,C=0),A*=1-D,ha.animateTo(C+.5|0,{easing:"outCubic",duration:A})}}),a.scrollTo(0,0),e.style.overflow=f.style.overflow="hidden"},Y=function(){var a,b,c,d,f,g,h,j,k,l,m,n=e.clientHeight,o=Ba();for(j=0,k=ia.length;k>j;j++)for(a=ia[j],b=a.element,c=a.anchorTarget,d=a.keyFrames,f=0,g=d.length;g>f;f++)h=d[f],l=h.offset,m=o[h.constant]||0,h.frame=l,h.isPercentage&&(l*=n,h.frame=l),"relative"===h.mode&&(fa(b),h.frame=ha.relativeToAbsolute(c,h.anchors[0],h.anchors[1])-l,fa(b,!0)),h.frame+=m,la&&!h.isEnd&&h.frame>Ja&&(Ja=h.frame);for(Ja=i.max(Ja,Ca()),j=0,k=ia.length;k>j;j++){for(a=ia[j],d=a.keyFrames,f=0,g=d.length;g>f;f++)h=d[f],m=o[h.constant]||0,h.isEnd&&(h.frame=Ja-h.offset+m);a.keyFrames.sort(Ia)}},Z=function(a,b){for(var c=0,d=ia.length;d>c;c++){var e,f,i=ia[c],j=i.element,k=i.smoothScrolling?a:b,l=i.keyFrames,m=l.length,n=l[0],s=l[l.length-1],t=k<n.frame,u=k>s.frame,v=t?n:s,w=i.emitEvents,x=i.lastFrameIndex;if(t||u){if(t&&-1===i.edge||u&&1===i.edge)continue;switch(t?(Ea(j,[p],[r,q]),w&&x>-1&&(za(j,n.eventType,La),i.lastFrameIndex=-1)):(Ea(j,[r],[p,q]),w&&m>x&&(za(j,s.eventType,La),i.lastFrameIndex=m)),i.edge=t?-1:1,i.edgeStrategy){case"reset":fa(j);continue;case"ease":k=v.frame;break;default:case"set":var y=v.props;for(e in y)h.call(y,e)&&(f=ea(y[e].value),0===e.indexOf("@")?j.setAttribute(e.substr(1),f):g.setStyle(j,e,f));continue}}else 0!==i.edge&&(Ea(j,[o,q],[p,r]),i.edge=0);for(var z=0;m-1>z;z++)if(k>=l[z].frame&&k<=l[z+1].frame){var A=l[z],B=l[z+1];for(e in A.props)if(h.call(A.props,e)){var C=(k-A.frame)/(B.frame-A.frame);C=A.props[e].easing(C),f=da(A.props[e].value,B.props[e].value,C),f=ea(f),0===e.indexOf("@")?j.setAttribute(e.substr(1),f):g.setStyle(j,e,f)}w&&x!==z&&("down"===La?za(j,A.eventType,La):za(j,B.eventType,La),i.lastFrameIndex=z);break}}},$=function(){Qa&&(Qa=!1,Aa());var a,b,d=ha.getScrollTop(),e=Ha();if(oa)e>=oa.endTime?(d=oa.targetTop,a=oa.done,oa=c):(b=oa.easing((e-oa.startTime)/oa.duration),d=oa.startTop+b*oa.topDiff|0),ha.setScrollTop(d,!0);else if(!sa){var f=ra.targetTop-d;f&&(ra={startTop:Ma,topDiff:d-Ma,targetTop:d,startTime:Na,endTime:Na+qa}),e<=ra.endTime&&(b=W.sqrt((e-ra.startTime)/qa),d=ra.startTop+b*ra.topDiff|0)}if(sa||Ma!==d){La=d>Ma?"down":Ma>d?"up":La,sa=!1;var h={curTop:d,lastTop:Ma,maxTop:Ja,direction:La},i=ka.beforerender&&ka.beforerender.call(ha,h);i!==!1&&(Z(d,ha.getScrollTop()),Sa&&ja&&g.setStyle(ja,"transform","translate(0, "+-Ta+"px) "+ua),Ma=d,ka.render&&ka.render.call(ha,h)),a&&a.call(ha,!1)}Na=e},_=function(a){for(var b=0,c=a.keyFrames.length;c>b;b++){for(var d,e,f,g,h=a.keyFrames[b],i={};null!==(g=J.exec(h.props));)f=g[1],e=g[2],d=f.match(K),null!==d?(f=d[1],d=d[2]):d=w,e=e.indexOf("!")?aa(e):[e.slice(1)],i[f]={value:e,easing:W[d]};h.props=i}},aa=function(a){var b=[];return P.lastIndex=0,a=a.replace(P,function(a){return a.replace(N,function(a){return a/255*100+"%"})}),S&&(Q.lastIndex=0,a=a.replace(Q,function(a){return S+a})),a=a.replace(N,function(a){return b.push(+a),"{?}"}),b.unshift(a),b},ba=function(a){var b,c,d={};for(b=0,c=a.keyFrames.length;c>b;b++)ca(a.keyFrames[b],d);for(d={},b=a.keyFrames.length-1;b>=0;b--)ca(a.keyFrames[b],d)},ca=function(a,b){var c;for(c in b)h.call(a.props,c)||(a.props[c]=b[c]);for(c in a.props)b[c]=a.props[c]},da=function(a,b,c){var d,e=a.length;if(e!==b.length)throw"Can't interpolate between \""+a[0]+'" and "'+b[0]+'"';var f=[a[0]];for(d=1;e>d;d++)f[d]=a[d]+(b[d]-a[d])*c;return f},ea=function(a){var b=1;return O.lastIndex=0,a[0].replace(O,function(){return a[b++]})},fa=function(a,b){a=[].concat(a);for(var c,d,e=0,f=a.length;f>e;e++)d=a[e],c=ia[d[F]],c&&(b?(d.style.cssText=c.dirtyStyleAttr,Ea(d,c.dirtyClassAttr)):(c.dirtyStyleAttr=d.style.cssText,c.dirtyClassAttr=Da(d),d.style.cssText=c.styleAttr,Ea(d,c.classAttr)))},ga=function(){ua="translateZ(0)",g.setStyle(ja,"transform",ua);var a=j(ja),b=a.getPropertyValue("transform"),c=a.getPropertyValue(S+"transform"),d=b&&"none"!==b||c&&"none"!==c;d||(ua="")};g.setStyle=function(a,b,c){var d=a.style;if(b=b.replace(L,M).replace("-",""),"zIndex"===b)isNaN(c)?d[b]=c:d[b]=""+(0|c);else if("float"===b)d.styleFloat=d.cssFloat=c;else try{R&&(d[R+b.slice(0,1).toUpperCase()+b.slice(1)]=c),d[b]=c}catch(e){}};var ha,ia,ja,ka,la,ma,na,oa,pa,qa,ra,sa,ta,ua,va,wa=g.addEvent=function(b,c,d){var e=function(b){return b=b||a.event,b.target||(b.target=b.srcElement),b.preventDefault||(b.preventDefault=function(){b.returnValue=!1,b.defaultPrevented=!0}),d.call(this,b)};c=c.split(" ");for(var f,g=0,h=c.length;h>g;g++)f=c[g],b.addEventListener?b.addEventListener(f,d,!1):b.attachEvent("on"+f,e),Ua.push({element:b,name:f,listener:d})},xa=g.removeEvent=function(a,b,c){b=b.split(" ");for(var d=0,e=b.length;e>d;d++)a.removeEventListener?a.removeEventListener(b[d],c,!1):a.detachEvent("on"+b[d],c)},ya=function(){for(var a,b=0,c=Ua.length;c>b;b++)a=Ua[b],xa(a.element,a.name,a.listener);Ua=[]},za=function(a,b,c){ka.keyframe&&ka.keyframe.call(ha,a,b,c)},Aa=function(){var a=ha.getScrollTop();Ja=0,la&&!Sa&&(f.style.height=""),Y(),la&&!Sa&&(f.style.height=Ja+e.clientHeight+"px"),Sa?ha.setScrollTop(i.min(ha.getScrollTop(),Ja)):ha.setScrollTop(a,!0),sa=!0},Ba=function(){var a,b,c=e.clientHeight,d={};for(a in ma)b=ma[a],"function"==typeof b?b=b.call(ha):/p$/.test(b)&&(b=b.slice(0,-1)/100*c),d[a]=b;return d},Ca=function(){var a,b=0;return ja&&(b=i.max(ja.offsetHeight,ja.scrollHeight)),a=i.max(b,f.scrollHeight,f.offsetHeight,e.scrollHeight,e.offsetHeight,e.clientHeight),a-e.clientHeight},Da=function(b){var c="className";return a.SVGElement&&b instanceof a.SVGElement&&(b=b[c],c="baseVal"),b[c]},Ea=function(b,d,e){var f="className";if(a.SVGElement&&b instanceof a.SVGElement&&(b=b[f],f="baseVal"),e===c)return void(b[f]=d);for(var g=b[f],h=0,i=e.length;i>h;h++)g=Ga(g).replace(Ga(e[h])," ");g=Fa(g);for(var j=0,k=d.length;k>j;j++)-1===Ga(g).indexOf(Ga(d[j]))&&(g+=" "+d[j]);b[f]=Fa(g)},Fa=function(a){return a.replace(H,"")},Ga=function(a){return" "+a+" "},Ha=Date.now||function(){return+new Date},Ia=function(a,b){return a.frame-b.frame},Ja=0,Ka=1,La="down",Ma=-1,Na=Ha(),Oa=0,Pa=0,Qa=!1,Ra=0,Sa=!1,Ta=0,Ua=[];"function"==typeof define&&define.amd?define([],function(){return g}):"undefined"!=typeof module&&module.exports?module.exports=g:a.skrollr=g}(window,document);
!function($){function getHashtag(){var url=location.href;return hashtag=-1!==url.indexOf("#prettyPhoto")&&decodeURI(url.substring(url.indexOf("#prettyPhoto")+1,url.length)),hashtag&&(hashtag=hashtag.replace(/<|>/g,"")),hashtag}function setHashtag(){"undefined"!=typeof theRel&&(location.hash=theRel+"/"+rel_index+"/")}function clearHashtag(){-1!==location.href.indexOf("#prettyPhoto")&&(location.hash="prettyPhoto")}function getParam(name,url){name=name.replace(/[\[]/,"\\[").replace(/[\]]/,"\\]");var regexS="[\\?&]"+name+"=([^&#]*)",regex=new RegExp(regexS),results=regex.exec(url);return null==results?"":results[1]}$.prettyPhoto={version:"3.1.6"};var options=$.prettyPhoto.options={hook:"rel",animation_speed:"fast",ajaxcallback:function(){},slideshow:5e3,autoplay_slideshow:!1,opacity:.8,show_title:!0,allow_resize:!0,allow_expand:!0,default_width:500,default_height:344,counter_separator_label:"/",theme:"pp_default",horizontal_padding:20,hideflash:!1,wmode:"opaque",autoplay:!0,modal:!1,deeplinking:!0,overlay_gallery:!0,overlay_gallery_max:30,keyboard_shortcuts:!0,changepicturecallback:function(){},callback:function(){},ie6_fallback:!0,markup:'<div class="pp_pic_holder" {vc-data}> \t\t\t\t\t\t<div class="ppt">&nbsp;</div> \t\t\t\t\t\t<div class="pp_top"> \t\t\t\t\t\t\t<div class="pp_left"></div> \t\t\t\t\t\t\t<div class="pp_middle"></div> \t\t\t\t\t\t\t<div class="pp_right"></div> \t\t\t\t\t\t</div> \t\t\t\t\t\t<div class="pp_content_container"> \t\t\t\t\t\t\t<div class="pp_left"> \t\t\t\t\t\t\t<div class="pp_right"> \t\t\t\t\t\t\t\t<div class="pp_content"> \t\t\t\t\t\t\t\t\t<div class="pp_loaderIcon"></div> \t\t\t\t\t\t\t\t\t<div class="pp_fade"> \t\t\t\t\t\t\t\t\t\t<a href="#" class="pp_expand" title="Expand the image">Expand</a> \t\t\t\t\t\t\t\t\t\t<div class="pp_hoverContainer"> \t\t\t\t\t\t\t\t\t\t\t<a class="pp_next" href="#">next</a> \t\t\t\t\t\t\t\t\t\t\t<a class="pp_previous" href="#">previous</a> \t\t\t\t\t\t\t\t\t\t</div> \t\t\t\t\t\t\t\t\t\t<div id="pp_full_res"></div> \t\t\t\t\t\t\t\t\t\t<div class="pp_details"> \t\t\t\t\t\t\t\t\t\t\t<div class="pp_nav"> \t\t\t\t\t\t\t\t\t\t\t\t<a href="#" class="pp_arrow_previous">Previous</a> \t\t\t\t\t\t\t\t\t\t\t\t<p class="currentTextHolder">0/0</p> \t\t\t\t\t\t\t\t\t\t\t\t<a href="#" class="pp_arrow_next">Next</a> \t\t\t\t\t\t\t\t\t\t\t</div> \t\t\t\t\t\t\t\t\t\t\t<p class="pp_description"></p> \t\t\t\t\t\t\t\t\t\t\t<div class="pp_social">{pp_social}</div> \t\t\t\t\t\t\t\t\t\t\t<a class="pp_close" href="#">Close</a> \t\t\t\t\t\t\t\t\t\t</div> \t\t\t\t\t\t\t\t\t</div> \t\t\t\t\t\t\t\t</div> \t\t\t\t\t\t\t</div> \t\t\t\t\t\t\t</div> \t\t\t\t\t\t</div> \t\t\t\t\t\t<div class="pp_bottom"> \t\t\t\t\t\t\t<div class="pp_left"></div> \t\t\t\t\t\t\t<div class="pp_middle"></div> \t\t\t\t\t\t\t<div class="pp_right"></div> \t\t\t\t\t\t</div> \t\t\t\t\t</div> \t\t\t\t\t<div class="pp_overlay"></div>',gallery_markup:'<div class="pp_gallery"> \t\t\t\t\t\t\t\t<a href="#" class="pp_arrow_previous">Previous</a> \t\t\t\t\t\t\t\t<div> \t\t\t\t\t\t\t\t\t<ul> \t\t\t\t\t\t\t\t\t\t{gallery} \t\t\t\t\t\t\t\t\t</ul> \t\t\t\t\t\t\t\t</div> \t\t\t\t\t\t\t\t<a href="#" class="pp_arrow_next">Next</a> \t\t\t\t\t\t\t</div>',image_markup:'<img id="fullResImage" src="{path}" />',flash_markup:'<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="{width}" height="{height}"><param name="wmode" value="{wmode}" /><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="{path}" /><embed src="{path}" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="{width}" height="{height}" wmode="{wmode}"></embed></object>',quicktime_markup:'<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" codebase="http://www.apple.com/qtactivex/qtplugin.cab" height="{height}" width="{width}"><param name="src" value="{path}"><param name="autoplay" value="{autoplay}"><param name="type" value="video/quicktime"><embed src="{path}" height="{height}" width="{width}" autoplay="{autoplay}" type="video/quicktime" pluginspage="http://www.apple.com/quicktime/download/"></embed></object>',iframe_markup:'<iframe src="{path}" width="{width}" height="{height}" frameborder="no"></iframe>',inline_markup:'<div class="pp_inline">{content}</div>',custom_markup:"",social_tools:'<div class="twitter"><a href="http://twitter.com/share" class="twitter-share-button" data-count="none">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"><\/script></div><div class="facebook"><iframe src="//www.facebook.com/plugins/like.php?locale=en_US&href={location_href}&amp;layout=button_count&amp;show_faces=true&amp;width=500&amp;action=like&amp;font&amp;colorscheme=light&amp;height=23" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:500px; height:23px;" allowTransparency="true"></iframe></div>'};$.fn.prettyPhoto=function(pp_settings){function _showContent(){$(".pp_loaderIcon").hide(),projectedTop=scroll_pos.scrollTop+(windowHeight/2-pp_dimensions.containerHeight/2),projectedTop<0&&(projectedTop=0),$ppt.fadeTo(settings.animation_speed,1),$pp_pic_holder.find(".pp_content").animate({height:pp_dimensions.contentHeight,width:pp_dimensions.contentWidth},settings.animation_speed),$pp_pic_holder.animate({top:projectedTop,left:windowWidth/2-pp_dimensions.containerWidth/2<0?0:windowWidth/2-pp_dimensions.containerWidth/2,width:pp_dimensions.containerWidth},settings.animation_speed,function(){$pp_pic_holder.find(".pp_hoverContainer,#fullResImage").height(pp_dimensions.height).width(pp_dimensions.width),$pp_pic_holder.find(".pp_fade").fadeIn(settings.animation_speed),isSet&&"image"==_getFileType(pp_images[set_position])?$pp_pic_holder.find(".pp_hoverContainer").show():$pp_pic_holder.find(".pp_hoverContainer").hide(),settings.allow_expand&&(pp_dimensions.resized?$("a.pp_expand,a.pp_contract").show():$("a.pp_expand").hide()),!settings.autoplay_slideshow||pp_slideshow||pp_open||$.prettyPhoto.startSlideshow(),settings.changepicturecallback(),pp_open=!0}),_insert_gallery(),pp_settings.ajaxcallback()}function _hideContent(callback){$pp_pic_holder.find("#pp_full_res object,#pp_full_res embed").css("visibility","hidden"),$pp_pic_holder.find(".pp_fade").fadeOut(settings.animation_speed,function(){$(".pp_loaderIcon").show(),callback()})}function _checkPosition(setCount){setCount>1?$(".pp_nav").show():$(".pp_nav").hide()}function _fitToViewport(width,height){if(resized=!1,_getDimensions(width,height),imageWidth=width,imageHeight=height,(pp_containerWidth>windowWidth||pp_containerHeight>windowHeight)&&doresize&&settings.allow_resize&&!percentBased){for(resized=!0,fitting=!1;!fitting;)pp_containerWidth>windowWidth?(imageWidth=windowWidth-200,imageHeight=height/width*imageWidth):pp_containerHeight>windowHeight?(imageHeight=windowHeight-200,imageWidth=width/height*imageHeight):fitting=!0,pp_containerHeight=imageHeight,pp_containerWidth=imageWidth;(pp_containerWidth>windowWidth||pp_containerHeight>windowHeight)&&_fitToViewport(pp_containerWidth,pp_containerHeight),_getDimensions(imageWidth,imageHeight)}return{width:Math.floor(imageWidth),height:Math.floor(imageHeight),containerHeight:Math.floor(pp_containerHeight),containerWidth:Math.floor(pp_containerWidth)+2*settings.horizontal_padding,contentHeight:Math.floor(pp_contentHeight),contentWidth:Math.floor(pp_contentWidth),resized:resized}}function _getDimensions(width,height){width=parseFloat(width),height=parseFloat(height),$pp_details=$pp_pic_holder.find(".pp_details"),$pp_details.width(width),detailsHeight=parseFloat($pp_details.css("marginTop"))+parseFloat($pp_details.css("marginBottom")),$pp_details=$pp_details.clone().addClass(settings.theme).width(width).appendTo($("body")).css({position:"absolute",top:-1e4}),detailsHeight+=$pp_details.height(),detailsHeight=detailsHeight<=34?36:detailsHeight,$pp_details.remove(),$pp_title=$pp_pic_holder.find(".ppt"),$pp_title.width(width),titleHeight=parseFloat($pp_title.css("marginTop"))+parseFloat($pp_title.css("marginBottom")),$pp_title=$pp_title.clone().appendTo($("body")).css({position:"absolute",top:-1e4}),titleHeight+=$pp_title.height(),$pp_title.remove(),pp_contentHeight=height+detailsHeight,pp_contentWidth=width,pp_containerHeight=pp_contentHeight+titleHeight+$pp_pic_holder.find(".pp_top").height()+$pp_pic_holder.find(".pp_bottom").height(),pp_containerWidth=width}function _getFileType(itemSrc){return itemSrc.match(/youtube\.com\/watch/i)||itemSrc.match(/youtu\.be/i)?"youtube":itemSrc.match(/vimeo\.com/i)?"vimeo":itemSrc.match(/\b.mov\b/i)?"quicktime":itemSrc.match(/\b.swf\b/i)?"flash":itemSrc.match(/\biframe=true\b/i)?"iframe":itemSrc.match(/\bajax=true\b/i)?"ajax":itemSrc.match(/\bcustom=true\b/i)?"custom":"#"==itemSrc.substr(0,1)?"inline":"image"}function _center_overlay(){if(doresize&&"undefined"!=typeof $pp_pic_holder){if(scroll_pos=_get_scroll(),contentHeight=$pp_pic_holder.height(),contentwidth=$pp_pic_holder.width(),projectedTop=windowHeight/2+scroll_pos.scrollTop-contentHeight/2,projectedTop<0&&(projectedTop=0),contentHeight>windowHeight)return;$pp_pic_holder.css({top:projectedTop,left:windowWidth/2+scroll_pos.scrollLeft-contentwidth/2})}}function _get_scroll(){return self.pageYOffset?{scrollTop:self.pageYOffset,scrollLeft:self.pageXOffset}:document.documentElement&&document.documentElement.scrollTop?{scrollTop:document.documentElement.scrollTop,scrollLeft:document.documentElement.scrollLeft}:document.body?{scrollTop:document.body.scrollTop,scrollLeft:document.body.scrollLeft}:void 0}function _resize_overlay(){windowHeight=$(window).height(),windowWidth=$(window).width(),"undefined"!=typeof $pp_overlay&&$pp_overlay.height($(document).height()).width(windowWidth)}function _insert_gallery(){isSet&&settings.overlay_gallery&&"image"==_getFileType(pp_images[set_position])?(itemWidth=57,navWidth="facebook"==settings.theme||"pp_default"==settings.theme?50:30,itemsPerPage=Math.floor((pp_dimensions.containerWidth-100-navWidth)/itemWidth),itemsPerPage=itemsPerPage<pp_images.length?itemsPerPage:pp_images.length,totalPage=Math.ceil(pp_images.length/itemsPerPage)-1,0==totalPage?(navWidth=0,$pp_gallery.find(".pp_arrow_next,.pp_arrow_previous").hide()):$pp_gallery.find(".pp_arrow_next,.pp_arrow_previous").show(),galleryWidth=itemsPerPage*itemWidth,fullGalleryWidth=pp_images.length*itemWidth,$pp_gallery.css("margin-left",-(galleryWidth/2+navWidth/2)).find("div:first").width(galleryWidth+5).find("ul").width(fullGalleryWidth).find("li.selected").removeClass("selected"),goToPage=Math.floor(set_position/itemsPerPage)<totalPage?Math.floor(set_position/itemsPerPage):totalPage,$.prettyPhoto.changeGalleryPage(goToPage),$pp_gallery_li.filter(":eq("+set_position+")").addClass("selected")):$pp_pic_holder.find(".pp_content").unbind("mouseenter mouseleave")}function _build_overlay(caller){if(settings.social_tools&&(facebook_like_link=settings.social_tools.replace("{location_href}",encodeURIComponent(location.href))),settings.markup=settings.markup.replace("{pp_social}",""),$("body").append(settings.markup),$pp_pic_holder=$(".pp_pic_holder"),$ppt=$(".ppt"),$pp_overlay=$("div.pp_overlay"),$pp_pic_holder.toggleClass("is-single",pp_images.length<=1),isSet&&settings.overlay_gallery){currentGalleryPage=0,toInject="";for(var i=0;i<pp_images.length;i++)pp_images[i].match(/\b(jpg|jpeg|png|gif)\b/gi)?(classname="",img_src=pp_images[i]):(classname="default",img_src=""),toInject+="<li class='"+classname+"'><a href='#'><img src='"+img_src+"' width='50' alt='' /></a></li>";toInject=settings.gallery_markup.replace(/{gallery}/g,toInject),$pp_pic_holder.find("#pp_full_res").after(toInject),$pp_gallery=$(".pp_pic_holder .pp_gallery"),$pp_gallery_li=$pp_gallery.find("li"),$pp_gallery.find(".pp_arrow_next").click(function(){return $.prettyPhoto.changeGalleryPage("next"),$.prettyPhoto.stopSlideshow(),!1}),$pp_gallery.find(".pp_arrow_previous").click(function(){return $.prettyPhoto.changeGalleryPage("previous"),$.prettyPhoto.stopSlideshow(),!1}),$pp_pic_holder.find(".pp_content").hover(function(){$pp_pic_holder.find(".pp_gallery:not(.disabled)").fadeIn()},function(){$pp_pic_holder.find(".pp_gallery:not(.disabled)").fadeOut()}),itemWidth=57,$pp_gallery_li.each(function(i){$(this).find("a").click(function(){return $.prettyPhoto.changePage(i),$.prettyPhoto.stopSlideshow(),!1})})}settings.slideshow&&($pp_pic_holder.find(".pp_nav").prepend('<a href="#" class="pp_play">Play</a>'),$pp_pic_holder.find(".pp_nav .pp_play").click(function(){return $.prettyPhoto.startSlideshow(),!1})),$pp_pic_holder.addClass("pp_pic_holder "+settings.theme),$pp_overlay.css({opacity:0,height:$(document).height(),width:$(window).width()}).bind("click",function(){settings.modal||$.prettyPhoto.close()}),$("a.pp_close").bind("click",function(e){return e&&e.preventDefault&&e.preventDefault(),$.prettyPhoto.close(),!1}),settings.allow_expand&&$("a.pp_expand").bind("click",function(e){return $(this).hasClass("pp_expand")?($(this).removeClass("pp_expand").addClass("pp_contract"),doresize=!1):($(this).removeClass("pp_contract").addClass("pp_expand"),doresize=!0),_hideContent(function(){$.prettyPhoto.open()}),!1}),$pp_pic_holder.find(".pp_previous, .pp_nav .pp_arrow_previous").bind("click",function(){return $.prettyPhoto.changePage("previous"),$.prettyPhoto.stopSlideshow(),!1}),$pp_pic_holder.find(".pp_next, .pp_nav .pp_arrow_next").bind("click",function(){return $.prettyPhoto.changePage("next"),$.prettyPhoto.stopSlideshow(),!1}),_center_overlay()}pp_settings=jQuery.extend({},options,pp_settings);var pp_dimensions,pp_open,pp_contentHeight,pp_contentWidth,pp_containerHeight,pp_containerWidth,pp_slideshow,matchedObjects=this,percentBased=!1,windowHeight=$(window).height(),windowWidth=$(window).width();return doresize=!0,scroll_pos=_get_scroll(),$(window).unbind("resize.prettyphoto").bind("resize.prettyphoto",function(){_center_overlay(),_resize_overlay()}),pp_settings.keyboard_shortcuts&&$(document).unbind("keydown.prettyphoto").bind("keydown.prettyphoto",function(e){if("undefined"!=typeof $pp_pic_holder&&$pp_pic_holder.is(":visible"))switch(e.keyCode){case 37:$.prettyPhoto.changePage("previous"),e.preventDefault();break;case 39:$.prettyPhoto.changePage("next"),e.preventDefault();break;case 27:settings.modal||$.prettyPhoto.close(),e.preventDefault()}}),$.prettyPhoto.initialize=function(){return settings=pp_settings,"pp_default"==settings.theme&&(settings.horizontal_padding=16),theRel=$(this).attr(settings.hook),galleryRegExp=/\[(?:.*)\]/,isSet=!!galleryRegExp.exec(theRel),pp_images=isSet?jQuery.map(matchedObjects,function(n,i){if(-1!=$(n).attr(settings.hook).indexOf(theRel))return $(n).attr("href")}):$.makeArray($(this).attr("href")),pp_titles=isSet?jQuery.map(matchedObjects,function(n,i){if(-1!=$(n).attr(settings.hook).indexOf(theRel))return $(n).find("img").attr("alt")?$(n).find("img").attr("alt"):""}):$.makeArray($(this).find("img").attr("alt")),pp_descriptions=isSet?jQuery.map(matchedObjects,function(n,i){if(-1!=$(n).attr(settings.hook).indexOf(theRel))return $(n).attr("title")?$(n).attr("title"):""}):$.makeArray($(this).attr("title")),pp_images.length>settings.overlay_gallery_max&&(settings.overlay_gallery=!1),set_position=jQuery.inArray($(this).attr("href"),pp_images),rel_index=isSet?set_position:$("a["+settings.hook+"^='"+theRel+"']").index($(this)),_build_overlay(this),settings.allow_resize&&$(window).bind("scroll.prettyphoto",function(){_center_overlay()}),$.prettyPhoto.open(),!1},$.prettyPhoto.open=function(event){return"undefined"==typeof settings&&(settings=pp_settings,pp_images=$.makeArray(arguments[0]),pp_titles=arguments[1]?$.makeArray(arguments[1]):$.makeArray(""),pp_descriptions=arguments[2]?$.makeArray(arguments[2]):$.makeArray(""),isSet=pp_images.length>1,set_position=arguments[3]?arguments[3]:0,_build_overlay(event.target)),settings.hideflash&&$("object,embed,iframe[src*=youtube],iframe[src*=vimeo]").css("visibility","hidden"),_checkPosition($(pp_images).size()),$(".pp_loaderIcon").show(),settings.deeplinking&&setHashtag(),settings.social_tools&&(facebook_like_link=settings.social_tools.replace("{location_href}",encodeURIComponent(location.href)),$pp_pic_holder.find(".pp_social").html(facebook_like_link)),$ppt.is(":hidden")&&$ppt.css("opacity",0).show(),$pp_overlay.show().fadeTo(settings.animation_speed,settings.opacity),$pp_pic_holder.find(".currentTextHolder").text(set_position+1+settings.counter_separator_label+$(pp_images).size()),void 0!==pp_descriptions[set_position]&&""!=pp_descriptions[set_position]?$pp_pic_holder.find(".pp_description").show().html(unescape(pp_descriptions[set_position])):$pp_pic_holder.find(".pp_description").hide(),movie_width=parseFloat(getParam("width",pp_images[set_position]))?getParam("width",pp_images[set_position]):settings.default_width.toString(),movie_height=parseFloat(getParam("height",pp_images[set_position]))?getParam("height",pp_images[set_position]):settings.default_height.toString(),percentBased=!1,-1!=movie_height.indexOf("%")&&(movie_height=parseFloat($(window).height()*parseFloat(movie_height)/100-150),percentBased=!0),-1!=movie_width.indexOf("%")&&(movie_width=parseFloat($(window).width()*parseFloat(movie_width)/100-150),percentBased=!0),$pp_pic_holder.fadeIn(function(){switch(settings.show_title&&""!=pp_titles[set_position]&&void 0!==pp_titles[set_position]?$ppt.html(unescape(pp_titles[set_position])):$ppt.html("&nbsp;"),imgPreloader="",skipInjection=!1,_getFileType(pp_images[set_position])){case"image":imgPreloader=new Image,nextImage=new Image,isSet&&set_position<$(pp_images).size()-1&&(nextImage.src=pp_images[set_position+1]),prevImage=new Image,isSet&&pp_images[set_position-1]&&(prevImage.src=pp_images[set_position-1]),$pp_pic_holder.find("#pp_full_res")[0].innerHTML=settings.image_markup.replace(/{path}/g,pp_images[set_position]),imgPreloader.onload=function(){pp_dimensions=_fitToViewport(imgPreloader.width,imgPreloader.height),_showContent()},imgPreloader.onerror=function(){alert("Image cannot be loaded. Make sure the path is correct and image exist."),$.prettyPhoto.close()},imgPreloader.src=pp_images[set_position];break;case"youtube":pp_dimensions=_fitToViewport(movie_width,movie_height),movie_id=getParam("v",pp_images[set_position]),""==movie_id&&(movie_id=pp_images[set_position].split("youtu.be/"),movie_id=movie_id[1],movie_id.indexOf("?")>0&&(movie_id=movie_id.substr(0,movie_id.indexOf("?"))),movie_id.indexOf("&")>0&&(movie_id=movie_id.substr(0,movie_id.indexOf("&")))),movie="http://www.youtube.com/embed/"+movie_id,getParam("rel",pp_images[set_position])?movie+="?rel="+getParam("rel",pp_images[set_position]):movie+="?rel=1",settings.autoplay&&(movie+="&autoplay=1"),toInject=settings.iframe_markup.replace(/{width}/g,pp_dimensions.width).replace(/{height}/g,pp_dimensions.height).replace(/{wmode}/g,settings.wmode).replace(/{path}/g,movie);break;case"vimeo":pp_dimensions=_fitToViewport(movie_width,movie_height),movie_id=pp_images[set_position];var regExp=/http(s?):\/\/(www\.)?vimeo.com\/(\d+)/,match=movie_id.match(regExp);movie="http://player.vimeo.com/video/"+match[3]+"?title=0&amp;byline=0&amp;portrait=0",settings.autoplay&&(movie+="&autoplay=1;"),vimeo_width=pp_dimensions.width+"/embed/?moog_width="+pp_dimensions.width,toInject=settings.iframe_markup.replace(/{width}/g,vimeo_width).replace(/{height}/g,pp_dimensions.height).replace(/{path}/g,movie);break;case"quicktime":pp_dimensions=_fitToViewport(movie_width,movie_height),pp_dimensions.height+=15,pp_dimensions.contentHeight+=15,pp_dimensions.containerHeight+=15,toInject=settings.quicktime_markup.replace(/{width}/g,pp_dimensions.width).replace(/{height}/g,pp_dimensions.height).replace(/{wmode}/g,settings.wmode).replace(/{path}/g,pp_images[set_position]).replace(/{autoplay}/g,settings.autoplay);break;case"flash":pp_dimensions=_fitToViewport(movie_width,movie_height),flash_vars=pp_images[set_position],flash_vars=flash_vars.substring(pp_images[set_position].indexOf("flashvars")+10,pp_images[set_position].length),filename=pp_images[set_position],filename=filename.substring(0,filename.indexOf("?")),toInject=settings.flash_markup.replace(/{width}/g,pp_dimensions.width).replace(/{height}/g,pp_dimensions.height).replace(/{wmode}/g,settings.wmode).replace(/{path}/g,filename+"?"+flash_vars);break;case"iframe":pp_dimensions=_fitToViewport(movie_width,movie_height),frame_url=pp_images[set_position],frame_url=frame_url.substr(0,frame_url.indexOf("iframe")-1),toInject=settings.iframe_markup.replace(/{width}/g,pp_dimensions.width).replace(/{height}/g,pp_dimensions.height).replace(/{path}/g,frame_url);break;case"ajax":doresize=!1,pp_dimensions=_fitToViewport(movie_width,movie_height),doresize=!0,skipInjection=!0,$.get(pp_images[set_position],function(responseHTML){toInject=settings.inline_markup.replace(/{content}/g,responseHTML),$pp_pic_holder.find("#pp_full_res")[0].innerHTML=toInject,_showContent()});break;case"custom":pp_dimensions=_fitToViewport(movie_width,movie_height),toInject=settings.custom_markup;break;case"inline":myClone=$(pp_images[set_position]).clone().append('<br clear="all" />').css({width:settings.default_width}).wrapInner('<div id="pp_full_res"><div class="pp_inline"></div></div>').appendTo($("body")).show(),doresize=!1,pp_dimensions=_fitToViewport($(myClone).width(),$(myClone).height()),doresize=!0,$(myClone).remove(),toInject=settings.inline_markup.replace(/{content}/g,$(pp_images[set_position]).html())}imgPreloader||skipInjection||($pp_pic_holder.find("#pp_full_res")[0].innerHTML=toInject,_showContent())}),!1},$.prettyPhoto.changePage=function(direction){currentGalleryPage=0,"previous"==direction?--set_position<0&&(set_position=$(pp_images).size()-1):"next"==direction?++set_position>$(pp_images).size()-1&&(set_position=0):set_position=direction,rel_index=set_position,doresize||(doresize=!0),settings.allow_expand&&$(".pp_contract").removeClass("pp_contract").addClass("pp_expand"),_hideContent(function(){$.prettyPhoto.open()})},$.prettyPhoto.changeGalleryPage=function(direction){"next"==direction?++currentGalleryPage>totalPage&&(currentGalleryPage=0):"previous"==direction?--currentGalleryPage<0&&(currentGalleryPage=totalPage):currentGalleryPage=direction,slide_speed="next"==direction||"previous"==direction?settings.animation_speed:0,slide_to=currentGalleryPage*(itemsPerPage*itemWidth),$pp_gallery.find("ul").animate({left:-slide_to},slide_speed)},$.prettyPhoto.startSlideshow=function(){void 0===pp_slideshow?($pp_pic_holder.find(".pp_play").unbind("click").removeClass("pp_play").addClass("pp_pause").click(function(){return $.prettyPhoto.stopSlideshow(),!1}),pp_slideshow=setInterval($.prettyPhoto.startSlideshow,settings.slideshow)):$.prettyPhoto.changePage("next")},$.prettyPhoto.stopSlideshow=function(){$pp_pic_holder.find(".pp_pause").unbind("click").removeClass("pp_pause").addClass("pp_play").click(function(){return $.prettyPhoto.startSlideshow(),!1}),clearInterval(pp_slideshow),pp_slideshow=void 0},$.prettyPhoto.close=function(){$pp_overlay.is(":animated")||($.prettyPhoto.stopSlideshow(),$pp_pic_holder.stop().find("object,embed").css("visibility","hidden"),$("div.pp_pic_holder,div.ppt,.pp_fade").fadeOut(settings.animation_speed,function(){$(this).remove()}),$pp_overlay.fadeOut(settings.animation_speed,function(){settings.hideflash&&$("object,embed,iframe[src*=youtube],iframe[src*=vimeo]").css("visibility","visible"),$(this).remove(),$(window).unbind("scroll.prettyphoto"),clearHashtag(),settings.callback(),doresize=!0,pp_open=!1,delete settings}))},!pp_alreadyInitialized&&getHashtag()&&(pp_alreadyInitialized=!0,hashIndex=getHashtag(),hashRel=hashIndex,hashIndex=hashIndex.substring(hashIndex.indexOf("/")+1,hashIndex.length-1),hashRel=hashRel.substring(0,hashRel.indexOf("/")),setTimeout(function(){$("a["+pp_settings.hook+"^='"+hashRel+"']:eq("+hashIndex+")").trigger("click")},50)),this.unbind("click.prettyphoto").bind("click.prettyphoto",$.prettyPhoto.initialize)}}(jQuery);var pp_alreadyInitialized=!1;
!function(t,e,i,s){function n(e,i){this.settings=null,this.options=t.extend({},n.Defaults,i),this.$element=t(e),this._handlers={},this._plugins={},this._supress={},this._current=null,this._speed=null,this._coordinates=[],this._breakpoint=null,this._width=null,this._items=[],this._clones=[],this._mergers=[],this._widths=[],this._invalidated={},this._pipe=[],this._drag={time:null,target:null,pointer:null,stage:{start:null,current:null},direction:null},this._states={current:{},tags:{initializing:["busy"],animating:["busy"],dragging:["interacting"]}},t.each(["onResize","onThrottledResize"],t.proxy(function(e,i){this._handlers[i]=t.proxy(this[i],this)},this)),t.each(n.Plugins,t.proxy(function(t,e){this._plugins[t[0].toLowerCase()+t.slice(1)]=new e(this)},this)),t.each(n.Workers,t.proxy(function(e,i){this._pipe.push({filter:i.filter,run:t.proxy(i.run,this)})},this)),this.setup(),this.initialize()}n.Defaults={items:3,loop:!1,center:!1,rewind:!1,mouseDrag:!0,touchDrag:!0,pullDrag:!0,freeDrag:!1,margin:0,stagePadding:0,merge:!1,mergeFit:!0,autoWidth:!1,startPosition:0,rtl:!1,smartSpeed:250,fluidSpeed:!1,dragEndSpeed:!1,responsive:{},responsiveRefreshRate:200,responsiveBaseElement:e,fallbackEasing:"swing",info:!1,nestedItemSelector:!1,itemElement:"div",stageElement:"div",refreshClass:"owl-refresh",loadedClass:"owl-loaded",loadingClass:"owl-loading",rtlClass:"owl-rtl",responsiveClass:"owl-responsive",dragClass:"owl-drag",itemClass:"owl-item",stageClass:"owl-stage",stageOuterClass:"owl-stage-outer",grabClass:"owl-grab"},n.Width={Default:"default",Inner:"inner",Outer:"outer"},n.Type={Event:"event",State:"state"},n.Plugins={},n.Workers=[{filter:["width","settings"],run:function(){this._width=this.$element.width()}},{filter:["width","items","settings"],run:function(t){t.current=this._items&&this._items[this.relative(this._current)]}},{filter:["items","settings"],run:function(){this.$stage.children(".cloned").remove()}},{filter:["width","items","settings"],run:function(t){var e=this.settings.margin||"",i=!this.settings.autoWidth,s=this.settings.rtl,n={width:"auto","margin-left":s?e:"","margin-right":s?"":e};!i&&this.$stage.children().css(n),t.css=n}},{filter:["width","items","settings"],run:function(t){var e=(this.width()/this.settings.items).toFixed(3)-this.settings.margin,i=null,s=this._items.length,n=!this.settings.autoWidth,o=[];for(t.items={merge:!1,width:e};s--;)i=this._mergers[s],i=this.settings.mergeFit&&Math.min(i,this.settings.items)||i,t.items.merge=i>1||t.items.merge,o[s]=n?e*i:this._items[s].width();this._widths=o}},{filter:["items","settings"],run:function(){var e=[],i=this._items,s=this.settings,n=Math.max(2*s.items,4),o=2*Math.ceil(i.length/2),r=s.loop&&i.length?s.rewind?n:Math.max(n,o):0,a="",h="";for(r/=2;r--;)e.push(this.normalize(e.length/2,!0)),a+=i[e[e.length-1]][0].outerHTML,e.push(this.normalize(i.length-1-(e.length-1)/2,!0)),h=i[e[e.length-1]][0].outerHTML+h;this._clones=e,t(a).addClass("cloned").appendTo(this.$stage),t(h).addClass("cloned").prependTo(this.$stage)}},{filter:["width","items","settings"],run:function(){for(var t=this.settings.rtl?1:-1,e=this._clones.length+this._items.length,i=-1,s=0,n=0,o=[];++i<e;)s=o[i-1]||0,n=this._widths[this.relative(i)]+this.settings.margin,o.push(s+n*t);this._coordinates=o}},{filter:["width","items","settings"],run:function(){var t=this.settings.stagePadding,e=this._coordinates,i={width:Math.ceil(Math.abs(e[e.length-1]))+2*t,"padding-left":t||"","padding-right":t||""};this.$stage.css(i)}},{filter:["width","items","settings"],run:function(t){var e=this._coordinates.length,i=!this.settings.autoWidth,s=this.$stage.children();if(i&&t.items.merge)for(;e--;)t.css.width=this._widths[this.relative(e)],s.eq(e).css(t.css);else i&&(t.css.width=t.items.width,s.css(t.css))}},{filter:["items"],run:function(){this._coordinates.length<1&&this.$stage.removeAttr("style")}},{filter:["width","items","settings"],run:function(t){t.current=t.current?this.$stage.children().index(t.current):0,t.current=Math.max(this.minimum(),Math.min(this.maximum(),t.current)),this.reset(t.current)}},{filter:["position"],run:function(){this.animate(this.coordinates(this._current))}},{filter:["width","position","items","settings"],run:function(){var t,e,i,s,n=this.settings.rtl?1:-1,o=2*this.settings.stagePadding,r=this.coordinates(this.current())+o,a=r+this.width()*n,h=[];for(i=0,s=this._coordinates.length;s>i;i++)t=this._coordinates[i-1]||0,e=Math.abs(this._coordinates[i])+o*n,(this.op(t,"<=",r)&&this.op(t,">",a)||this.op(e,"<",r)&&this.op(e,">",a))&&h.push(i);this.$stage.children(".active").removeClass("active"),this.$stage.children(":eq("+h.join("), :eq(")+")").addClass("active"),this.settings.center&&(this.$stage.children(".center").removeClass("center"),this.$stage.children().eq(this.current()).addClass("center"))}}],n.prototype.initialize=function(){if(this.enter("initializing"),this.trigger("initialize"),this.$element.toggleClass(this.settings.rtlClass,this.settings.rtl),this.settings.autoWidth&&!this.is("pre-loading")){var e,i,n;e=this.$element.find("img"),i=this.settings.nestedItemSelector?"."+this.settings.nestedItemSelector:s,n=this.$element.children(i).width(),e.length&&0>=n&&this.preloadAutoWidthImages(e)}this.$element.addClass(this.options.loadingClass),this.$stage=t("<"+this.settings.stageElement+' class="'+this.settings.stageClass+'"/>').wrap('<div class="'+this.settings.stageOuterClass+'"/>'),this.$element.append(this.$stage.parent()),this.replace(this.$element.children().not(this.$stage.parent())),this.$element.is(":visible")?this.refresh():this.invalidate("width"),this.$element.removeClass(this.options.loadingClass).addClass(this.options.loadedClass),this.registerEventHandlers(),this.leave("initializing"),this.trigger("initialized")},n.prototype.setup=function(){var e=this.viewport(),i=this.options.responsive,s=-1,n=null;i?(t.each(i,function(t){e>=t&&t>s&&(s=Number(t))}),n=t.extend({},this.options,i[s]),delete n.responsive,n.responsiveClass&&this.$element.attr("class",this.$element.attr("class").replace(new RegExp("("+this.options.responsiveClass+"-)\\S+\\s","g"),"$1"+s))):n=t.extend({},this.options),(null===this.settings||this._breakpoint!==s)&&(this.trigger("change",{property:{name:"settings",value:n}}),this._breakpoint=s,this.settings=n,this.invalidate("settings"),this.trigger("changed",{property:{name:"settings",value:this.settings}}))},n.prototype.optionsLogic=function(){this.settings.autoWidth&&(this.settings.stagePadding=!1,this.settings.merge=!1)},n.prototype.prepare=function(e){var i=this.trigger("prepare",{content:e});return i.data||(i.data=t("<"+this.settings.itemElement+"/>").addClass(this.options.itemClass).append(e)),this.trigger("prepared",{content:i.data}),i.data},n.prototype.update=function(){for(var e=0,i=this._pipe.length,s=t.proxy(function(t){return this[t]},this._invalidated),n={};i>e;)(this._invalidated.all||t.grep(this._pipe[e].filter,s).length>0)&&this._pipe[e].run(n),e++;this._invalidated={},!this.is("valid")&&this.enter("valid")},n.prototype.width=function(t){switch(t=t||n.Width.Default){case n.Width.Inner:case n.Width.Outer:return this._width;default:return this._width-2*this.settings.stagePadding+this.settings.margin}},n.prototype.refresh=function(){this.enter("refreshing"),this.trigger("refresh"),this.setup(),this.optionsLogic(),this.$element.addClass(this.options.refreshClass),this.update(),this.$element.removeClass(this.options.refreshClass),this.leave("refreshing"),this.trigger("refreshed")},n.prototype.onThrottledResize=function(){e.clearTimeout(this.resizeTimer),this.resizeTimer=e.setTimeout(this._handlers.onResize,this.settings.responsiveRefreshRate)},n.prototype.onResize=function(){return this._items.length?this._width===this.$element.width()?!1:this.$element.is(":visible")?(this.enter("resizing"),this.trigger("resize").isDefaultPrevented()?(this.leave("resizing"),!1):(this.invalidate("width"),this.refresh(),this.leave("resizing"),void this.trigger("resized"))):!1:!1},n.prototype.registerEventHandlers=function(){t.support.transition&&this.$stage.on(t.support.transition.end+".owl.core",t.proxy(this.onTransitionEnd,this)),this.settings.responsive!==!1&&this.on(e,"resize",this._handlers.onThrottledResize),this.settings.mouseDrag&&(this.$element.addClass(this.options.dragClass),this.$stage.on("mousedown.owl.core",t.proxy(this.onDragStart,this)),this.$stage.on("dragstart.owl.core selectstart.owl.core",function(){return!1})),this.settings.touchDrag&&(this.$stage.on("touchstart.owl.core",t.proxy(this.onDragStart,this)),this.$stage.on("touchcancel.owl.core",t.proxy(this.onDragEnd,this)))},n.prototype.onDragStart=function(e){var s=null;3!==e.which&&(t.support.transform?(s=this.$stage.css("transform").replace(/.*\(|\)| /g,"").split(","),s={x:s[16===s.length?12:4],y:s[16===s.length?13:5]}):(s=this.$stage.position(),s={x:this.settings.rtl?s.left+this.$stage.width()-this.width()+this.settings.margin:s.left,y:s.top}),this.is("animating")&&(t.support.transform?this.animate(s.x):this.$stage.stop(),this.invalidate("position")),this.$element.toggleClass(this.options.grabClass,"mousedown"===e.type),this.speed(0),this._drag.time=(new Date).getTime(),this._drag.target=t(e.target),this._drag.stage.start=s,this._drag.stage.current=s,this._drag.pointer=this.pointer(e),t(i).on("mouseup.owl.core touchend.owl.core",t.proxy(this.onDragEnd,this)),t(i).one("mousemove.owl.core touchmove.owl.core",t.proxy(function(e){var s=this.difference(this._drag.pointer,this.pointer(e));t(i).on("mousemove.owl.core touchmove.owl.core",t.proxy(this.onDragMove,this)),Math.abs(s.x)<Math.abs(s.y)&&this.is("valid")||(e.preventDefault(),this.enter("dragging"),this.trigger("drag"))},this)))},n.prototype.onDragMove=function(t){var e=null,i=null,s=null,n=this.difference(this._drag.pointer,this.pointer(t)),o=this.difference(this._drag.stage.start,n);this.is("dragging")&&(t.preventDefault(),this.settings.loop?(e=this.coordinates(this.minimum()),i=this.coordinates(this.maximum()+1)-e,o.x=((o.x-e)%i+i)%i+e):(e=this.coordinates(this.settings.rtl?this.maximum():this.minimum()),i=this.coordinates(this.settings.rtl?this.minimum():this.maximum()),s=this.settings.pullDrag?-1*n.x/5:0,o.x=Math.max(Math.min(o.x,e+s),i+s)),this._drag.stage.current=o,this.animate(o.x))},n.prototype.onDragEnd=function(e){var s=this.difference(this._drag.pointer,this.pointer(e)),n=this._drag.stage.current,o=s.x>0^this.settings.rtl?"left":"right";t(i).off(".owl.core"),this.$element.removeClass(this.options.grabClass),(0!==s.x&&this.is("dragging")||!this.is("valid"))&&(this.speed(this.settings.dragEndSpeed||this.settings.smartSpeed),this.current(this.closest(n.x,0!==s.x?o:this._drag.direction)),this.invalidate("position"),this.update(),this._drag.direction=o,(Math.abs(s.x)>3||(new Date).getTime()-this._drag.time>300)&&this._drag.target.one("click.owl.core",function(){return!1})),this.is("dragging")&&(this.leave("dragging"),this.trigger("dragged"))},n.prototype.closest=function(e,i){var s=-1,n=30,o=this.width(),r=this.coordinates();return this.settings.freeDrag||t.each(r,t.proxy(function(t,a){return e>a-n&&a+n>e?s=t:this.op(e,"<",a)&&this.op(e,">",r[t+1]||a-o)&&(s="left"===i?t+1:t),-1===s},this)),this.settings.loop||(this.op(e,">",r[this.minimum()])?s=e=this.minimum():this.op(e,"<",r[this.maximum()])&&(s=e=this.maximum())),s},n.prototype.animate=function(e){var i=this.speed()>0;this.is("animating")&&this.onTransitionEnd(),i&&(this.enter("animating"),this.trigger("translate")),t.support.transform3d&&t.support.transition?this.$stage.css({transform:"translate3d("+e+"px,0px,0px)",transition:this.speed()/1e3+"s"}):i?this.$stage.animate({left:e+"px"},this.speed(),this.settings.fallbackEasing,t.proxy(this.onTransitionEnd,this)):this.$stage.css({left:e+"px"})},n.prototype.is=function(t){return this._states.current[t]&&this._states.current[t]>0},n.prototype.current=function(t){if(t===s)return this._current;if(0===this._items.length)return s;if(t=this.normalize(t),this._current!==t){var e=this.trigger("change",{property:{name:"position",value:t}});e.data!==s&&(t=this.normalize(e.data)),this._current=t,this.invalidate("position"),this.trigger("changed",{property:{name:"position",value:this._current}})}return this._current},n.prototype.invalidate=function(e){return"string"===t.type(e)&&(this._invalidated[e]=!0,this.is("valid")&&this.leave("valid")),t.map(this._invalidated,function(t,e){return e})},n.prototype.reset=function(t){t=this.normalize(t),t!==s&&(this._speed=0,this._current=t,this.suppress(["translate","translated"]),this.animate(this.coordinates(t)),this.release(["translate","translated"]))},n.prototype.normalize=function(e,i){var n=this._items.length,o=i?0:this._clones.length;return!t.isNumeric(e)||1>n?e=s:(0>e||e>=n+o)&&(e=((e-o/2)%n+n)%n+o/2),e},n.prototype.relative=function(t){return t-=this._clones.length/2,this.normalize(t,!0)},n.prototype.maximum=function(t){var e,i=this.settings,s=this._coordinates.length,n=Math.abs(this._coordinates[s-1])-this._width,o=-1;if(i.loop)s=this._clones.length/2+this._items.length-1;else if(i.autoWidth||i.merge)for(;s-o>1;)Math.abs(this._coordinates[e=s+o>>1])<n?o=e:s=e;else s=i.center?this._items.length-1:this._items.length-i.items;return t&&(s-=this._clones.length/2),Math.max(s,0)},n.prototype.minimum=function(t){return t?0:this._clones.length/2},n.prototype.items=function(t){return t===s?this._items.slice():(t=this.normalize(t,!0),this._items[t])},n.prototype.mergers=function(t){return t===s?this._mergers.slice():(t=this.normalize(t,!0),this._mergers[t])},n.prototype.clones=function(e){var i=this._clones.length/2,n=i+this._items.length,o=function(t){return t%2===0?n+t/2:i-(t+1)/2};return e===s?t.map(this._clones,function(t,e){return o(e)}):t.map(this._clones,function(t,i){return t===e?o(i):null})},n.prototype.speed=function(t){return t!==s&&(this._speed=t),this._speed},n.prototype.coordinates=function(e){var i=null;return e===s?t.map(this._coordinates,t.proxy(function(t,e){return this.coordinates(e)},this)):(this.settings.center?(i=this._coordinates[e],i+=(this.width()-i+(this._coordinates[e-1]||0))/2*(this.settings.rtl?-1:1)):i=this._coordinates[e-1]||0,i)},n.prototype.duration=function(t,e,i){return Math.min(Math.max(Math.abs(e-t),1),6)*Math.abs(i||this.settings.smartSpeed)},n.prototype.to=function(t,e){var i=this.current(),s=null,n=t-this.relative(i),o=(n>0)-(0>n),r=this._items.length,a=this.minimum(),h=this.maximum();this.settings.loop?(!this.settings.rewind&&Math.abs(n)>r/2&&(n+=-1*o*r),t=i+n,s=((t-a)%r+r)%r+a,s!==t&&h>=s-n&&s-n>0&&(i=s-n,t=s,this.reset(i))):this.settings.rewind?(h+=1,t=(t%h+h)%h):t=Math.max(a,Math.min(h,t)),this.speed(this.duration(i,t,e)),this.current(t),this.$element.is(":visible")&&this.update()},n.prototype.next=function(t){t=t||!1,this.to(this.relative(this.current())+1,t)},n.prototype.prev=function(t){t=t||!1,this.to(this.relative(this.current())-1,t)},n.prototype.onTransitionEnd=function(t){return t!==s&&(t.stopPropagation(),(t.target||t.srcElement||t.originalTarget)!==this.$stage.get(0))?!1:(this.leave("animating"),void this.trigger("translated"))},n.prototype.viewport=function(){var s;if(this.options.responsiveBaseElement!==e)s=t(this.options.responsiveBaseElement).width();else if(e.innerWidth)s=e.innerWidth;else{if(!i.documentElement||!i.documentElement.clientWidth)throw"Can not detect viewport width.";s=i.documentElement.clientWidth}return s},n.prototype.replace=function(e){this.$stage.empty(),this._items=[],e&&(e=e instanceof jQuery?e:t(e)),this.settings.nestedItemSelector&&(e=e.find("."+this.settings.nestedItemSelector)),e.filter(function(){return 1===this.nodeType}).each(t.proxy(function(t,e){e=this.prepare(e),this.$stage.append(e),this._items.push(e),this._mergers.push(1*e.find("[data-merge]").andSelf("[data-merge]").attr("data-merge")||1)},this)),this.reset(t.isNumeric(this.settings.startPosition)?this.settings.startPosition:0),this.invalidate("items")},n.prototype.add=function(e,i){var n=this.relative(this._current);i=i===s?this._items.length:this.normalize(i,!0),e=e instanceof jQuery?e:t(e),this.trigger("add",{content:e,position:i}),e=this.prepare(e),0===this._items.length||i===this._items.length?(0===this._items.length&&this.$stage.append(e),0!==this._items.length&&this._items[i-1].after(e),this._items.push(e),this._mergers.push(1*e.find("[data-merge]").andSelf("[data-merge]").attr("data-merge")||1)):(this._items[i].before(e),this._items.splice(i,0,e),this._mergers.splice(i,0,1*e.find("[data-merge]").andSelf("[data-merge]").attr("data-merge")||1)),this._items[n]&&this.reset(this._items[n].index()),this.invalidate("items"),this.trigger("added",{content:e,position:i})},n.prototype.remove=function(t){t=this.normalize(t,!0),t!==s&&(this.trigger("remove",{content:this._items[t],position:t}),this._items[t].remove(),this._items.splice(t,1),this._mergers.splice(t,1),this.invalidate("items"),this.trigger("removed",{content:null,position:t}))},n.prototype.preloadAutoWidthImages=function(e){e.each(t.proxy(function(e,i){this.enter("pre-loading"),i=t(i),t(new Image).one("load",t.proxy(function(t){i.attr("src",t.target.src),i.css("opacity",1),this.leave("pre-loading"),!this.is("pre-loading")&&!this.is("initializing")&&this.refresh()},this)).attr("src",i.attr("src")||i.attr("data-src")||i.attr("data-src-retina"))},this))},n.prototype.destroy=function(){this.$element.off(".owl.core"),this.$stage.off(".owl.core"),t(i).off(".owl.core"),this.settings.responsive!==!1&&(e.clearTimeout(this.resizeTimer),this.off(e,"resize",this._handlers.onThrottledResize));for(var s in this._plugins)this._plugins[s].destroy();this.$stage.children(".cloned").remove(),this.$stage.unwrap(),this.$stage.children().contents().unwrap(),this.$stage.children().unwrap(),this.$element.removeClass(this.options.refreshClass).removeClass(this.options.loadingClass).removeClass(this.options.loadedClass).removeClass(this.options.rtlClass).removeClass(this.options.dragClass).removeClass(this.options.grabClass).attr("class",this.$element.attr("class").replace(new RegExp(this.options.responsiveClass+"-\\S+\\s","g"),"")).removeData("owl.vccarousel")},n.prototype.op=function(t,e,i){var s=this.settings.rtl;switch(e){case"<":return s?t>i:i>t;case">":return s?i>t:t>i;case">=":return s?i>=t:t>=i;case"<=":return s?t>=i:i>=t}},n.prototype.on=function(t,e,i,s){t.addEventListener?t.addEventListener(e,i,s):t.attachEvent&&t.attachEvent("on"+e,i)},n.prototype.off=function(t,e,i,s){t.removeEventListener?t.removeEventListener(e,i,s):t.detachEvent&&t.detachEvent("on"+e,i)},n.prototype.trigger=function(e,i,s,o,r){var a={item:{count:this._items.length,index:this.current()}},h=t.camelCase(t.grep(["on",e,s],function(t){return t}).join("-").toLowerCase()),l=t.Event([e,"owl",s||"vccarousel"].join(".").toLowerCase(),t.extend({relatedTarget:this},a,i));return this._supress[e]||(t.each(this._plugins,function(t,e){e.onTrigger&&e.onTrigger(l)}),this.register({type:n.Type.Event,name:e}),this.$element.trigger(l),this.settings&&"function"==typeof this.settings[h]&&this.settings[h].call(this,l)),l},n.prototype.enter=function(e){t.each([e].concat(this._states.tags[e]||[]),t.proxy(function(t,e){this._states.current[e]===s&&(this._states.current[e]=0),this._states.current[e]++},this))},n.prototype.leave=function(e){t.each([e].concat(this._states.tags[e]||[]),t.proxy(function(t,e){this._states.current[e]--},this))},n.prototype.register=function(e){if(e.type===n.Type.Event){if(t.event.special[e.name]||(t.event.special[e.name]={}),!t.event.special[e.name].owl){var i=t.event.special[e.name]._default;t.event.special[e.name]._default=function(t){return!i||!i.apply||t.namespace&&-1!==t.namespace.indexOf("owl")?t.namespace&&t.namespace.indexOf("owl")>-1:i.apply(this,arguments)},t.event.special[e.name].owl=!0}}else e.type===n.Type.State&&(this._states.tags[e.name]?this._states.tags[e.name]=this._states.tags[e.name].concat(e.tags):this._states.tags[e.name]=e.tags,this._states.tags[e.name]=t.grep(this._states.tags[e.name],t.proxy(function(i,s){return t.inArray(i,this._states.tags[e.name])===s},this)))},n.prototype.suppress=function(e){t.each(e,t.proxy(function(t,e){this._supress[e]=!0},this))},n.prototype.release=function(e){t.each(e,t.proxy(function(t,e){delete this._supress[e]},this))},n.prototype.pointer=function(t){var i={x:null,y:null};return t=t.originalEvent||t||e.event,t=t.touches&&t.touches.length?t.touches[0]:t.changedTouches&&t.changedTouches.length?t.changedTouches[0]:t,t.pageX?(i.x=t.pageX,i.y=t.pageY):(i.x=t.clientX,i.y=t.clientY),i},n.prototype.difference=function(t,e){return{x:t.x-e.x,y:t.y-e.y}},t.fn.vcOwlCarousel=function(e){var i=Array.prototype.slice.call(arguments,1);return this.each(function(){var s=t(this),o=s.data("owl.vccarousel");o||(o=new n(this,"object"==typeof e&&e),s.data("owl.vccarousel",o),t.each(["next","prev","to","destroy","refresh","replace","add","remove"],function(e,i){o.register({type:n.Type.Event,name:i}),o.$element.on(i+".owl.vccarousel.core",t.proxy(function(t){t.namespace&&t.relatedTarget!==this&&(this.suppress([i]),o[i].apply(this,[].slice.call(arguments,1)),this.release([i]))},o))})),"string"==typeof e&&"_"!==e.charAt(0)&&o[e].apply(o,i)})},t.fn.vcOwlCarousel.Constructor=n}(window.Zepto||window.jQuery,window,document),function(t,e,i,s){var n=function(e){this._core=e,this._interval=null,this._visible=null,this._handlers={"initialized.owl.vccarousel":t.proxy(function(t){t.namespace&&this._core.settings.autoRefresh&&this.watch()},this)},this._core.options=t.extend({},n.Defaults,this._core.options),this._core.$element.on(this._handlers)};n.Defaults={autoRefresh:!0,autoRefreshInterval:500},n.prototype.watch=function(){this._interval||(this._visible=this._core.$element.is(":visible"),this._interval=e.setInterval(t.proxy(this.refresh,this),this._core.settings.autoRefreshInterval))},n.prototype.refresh=function(){this._core.$element.is(":visible")!==this._visible&&(this._visible=!this._visible,this._core.$element.toggleClass("owl-hidden",!this._visible),this._visible&&this._core.invalidate("width")&&this._core.refresh())},n.prototype.destroy=function(){var t,i;e.clearInterval(this._interval);for(t in this._handlers)this._core.$element.off(t,this._handlers[t]);for(i in Object.getOwnPropertyNames(this))"function"!=typeof this[i]&&(this[i]=null)},t.fn.vcOwlCarousel.Constructor.Plugins.AutoRefresh=n}(window.Zepto||window.jQuery,window,document),function(t,e,i,s){var n=function(e){this._core=e,this._loaded=[],this._handlers={"initialized.owl.vccarousel change.owl.vccarousel":t.proxy(function(e){if(e.namespace&&this._core.settings&&this._core.settings.lazyLoad&&(e.property&&"position"==e.property.name||"initialized"==e.type))for(var i=this._core.settings,s=i.center&&Math.ceil(i.items/2)||i.items,n=i.center&&-1*s||0,o=(e.property&&e.property.value||this._core.current())+n,r=this._core.clones().length,a=t.proxy(function(t,e){this.load(e)},this);n++<s;)this.load(r/2+this._core.relative(o)),r&&t.each(this._core.clones(this._core.relative(o)),a),o++},this)},this._core.options=t.extend({},n.Defaults,this._core.options),this._core.$element.on(this._handlers)};n.Defaults={lazyLoad:!1},n.prototype.load=function(i){var s=this._core.$stage.children().eq(i),n=s&&s.find(".owl-lazy");!n||t.inArray(s.get(0),this._loaded)>-1||(n.each(t.proxy(function(i,s){var n,o=t(s),r=e.devicePixelRatio>1&&o.attr("data-src-retina")||o.attr("data-src");this._core.trigger("load",{element:o,url:r},"lazy"),o.is("img")?o.one("load.owl.lazy",t.proxy(function(){o.css("opacity",1),this._core.trigger("loaded",{element:o,url:r},"lazy")},this)).attr("src",r):(n=new Image,n.onload=t.proxy(function(){o.css({"background-image":"url("+r+")",opacity:"1"}),this._core.trigger("loaded",{element:o,url:r},"lazy")},this),n.src=r)},this)),this._loaded.push(s.get(0)))},n.prototype.destroy=function(){var t,e;for(t in this.handlers)this._core.$element.off(t,this.handlers[t]);for(e in Object.getOwnPropertyNames(this))"function"!=typeof this[e]&&(this[e]=null)},t.fn.vcOwlCarousel.Constructor.Plugins.Lazy=n}(window.Zepto||window.jQuery,window,document),function(t,e,i,s){var n=function(e){this._core=e,this._handlers={"initialized.owl.vccarousel refreshed.owl.vccarousel":t.proxy(function(t){t.namespace&&this._core.settings.autoHeight&&this.update()},this),"changed.owl.vccarousel":t.proxy(function(t){t.namespace&&this._core.settings.autoHeight&&"position"==t.property.name&&this.update()},this),"loaded.owl.lazy":t.proxy(function(t){t.namespace&&this._core.settings.autoHeight&&t.element.closest("."+this._core.settings.itemClass).index()===this._core.current()&&this.update()},this)},this._core.options=t.extend({},n.Defaults,this._core.options),this._core.$element.on(this._handlers)};n.Defaults={autoHeight:!1,autoHeightClass:"owl-height"},n.prototype.update=function(){this._core.$stage.parent().height(this._core.$stage.children().eq(this._core.current()).height()).addClass(this._core.settings.autoHeightClass)},n.prototype.destroy=function(){var t,e;for(t in this._handlers)this._core.$element.off(t,this._handlers[t]);for(e in Object.getOwnPropertyNames(this))"function"!=typeof this[e]&&(this[e]=null)},t.fn.vcOwlCarousel.Constructor.Plugins.AutoHeight=n}(window.Zepto||window.jQuery,window,document),function(t,e,i,s){var n=function(e){this._core=e,this._videos={},this._playing=null,this._handlers={"initialized.owl.vccarousel":t.proxy(function(t){t.namespace&&this._core.register({type:"state",name:"playing",tags:["interacting"]})},this),"resize.owl.vccarousel":t.proxy(function(t){t.namespace&&this._core.settings.video&&this.isInFullScreen()&&t.preventDefault()},this),"refreshed.owl.vccarousel":t.proxy(function(t){t.namespace&&this._core.is("resizing")&&this._core.$stage.find(".cloned .owl-video-frame").remove()},this),"changed.owl.vccarousel":t.proxy(function(t){t.namespace&&"position"===t.property.name&&this._playing&&this.stop()},this),"prepared.owl.vccarousel":t.proxy(function(e){if(e.namespace){var i=t(e.content).find(".owl-video");i.length&&(i.css("display","none"),this.fetch(i,t(e.content)))}},this)},this._core.options=t.extend({},n.Defaults,this._core.options),this._core.$element.on(this._handlers),this._core.$element.on("click.owl.video",".owl-video-play-icon",t.proxy(function(t){this.play(t)},this))};n.Defaults={video:!1,videoHeight:!1,videoWidth:!1},n.prototype.fetch=function(t,e){var i=t.attr("data-vimeo-id")?"vimeo":"youtube",s=t.attr("data-vimeo-id")||t.attr("data-youtube-id"),n=t.attr("data-width")||this._core.settings.videoWidth,o=t.attr("data-height")||this._core.settings.videoHeight,r=t.attr("href");if(!r)throw new Error("Missing video URL.");if(s=r.match(/(http:|https:|)\/\/(player.|www.)?(vimeo\.com|youtu(be\.com|\.be|be\.googleapis\.com))\/(video\/|embed\/|watch\?v=|v\/)?([A-Za-z0-9._%-]*)(\&\S+)?/),s[3].indexOf("youtu")>-1)i="youtube";else{if(!(s[3].indexOf("vimeo")>-1))throw new Error("Video URL not supported.");i="vimeo"}s=s[6],this._videos[r]={type:i,id:s,width:n,height:o},e.attr("data-video",r),this.thumbnail(t,this._videos[r])},n.prototype.thumbnail=function(e,i){var s,n,o,r=i.width&&i.height?'style="width:'+i.width+"px;height:"+i.height+'px;"':"",a=e.find("img"),h="src",l="",c=this._core.settings,p=function(t){n='<div class="owl-video-play-icon"></div>',s=c.lazyLoad?'<div class="owl-video-tn '+l+'" '+h+'="'+t+'"></div>':'<div class="owl-video-tn" style="opacity:1;background-image:url('+t+')"></div>',e.after(s),e.after(n)};return e.wrap('<div class="owl-video-wrapper"'+r+"></div>"),this._core.settings.lazyLoad&&(h="data-src",l="owl-lazy"),a.length?(p(a.attr(h)),a.remove(),!1):void("youtube"===i.type?(o="http://img.youtube.com/vi/"+i.id+"/hqdefault.jpg",p(o)):"vimeo"===i.type&&t.ajax({type:"GET",url:"http://vimeo.com/api/v2/video/"+i.id+".json",jsonp:"callback",dataType:"jsonp",success:function(t){o=t[0].thumbnail_large,p(o)}}))},n.prototype.stop=function(){this._core.trigger("stop",null,"video"),this._playing.find(".owl-video-frame").remove(),this._playing.removeClass("owl-video-playing"),this._playing=null,this._core.leave("playing"),this._core.trigger("stopped",null,"video")},n.prototype.play=function(e){var i,s=t(e.target),n=s.closest("."+this._core.settings.itemClass),o=this._videos[n.attr("data-video")],r=o.width||"100%",a=o.height||this._core.$stage.height();this._playing||(this._core.enter("playing"),this._core.trigger("play",null,"video"),n=this._core.items(this._core.relative(n.index())),this._core.reset(n.index()),"youtube"===o.type?i='<iframe width="'+r+'" height="'+a+'" src="http://www.youtube.com/embed/'+o.id+"?autoplay=1&v="+o.id+'" frameborder="0" allowfullscreen></iframe>':"vimeo"===o.type&&(i='<iframe src="http://player.vimeo.com/video/'+o.id+'?autoplay=1" width="'+r+'" height="'+a+'" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>'),t('<div class="owl-video-frame">'+i+"</div>").insertAfter(n.find(".owl-video")),this._playing=n.addClass("owl-video-playing"))},n.prototype.isInFullScreen=function(){var e=i.fullscreenElement||i.mozFullScreenElement||i.webkitFullscreenElement;return e&&t(e).parent().hasClass("owl-video-frame")},n.prototype.destroy=function(){var t,e;this._core.$element.off("click.owl.video");for(t in this._handlers)this._core.$element.off(t,this._handlers[t]);for(e in Object.getOwnPropertyNames(this))"function"!=typeof this[e]&&(this[e]=null)},t.fn.vcOwlCarousel.Constructor.Plugins.Video=n}(window.Zepto||window.jQuery,window,document),function(t,e,i,s){var n=function(e){this.core=e,this.core.options=t.extend({},n.Defaults,this.core.options),this.swapping=!0,this.previous=s,this.next=s,this.handlers={"change.owl.vccarousel":t.proxy(function(t){t.namespace&&"position"==t.property.name&&(this.previous=this.core.current(),this.next=t.property.value)},this),"drag.owl.vccarousel dragged.owl.vccarousel translated.owl.vccarousel":t.proxy(function(t){t.namespace&&(this.swapping="translated"==t.type)},this),"translate.owl.vccarousel":t.proxy(function(t){t.namespace&&this.swapping&&(this.core.options.animateOut||this.core.options.animateIn)&&this.swap()},this)},this.core.$element.on(this.handlers)};n.Defaults={animateOut:!1,animateIn:!1},n.prototype.swap=function(){if(1===this.core.settings.items&&t.support.animation&&t.support.transition){this.core.speed(0);var e,i=t.proxy(this.clear,this),s=this.core.$stage.children().eq(this.previous),n=this.core.$stage.children().eq(this.next),o=this.core.settings.animateIn,r=this.core.settings.animateOut;this.core.current()!==this.previous&&(r&&(e=this.core.coordinates(this.previous)-this.core.coordinates(this.next),s.one(t.support.animation.end,i).css({left:e+"px"}).addClass("animated owl-animated-out").addClass(r)),o&&n.one(t.support.animation.end,i).addClass("animated owl-animated-in").addClass(o))}},n.prototype.clear=function(e){t(e.target).css({left:""}).removeClass("animated owl-animated-out owl-animated-in").removeClass(this.core.settings.animateIn).removeClass(this.core.settings.animateOut),this.core.onTransitionEnd()},n.prototype.destroy=function(){var t,e;for(t in this.handlers)this.core.$element.off(t,this.handlers[t]);for(e in Object.getOwnPropertyNames(this))"function"!=typeof this[e]&&(this[e]=null)},t.fn.vcOwlCarousel.Constructor.Plugins.Animate=n}(window.Zepto||window.jQuery,window,document),function(t,e,i,s){var n=function(e){this._core=e,this._interval=null,this._paused=!1,this._handlers={"changed.owl.vccarousel":t.proxy(function(t){t.namespace&&"settings"===t.property.name&&(this._core.settings.autoplay?this.play():this.stop())},this),"initialized.owl.vccarousel":t.proxy(function(t){t.namespace&&this._core.settings.autoplay&&this.play()},this),"play.owl.autoplay":t.proxy(function(t,e,i){t.namespace&&this.play(e,i)},this),"stop.owl.autoplay":t.proxy(function(t){t.namespace&&this.stop()},this),"mouseover.owl.autoplay":t.proxy(function(){this._core.settings.autoplayHoverPause&&this._core.is("rotating")&&this.pause()},this),"mouseleave.owl.autoplay":t.proxy(function(){this._core.settings.autoplayHoverPause&&this._core.is("rotating")&&this.play();
},this)},this._core.$element.on(this._handlers),this._core.options=t.extend({},n.Defaults,this._core.options)};n.Defaults={autoplay:!1,autoplayTimeout:5e3,autoplayHoverPause:!1,autoplaySpeed:!1},n.prototype.play=function(s,n){this._paused=!1,this._core.is("rotating")||(this._core.enter("rotating"),this._interval=e.setInterval(t.proxy(function(){this._paused||this._core.is("busy")||this._core.is("interacting")||i.hidden||this._core.next(n||this._core.settings.autoplaySpeed)},this),s||this._core.settings.autoplayTimeout))},n.prototype.stop=function(){this._core.is("rotating")&&(e.clearInterval(this._interval),this._core.leave("rotating"))},n.prototype.pause=function(){this._core.is("rotating")&&(this._paused=!0)},n.prototype.destroy=function(){var t,e;this.stop();for(t in this._handlers)this._core.$element.off(t,this._handlers[t]);for(e in Object.getOwnPropertyNames(this))"function"!=typeof this[e]&&(this[e]=null)},t.fn.vcOwlCarousel.Constructor.Plugins.autoplay=n}(window.Zepto||window.jQuery,window,document),function(t,e,i,s){"use strict";var n=function(e){this._core=e,this._initialized=!1,this._pages=[],this._controls={},this._templates=[],this.$element=this._core.$element,this._overrides={next:this._core.next,prev:this._core.prev,to:this._core.to},this._handlers={"prepared.owl.vccarousel":t.proxy(function(e){e.namespace&&this._core.settings.dotsData&&this._templates.push('<div class="'+this._core.settings.dotClass+'">'+t(e.content).find("[data-dot]").andSelf("[data-dot]").attr("data-dot")+"</div>")},this),"added.owl.vccarousel":t.proxy(function(t){t.namespace&&this._core.settings.dotsData&&this._templates.splice(t.position,0,this._templates.pop())},this),"remove.owl.vccarousel":t.proxy(function(t){t.namespace&&this._core.settings.dotsData&&this._templates.splice(t.position,1)},this),"changed.owl.vccarousel":t.proxy(function(t){t.namespace&&"position"==t.property.name&&this.draw()},this),"initialized.owl.vccarousel":t.proxy(function(t){t.namespace&&!this._initialized&&(this._core.trigger("initialize",null,"navigation"),this.initialize(),this.update(),this.draw(),this._initialized=!0,this._core.trigger("initialized",null,"navigation"))},this),"refreshed.owl.vccarousel":t.proxy(function(t){t.namespace&&this._initialized&&(this._core.trigger("refresh",null,"navigation"),this.update(),this.draw(),this._core.trigger("refreshed",null,"navigation"))},this)},this._core.options=t.extend({},n.Defaults,this._core.options),this.$element.on(this._handlers)};n.Defaults={nav:!1,navText:["prev","next"],navSpeed:!1,navElement:"div",navContainer:!1,navContainerClass:"owl-nav",navClass:["owl-prev","owl-next"],slideBy:1,dotClass:"owl-dot",dotsClass:"owl-dots",dots:!0,dotsEach:!1,dotsData:!1,dotsSpeed:!1,dotsContainer:!1},n.prototype.initialize=function(){var e,i=this._core.settings;this._controls.$relative=(i.navContainer?t(i.navContainer):t("<div>").addClass(i.navContainerClass).appendTo(this.$element)).addClass("disabled"),this._controls.$previous=t("<"+i.navElement+">").addClass(i.navClass[0]).html(i.navText[0]).prependTo(this._controls.$relative).on("click",t.proxy(function(t){this.prev(i.navSpeed)},this)),this._controls.$next=t("<"+i.navElement+">").addClass(i.navClass[1]).html(i.navText[1]).appendTo(this._controls.$relative).on("click",t.proxy(function(t){this.next(i.navSpeed)},this)),i.dotsData||(this._templates=[t("<div>").addClass(i.dotClass).append(t("<span>")).prop("outerHTML")]),this._controls.$absolute=(i.dotsContainer?t(i.dotsContainer):t("<div>").addClass(i.dotsClass).appendTo(this.$element)).addClass("disabled"),this._controls.$absolute.on("click","div",t.proxy(function(e){var s=t(e.target).parent().is(this._controls.$absolute)?t(e.target).index():t(e.target).parent().index();e.preventDefault(),this.to(s,i.dotsSpeed)},this));for(e in this._overrides)this._core[e]=t.proxy(this[e],this)},n.prototype.destroy=function(){var t,e,i,s;for(t in this._handlers)this.$element.off(t,this._handlers[t]);for(e in this._controls)this._controls[e].remove();for(s in this.overides)this._core[s]=this._overrides[s];for(i in Object.getOwnPropertyNames(this))"function"!=typeof this[i]&&(this[i]=null)},n.prototype.update=function(){var t,e,i,s=this._core.clones().length/2,n=s+this._core.items().length,o=this._core.maximum(!0),r=this._core.settings,a=r.center||r.autoWidth||r.dotsData?1:r.dotsEach||r.items;if("page"!==r.slideBy&&(r.slideBy=Math.min(r.slideBy,r.items)),r.dots||"page"==r.slideBy)for(this._pages=[],t=s,e=0,i=0;n>t;t++){if(e>=a||0===e){if(this._pages.push({start:Math.min(o,t-s),end:t-s+a-1}),Math.min(o,t-s)===o)break;e=0,++i}e+=this._core.mergers(this._core.relative(t))}},n.prototype.draw=function(){var e,i=this._core.settings,s=this._core.items().length<=i.items,n=this._core.relative(this._core.current()),o=i.loop||i.rewind;this._controls.$relative.toggleClass("disabled",!i.nav||s),i.nav&&(this._controls.$previous.toggleClass("disabled",!o&&n<=this._core.minimum(!0)),this._controls.$next.toggleClass("disabled",!o&&n>=this._core.maximum(!0))),this._controls.$absolute.toggleClass("disabled",!i.dots||s),i.dots&&(e=this._pages.length-this._controls.$absolute.children().length,i.dotsData&&0!==e?this._controls.$absolute.html(this._templates.join("")):e>0?this._controls.$absolute.append(new Array(e+1).join(this._templates[0])):0>e&&this._controls.$absolute.children().slice(e).remove(),this._controls.$absolute.find(".active").removeClass("active"),this._controls.$absolute.children().eq(t.inArray(this.current(),this._pages)).addClass("active"))},n.prototype.onTrigger=function(e){var i=this._core.settings;e.page={index:t.inArray(this.current(),this._pages),count:this._pages.length,size:i&&(i.center||i.autoWidth||i.dotsData?1:i.dotsEach||i.items)}},n.prototype.current=function(){var e=this._core.relative(this._core.current());return t.grep(this._pages,t.proxy(function(t,i){return t.start<=e&&t.end>=e},this)).pop()},n.prototype.getPosition=function(e){var i,s,n=this._core.settings;return"page"==n.slideBy?(i=t.inArray(this.current(),this._pages),s=this._pages.length,e?++i:--i,i=this._pages[(i%s+s)%s].start):(i=this._core.relative(this._core.current()),s=this._core.items().length,e?i+=n.slideBy:i-=n.slideBy),i},n.prototype.next=function(e){t.proxy(this._overrides.to,this._core)(this.getPosition(!0),e)},n.prototype.prev=function(e){t.proxy(this._overrides.to,this._core)(this.getPosition(!1),e)},n.prototype.to=function(e,i,s){var n;s?t.proxy(this._overrides.to,this._core)(e,i):(n=this._pages.length,t.proxy(this._overrides.to,this._core)(this._pages[(e%n+n)%n].start,i))},t.fn.vcOwlCarousel.Constructor.Plugins.Navigation=n}(window.Zepto||window.jQuery,window,document),function(t,e,i,s){"use strict";var n=function(i){this._core=i,this._hashes={},this.$element=this._core.$element,this._handlers={"initialized.owl.vccarousel":t.proxy(function(i){i.namespace&&"URLHash"===this._core.settings.startPosition&&t(e).trigger("hashchange.owl.navigation")},this),"prepared.owl.vccarousel":t.proxy(function(e){if(e.namespace){var i=t(e.content).find("[data-hash]").andSelf("[data-hash]").attr("data-hash");if(!i)return;this._hashes[i]=e.content}},this),"changed.owl.vccarousel":t.proxy(function(i){if(i.namespace&&"position"===i.property.name){var s=this._core.items(this._core.relative(this._core.current())),n=t.map(this._hashes,function(t,e){return t===s?e:null}).join();if(!n||e.location.hash.slice(1)===n)return;e.location.hash=n}},this)},this._core.options=t.extend({},n.Defaults,this._core.options),this.$element.on(this._handlers),t(e).on("hashchange.owl.navigation",t.proxy(function(t){var i=e.location.hash.substring(1),n=this._core.$stage.children(),o=this._hashes[i]&&n.index(this._hashes[i]);o!==s&&o!==this._core.current()&&this._core.to(this._core.relative(o),!1,!0)},this))};n.Defaults={URLhashListener:!1},n.prototype.destroy=function(){var i,s;t(e).off("hashchange.owl.navigation");for(i in this._handlers)this._core.$element.off(i,this._handlers[i]);for(s in Object.getOwnPropertyNames(this))"function"!=typeof this[s]&&(this[s]=null)},t.fn.vcOwlCarousel.Constructor.Plugins.Hash=n}(window.Zepto||window.jQuery,window,document),function(t,e,i,s){function n(e,i){var n=!1,o=e.charAt(0).toUpperCase()+e.slice(1);return t.each((e+" "+a.join(o+" ")+o).split(" "),function(t,e){return r[e]!==s?(n=i?e:!0,!1):void 0}),n}function o(t){return n(t,!0)}var r=t("<support>").get(0).style,a="Webkit Moz O ms".split(" "),h={transition:{end:{WebkitTransition:"webkitTransitionEnd",MozTransition:"transitionend",OTransition:"oTransitionEnd",transition:"transitionend"}},animation:{end:{WebkitAnimation:"webkitAnimationEnd",MozAnimation:"animationend",OAnimation:"oAnimationEnd",animation:"animationend"}}},l={csstransforms:function(){return!!n("transform")},csstransforms3d:function(){return!!n("perspective")},csstransitions:function(){return!!n("transition")},cssanimations:function(){return!!n("animation")}};l.csstransitions()&&(t.support.transition=new String(o("transition")),t.support.transition.end=h.transition.end[t.support.transition]),l.cssanimations()&&(t.support.animation=new String(o("animation")),t.support.animation.end=h.animation.end[t.support.animation]),l.csstransforms()&&(t.support.transform=new String(o("transform")),t.support.transform3d=l.csstransforms3d())}(window.Zepto||window.jQuery,window,document);
(function(){function e(){}function t(e,t){for(var n=e.length;n--;)if(e[n].listener===t)return n;return-1}function n(e){return function(){return this[e].apply(this,arguments)}}var i=e.prototype,r=this,o=r.EventEmitter;i.getListeners=function(e){var t,n,i=this._getEvents();if("object"==typeof e){t={};for(n in i)i.hasOwnProperty(n)&&e.test(n)&&(t[n]=i[n])}else t=i[e]||(i[e]=[]);return t},i.flattenListeners=function(e){var t,n=[];for(t=0;e.length>t;t+=1)n.push(e[t].listener);return n},i.getListenersAsObject=function(e){var t,n=this.getListeners(e);return n instanceof Array&&(t={},t[e]=n),t||n},i.addListener=function(e,n){var i,r=this.getListenersAsObject(e),o="object"==typeof n;for(i in r)r.hasOwnProperty(i)&&-1===t(r[i],n)&&r[i].push(o?n:{listener:n,once:!1});return this},i.on=n("addListener"),i.addOnceListener=function(e,t){return this.addListener(e,{listener:t,once:!0})},i.once=n("addOnceListener"),i.defineEvent=function(e){return this.getListeners(e),this},i.defineEvents=function(e){for(var t=0;e.length>t;t+=1)this.defineEvent(e[t]);return this},i.removeListener=function(e,n){var i,r,o=this.getListenersAsObject(e);for(r in o)o.hasOwnProperty(r)&&(i=t(o[r],n),-1!==i&&o[r].splice(i,1));return this},i.off=n("removeListener"),i.addListeners=function(e,t){return this.manipulateListeners(!1,e,t)},i.removeListeners=function(e,t){return this.manipulateListeners(!0,e,t)},i.manipulateListeners=function(e,t,n){var i,r,o=e?this.removeListener:this.addListener,s=e?this.removeListeners:this.addListeners;if("object"!=typeof t||t instanceof RegExp)for(i=n.length;i--;)o.call(this,t,n[i]);else for(i in t)t.hasOwnProperty(i)&&(r=t[i])&&("function"==typeof r?o.call(this,i,r):s.call(this,i,r));return this},i.removeEvent=function(e){var t,n=typeof e,i=this._getEvents();if("string"===n)delete i[e];else if("object"===n)for(t in i)i.hasOwnProperty(t)&&e.test(t)&&delete i[t];else delete this._events;return this},i.removeAllListeners=n("removeEvent"),i.emitEvent=function(e,t){var n,i,r,o,s=this.getListenersAsObject(e);for(r in s)if(s.hasOwnProperty(r))for(i=s[r].length;i--;)n=s[r][i],n.once===!0&&this.removeListener(e,n.listener),o=n.listener.apply(this,t||[]),o===this._getOnceReturnValue()&&this.removeListener(e,n.listener);return this},i.trigger=n("emitEvent"),i.emit=function(e){var t=Array.prototype.slice.call(arguments,1);return this.emitEvent(e,t)},i.setOnceReturnValue=function(e){return this._onceReturnValue=e,this},i._getOnceReturnValue=function(){return this.hasOwnProperty("_onceReturnValue")?this._onceReturnValue:!0},i._getEvents=function(){return this._events||(this._events={})},e.noConflict=function(){return r.EventEmitter=o,e},"function"==typeof define&&define.amd?define("eventEmitter/EventEmitter",[],function(){return e}):"object"==typeof module&&module.exports?module.exports=e:this.EventEmitter=e}).call(this),function(e){function t(t){var n=e.event;return n.target=n.target||n.srcElement||t,n}var n=document.documentElement,i=function(){};n.addEventListener?i=function(e,t,n){e.addEventListener(t,n,!1)}:n.attachEvent&&(i=function(e,n,i){e[n+i]=i.handleEvent?function(){var n=t(e);i.handleEvent.call(i,n)}:function(){var n=t(e);i.call(e,n)},e.attachEvent("on"+n,e[n+i])});var r=function(){};n.removeEventListener?r=function(e,t,n){e.removeEventListener(t,n,!1)}:n.detachEvent&&(r=function(e,t,n){e.detachEvent("on"+t,e[t+n]);try{delete e[t+n]}catch(i){e[t+n]=void 0}});var o={bind:i,unbind:r};"function"==typeof define&&define.amd?define("eventie/eventie",o):e.eventie=o}(this),function(e,t){"function"==typeof define&&define.amd?define(["eventEmitter/EventEmitter","eventie/eventie"],function(n,i){return t(e,n,i)}):"object"==typeof exports?module.exports=t(e,require("wolfy87-eventemitter"),require("eventie")):e.imagesLoaded=t(e,e.EventEmitter,e.eventie)}(window,function(e,t,n){function i(e,t){for(var n in t)e[n]=t[n];return e}function r(e){return"[object Array]"===d.call(e)}function o(e){var t=[];if(r(e))t=e;else if("number"==typeof e.length)for(var n=0,i=e.length;i>n;n++)t.push(e[n]);else t.push(e);return t}function s(e,t,n){if(!(this instanceof s))return new s(e,t);"string"==typeof e&&(e=document.querySelectorAll(e)),this.elements=o(e),this.options=i({},this.options),"function"==typeof t?n=t:i(this.options,t),n&&this.on("always",n),this.getImages(),a&&(this.jqDeferred=new a.Deferred);var r=this;setTimeout(function(){r.check()})}function f(e){this.img=e}function c(e){this.src=e,v[e]=this}var a=e.jQuery,u=e.console,h=u!==void 0,d=Object.prototype.toString;s.prototype=new t,s.prototype.options={},s.prototype.getImages=function(){this.images=[];for(var e=0,t=this.elements.length;t>e;e++){var n=this.elements[e];"IMG"===n.nodeName&&this.addImage(n);var i=n.nodeType;if(i&&(1===i||9===i||11===i))for(var r=n.querySelectorAll("img"),o=0,s=r.length;s>o;o++){var f=r[o];this.addImage(f)}}},s.prototype.addImage=function(e){var t=new f(e);this.images.push(t)},s.prototype.check=function(){function e(e,r){return t.options.debug&&h&&u.log("confirm",e,r),t.progress(e),n++,n===i&&t.complete(),!0}var t=this,n=0,i=this.images.length;if(this.hasAnyBroken=!1,!i)return this.complete(),void 0;for(var r=0;i>r;r++){var o=this.images[r];o.on("confirm",e),o.check()}},s.prototype.progress=function(e){this.hasAnyBroken=this.hasAnyBroken||!e.isLoaded;var t=this;setTimeout(function(){t.emit("progress",t,e),t.jqDeferred&&t.jqDeferred.notify&&t.jqDeferred.notify(t,e)})},s.prototype.complete=function(){var e=this.hasAnyBroken?"fail":"done";this.isComplete=!0;var t=this;setTimeout(function(){if(t.emit(e,t),t.emit("always",t),t.jqDeferred){var n=t.hasAnyBroken?"reject":"resolve";t.jqDeferred[n](t)}})},a&&(a.fn.imagesLoaded=function(e,t){var n=new s(this,e,t);return n.jqDeferred.promise(a(this))}),f.prototype=new t,f.prototype.check=function(){var e=v[this.img.src]||new c(this.img.src);if(e.isConfirmed)return this.confirm(e.isLoaded,"cached was confirmed"),void 0;if(this.img.complete&&void 0!==this.img.naturalWidth)return this.confirm(0!==this.img.naturalWidth,"naturalWidth"),void 0;var t=this;e.on("confirm",function(e,n){return t.confirm(e.isLoaded,n),!0}),e.check()},f.prototype.confirm=function(e,t){this.isLoaded=e,this.emit("confirm",this,t)};var v={};return c.prototype=new t,c.prototype.check=function(){if(!this.isChecked){var e=new Image;n.bind(e,"load",this),n.bind(e,"error",this),e.src=this.src,this.isChecked=!0}},c.prototype.handleEvent=function(e){var t="on"+e.type;this[t]&&this[t](e)},c.prototype.onload=function(e){this.confirm(!0,"onload"),this.unbindProxyEvents(e)},c.prototype.onerror=function(e){this.confirm(!1,"onerror"),this.unbindProxyEvents(e)},c.prototype.confirm=function(e,t){this.isConfirmed=!0,this.isLoaded=e,this.emit("confirm",this,t)},c.prototype.unbindProxyEvents=function(e){n.unbind(e.target,"load",this),n.unbind(e.target,"error",this)},s});
(function(){var t=[].indexOf||function(t){for(var e=0,n=this.length;e<n;e++){if(e in this&&this[e]===t)return e}return-1},e=[].slice;(function(t,e){if(typeof define==="function"&&define.amd){return define("waypoints",["jquery"],function(n){return e(n,t)})}else{return e(t.jQuery,t)}})(this,function(n,r){var i,o,l,s,f,u,a,c,h,d,p,y,v,w,g,m;i=n(r);c=t.call(r,"ontouchstart")>=0;s={horizontal:{},vertical:{}};f=1;a={};u="waypoints-context-id";p="resize.waypoints";y="scroll.waypoints";v=1;w="waypoints-waypoint-ids";g="waypoint";m="waypoints";o=function(){function t(t){var e=this;this.$element=t;this.element=t[0];this.didResize=false;this.didScroll=false;this.id="context"+f++;this.oldScroll={x:t.scrollLeft(),y:t.scrollTop()};this.waypoints={horizontal:{},vertical:{}};t.data(u,this.id);a[this.id]=this;t.bind(y,function(){var t;if(!(e.didScroll||c)){e.didScroll=true;t=function(){e.doScroll();return e.didScroll=false};return r.setTimeout(t,n[m].settings.scrollThrottle)}});t.bind(p,function(){var t;if(!e.didResize){e.didResize=true;t=function(){n[m]("refresh");return e.didResize=false};return r.setTimeout(t,n[m].settings.resizeThrottle)}})}t.prototype.doScroll=function(){var t,e=this;t={horizontal:{newScroll:this.$element.scrollLeft(),oldScroll:this.oldScroll.x,forward:"right",backward:"left"},vertical:{newScroll:this.$element.scrollTop(),oldScroll:this.oldScroll.y,forward:"down",backward:"up"}};if(c&&(!t.vertical.oldScroll||!t.vertical.newScroll)){n[m]("refresh")}n.each(t,function(t,r){var i,o,l;l=[];o=r.newScroll>r.oldScroll;i=o?r.forward:r.backward;n.each(e.waypoints[t],function(t,e){var n,i;if(r.oldScroll<(n=e.offset)&&n<=r.newScroll){return l.push(e)}else if(r.newScroll<(i=e.offset)&&i<=r.oldScroll){return l.push(e)}});l.sort(function(t,e){return t.offset-e.offset});if(!o){l.reverse()}return n.each(l,function(t,e){if(e.options.continuous||t===l.length-1){return e.trigger([i])}})});return this.oldScroll={x:t.horizontal.newScroll,y:t.vertical.newScroll}};t.prototype.refresh=function(){var t,e,r,i=this;r=n.isWindow(this.element);e=this.$element.offset();this.doScroll();t={horizontal:{contextOffset:r?0:e.left,contextScroll:r?0:this.oldScroll.x,contextDimension:this.$element.width(),oldScroll:this.oldScroll.x,forward:"right",backward:"left",offsetProp:"left"},vertical:{contextOffset:r?0:e.top,contextScroll:r?0:this.oldScroll.y,contextDimension:r?n[m]("viewportHeight"):this.$element.height(),oldScroll:this.oldScroll.y,forward:"down",backward:"up",offsetProp:"top"}};return n.each(t,function(t,e){return n.each(i.waypoints[t],function(t,r){var i,o,l,s,f;i=r.options.offset;l=r.offset;o=n.isWindow(r.element)?0:r.$element.offset()[e.offsetProp];if(n.isFunction(i)){i=i.apply(r.element)}else if(typeof i==="string"){i=parseFloat(i);if(r.options.offset.indexOf("%")>-1){i=Math.ceil(e.contextDimension*i/100)}}r.offset=o-e.contextOffset+e.contextScroll-i;if(r.options.onlyOnScroll&&l!=null||!r.enabled){return}if(l!==null&&l<(s=e.oldScroll)&&s<=r.offset){return r.trigger([e.backward])}else if(l!==null&&l>(f=e.oldScroll)&&f>=r.offset){return r.trigger([e.forward])}else if(l===null&&e.oldScroll>=r.offset){return r.trigger([e.forward])}})})};t.prototype.checkEmpty=function(){if(n.isEmptyObject(this.waypoints.horizontal)&&n.isEmptyObject(this.waypoints.vertical)){this.$element.unbind([p,y].join(" "));return delete a[this.id]}};return t}();l=function(){function t(t,e,r){var i,o;r=n.extend({},n.fn[g].defaults,r);if(r.offset==="bottom-in-view"){r.offset=function(){var t;t=n[m]("viewportHeight");if(!n.isWindow(e.element)){t=e.$element.height()}return t-n(this).outerHeight()}}this.$element=t;this.element=t[0];this.axis=r.horizontal?"horizontal":"vertical";this.callback=r.handler;this.context=e;this.enabled=r.enabled;this.id="waypoints"+v++;this.offset=null;this.options=r;e.waypoints[this.axis][this.id]=this;s[this.axis][this.id]=this;i=(o=t.data(w))!=null?o:[];i.push(this.id);t.data(w,i)}t.prototype.trigger=function(t){if(!this.enabled){return}if(this.callback!=null){this.callback.apply(this.element,t)}if(this.options.triggerOnce){return this.destroy()}};t.prototype.disable=function(){return this.enabled=false};t.prototype.enable=function(){this.context.refresh();return this.enabled=true};t.prototype.destroy=function(){delete s[this.axis][this.id];delete this.context.waypoints[this.axis][this.id];return this.context.checkEmpty()};t.getWaypointsByElement=function(t){var e,r;r=n(t).data(w);if(!r){return[]}e=n.extend({},s.horizontal,s.vertical);return n.map(r,function(t){return e[t]})};return t}();d={init:function(t,e){var r;if(e==null){e={}}if((r=e.handler)==null){e.handler=t}this.each(function(){var t,r,i,s;t=n(this);i=(s=e.context)!=null?s:n.fn[g].defaults.context;if(!n.isWindow(i)){i=t.closest(i)}i=n(i);r=a[i.data(u)];if(!r){r=new o(i)}return new l(t,r,e)});n[m]("refresh");return this},disable:function(){return d._invoke(this,"disable")},enable:function(){return d._invoke(this,"enable")},destroy:function(){return d._invoke(this,"destroy")},prev:function(t,e){return d._traverse.call(this,t,e,function(t,e,n){if(e>0){return t.push(n[e-1])}})},next:function(t,e){return d._traverse.call(this,t,e,function(t,e,n){if(e<n.length-1){return t.push(n[e+1])}})},_traverse:function(t,e,i){var o,l;if(t==null){t="vertical"}if(e==null){e=r}l=h.aggregate(e);o=[];this.each(function(){var e;e=n.inArray(this,l[t]);return i(o,e,l[t])});return this.pushStack(o)},_invoke:function(t,e){t.each(function(){var t;t=l.getWaypointsByElement(this);return n.each(t,function(t,n){n[e]();return true})});return this}};n.fn[g]=function(){var t,r;r=arguments[0],t=2<=arguments.length?e.call(arguments,1):[];if(d[r]){return d[r].apply(this,t)}else if(n.isFunction(r)){return d.init.apply(this,arguments)}else if(n.isPlainObject(r)){return d.init.apply(this,[null,r])}else if(!r){return n.error("jQuery Waypoints needs a callback function or handler option.")}else{return n.error("The "+r+" method does not exist in jQuery Waypoints.")}};n.fn[g].defaults={context:r,continuous:true,enabled:true,horizontal:false,offset:0,triggerOnce:false};h={refresh:function(){return n.each(a,function(t,e){return e.refresh()})},viewportHeight:function(){var t;return(t=r.innerHeight)!=null?t:i.height()},aggregate:function(t){var e,r,i;e=s;if(t){e=(i=a[n(t).data(u)])!=null?i.waypoints:void 0}if(!e){return[]}r={horizontal:[],vertical:[]};n.each(r,function(t,i){n.each(e[t],function(t,e){return i.push(e)});i.sort(function(t,e){return t.offset-e.offset});r[t]=n.map(i,function(t){return t.element});return r[t]=n.unique(r[t])});return r},above:function(t){if(t==null){t=r}return h._filter(t,"vertical",function(t,e){return e.offset<=t.oldScroll.y})},below:function(t){if(t==null){t=r}return h._filter(t,"vertical",function(t,e){return e.offset>t.oldScroll.y})},left:function(t){if(t==null){t=r}return h._filter(t,"horizontal",function(t,e){return e.offset<=t.oldScroll.x})},right:function(t){if(t==null){t=r}return h._filter(t,"horizontal",function(t,e){return e.offset>t.oldScroll.x})},enable:function(){return h._invoke("enable")},disable:function(){return h._invoke("disable")},destroy:function(){return h._invoke("destroy")},extendFn:function(t,e){return d[t]=e},_invoke:function(t){var e;e=n.extend({},s.vertical,s.horizontal);return n.each(e,function(e,n){n[t]();return true})},_filter:function(t,e,r){var i,o;i=a[n(t).data(u)];if(!i){return[]}o=[];n.each(i.waypoints[e],function(t,e){if(r(i,e)){return o.push(e)}});o.sort(function(t,e){return t.offset-e.offset});return n.map(o,function(t){return t.element})}};n[m]=function(){var t,n;n=arguments[0],t=2<=arguments.length?e.call(arguments,1):[];if(h[n]){return h[n].apply(null,t)}else{return h.aggregate.call(null,n)}};n[m].settings={resizeThrottle:100,scrollThrottle:30};return i.load(function(){return n[m]("refresh")})})}).call(this);
var vcGridStyleAll;!function($){vcGridStyleAll=function(grid){this.grid=grid,this.settings=grid.settings,this.filterValue=null,this.$el=!1,this.$content=!1,this.isLoading=!1,this.$loader=$('<div class="vc_grid-loading"></div>'),this.init()},vcGridStyleAll.prototype.init=function(){_.bindAll(this,"addItems","showItems")},vcGridStyleAll.prototype.render=function(){this.$el=this.grid.$el,this.$content=this.$el,this.setIsLoading(),this.grid.ajax({},this.addItems)},vcGridStyleAll.prototype.setIsLoading=function(){this.$content.append(this.$loader),this.isLoading=!0},vcGridStyleAll.prototype.unsetIsLoading=function(){this.isLoading=!1,this.$loader&&this.$loader.remove()},vcGridStyleAll.prototype.filter=function(filter){if(filter=_.isUndefined(filter)||"*"===filter?"":filter,this.filterValue==filter)return!1;var animation=this.$content.closest(".vc_grid-container").data("initial-loading-animation");vcGridSettings.addItemsAnimation=animation,this.$content.find(".vc_visible-item").removeClass("vc_visible-item "+vcGridSettings.addItemsAnimation+" animated"),this.filterValue=filter,_.defer(this.showItems)},vcGridStyleAll.prototype.showItems=function(){var $els=this.$content.find(".vc_grid-item"+this.filterValue);this.setIsLoading();var animation=this.$content.closest(".vc_grid-container").data("initial-loading-animation");vcGridSettings.addItemsAnimation=animation,$els.addClass("vc_visible-item "+("none"!==vcGridSettings.addItemsAnimation?vcGridSettings.addItemsAnimation+" animated":"")),this.unsetIsLoading(),$(window).trigger("grid:items:added",this.$el)},vcGridStyleAll.prototype.addItems=function(html){var els=$(html);this.$el.append(els),this.unsetIsLoading(),this.$content=els.find('[data-vc-grid-content="true"]'),this.grid.initFilter(),this.filter(),window.vc_prettyPhoto()}}(window.jQuery);var vcGridStyleLoadMore=null;!function($){vcGridStyleLoadMore=function(grid){this.grid=grid,this.settings=grid.settings,this.$loadMoreBtn=!1,this.$el=!1,this.filterValue=null,this.$content=!1,this.isLoading=!1,this.$loader=$('<div class="vc_grid-loading"></div>'),this.init()},vcGridStyleLoadMore.prototype.setIsLoading=function(){this.$loadMoreBtn&&this.$loadMoreBtn.hide(),this.isLoading=!0},vcGridStyleLoadMore.prototype.unsetIsLoading=function(){this.isLoading=!1,this.setLoadMoreBtn()},vcGridStyleLoadMore.prototype.init=function(){_.bindAll(this,"addItems")},vcGridStyleLoadMore.prototype.render=function(){this.$el=this.grid.$el,this.$content=this.$el,this.setIsLoading(),this.$content.append(this.$loader),this.grid.ajax({},this.addItems)},vcGridStyleLoadMore.prototype.showItems=function(){var $els=this.$content.find(".vc_grid_filter-item:not(.vc_visible-item):lt("+this.settings.items_per_page+")");this.setIsLoading();var animation=this.$content.closest(".vc_grid-container").data("initial-loading-animation");vcGridSettings.addItemsAnimation=animation,$els.addClass("vc_visible-item "+vcGridSettings.addItemsAnimation+" animated"),this.unsetIsLoading(),$(window).trigger("grid:items:added",this.$el)},vcGridStyleLoadMore.prototype.filter=function(filter){if(filter=_.isUndefined(filter)||"*"===filter?"":filter,this.filterValue==filter)return!1;this.$content.closest(".vc_grid-container").data("initial-loading-animation");this.$content.find(".vc_visible-item, .vc_grid_filter-item").removeClass("vc_visible-item vc_grid_filter-item "),this.filterValue=filter,this.$content.find(".vc_grid-item"+this.filterValue).addClass("vc_grid_filter-item"),this.showItems()},vcGridStyleLoadMore.prototype.addItems=function(html){var els=$(html);this.$el.append(els),this.unsetIsLoading(),this.$content=els.find('[data-vc-grid-content="true"]'),this.$loadMoreBtn=els.find('[data-vc-grid-load-more-btn="true"] .vc_btn3'),this.$loadMoreBtn.length||(this.$loadMoreBtn=els.find('[data-vc-grid-load-more-btn="true"] .vc_btn'));var self=this;this.$loadMoreBtn.click(function(e){e.preventDefault(),self.showItems()}),this.$loadMoreBtn.hide(),this.grid.initFilter(),this.filter(),this.$loader.remove(),window.vc_prettyPhoto()},vcGridStyleLoadMore.prototype.setLoadMoreBtn=function(){$('.vc_grid_filter-item:not(".vc_visible-item")',this.$content).length&&$(".vc_grid_filter-item",this.$content).length?this.$loadMoreBtn&&this.$loadMoreBtn.show():this.$loadMoreBtn&&this.$loadMoreBtn.hide()}}(window.jQuery);var vcGridStyleLazy=null;!function($){$.waypoints("extendFn","vc_grid-infinite",function(options){var opts,el=this;return opts=$.extend({},$.fn.waypoint.defaults,{container:"auto",items:".infinite-item",offset:"bottom-in-view",handle:{load:function(opts){}}},options),"auto"===opts.container?el:$(opts.container,el),opts.handler=function(direction){var $this;"down"!==direction&&"right"!==direction||($this=$(this),$this.waypoint("destroy"),opts.handle.load.call(this,opts))},this.waypoint(opts)}),vcGridStyleLazy=function(grid){this.grid=grid,this.settings=grid.settings,this.$el=!1,this.filterValue=null,this.$content=!1,this.isLoading=!1,this.$loader=$('<div class="vc_grid-loading"></div>'),this.init()},vcGridStyleLazy.prototype.setIsLoading=function(){this.$content.append(this.$loader),this.isLoading=!0},vcGridStyleLazy.prototype.unsetIsLoading=function(){this.isLoading=!1,this.$loader&&this.$loader.remove()},vcGridStyleLazy.prototype.init=function(){_.bindAll(this,"addItems","showItems")},vcGridStyleLazy.prototype.render=function(){this.$el=this.grid.$el,this.$content=this.$el,this.setIsLoading(),this.grid.ajax({},this.addItems)},vcGridStyleLazy.prototype.showItems=function(){var $els=this.$content.find(".vc_grid_filter-item:not(.vc_visible-item):lt("+this.settings.items_per_page+")");this.setIsLoading();var animation=this.$content.closest(".vc_grid-container").data("initial-loading-animation");vcGridSettings.addItemsAnimation=animation,$els.addClass("vc_visible-item "+vcGridSettings.addItemsAnimation+" animated"),this.unsetIsLoading(),$(window).trigger("grid:items:added",this.$el)},vcGridStyleLazy.prototype.filter=function(filter){if(filter=_.isUndefined(filter)||"*"===filter?"":filter,this.filterValue==filter)return!1;var animation=this.$content.closest(".vc_grid-container").data("initial-loading-animation");vcGridSettings.addItemsAnimation=animation,this.$content.find(".vc_visible-item, .vc_grid_filter-item").removeClass("vc_visible-item vc_grid_filter-item "+("none"!==vcGridSettings.addItemsAnimation?vcGridSettings.addItemsAnimation+" animated":"")),this.filterValue=filter,this.$content.find(".vc_grid-item"+this.filterValue).addClass("vc_grid_filter-item"),_.defer(this.showItems),this.initScroll()},vcGridStyleLazy.prototype.addItems=function(html){var els=$(html);this.$el.append(els),this.unsetIsLoading(),this.$content=els.find('[data-vc-grid-content="true"]'),this.grid.initFilter(),this.filter(),window.vc_prettyPhoto()},vcGridStyleLazy.prototype.initScroll=function(){var self=this;this.$content.unbind("waypoint").waypoint("vc_grid-infinite",{container:"auto",items:".vc_grid-item",handle:{load:function(opts){self.showItems(),self.checkNext(opts)}}})},vcGridStyleLazy.prototype.checkNext=function(opts){if(this.$content.find('.vc_grid_filter-item:not(".vc_visible-item")').length){var fn,self=this;fn=function(){return self.$content.waypoint(opts)},_.defer(fn)}}}(window.jQuery);var vcGridStylePagination=null;!function($){vcGridStylePagination=function(grid){this.grid=grid,this.settings=grid.settings,this.$el=!1,this.$content=!1,this.filterValue=null,this.isLoading=!1,this.htmlCache=!1,this.$loader=$('<div class="vc_grid-loading"></div>'),this.$firstSlideItems,this.init()},vcGridStylePagination.prototype.init=function(){_.bindAll(this,"addItems","initCarousel")},vcGridStylePagination.prototype.setIsLoading=function(){this.$loader.show(),this.isLoading=!0},vcGridStylePagination.prototype.unsetIsLoading=function(){this.isLoading=!1,this.$loader.hide()},vcGridStylePagination.prototype.render=function(){this.$el=this.grid.$el,this.$content=this.$el,this.$content.append(this.$loader),this.setIsLoading(),this.grid.ajax({},this.addItems)},vcGridStylePagination.prototype.filter=function(filter){if(filter=_.isUndefined(filter)||"*"===filter?"":filter,this.filterValue==filter)return!1;var $html;this.$content.data("owl.vccarousel")&&(this.$content.off("initialized.owl.vccarousel"),this.$content.off("changed.owl.vccarousel"),this.$content.data("vcPagination")&&this.$content.data("vcPagination").twbsPagination("destroy"),this.$content.data("owl.vccarousel").destroy()),this.$content.empty(),$html=$(".vc_grid-item",this.htmlCache),""!==filter&&($html=$html.filter(filter)),this.filterValue=filter,this.buildSlides($html.addClass("vc_visible-item"))},vcGridStylePagination.prototype.buildSlides=function($html){var i,j,tempArray,chunk=parseInt(this.settings.items_per_page);for(i=0,j=$html.length;i<j;i+=chunk)tempArray=$html.slice(i,i+chunk),$('<div class="vc_pageable-slide-wrapper">').append($(tempArray)).appendTo(this.$content);this.$content.find(".vc_pageable-slide-wrapper:first").imagesLoaded(this.initCarousel)},vcGridStylePagination.prototype.addItems=function(html){this.$el.append(html),!1===this.htmlCache&&(this.htmlCache=html),this.unsetIsLoading(),this.$content=this.$el.find('[data-vc-pageable-content="true"]'),this.$content.addClass("owl-carousel vc_grid-owl-theme"),this.grid.initFilter(),this.filter(),window.vc_prettyPhoto()},vcGridStylePagination.prototype.initCarousel=function(){if($.fn.vcOwlCarousel){var $vcCarousel,that=this;$vcCarousel=this.$content.data("owl.vccarousel"),$vcCarousel&&$vcCarousel.destroy(),this.$content.on("initialized.owl.vccarousel",function(event){var $carousel=event.relatedTarget,items=$carousel.items(),animation=that.$content.closest(".vc_grid-container").data("initial-loading-animation");if(items.forEach(function(i){jQuery(i).find(".vc_grid-item").addClass(animation+" animated")}),that.settings.paging_design.indexOf("pagination")>-1){var itemsCount=$carousel.items().length,$pagination=$("<div></div>").addClass("vc_grid-pagination").appendTo(that.$el);$pagination.twbsPagination({totalPages:itemsCount,visiblePages:that.settings.visible_pages,onPageClick:function(event,page){$carousel.to(page-1)},paginationClass:"vc_grid-pagination-list vc_grid-"+that.settings.paging_design+" vc_grid-pagination-color-"+that.settings.paging_color,nextClass:"vc_grid-next",first:20<itemsCount&&" ",last:20<itemsCount&&" ",prev:5<itemsCount&&" ",next:5<itemsCount&&" ",prevClass:"vc_grid-prev",lastClass:"vc_grid-last",loop:that.settings.loop,firstClass:"vc_grid-first",pageClass:"vc_grid-page",activeClass:"vc_grid-active",disabledClass:"vc_grid-disabled"}),$(this).data("vcPagination",$pagination),that.$content.on("changed.owl.vccarousel",function(event){var $pagination=$(this).data("vcPagination"),$pag_object=$pagination.data("twbsPagination");$pag_object.render($pag_object.getPages(1+event.page.index)),$pag_object.setupEvents()}),window.vc_prettyPhoto()}}).vcOwlCarousel({items:1,loop:this.settings.loop,margin:10,nav:!0,navText:["",""],navContainerClass:"vc_grid-owl-nav vc_grid-owl-nav-color-"+this.settings.arrows_color,dotClass:"vc_grid-owl-dot",dotsClass:"vc_grid-owl-dots vc_grid-"+this.settings.paging_design+" vc_grid-owl-dots-color-"+this.settings.paging_color,navClass:["vc_grid-owl-prev "+this.settings.arrows_design+" vc_grid-nav-prev-"+this.settings.arrows_position,"vc_grid-owl-next "+this.settings.arrows_design.replace("_left","_right")+" vc_grid-nav-next-"+this.settings.arrows_position],animateIn:"none"!==this.settings.animation_in&&this.settings.animation_in,animateOut:"none"!==this.settings.animation_out&&this.settings.animation_out,autoHeight:!0,autoplay:!0===this.settings.auto_play,autoplayTimeout:this.settings.speed,callbacks:!0,onTranslated:function(){setTimeout(function(){$(window).trigger("grid:items:added",that.$el)},750)},onRefreshed:function(){setTimeout(function(){$(window).trigger("grid:items:added",that.$el)},750)}})}}}(window.jQuery);var vcGridStyleAllMasonry;!function($){vcGridStyleAllMasonry=function(grid){this.grid=grid,this.settings=grid.settings,this.filterValue=null,this.$el=!1,this.$content=!1,this.isLoading=!1,this.filtered=!1,this.$loader=$('<div class="vc_grid-loading"></div>'),this.masonryEnabled=!1,_.bindAll(this,"setMasonry"),this.init()},vcGridStyleAllMasonry.prototype=_.extend({},vcGridStyleAll.prototype,{showItems:function(){var $els=this.$content.find(".vc_grid-item"+this.filterValue),self=this;this.setIsLoading(),$els.imagesLoaded(function(){$els.addClass("vc_visible-item"),self.setItems($els),self.filtered&&(self.filtered=!1,self.setMasonry()),self.unsetIsLoading(),window.vc_prettyPhoto(),$(window).trigger("grid:items:added",self.$el)})},filter:function(filter){if(filter=_.isUndefined(filter)||"*"===filter?"":filter,this.filterValue==filter)return!1;this.filterValue=filter,this.$content.data("masonry")&&this.$content.masonry("destroy"),this.masonryEnabled=!1,this.$content.find(".vc_visible-item").removeClass("vc_visible-item"),this.$content.find(".vc_grid-item"+this.filterValue),this.filtered=!0,$(window).resize(this.setMasonry),this.setMasonry(),this.showItems()},setIsLoading:function(){this.$el.append(this.$loader),this.isLoading=!0},unsetIsLoading:function(){this.isLoading=!1,this.$loader&&this.$loader.remove()},setItems:function(els){this.masonryEnabled?this.$content.masonry("appended",els):this.setMasonry()},setMasonry:function(){var animation,settings;window.innerWidth<vcGridSettings.mobileWindowWidth?(this.$content.data("masonry")&&this.$content.masonry("destroy"),this.masonryEnabled=!1):this.masonryEnabled?(this.$content.masonry("reloadItems"),this.$content.masonry("layout")):(animation=this.$content.closest(".vc_grid-container").data("initial-loading-animation"),settings={itemSelector:".vc_visible-item",isResizeBound:!1},"none"==animation?(settings.hiddenStyle={visibility:"hidden"},settings.visibleStyle={visibility:"visible"}):"fadeIn"==animation?(settings.hiddenStyle={opacity:0},settings.visibleStyle={opacity:1}):(settings.hiddenStyle={opacity:0,transform:"scale(0.001)"},settings.visibleStyle={opacity:1,transform:"scale(1)"}),this.$content.masonry(settings),this.masonryEnabled=!0)}})}(window.jQuery);var vcGridStyleLazyMasonry;!function($){vcGridStyleLazyMasonry=function(grid){this.grid=grid,this.settings=grid.settings,this.$el=!1,this.filterValue=null,this.filtered=!1,this.$content=!1,this.isLoading=!1,this.$loader=$('<div class="vc_grid-loading"></div>'),this.masonryEnabled=!1,_.bindAll(this,"setMasonry"),this.init()},vcGridStyleLazyMasonry.prototype=_.extend({},vcGridStyleLazy.prototype,{showItems:function(){if(!0===this.isLoading)return!1;this.setIsLoading();var $els=this.$content.find(".vc_grid_filter-item:not(.vc_visible-item):lt("+this.settings.items_per_page+")"),self=this;$els.imagesLoaded(function(){$els.addClass("vc_visible-item"),self.setItems($els),self.filtered&&(self.filtered=!1,self.setMasonry(),self.initScroll(),window.vc_prettyPhoto()),self.unsetIsLoading(),$(window).trigger("grid:items:added",self.$el)})},setIsLoading:function(){this.$el.append(this.$loader),this.isLoading=!0},filter:function(filter){if(filter=_.isUndefined(filter)||"*"===filter?"":filter,this.filterValue==filter)return!1;this.$content.data("masonry")&&this.$content.masonry("destroy"),this.masonryEnabled=!1,this.$content.find(".vc_visible-item, .vc_grid_filter-item").removeClass("vc_visible-item vc_grid_filter-item"),this.filterValue=filter,this.$content.find(".vc_grid-item"+this.filterValue).addClass("vc_grid_filter-item"),this.filtered=!0,$(window).resize(this.setMasonry),this.setMasonry(),_.defer(this.showItems)},setItems:function(els){this.masonryEnabled?this.$content.masonry("appended",els):this.setMasonry()},setMasonry:function(){var animation,settings;window.innerWidth<vcGridSettings.mobileWindowWidth?(this.$content.data("masonry")&&this.$content.masonry("destroy"),this.masonryEnabled=!1):this.masonryEnabled?(this.$content.masonry("reloadItems"),this.$content.masonry("layout")):(animation=this.$content.closest(".vc_grid-container").data("initial-loading-animation"),settings={itemSelector:".vc_visible-item",isResizeBound:!1},"none"==animation?(settings.hiddenStyle={visibility:"hidden"},settings.visibleStyle={visibility:"visible"}):"fadeIn"==animation?(settings.hiddenStyle={opacity:0},settings.visibleStyle={opacity:1}):(settings.hiddenStyle={opacity:0,transform:"scale(0.001)"},settings.visibleStyle={opacity:1,transform:"scale(1)"}),this.$content.masonry(settings),this.masonryEnabled=!0)}})}(window.jQuery);var vcGridStyleLoadMoreMasonry;!function($){vcGridStyleLoadMoreMasonry=function(grid){this.grid=grid,this.settings=grid.settings,this.$loadMoreBtn=!1,this.$el=!1,this.filterValue=null,this.$content=!1,this.isLoading=!1,this.filtered=!1,this.$loader=$('<div class="vc_grid-loading"></div>'),this.masonryEnabled=!1,_.bindAll(this,"setMasonry"),this.init()},vcGridStyleLoadMoreMasonry.prototype=_.extend({},vcGridStyleLoadMore.prototype,{showItems:function(){if(!0===this.isLoading)return!1;this.setIsLoading();var $els=this.$content.find(".vc_grid_filter-item:not(.vc_visible-item):lt("+this.settings.items_per_page+")"),self=this;$els.imagesLoaded(function(){$els.addClass("vc_visible-item"),self.setItems($els),self.filtered&&(self.filtered=!1,self.setMasonry(),window.vc_prettyPhoto()),self.unsetIsLoading(),$(window).trigger("grid:items:added",self.$el)})},filter:function(filter){if(filter=_.isUndefined(filter)||"*"===filter?"":filter,this.filterValue==filter)return!1;this.$content.data("masonry")&&this.$content.masonry("destroy"),this.masonryEnabled=!1,this.$content.find(".vc_visible-item, .vc_grid_filter-item").removeClass("vc_visible-item vc_grid_filter-item"),this.filterValue=filter,this.$content.find(".vc_grid-item"+this.filterValue).addClass("vc_grid_filter-item"),this.filtered=!0,$(window).resize(this.setMasonry),this.setMasonry(),this.showItems()},setIsLoading:function(){this.$el.append(this.$loader),this.$loadMoreBtn&&this.$loadMoreBtn.hide(),this.isLoading=!0},unsetIsLoading:function(){this.isLoading=!1,this.$loader&&this.$loader.remove(),this.setLoadMoreBtn()},setItems:function(els){this.masonryEnabled?this.$content.masonry("appended",els):this.setMasonry()},setMasonry:function(){var animation,settings;window.innerWidth<vcGridSettings.mobileWindowWidth?(this.$content.data("masonry")&&this.$content.masonry("destroy"),this.masonryEnabled=!1):this.masonryEnabled?(this.$content.masonry("reloadItems"),this.$content.masonry("layout")):(animation=this.$content.closest(".vc_grid-container").data("initial-loading-animation"),settings={itemSelector:".vc_visible-item",isResizeBound:!1},"none"==animation?(settings.hiddenStyle={visibility:"hidden"},settings.visibleStyle={visibility:"visible"}):"fadeIn"==animation?(settings.hiddenStyle={opacity:0},settings.visibleStyle={opacity:1}):(settings.hiddenStyle={opacity:0,transform:"scale(0.001)"},settings.visibleStyle={opacity:1,transform:"scale(1)"}),this.$content.masonry(settings),this.masonryEnabled=!0)}})}(window.jQuery);var VcGrid,vcGridSettings={addItemsAnimation:"zoomIn",mobileWindowWidth:768,itemAnimationSpeed:1e3,itemAnimationDelay:[],clearAnimationDelays:function(){_.each(this.itemAnimationDelay,function(id){window.clearTimeout(id)}),this.itemAnimationDelay=[]}};!function($){"use strict";function Plugin(option){return this.each(function(){var $this=$(this),data=$this.data("vcGrid");data||$this.data("vcGrid",data=new VcGrid(this)),"string"==typeof option&&data[option]()})}VcGrid=function(el){this.$el=$(el),this.settings={},this.$filter=!1,this.gridBuilder=!1,this.init()},VcGrid.prototype.init=function(){_.bindAll(this,"filterItems","filterItemsDropdown"),this.setSettings(),this.initStyle(),this.initHover(),this.initZoneLink()},VcGrid.prototype.setSettings=function(){this.settings=$.extend({visible_pages:5},this.$el.data("vcGridSettings")||{})},VcGrid.prototype.initStyle=function(){var styleObject=!!this.settings.style&&$.camelCase("vc-grid-style-"+this.settings.style);styleObject&&!_.isUndefined(window[styleObject])&&window[styleObject].prototype.render&&(this.gridBuilder=new window[styleObject](this),this.gridBuilder.render())},VcGrid.prototype.initFilter=function(){this.$filter=this.$el.find("[data-vc-grid-filter]"),this.$filterDropdown=this.$el.find("[data-vc-grid-filter-select]"),this.$filter.length&&this.$filter.find(".vc_grid-filter-item").unbind("click").click(this.filterItems),this.$filterDropdown.length&&this.$filterDropdown.unbind("change").change(this.filterItemsDropdown)},VcGrid.prototype.initHover=function(){this.$el.on("mouseover",".vc_grid-item-mini",function(){$(this).addClass("vc_is-hover")}).on("mouseleave",".vc_grid-item-mini",function(){$(this).removeClass("vc_is-hover")})},VcGrid.prototype.initZoneLink=function(){window.vc_iframe?(this.$el.on("click.zonePostLink","[data-vc-link]",function(){var href=($(this),$(this).data("vcLink"));window.open(href)}),this.$el.on("click",".vc_gitem-link",function(e){var $this=$(this);e.preventDefault(),!$this.hasClass("vc-gitem-link-ajax")&&window.open($this.attr("href"))})):(this.$el.on("click.zonePostLink","[data-vc-link]",function(){var $this=$(this),href=$(this).data("vcLink");"_blank"===$this.data("vcTarget")?window.open(href):window.location.href=href}),this.$el.on("click",".vc_gitem-link",function(e){var $this=$(this);if(e.preventDefault(),$this.hasClass("vc-gitem-link-ajax")){var httpRequest=new XMLHttpRequest;$this.addClass("vc-spinner"),httpRequest.open("GET",$this.attr("href"),!0),httpRequest.send();var removeSpinner=function(){$this.removeClass("vc-spinner vc-spinner-complete vc-spinner-failed")},timeout=0;httpRequest.onreadystatechange=function(){httpRequest.readyState===XMLHttpRequest.DONE&&(200===httpRequest.status?($this.addClass("vc-spinner-complete"),timeout&&(window.clearTimeout(timeout),timeout=0),timeout=window.setTimeout(removeSpinner,500)):($this.addClass("vc-spinner-failed"),timeout&&(window.clearTimeout(timeout),timeout=0),timeout=window.setTimeout(removeSpinner,500)))}}else"_blank"===$this.attr("_target")?window.open($this.attr("href")):window.location.href=$this.attr("href")}))},VcGrid.prototype.initHover_old=function(){this.$el.on("mouseover",".vc_grid-item",function(){var $this=$(this);$this.hasClass("vc_is-hover")||(vcGridSettings.clearAnimationDelays(),$this.addClass("vc_is-hover vc_is-animated"),$this.find(".vc_grid-item-row-animate").each(function(){var $animate=$(this),animationIn=$animate.data("animationIn"),animationOut=$animate.data("animationOut");$animate.removeClass(animationOut).addClass(animationIn),vcGridSettings.itemAnimationDelay.push(_.delay(function(){$animate.removeClass(animationIn),$this.removeClass("vc_is-animated")},vcGridSettings.itemAnimationSpeed))}))}).on("mouseleave",".vc_grid-item",function(){var $this=$(this);vcGridSettings.clearAnimationDelays(),$this.addClass("vc_is-animated").removeClass("vc_is-hover"),$this.find(".vc_grid-item-row-animate").each(function(){var $animate=$this.find(".vc_grid-item-row-animate"),animationOut=$animate.data("animationOut"),animationIn=$animate.data("animationIn");$animate.addClass(animationOut),vcGridSettings.itemAnimationDelay.push(_.delay(function(){$animate.removeClass(animationOut+" "+animationIn),$this.removeClass("vc_is-animated")},vcGridSettings.itemAnimationSpeed-1))})})},VcGrid.prototype.filterItems=function(e){var $control=(this.style&&$.camelCase("filter-"+this.style),$(e.currentTarget).find("[data-vc-grid-filter-value]")),filter=$control.data("vcGridFilterValue");if(e&&e.preventDefault(),$control.hasClass("vc_active"))return!1;this.$filter.find(".vc_active").removeClass("vc_active"),this.$filterDropdown.find(".vc_active").removeClass("vc_active"),this.$filterDropdown.find('[value="'+filter+'"]').addClass("vc_active").attr("selected","selected"),$control.parent().addClass("vc_active"),this.gridBuilder.filter(filter)},VcGrid.prototype.filterItemsDropdown=function(e){var $control=this.$filterDropdown.find(":selected"),filter=$control.val();if($control.hasClass("vc_active"))return!1;this.$filterDropdown.find(".vc_active").removeClass("vc_active"),this.$filter.find(".vc_active").removeClass("vc_active"),this.$filter.find('[data-vc-grid-filter-value="'+filter+'"]').parent().addClass("vc_active"),$control.addClass("vc_active"),this.gridBuilder.filter(filter)},VcGrid.prototype.ajax=function(data,callback){var requestData;_.isUndefined(data)&&(data={}),requestData=_.extend({action:"vc_get_vc_grid_data",vc_action:"vc_get_vc_grid_data",tag:this.settings.tag,data:this.settings,vc_post_id:this.$el.data("vcPostId"),_vcnonce:this.$el.data("vcPublicNonce")},data),$.ajax({type:"POST",dataType:"html",url:this.$el.data("vcRequest"),data:requestData}).done(callback)},$.fn.vcGrid=Plugin,$.fn.vcGrid.Constructor=VcGrid,$(document).ready(function(){$("[data-vc-grid-settings]").vcGrid()})}(window.jQuery);
!function(){var e=void 0,t=void 0;!function t(n,r,i){function o(a,c){if(!r[a]){if(!n[a]){var u="function"==typeof e&&e;if(!c&&u)return u(a,!0);if(s)return s(a,!0);var f=new Error("Cannot find module '"+a+"'");throw f.code="MODULE_NOT_FOUND",f}var l=r[a]={exports:{}};n[a][0].call(l.exports,function(e){var t=n[a][1][e];return o(t||e)},l,l.exports,t,n,r,i)}return r[a].exports}for(var s="function"==typeof e&&e,a=0;a<i.length;a++)o(i[a]);return o}({1:[function(e,t,n){"use strict";function r(e){var t="animated"===u.auto_scroll,n={behavior:t?"smooth":"instant"};e.element.scrollIntoView(n)}function i(e,t,n){return function(){var r=this.value.trim(),i="radio"!==this.getAttribute("type")&&"checked"!==this.getAttribute("type")||this.checked,o=i&&(r===t&&""!==t||""===t&&r.length>0);e.style.display=n?o?"":"none":o?"none":""}}function o(){var e=this,t=e.form.querySelectorAll("[data-show-if], [data-hide-if]"),n=(e.getAttribute("name")||"").toLowerCase();[].forEach.call(t,function(t){var r=!!t.getAttribute("data-show-if"),o=r?t.getAttribute("data-show-if").split(":"):t.getAttribute("data-hide-if").split(":"),s=o[0],a=o[1]||"";if(n===s.toLowerCase()){i(t,a,r).call(e)}})}var s=window.mc4wp||{},a=e("gator"),c=e("./forms/forms.js"),u=window.mc4wp_forms_config||{};if(a(document.body).on("keyup",".mc4wp-form input, .mc4wp-form textarea, .mc4wp-form select",o),a(document.body).on("change",".mc4wp-form input, .mc4wp-form textarea, .mc4wp-form select",o),window.addEventListener("load",function(){[].forEach.call(document.querySelectorAll(".mc4wp-form input, .mc4wp-form textarea, .mc4wp-form select"),function(e){o.call(e)})}),a(document.body).on("submit",".mc4wp-form",function(e){var t=c.getByElement(e.target||e.srcElement);c.trigger("submit",[t,e]),c.trigger(t.id+".submit",[t,e])}),a(document.body).on("focus",".mc4wp-form",function(e){var t=c.getByElement(e.target||e.srcElement);t.started||(c.trigger("started",[t,e]),c.trigger(t.id+".started",[t,e]),t.started=!0)}),a(document.body).on("change",".mc4wp-form",function(e){var t=c.getByElement(e.target||e.srcElement);c.trigger("change",[t,e]),c.trigger(t.id+".change",[t,e])}),s.listeners){for(var f=s.listeners,l=0;l<f.length;l++)c.on(f[l].event,f[l].callback);delete s.listeners}if(s.forms=c,u.submitted_form){var h=u.submitted_form,d=document.getElementById(h.element_id),m=c.getByElement(d);!function(e,t,n,i){var o=document.body.clientHeight,s=Date.now();n&&e.setData(i),u.auto_scroll&&r(e),window.addEventListener("load",function(){var a=Date.now()-s;u.auto_scroll&&a<800&&document.body.clientHeight!==o&&r(e),c.trigger("submitted",[e]),c.trigger(e.id+".submitted",[e]),n?(c.trigger("error",[e,n]),c.trigger(e.id+".error",[e,n])):(c.trigger("success",[e,i]),c.trigger(e.id+".success",[e,i]),c.trigger(t+"d",[e,i]),c.trigger(e.id+"."+t+"d",[e,i]))})}(m,h.action,h.errors,h.data)}window.mc4wp=s},{"./forms/forms.js":3,gator:5}],2:[function(e,t,n){"use strict";var r=e("form-serialize"),i=e("populate.js"),o=function(e,t){this.id=e,this.element=t||document.createElement("form"),this.name=this.element.getAttribute("data-name")||"Form #"+this.id,this.errors=[],this.started=!1};o.prototype.setData=function(e){try{i(this.element,e)}catch(e){console.error(e)}},o.prototype.getData=function(){return r(this.element,{hash:!0,empty:!0})},o.prototype.getSerializedData=function(){return r(this.element,{hash:!1,empty:!0})},o.prototype.setResponse=function(e){this.element.querySelector(".mc4wp-response").innerHTML=e},o.prototype.reset=function(){this.setResponse(""),this.element.querySelector(".mc4wp-form-fields").style.display="",this.element.reset()},t.exports=o},{"form-serialize":4,"populate.js":6}],3:[function(e,t,n){"use strict";function r(e){for(var t=0;t<f.length;t++)if(f[t].id==e)return f[t];return o(document.querySelector(".mc4wp-form-"+e),e)}function i(e){for(var t=e.form||e,n=0;n<f.length;n++)if(f[n].element==t)return f[n];return o(t)}function o(e,t){t=t||parseInt(e.getAttribute("data-id"))||0;var n=new c(t,e);return f.push(n),n}function s(){return f}var a=e("wolfy87-eventemitter"),c=e("./form.js"),u=new a,f=[];t.exports={all:s,get:r,getByElement:i,on:u.on.bind(u),trigger:u.trigger.bind(u),off:u.off.bind(u)}},{"./form.js":2,"wolfy87-eventemitter":7}],4:[function(e,t,n){function r(e,t){"object"!=typeof t?t={hash:!!t}:void 0===t.hash&&(t.hash=!0);for(var n=t.hash?{}:"",r=t.serializer||(t.hash?s:a),i=e&&e.elements?e.elements:[],o=Object.create(null),f=0;f<i.length;++f){var l=i[f];if((t.disabled||!l.disabled)&&l.name&&(u.test(l.nodeName)&&!c.test(l.type))){var h=l.name,d=l.value;if("checkbox"!==l.type&&"radio"!==l.type||l.checked||(d=void 0),t.empty){if("checkbox"!==l.type||l.checked||(d=""),"radio"===l.type&&(o[l.name]||l.checked?l.checked&&(o[l.name]=!0):o[l.name]=!1),void 0==d&&"radio"==l.type)continue}else if(!d)continue;if("select-multiple"!==l.type)n=r(n,h,d);else{d=[];for(var m=l.options,p=!1,v=0;v<m.length;++v){var g=m[v],y=t.empty&&!g.value,w=g.value||y;g.selected&&w&&(p=!0,n=t.hash&&"[]"!==h.slice(h.length-2)?r(n,h+"[]",g.value):r(n,h,g.value))}!p&&t.empty&&(n=r(n,h,""))}}}if(t.empty)for(var h in o)o[h]||(n=r(n,h,""));return n}function i(e){var t=[],n=/^([^\[\]]*)/,r=new RegExp(f),i=n.exec(e);for(i[1]&&t.push(i[1]);null!==(i=r.exec(e));)t.push(i[1]);return t}function o(e,t,n){if(0===t.length)return e=n;var r=t.shift(),i=r.match(/^\[(.+?)\]$/);if("[]"===r)return e=e||[],Array.isArray(e)?e.push(o(null,t,n)):(e._values=e._values||[],e._values.push(o(null,t,n))),e;if(i){var s=i[1],a=+s;isNaN(a)?(e=e||{},e[s]=o(e[s],t,n)):(e=e||[],e[a]=o(e[a],t,n))}else e[r]=o(e[r],t,n);return e}function s(e,t,n){if(t.match(f))o(e,i(t),n);else{var r=e[t];r?(Array.isArray(r)||(e[t]=[r]),e[t].push(n)):e[t]=n}return e}function a(e,t,n){return n=n.replace(/(\r)?\n/g,"\r\n"),n=encodeURIComponent(n),n=n.replace(/%20/g,"+"),e+(e?"&":"")+encodeURIComponent(t)+"="+n}var c=/^(?:submit|button|image|reset|file)$/i,u=/^(?:input|select|textarea|keygen)/i,f=/(\[[^\[\]]*\])/g;t.exports=r},{}],5:[function(e,t,n){!function(){function e(e,t,n){var r="blur"==t||"focus"==t;e.element.addEventListener(t,n,r)}function n(e){e.preventDefault(),e.stopPropagation()}function r(e){return f||(f=e.matches?e.matches:e.webkitMatchesSelector?e.webkitMatchesSelector:e.mozMatchesSelector?e.mozMatchesSelector:e.msMatchesSelector?e.msMatchesSelector:e.oMatchesSelector?e.oMatchesSelector:u.matchesSelector)}function i(e,t,n){if("_root"==t)return n;if(e!==n)return r(e).call(e,t)?e:e.parentNode?(l++,i(e.parentNode,t,n)):void 0}function o(e,t,n,r){d[e.id]||(d[e.id]={}),d[e.id][t]||(d[e.id][t]={}),d[e.id][t][n]||(d[e.id][t][n]=[]),d[e.id][t][n].push(r)}function s(e,t,n,r){if(d[e.id])if(t){if(!r&&!n)return void(d[e.id][t]={});if(!r)return void delete d[e.id][t][n];if(d[e.id][t][n])for(var i=0;i<d[e.id][t][n].length;i++)if(d[e.id][t][n][i]===r){d[e.id][t][n].splice(i,1);break}}else for(var o in d[e.id])d[e.id].hasOwnProperty(o)&&(d[e.id][o]={})}function a(e,t,n){if(d[e][n]){var r,o,s=t.target||t.srcElement,a={},c=0,f=0;l=0;for(r in d[e][n])d[e][n].hasOwnProperty(r)&&(o=i(s,r,m[e].element))&&u.matchesEvent(n,m[e].element,o,"_root"==r,t)&&(l++,d[e][n][r].match=o,a[l]=d[e][n][r]);for(t.stopPropagation=function(){t.cancelBubble=!0},c=0;c<=l;c++)if(a[c])for(f=0;f<a[c].length;f++){if(!1===a[c][f].call(a[c].match,t))return void u.cancel(t);if(t.cancelBubble)return}}}function c(e,t,n,r){if(this.element){e instanceof Array||(e=[e]),n||"function"!=typeof t||(n=t,t="_root");var i,c=this.id;for(i=0;i<e.length;i++)r?s(this,e[i],t,n):(d[c]&&d[c][e[i]]||u.addEvent(this,e[i],function(e){return function(t){a(c,t,e)}}(e[i])),o(this,e[i],t,n));return this}}function u(e,t){if(!(this instanceof u)){for(var n in m)if(m[n].element===e)return m[n];return h++,m[h]=new u(e,h),m[h]}this.element=e,this.id=t}var f,l=0,h=0,d={},m={};u.prototype.on=function(e,t,n){return c.call(this,e,t,n)},u.prototype.off=function(e,t,n){return c.call(this,e,t,n,!0)},u.matchesSelector=function(){},u.cancel=n,u.addEvent=e,u.matchesEvent=function(){return!0},void 0!==t&&t.exports&&(t.exports=u),window.Gator=u}()},{}],6:[function(e,n,r){!function(e){var r=function(e,t,n){for(var i in t)if(t.hasOwnProperty(i)){var o=i,s=t[i];if(void 0===s&&(s=""),null===s&&(s=""),void 0!==n&&(o=n+"["+i+"]"),s.constructor===Array)o+="[]";else if("object"==typeof s){r(e,s,o);continue}var a=e.elements.namedItem(o);if(a){var c=a.type||a[0].type;switch(c){default:a.value=s;break;case"radio":case"checkbox":for(var u=0;u<a.length;u++)a[u].checked=s.indexOf(a[u].value)>-1;break;case"select-multiple":for(var f=s.constructor==Array?s:[s],l=0;l<a.options.length;l++)a.options[l].selected|=f.indexOf(a.options[l].value)>-1;break;case"select":case"select-one":a.value=s.toString()||s;break;case"date":a.value=new Date(s).toISOString().split("T")[0]}}}};"function"==typeof t&&"object"==typeof t.amd&&t.amd?t(function(){return r}):void 0!==n&&n.exports?n.exports=r:e.populate=r}(this)},{}],7:[function(e,n,r){!function(e){"use strict";function r(){}function i(e,t){for(var n=e.length;n--;)if(e[n].listener===t)return n;return-1}function o(e){return function(){return this[e].apply(this,arguments)}}function s(e){return"function"==typeof e||e instanceof RegExp||!(!e||"object"!=typeof e)&&s(e.listener)}var a=r.prototype,c=e.EventEmitter;a.getListeners=function(e){var t,n,r=this._getEvents();if(e instanceof RegExp){t={};for(n in r)r.hasOwnProperty(n)&&e.test(n)&&(t[n]=r[n])}else t=r[e]||(r[e]=[]);return t},a.flattenListeners=function(e){var t,n=[];for(t=0;t<e.length;t+=1)n.push(e[t].listener);return n},a.getListenersAsObject=function(e){var t,n=this.getListeners(e);return n instanceof Array&&(t={},t[e]=n),t||n},a.addListener=function(e,t){if(!s(t))throw new TypeError("listener must be a function");var n,r=this.getListenersAsObject(e),o="object"==typeof t;for(n in r)r.hasOwnProperty(n)&&-1===i(r[n],t)&&r[n].push(o?t:{listener:t,once:!1});return this},a.on=o("addListener"),a.addOnceListener=function(e,t){return this.addListener(e,{listener:t,once:!0})},a.once=o("addOnceListener"),a.defineEvent=function(e){return this.getListeners(e),this},a.defineEvents=function(e){for(var t=0;t<e.length;t+=1)this.defineEvent(e[t]);return this},a.removeListener=function(e,t){var n,r,o=this.getListenersAsObject(e);for(r in o)o.hasOwnProperty(r)&&-1!==(n=i(o[r],t))&&o[r].splice(n,1);return this},a.off=o("removeListener"),a.addListeners=function(e,t){return this.manipulateListeners(!1,e,t)},a.removeListeners=function(e,t){return this.manipulateListeners(!0,e,t)},a.manipulateListeners=function(e,t,n){var r,i,o=e?this.removeListener:this.addListener,s=e?this.removeListeners:this.addListeners;if("object"!=typeof t||t instanceof RegExp)for(r=n.length;r--;)o.call(this,t,n[r]);else for(r in t)t.hasOwnProperty(r)&&(i=t[r])&&("function"==typeof i?o.call(this,r,i):s.call(this,r,i));return this},a.removeEvent=function(e){var t,n=typeof e,r=this._getEvents();if("string"===n)delete r[e];else if(e instanceof RegExp)for(t in r)r.hasOwnProperty(t)&&e.test(t)&&delete r[t];else delete this._events;return this},a.removeAllListeners=o("removeEvent"),a.emitEvent=function(e,t){var n,r,i,o,s=this.getListenersAsObject(e);for(o in s)if(s.hasOwnProperty(o))for(n=s[o].slice(0),i=0;i<n.length;i++)r=n[i],!0===r.once&&this.removeListener(e,r.listener),r.listener.apply(this,t||[])===this._getOnceReturnValue()&&this.removeListener(e,r.listener);return this},a.trigger=o("emitEvent"),a.emit=function(e){var t=Array.prototype.slice.call(arguments,1);return this.emitEvent(e,t)},a.setOnceReturnValue=function(e){return this._onceReturnValue=e,this},a._getOnceReturnValue=function(){return!this.hasOwnProperty("_onceReturnValue")||this._onceReturnValue},a._getEvents=function(){return this._events||(this._events={})},r.noConflict=function(){return e.EventEmitter=c,r},"function"==typeof t&&t.amd?t(function(){return r}):"object"==typeof n&&n.exports?n.exports=r:e.EventEmitter=r}(this||{})},{}]},{},[1])}();