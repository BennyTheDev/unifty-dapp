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

function toggleList(id,erc) {
    console.log(erc,id);
    const inner_id = 'collapse'+erc+id;
    const new_id=erc+id;
    console.log(inner_id);
    const div = document.querySelector('#'+inner_id);
    const btn = document.getElementById(new_id);
    console.log(div,btn);
    if(div.classList.contains('show') == true) {
        btn.innerText="Show Traits";
    } else {
        btn.innerText="Hide Traits";
    }
}