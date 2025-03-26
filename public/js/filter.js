    document.addEventListener("DOMContentLoaded", function() {
        const categoryFilter = document.getElementById("categoryFilter");
        const tagFilter = document.getElementById("tagFilter");
        const blogCards = document.querySelectorAll(".blog-card");

        function filterPosts() {
            const selectedCategory = categoryFilter.value.toLowerCase();
            const selectedTag = tagFilter.value.toLowerCase();

            blogCards.forEach(card => {
                const cardCategory = card.getAttribute("data-category").toLowerCase();
                const cardTags = card.getAttribute("data-tags").toLowerCase();
                
                const matchesCategory = selectedCategory === "" || cardCategory === selectedCategory;
                const matchesTag = selectedTag === "" || cardTags.includes(selectedTag);

                if (matchesCategory && matchesTag) {
                    card.style.display = "block";
                } else {
                    card.style.display = "none";
                }
            });
        }
        
        categoryFilter.addEventListener("change", filterPosts);
        tagFilter.addEventListener("change", filterPosts);
    });