// add event click on all links with class .like
let links = document.querySelectorAll(".like");
for (let i = 0; i < links.length; i++) {
    links[i].addEventListener("click", function(e) {
        e.preventDefault();
        // get the id of the post
        let id = this.getAttribute("data-id");
        let span = document.querySelector("#likes" + id);
        let a = document.querySelector("#like" + id);

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
                if (a.classList.contains("liked")) {
                    a.innerHTML = "Like";
                    a.classList.remove("liked");
                } else {
                    a.innerHTML = "Liked";
                    a.classList.add("liked");
                }
                span.innerHTML = json.likes;
            })
            .catch(error => {
                console.error('Error:', error, id, span);
            });
    });
}

fetch("ajax/getLikes.php")
    .then(function(response) {
        return response.json();
    })
    .then(function(json) {
    let likes = json.liked;
        console.log(likes);
        for (let i = 0; i < likes.length; i++) {
            // get the id of the favourited prompt
            let id = likes[i];
            let a = document.querySelector("#like" + id);

            
            // update the text and class of the corresponding element on the page
            a.innerHTML = "Liked";
            a.classList.add("liked");
        }
    })
    .catch(error => {
    console.error('Error:', error);
});


//add event click on all btns with class btn_comments
let btns = document.querySelectorAll(".btn_comments");
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
                newComment.innerHTML = '<strong>' + result.username + '</strong>' + ' ' + result.body;
                document
                    .querySelector(".post_comments_list" + id)
                    .appendChild(newComment);
            })
            .catch(error => {
                console.error('Error:', error, id, text);
            });
    });
}


let favourites = document.querySelectorAll(".favourite");
for (let i = 0; i < favourites.length; i++) {
    favourites[i].addEventListener("click", function(e) {
        e.preventDefault();
        // get the id of the post
        let id = this.getAttribute("data-id");
        let a = document.querySelector("#favourite" + id);

        // fetch (POST) to ajax/lik.php, use formdata
        let formData = new FormData();
        formData.append("id", id);

        fetch("ajax/favourite.php", {
                method: "POST",
                body: formData
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(json) {
                if (a.classList.contains("favourited")) {
                    a.innerHTML = "Add to favourites";
                    a.classList.remove("favourited");
                } else {
                    a.innerHTML = "Remove from favourites";
                    a.classList.add("favourited");
                }

            })
            .catch(error => {
                console.error('Error:', error, id, a);
            });
    });
}


fetch("ajax/getFavourites.php")
    .then(function(response) {
        return response.json();
    })
    .then(function(json) {
    let favourites = json.favourited;
        console.log(favourites);
        for (let i = 0; i < favourites.length; i++) {
            // get the id of the favourited prompt
            let id = favourites[i];
            let a = document.querySelector("#favourite" + id);
            
            // update the text and class of the corresponding element on the page
            a.innerHTML = "Remove from favourites";
            a.classList.add("favourited");
        }
    })
    .catch(error => {
    console.error('Error:', error);
});
