$(document).ready(function () {
    var vuex = localStorage.getItem("vuex");
    if (vuex) {
        console.log(vuex);
    }
    $("#login").attr("href", "https://www.iitcebu.net/");
});
