/*
*  clientWidth -- 对象可见宽度，不包含滚动条等边线，辉随窗口显示大小改变。
*  html宽度是 实际宽度/320*10
*/

(function (doc, win) {    
    var docEl = doc.documentElement,    
    resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',    
    recalc = function () {    
    var clientWidth = docEl.clientWidth;    
    if (!clientWidth) return;    
    docEl.style.fontSize = 10 * (clientWidth / 75) + 'px';    
};    
if (!doc.addEventListener) return;    
win.addEventListener(resizeEvt, recalc, false);    
doc.addEventListener('DOMContentLoaded', recalc, false);    
})(document, window);    