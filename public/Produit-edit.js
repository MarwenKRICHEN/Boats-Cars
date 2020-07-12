// modifier=document.querySelectorAll('[src="img/1486564724-pencil_81530.png"]');
// modifier.forEach(function (item) {
//     item.addEventListener("click",function () {
//         let img;
//         let champ;
//         let selection = document.querySelector('.modify') !== null;
//         if (!selection) {
//             let td = item.parentElement;
//             let tr = td.parentElement;
//             td.parentElement.classList.add('modify');
//             td.parentElement.removeChild(td);
//             let newtd = document.createElement("td");
//             let img1 = document.createElement("img");
//             let img2 = document.createElement("img");
//             img1.src = "img/savedisk_floppydisk_guardar_1543.png"
//             img2.src = "img/Cancel_icon-icons.com_73703.png";
//             let cancel = document.createElement("a");
//             cancel.href="produit";
//             cancel.appendChild(img2);
//             newtd.appendChild(img1);
//             newtd.appendChild(cancel);
//             tr.appendChild(newtd);
//             img = document.querySelector(".table .modify td:nth-of-type(1) img");
//             img.addEventListener("click", function () {
//                 if (img.getAttribute("src") === "img/accept_icon-icons.com_74428.png") {
//                     img.src = "img/crosscircleregular_106260.png";
//                 } else {
//                     img.src = "img/accept_icon-icons.com_74428.png";
//                 }
//             });
//             tr.addEventListener("click", function (e) {
//                 let td = e.target;
//                 if ((td.firstChild.nodeName !== "IMG") && (td.firstChild.nodeName !== "INPUT") && (td.firstElementChild === null)) {
//                     let c = td.textContent;
//                     let a = document.createElement("input");
//                     a.value = c.toString();
//                     td.textContent = null;
//                     td.appendChild(a);
//                     a.focus();
//                     a.addEventListener("blur", function () {
//                         let content = a.value;
//                         td.removeChild(td.firstChild);
//                         td.textContent = content;
//                     })
//                 }
//             });
//
//         }
//     })
// });
