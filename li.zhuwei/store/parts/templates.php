<?php

include_once("lib/php/functions.php");

function productListTemplate($r,$o) {
return $r.<<<HTML
<div class="col-xs-6 col-md-4">
  <a href="product_item.php?id=$o->id" class="display-block">
    <figure class="product-figure soft">
      <div class="product-image"><img src="$o->thumbnail" alt=""></div>
      <figcaption class="product-description">
        <div class="product-price">&dollar;$o->price</div>
        <div class="product-title">$o->name</div>
      </figcaption>
    </figure>
  </a>
</div>
HTML;
}

function productListCardTemplate($r,$o) {
return $r.<<<HTML
<div class="hscroll-card">
  <a href="product_item.php?id=$o->id" class="display-block">
  <figure class="product-figure soft">
    <div class="product-image"><img src="$o->thumbnail" alt=""></div>
    <figcaption class="product-description">
      <div class="product-price">&dollar;$o->price</div>
      <div class="product-title">$o->name</div>
    </figcaption>
  </figure>
</a>
</div>
HTML;
}

function cartListTemplate($r,$o) {
$pricefixed = number_format($o->total, 2, '.', '');
$selectamount = selectAmount($o->amount,10);
return $r.<<<HTML
<div class="display-flex card-section" style="flex-direction: row; justify-content: space-between; align-items: flex-start;">
  <div style="margin-left: -4em; width:20em; flex-grow: 0">
    <img src="$o->thumbnail">
  </div>
  <div class="display-flex" style="flex-direction: column; margin-right:10em;
    align-items: flex-start; justify-content: flex-start; flex-grow: 1">
    <strong>$o->name</strong>
    <div style="margin-top: 0.4em;">
      <form method="get" action="data/form_actions.php" onchange="this.submit()">
        <label style="float:left; font-size: 0.8em; font-style: italic; padding-right: 8px; padding-top: 12px;">quantity </label>
        <input type="hidden" name="action" value="update-cart-amount">
        <input type="hidden" name="id" value="$o->id">
        <div class="display-flex" style="padding-top: 2px;">
          <div class="flex-none">$selectamount</div>
        </div>
      </form>
    </div>
  </div>
  <div class="display-flex" style="flex-direction: column; flex-grow: 0">
    <div class="flex-stretch">
      <div class="flex-none">&dollar;$pricefixed</div>
      <form style="margin-top: 0.4em;" method="get" action="data/form_actions.php">
        <input type="hidden" name="action" value="delete-cart-item">
        <input type="hidden" name="id" value="$o->id">
        <div class="display-flex">
          <div class="flex-none">
            <input type="submit" class="form-button delete" value="delete">
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
HTML;
}

function selectAmount($amount=1,$total=10) {
  $output = "<select class='form-input' name='amount'>";
  for($i=1;$i<=$total;$i++){
    $output .= "<option ".($i==$amount?"selected":"").">$i</option>";
  }
  $output .= "</select>";
  return $output;
}

function cartTotals() {
$cart = getCart();

$cartprice = array_reduce($cart,function($r,$o){return $r + ($o->amount*$o->price);},0);


$pricefixed = number_format($cartprice, 2, '.', '');
$taxfixed = number_format($cartprice*0.0725, 2, '.', '');
$taxedfixed = number_format($cartprice*1.0725, 2, '.', '');

return <<<HTML
<div class="card-section">
  <div class="display-flex">
    <div class="flex-stretch">
      <strong>Sub-Total</strong>
    </div>
    <div class="flex-none">&dollar;$pricefixed</div>
  </div>

    <div class="display-flex">    
    <div class="flex-stretch">
      <strong>Taxes</strong>
    </div>
    <div class="flex-none">&dollar;$taxfixed</div>
  </div>
</div>
<div class="card-section">
  <div class="display-flex">
    <div class="flex-stretch">
      <strong>Total</strong>
    </div>
    <div class="flex-none">&dollar;$taxedfixed</div>
  </div>
</div>
HTML;
}

function makeCartBadge() {
  if(!isset($_SESSION['cart']) || !count($_SESSION['cart'])) {
    return "";
  } else return "(".array_reduce($_SESSION['cart'],function($r,$o){
    return $r + $o->amount;
  },0).")";
}

function recommendedProducts($rows, $useCard=False) {
$products = array_reduce($rows, $useCard ? 'productListCardTemplate' : 'productListTemplate');
echo <<<HTML
$products
HTML;
}

function recommendedCategory($cat, $limit=3, $useCard=False) {
  $rows = getRows(makePDOConn(),"SELECT * FROM `products` WHERE category='$cat'
  LIMIT $limit");
  recommendedProducts($rows, $useCard);
}

function recommendedSimilar($cat, $limit=3) {
  $rows = getRows(makePDOConn(),"SELECT * FROM `products` WHERE category='$cat'
  ORDER BY rand() LIMIT $limit");
  recommendedProducts($rows);
}