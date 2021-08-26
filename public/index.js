let inputMedia = document.getElementById("product_media");
let labelMedia = inputMedia.getElementsByTagName("label");
console.log(labelMedia.length);
for (let  i = 0; i < labelMedia.length; i++){
    let img = labelMedia[i].innerHTML;
    labelMedia[i].innerHTML = `<img src="/projet_final_symfony/public/img/media/${img}">`;
}

document.getElementById("product_submit").innerHTML = "Enregistrer";