//Script used for giving a starting theme to the page
if (localStorage.getItem("pageTheme") === "light"){
    document.body.classList.add("light-edition");
}
else{
    document.body.classList.add("dark-edition");
}