document.addEventListener('DOMContentLoaded',function(){
var b=document.querySelector('.burger');if(!b)return;
b.addEventListener('click',function(){var o=document.body.classList.toggle('menu-open');b.setAttribute('aria-expanded',o)});
document.querySelectorAll('.nav a').forEach(function(a){a.addEventListener('click',function(){document.body.classList.remove('menu-open')})});
});

if(window.jQuery){jQuery(document.body).on('added_to_cart',function(e,f){if(f&&f['b.cart-n'])jQuery('b.cart-n').replaceWith(f['b.cart-n'])})}
