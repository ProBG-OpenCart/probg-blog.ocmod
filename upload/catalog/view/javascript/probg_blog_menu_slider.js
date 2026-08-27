(function(){
  'use strict';
  function initSlider(root){
    if(root.dataset.probgBlogReady==='1')return;
    root.dataset.probgBlogReady='1';
    var track=root.querySelector('.probg-blog-slider-track');
    var slides=[].slice.call(root.querySelectorAll('.probg-blog-slider-slide'));
    var wrapper=root.closest('.probg-blog-menu');
    if(!track||!slides.length||!wrapper)return;
    var prev=wrapper.querySelector('.probg-blog-slider-prev');
    var next=wrapper.querySelector('.probg-blog-slider-next');
    var dots=root.querySelector('.probg-blog-slider-dots');
    function items(name,fallback){return Math.max(1,Math.min(6,parseInt(root.getAttribute(name)||fallback,10)));}
    var desktop=items('data-items-desktop','3');
    var tablet=items('data-items-tablet',String(Math.min(2,desktop)));
    var mobile=items('data-items-mobile','1');
    var autoplay=root.getAttribute('data-autoplay')==='1';
    var interval=Math.max(1000,Math.min(30000,parseInt(root.getAttribute('data-interval')||'5000',10)));
    var index=0,visible=1,timer=null,startX=null;
    function calcVisible(){var w=window.innerWidth||document.documentElement.clientWidth;var configured=w<576?mobile:(w<992?tablet:desktop);return Math.max(1,Math.min(configured,slides.length));}
    function maxIndex(){return Math.max(0,slides.length-visible);}
    function buildDots(){if(!dots)return;dots.innerHTML='';for(var i=0;i<=maxIndex();i++){var b=document.createElement('button');b.type='button';b.className='probg-blog-slider-dot';b.setAttribute('tabindex','-1');b.dataset.index=i;dots.appendChild(b);}}
    function render(){visible=calcVisible();slides.forEach(function(slide){slide.style.flexBasis=(100/visible)+'%';});if(index>maxIndex())index=maxIndex();track.style.transform='translate3d(-'+(index*(100/visible))+'%,0,0)';if(prev)prev.disabled=index<=0;if(next)next.disabled=index>=maxIndex();if(dots){if(dots.children.length!==maxIndex()+1)buildDots();[].forEach.call(dots.children,function(dot,i){dot.classList.toggle('is-active',i===index);});}}
    function move(step){var target=index+step;if(target>maxIndex())target=autoplay?0:maxIndex();if(target<0)target=autoplay?maxIndex():0;index=target;render();}
    function stop(){if(timer){clearInterval(timer);timer=null;}}
    function start(){stop();if(autoplay&&maxIndex()>0&&!window.matchMedia('(prefers-reduced-motion: reduce)').matches)timer=setInterval(function(){move(1);},interval);}
    if(prev)prev.addEventListener('click',function(){move(-1);start();});
    if(next)next.addEventListener('click',function(){move(1);start();});
    if(dots)dots.addEventListener('click',function(e){var dot=e.target.closest('.probg-blog-slider-dot');if(!dot)return;index=parseInt(dot.dataset.index,10)||0;render();start();});
    root.addEventListener('mouseenter',stop);root.addEventListener('mouseleave',start);root.addEventListener('focusin',stop);root.addEventListener('focusout',start);
    root.addEventListener('touchstart',function(e){startX=e.touches&&e.touches[0]?e.touches[0].clientX:null;},{passive:true});
    root.addEventListener('touchend',function(e){if(startX===null)return;var end=e.changedTouches&&e.changedTouches[0]?e.changedTouches[0].clientX:startX;var diff=end-startX;startX=null;if(Math.abs(diff)>40){move(diff<0?1:-1);start();}},{passive:true});
    var resizeTimer;window.addEventListener('resize',function(){clearTimeout(resizeTimer);resizeTimer=setTimeout(render,120);});
    render();start();
  }
  function init(){[].forEach.call(document.querySelectorAll('[data-probg-blog-slider]'),initSlider);}
  if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',init);else init();
})();
