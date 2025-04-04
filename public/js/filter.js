document.addEventListener("DOMContentLoaded", function() {
    const categoryFilter = document.getElementById("categoryFilter");
    const tagFilter = document.getElementById("tagFilter");
    const blogCards = document.querySelectorAll(".blog-card");
    const notFoundImg = document.getElementById("notFoundImg");
    // const notFoundResult = document.getElementById("notFoundResult");

    // notFoundImg.style.display = "none"; 
    notFoundImg.style.setProperty('display', 'none', 'important');
    // notFoundResult.style.display = "none"; 

    function filterPosts() {
        const selectedCategory = categoryFilter.value.toLowerCase();
        const selectedTag = tagFilter.value.toLowerCase();

        let visibleCount = 0; // Counter to track visible cards
        var j=0;
        var marginCatch=0;

        
        

        blogCards.forEach(card => {
            const cardCategory = card.getAttribute("data-category").toLowerCase();
            const cardTags = card.getAttribute("data-tags").toLowerCase();

            const matchesCategory = selectedCategory === "" || cardCategory === selectedCategory;
            const matchesTag = selectedTag === "" || cardTags.includes(selectedTag);

            if (matchesCategory && matchesTag) {
                card.style.display = "block";
                visibleCount++;
            } else {
                card.style.display = "none";
            }
        });


        for(var i=0; i<blogCards.length; i++){
            if(blogCards[i].style.display == 'block'){
                j++;
                console.log(j);
                marginCatch = i;
            }
            blogCards[i].classList.remove("mx-auto");          


            if(i==(blogCards.length-1) && j==1){
                blogCards[marginCatch].classList.add("mx-auto");
                console.log("Catched");
            }

        }

        // Show/hide "not found" message
        if (visibleCount === 0) {
            notFoundImg.style.display = "block";

        } else {
            notFoundImg.style.setProperty('display', 'none', 'important');
        }

        if (blogCards.length % 3 === 1) {
            blogCards[blogCards.length - 1].classList.add("mx-auto");
        }
 


        


    }

    categoryFilter.addEventListener("change", filterPosts);
    tagFilter.addEventListener("change", filterPosts);


    if (blogCards.length % 3 === 1) {
        blogCards[blogCards.length - 1].classList.add("mx-auto");
    }
    

});
