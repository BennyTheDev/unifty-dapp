function toggleName(inner) {
    const id = 'collapse2'+ inner
    const div = document.querySelector('#'+id);
    const btn = document.getElementById(inner);
    if(div.classList.contains('show') == true) {
        btn.innerText="Show Traits";
    } else {
        btn.innerText="Hide Traits";
    }
}