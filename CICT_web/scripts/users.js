editBtn = document.getElementById("editBtn");
deleteBtn = document.getElementById("deleteBtn");

var tableRows = document.querySelectorAll("#table tbody tr");
tableRows.forEach(function(row, index) {
    row.addEventListener("click", function() {
        tableRows.forEach(function(row) {
            row.classList.remove("bg-danger", "text-white");
            
        });
        
        this.classList.add("bg-danger", "text-white");
        uid = this.querySelector('#uid').textContent;
        firstName = this.querySelector('#firstName').textContent;
        lastName = this.querySelector('#lastName').textContent;
        username = this.querySelector('#username').textContent;
        email = this.querySelector('#email').textContent;
        role = this.querySelector('#role').textContent;

        editBtn.removeAttribute("disabled");
        deleteBtn.removeAttribute("disabled");
        
        document.getElementById("update_uid").value = uid;
        document.getElementById("update_fName").value = firstName;
        document.getElementById("update_lName").value = lastName;
        document.getElementById("update_username").value = username;
        document.getElementById("update_email").value = email;
        document.getElementById("update_Role").textContent = "-- "+ role +" --";
        document.getElementById("userId").textContent = "Update - ID # " + uid;

        document.getElementById("delete_id").textContent= "Delete - ID # " + uid;
        document.getElementById("delete_uid").value = uid;
        document.getElementById("deleteTxt").textContent = uid + " • " + firstName + "  " + lastName + " • " + username + " • " + email;

    });
});

