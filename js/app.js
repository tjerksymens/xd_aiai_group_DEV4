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

//add event click on all btns with class btn
let btns = document.querySelectorAll(".btn");
for (let i = 0; i < btns.length; i++) {
    btns[i].addEventListener("click", function(e) {
        e.preventDefault();
        // get the id of the post
        //commnet text
        let id = this.getAttribute("data-id");
        let text = document.querySelector("#comment" + id).value;

        // post naar database(ajax)
        let formData = new FormData();
        formData.append("id", id);
        formData.append("text", text);
    
        fetch("ajax/saveComment.php", {
                method: "POST",
                body: formData
            })

            .then(response => response.json())
            .then(result => {
                let newComment = document.createElement('li');
                newComment.innerHTML = result.body;
                document
                    .querySelector(".post_comments_list" + id)
                    .appendChild(newComment);
            })
            .catch(error => {
                console.error('Error:', error, id, text);
            });
    });
}