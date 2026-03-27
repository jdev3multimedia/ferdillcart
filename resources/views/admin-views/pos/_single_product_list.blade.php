<?php
if(session()->get('cart_product_ids') && count(session()->get('cart_product_ids'))>0){
    $cart_product_ids = session()->get('cart_product_ids');
}else{
    $cart_product_ids = [];
}
?>
@foreach($products as $product)
    <div class="order--item-box item-box">
        @include('admin-views.pos._single_product',['product'=>$product, 'store_data'=>$store, 'cart_product_ids'=>$cart_product_ids])
    </div>
@endforeach
