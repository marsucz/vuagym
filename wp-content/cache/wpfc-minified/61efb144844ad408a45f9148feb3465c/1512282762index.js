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