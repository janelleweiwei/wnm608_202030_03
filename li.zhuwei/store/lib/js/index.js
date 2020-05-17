var collectionFormButtonClicked = null;

function highlight(element) {
  if (collectionFormButtonClicked != null) {
    collectionFormButtonClicked.style.background = "#283b54";
  }
  collectionFormButtonClicked = element;
  collectionFormButtonClicked.style.background = "#fb8ca9";
}

$(function() {

  $(".gallery-item").on("click", function(){
    let src = $(this). find("img").attr("src");
    $("body").append(`<div class='lightbox'><img src='${src}'></div>`);
  });

  $("body").on("click",".lightbox", function(){
    $(this).remove();
  })

  $("#scroll-button-left").click(function() {
    $(".scrolling-wrapper").animate({
        scrollLeft: -3500
    }, 1000);
  });

  $("#scroll-button-right").click(function() {
    $(".scrolling-wrapper").animate({
        scrollLeft: 4000
    }, 1000);
  });
});

$( document ).ready(function() {
  collectionFormButtonClicked = $("#default-button")[0];
  highlight(collectionFormButtonClicked);
});

function myFunction(imgs) {
  var expandImg = document.getElementById("expandedImg");
  var imgText = document.getElementById("imgtext");
  expandImg.src = imgs.src;
  imgText.innerHTML = imgs.alt;
  expandImg.parentElement.style.display = "block";
}

