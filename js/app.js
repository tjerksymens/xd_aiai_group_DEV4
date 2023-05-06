// add event click on all links with class .like
let links = document.querySelectorAll(".like");
for (let i = 0; i < links.length; i++) {
    links[i].addEventListener("click", function(e) {
        e.preventDefault();
        // get the id of the post
        let id = this.getAttribute("data-id");
        let span = document.querySelector("#likes" + id);

        // fetch (POST) to ajax/lik.php, use formdata
        let formData = new FormData();
        formData.append("id", id);
        fetch("ajax/like.php", {
                method: "POST",
                body: formData
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(json) {
                span.innerHTML = json.likes;
            })
            .catch(error => {
                console.error('Error:', error, id, span);
            });
    });
}