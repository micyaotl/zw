;(function(b){b.fn.onePageNav=function(k){var i=b.extend({},b.fn.onePageNav.defaults,k);return this.each(function(){var g=b(this),h=b.meta?b.extend({},i,g.data()):i,a={};a.positions=[];a.sections=[];a.bindNav=function(){g.find("a").bind("click",function(c){var d=b(this),e=d.parent(),f=d.attr("href"),j=b(document);if(!e.hasClass(h.currentClass)){a.adjustNav(e);j.unbind(".onePageNav");b.scrollTo(f,h.scrollSpeed,{onAfter:function(){if(h.changeHash)window.location.hash=f;j.bind("scroll.onePageNav",a.scrollChange)}})}c.preventDefault()})};
a.adjustNav=function(c){g.find("."+h.currentClass).removeClass(h.currentClass);c.addClass(h.currentClass)};a.buildArrays=function(){g.find("a").map(function(c){var d=b(this).attr("href"),e=b(d).offset();e=e.top;a.positions[c]=Math.round(e);a.sections[c]=d})};a.getArrayPos=function(c){for(var d=-1,e=Math.round(b(window).height()/2),f=0;f<a.positions.length;f++)if(a.positions[f]-e<c)d=f;return d};a.scrollChange=function(){var c=b(window).scrollTop();c=a.getArrayPos(c);c!==-1&&a.adjustNav(g.find("a[href="+
a.sections[c]+"]").parent())};a.initialHash=function(){window.location.hash.length&&a.adjustNav(g.find("a[href="+window.location.hash+"]").parent())};a.init=function(){a.bindNav();a.buildArrays();b(document).bind("scroll.onePageNav",a.scrollChange)};a.init()})};b.fn.onePageNav.defaults={currentClass:"current",changeHash:false,scrollSpeed:750}})(jQuery);