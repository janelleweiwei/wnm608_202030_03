const makeProductList = templater(o=>{
return `

<div class="col-xs-12 col-md-6 col-xl-4">
  <a href="product_item.php?id=${o.id}" class="display-block">
    <figure class="product-figure soft no-bottom-padding">
      <div class="product-image"><img src="${o.thumbnail}" alt=""></div>
      <figcaption class="product-description">
        <div class="price product-price">&dollar;${o.price}</div>
        <div class="product-title">${o.name}</div>
        <div style="display:flex; width:100%">
          <button class="product-description-button" onClick="location.href='product_item.php?id=${o.id}'">View More</button>
        </div>
      </figcaption>
    </figure>
  </a>
</div>


`;
})