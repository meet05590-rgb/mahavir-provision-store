<?php
require '../config.php';
require_admin();
$message='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    verify_csrf();
    $items=json_decode($_POST['items']??'[]',true);
    $customer=trim($_POST['customer_name']??'Walk-in Customer');
    $phone=trim($_POST['phone']??'0000000000');
    if(!is_array($items)||!count($items)) $message='Add at least one product.';
    else {
        try {
            $pdo->beginTransaction(); $lines=[]; $total=0;
            foreach($items as $item){
                $id=(int)($item['id']??0); $qty=max(1,min(99,(int)($item['qty']??1)));
                $q=$pdo->prepare('SELECT id,name,price,stock FROM products WHERE id=? AND active=1 FOR UPDATE');
                $q->execute([$id]); $p=$q->fetch();
                if(!$p||$p['stock']<$qty) throw new Exception('Insufficient stock for one of the selected products.');
                $lines[]=[$p,$qty]; $total+=(float)$p['price']*$qty;
            }
            $q=$pdo->prepare('INSERT INTO orders(user_id,customer_name,phone,address,total,status) VALUES(NULL,?,?,?,?,"Delivered")');
            $q->execute([$customer?:'Walk-in Customer',$phone?:'0000000000','In-store POS sale',$total]);
            $orderId=(int)$pdo->lastInsertId();
            foreach($lines as [$p,$qty]){
                $pdo->prepare('INSERT INTO order_items(order_id,product_id,product_name,quantity,price) VALUES(?,?,?,?,?)')->execute([$orderId,$p['id'],$p['name'],$qty,$p['price']]);
                $pdo->prepare('UPDATE products SET stock=stock-? WHERE id=?')->execute([$qty,$p['id']]);
            }
            $pdo->commit(); $message='Bill saved successfully. Receipt #'.$orderId;
        } catch(Throwable $e){ if($pdo->inTransaction())$pdo->rollBack(); $message=$e instanceof PDOException?'Could not save the bill.':$e->getMessage(); }
    }
}
$products=$pdo->query('SELECT id,name,price,stock FROM products WHERE active=1 AND stock>0 ORDER BY name')->fetchAll();
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>POS Billing · Mahavir Store</title><link rel="stylesheet" href="../assets/css/store-theme.css"><style>.pos-grid{display:grid;grid-template-columns:1.2fr .8fr;gap:18px}.pos-item{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:12px;border-bottom:1px solid var(--line)}.pos-item button,.qty button{border:0;border-radius:8px;padding:8px 12px;background:#15563c;color:#d8ffe9}.receipt-total{font-size:24px;font-weight:800;color:#f5a623}@media(max-width:760px){.pos-grid{grid-template-columns:1fr}}</style></head><body><main class="main" style="width:100%;margin:0;max-width:1100px;margin-inline:auto"><header class="topbar"><div><div class="eyebrow">MAHAVIR PROVISION STORE / ADMIN</div><h1 class="page-title">POS Billing</h1></div><a class="primary-btn" href="index.php">← Admin panel</a></header><?php if($message):?><p class="offer-strip"><?=e($message)?></p><?php endif;?><div class="pos-grid"><section class="panel"><h2 class="panel-title">Available products</h2><input id="search" placeholder="Search product..." style="width:100%;padding:12px;border-radius:10px;border:1px solid var(--line);background:#0b1b24;color:#fff;margin:14px 0"><?php foreach($products as $p):?><div class="pos-item" data-name="<?=e(strtolower($p['name']))?>"><div><b><?=e($p['name'])?></b><div class="muted">₹<?=number_format((float)$p['price'],2)?> · Stock <?= (int)$p['stock']?></div></div><button type="button" onclick='addItem(<?= (int)$p['id']?>,<?=json_encode($p['name'])?>,<?=json_encode((float)$p['price'])?>)'>Add +</button></div><?php endforeach;?></section><section class="panel"><h2 class="panel-title">Current bill</h2><form method="post" id="bill"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="items" id="items"><label>Customer name</label><input name="customer_name" placeholder="Walk-in Customer" style="width:100%;padding:10px;margin:7px 0 12px"><label>Mobile (optional)</label><input name="phone" placeholder="Mobile number" style="width:100%;padding:10px;margin:7px 0 12px"><div id="lines" class="muted">No products added.</div><p class="receipt-total" id="total">Total: ₹0.00</p><button class="primary-btn" style="width:100%">Complete sale & update stock</button></form></section></div></main><script>let cart=[];function addItem(id,name,price){let p=cart.find(x=>x.id===id);if(p)p.qty++;else cart.push({id,name,price,qty:1});draw()}function draw(){let total=0;document.getElementById('lines').innerHTML=cart.map((p,i)=>{total+=p.price*p.qty;return '<div class="pos-item"><span>'+p.name+'<br>₹'+p.price.toFixed(2)+' × '+p.qty+'</span><span><button type="button" onclick="cart['+i+'].qty++;draw()">+</button> <button type="button" onclick="cart['+i+'].qty=Math.max(1,cart['+i+'].qty-1);draw()">−</button> <button type="button" onclick="cart.splice('+i+',1);draw()">×</button></span></div>'}).join('')||'No products added.';document.getElementById('total').textContent='Total: ₹'+total.toFixed(2);document.getElementById('items').value=JSON.stringify(cart)}document.getElementById('search').addEventListener('input',e=>document.querySelectorAll('.pos-item[data-name]').forEach(x=>x.hidden=!x.dataset.name.includes(e.target.value.toLowerCase())));document.getElementById('bill').addEventListener('submit',e=>{if(!cart.length){e.preventDefault();alert('Add products first.')}});</script></body></html>