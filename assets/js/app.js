const CART_KEY='mahavir_cart_v1',WISH_KEY='mahavir_wishlist_v1';
function getCart(){try{return JSON.parse(localStorage.getItem(CART_KEY)||'[]')}catch{return []}}
function saveCart(v){localStorage.setItem(CART_KEY,JSON.stringify(v));updateCartCount()}
function updateCartCount(){const el=document.getElementById('cart-count');if(el)el.textContent=getCart().reduce((n,x)=>n+x.qty,0)}
function addToCart(id,name,price){const c=getCart(),i=c.findIndex(x=>x.id===id);if(i>=0)c[i].qty++;else c.push({id,name,price:Number(price),qty:1});saveCart(c);alert(name+' added to cart')}
function getWish(){try{return JSON.parse(localStorage.getItem(WISH_KEY)||'[]')}catch{return []}}
function toggleWish(p){let w=getWish();if(w.some(x=>x.id===p.id))w=w.filter(x=>x.id!==p.id);else w.push(p);localStorage.setItem(WISH_KEY,JSON.stringify(w));}
function renderCart(id){const el=document.getElementById(id),c=getCart();if(!c.length){el.innerHTML='<p>Your cart is empty. <a href="index.php">Browse products</a></p>';return}let total=0;el.innerHTML='<div class="table-wrap"><table><thead><tr><th>ITEM</th><th>PRICE</th><th>QTY</th><th>SUBTOTAL</th><th></th></tr></thead><tbody>'+c.map((x,i)=>{total+=x.price*x.qty;return '<tr><td>'+safe(x.name)+'</td><td>₹'+x.price.toFixed(2)+'</td><td><button onclick="changeQty('+i+',-1)">−</button> '+x.qty+' <button onclick="changeQty('+i+',1)">+</button></td><td>₹'+(x.price*x.qty).toFixed(2)+'</td><td><button onclick="removeCart('+i+')">Remove</button></td></tr>'}).join('')+'</tbody></table></div><h2>Total: ₹'+total.toFixed(2)+'</h2><a class="primary-btn" href="checkout.php">Proceed to checkout</a>'}
function changeQty(i,n){let c=getCart();c[i].qty=Math.max(1,c[i].qty+n);saveCart(c);renderCart('cart-view')}
function removeCart(i){let c=getCart();c.splice(i,1);saveCart(c);renderCart('cart-view')}
function renderWishlist(id){const el=document.getElementById(id),w=getWish();el.innerHTML=w.length?w.map(x=>'<article class="product-card"><div class="product-art">🛍️</div><h3>'+safe(x.name)+'</h3><b>₹'+Number(x.price).toFixed(2)+'</b><p><button onclick="addToCart('+x.id+',\''+safe(x.name).replace(/'/g,"\\'")+'\','+x.price+')">Add to cart</button></p></article>').join(''):'<p>Your wishlist is empty.</p>'}
function safe(s){return String(s).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]))}
document.addEventListener('DOMContentLoaded',updateCartCount);
