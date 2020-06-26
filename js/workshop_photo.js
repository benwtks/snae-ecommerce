function updateWorkshop(thumbnail) {
	const thumbs = document.querySelectorAll('.workshop-thumbnail');
	const featured = document.getElementById("featured");

	[].forEach.call(thumbs, function(thumb) {
		thumb.classList.remove("selected");
	});

	event.target.classList.add("selected");
	featured.setAttribute("src", thumbnail);
}
