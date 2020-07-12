/*
By Osvaldas Valutis, www.osvaldas.info
Available for use under the MIT License
*/

'use strict';

let icons=document.querySelectorAll("i");
icons.forEach(function (icon) {
	icon.addEventListener("click",function () {
		var com=confirm('Etes vous sur de vouloir supprimer cette image ?');
		if(!com){
			return false;
		}
		const url=icon.getAttribute("data-target");
		$.ajax(
			{
				url: url,
				method: 'GET',
			}
		).done(function (msg) {
			icon.parentElement.parentElement.parentElement.remove();
			let nb = document.querySelector('.nbimg').innerHTML;
			let nb1 = (nb.split(' '))[0] - 1;
			document.querySelector('.nbimg').innerHTML = nb1 +" Images";

		})
	})
})

;(function (document, window, index) {
	var inputs = document.querySelectorAll('.inputfile');
	Array.prototype.forEach.call(inputs, function (input) {
		var label = input.nextElementSibling,
			labelVal = label.innerHTML;

		input.addEventListener('change', function (e) {
			var fileName = '';
			if (this.files && this.files.length > 1)
				fileName = (this.getAttribute('data-multiple-caption') || '').replace('{count}', this.files.length);
			else
				fileName = e.target.value.split('\\').pop();

			if (fileName) {
				const url = input.getAttribute("data-target");
				const chemin =input.getAttribute("data");
				let files = input.files[0];
				let fd = new FormData();
				fd.append('files', files);
				for (var value of fd.values()) {
					console.log(value);
				}
				$.ajax({
					url: url,
					type: 'POST',
					data: fd,
					contentType: false,
					processData: false
				}).done(function (msg) {
					console.log(msg['img']);
					let newdiv = document.createElement("div");
					newdiv.classList.add('col-lg-3', 'col-md-4', 'col-xs-6', 'thumb', 'profile-pic');

					let img = document.createElement("img");
					img.src = "../../Uploads/"+chemin+"/" + msg['img'];
					img.classList.add("zoom", "img-fluid");
					let editdiv=document.createElement("div");
					editdiv.classList.add("edit");
					let aedit=document.createElement("a");
					aedit.href="#";
					let iedit=document.createElement("i");
					iedit.setAttribute("data-target","/admin/galerie/4x4/delete/"+msg['img']);
					iedit.classList.add("fa","fa-lg","fa-times-circle");
					iedit.addEventListener("click",function () {
						var com=confirm('Etes vous sur de vouloir supprimer cette image ?');
						if(!com){
							return false;
						}
						const url=iedit.getAttribute("data-target");
						$.ajax(
							{
								url: url,
								method: 'GET',
							}
						).done(function (msg) {
							iedit.parentElement.parentElement.parentElement.remove();
							let nb = document.querySelector('.nbimg').innerHTML;
							let nb1 = (nb.split(' '))[0] - 1;
							document.querySelector('.nbimg').innerHTML = nb1 +" Images";

						})
					})
					aedit.appendChild(iedit);
					editdiv.appendChild(aedit);
					newdiv.appendChild(img);
					newdiv.appendChild(editdiv);
					let images = document.querySelector(".my-images");
					images.appendChild(newdiv);

					let nb = document.querySelector('.nbimg').innerHTML;
					let nb1 = Number((nb.split(' '))[0]) + 1;
					console.log(nb1);
					document.querySelector('.nbimg').innerHTML = nb1 +" Images";

				})

			} else
				label.innerHTML = labelVal;
		});

		// Firefox bug fix
		input.addEventListener('focus', function () {
			input.classList.add('has-focus');
		});
		input.addEventListener('blur', function () {
			input.classList.remove('has-focus');
		});
	});
}(document, window, 0));