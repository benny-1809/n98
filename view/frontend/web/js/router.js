"use strict"

// Self calling function to isolate the scope of our "let" variables.
!function() {
    let blogPosts = document.getElementsByClassName("blog-post"),
        blogPreview = document.getElementById("blog-preview"),
        articleContainers = document.getElementsByClassName("blog-article"),
        articleDescriptions = document.getElementsByClassName("blog-description"),
        blogTitles = document.getElementsByClassName("blog-title"),
        currentPost = new URLSearchParams(window.location.search).get("post"),
        scrollPosition = 0;

    // Simple router based on history.pushState().
    function route(clickableElement) {
        for(let href of clickableElement) {
            href.addEventListener("click", function(event) {
                scrollPosition = window.scrollY;
                event.preventDefault();
                // Save the Scrolling position, then check for hardcoded URLs and fix them eventually.
                if(this.href.indexOf("dev98") !== -1) {
                    let pseudo = this.parentElement.parentElement.previousElementSibling.previousElementSibling;
                    this.href = pseudo.href;
                    this.setAttribute("data-id", pseudo.dataset.id);
                }
                // Push our state into the browser history and fetch the blog content deferred.
                history.pushState({}, null, "?post=" + this.dataset.id);
                fetch("/netz/blog/fetch?id=" + this.dataset.id)
                    .then(response => response.text())
                    .then(result => {
                        toggleBlogposts(true, href.dataset.id, result);
                });
            });
        }
    };

    // Toggle blogposts to show ALL at once, or only ONE blogpost at a time.
    function toggleBlogposts(bool, dataId, content) {
        for(let b = 0; b < blogPosts.length; b++) {
            // Continue if we want our blogpost to stay visible unlike the others.
            if(dataId && b == dataId && bool) {
                continue;
            }
            if(bool) {
                blogPosts[b].classList.add("hidden");
                articleContainers[dataId].innerHTML = content ? content : "";
                articleDescriptions[dataId].classList.add("hidden");
            }
            else {
                blogPosts[b].classList.remove("hidden");
                articleContainers[b].innerHTML = "";
                articleDescriptions[b].classList.remove("hidden");
            }
        }
    }

    // Upon pressing the browser back/forward buttons, act accordingly and route to the proper page.
    window.addEventListener("popstate", function() {
        if(location.href.indexOf("blog?") === -1) {
            toggleBlogposts(false);
            setTimeout(function() {
                window.scrollTo(0, scrollPosition);
            }, 25);
        }
    });

    // In case a blog is already chosen, render it on page load, lazyloaded.
    if(location.href.indexOf("blog?") !== -1 && currentPost) {
        fetch("/netz/blog/fetch?id=" + currentPost)
            .then(response => response.text())
            .then(result => {
                toggleBlogposts(true, currentPost, result);
        });
    }

    // Call the routing function upon every element that should route.
    route(document.querySelectorAll(".read-more a"));
    route(blogTitles);
}();
