$(function () {
    $(document).on("click", "#sil", function (e) {
        e.preventDefault();
        var link = $(this);
        var link3 = document.querySelector('#btn1');

        Swal.fire({
            title: "Are you sure?",
            text: "Permananently deleted?",
            icon: "warning",
            showCancelButton: true,
            cancelButtonText: "Cancel",
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete",
        }).then((result) => {
            if (result.isConfirmed) {
                console.log(link3);

                
                $(this).attr("id","");

                Swal.fire("Deleted!", "Permananetly deleted.", "success");

                setTimeout(clickButton, 1500);

                function clickButton() {
                    link.click();
                }
                
            }
        });
    });
});


function clickEdit(el) {
    // if(el.getAttribute("type") == 'button')
    Swal.fire("Edited!", "The post was edited.", "success");    
    setTimeout(edit, 1500);

    function edit() {
        el.setAttribute("type", "submit");
        el.removeAttribute("onclick");
        document.getElementById("input_content").removeAttribute("required");
        el.click();
    }
}

function clickAdd(el) {
    console.log(el);
    Swal.fire("Added!", "The new post was added.", "success");    
    setTimeout(add, 1500);

    function add() {
        el.setAttribute("type", "submit");
        el.removeAttribute("onclick");
        document.getElementById("input_content").removeAttribute("required");
        el.click();
    }
}