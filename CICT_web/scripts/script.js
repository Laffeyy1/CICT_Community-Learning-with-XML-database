function active_login(){
    document.getElementsByClassName("login")[0].classList.add("active");
}

function active_signup(){
    document.getElementsByClassName("signup")[0].classList.add("active")
}

function active_about(){
    document.getElementsByClassName("about")[0].classList.add("active")
}

function post(){
    console.log("cliclks");
}

jQuery(document).ready(function(e) {
    $('#complete').on('hidden.bs.modal', function () {
      window.location.href = "login.php"
    })
});

function login(){
    console.log("ano")
    window.open("login.php");
}

