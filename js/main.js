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



function toggleVideo(id, address) {
    const img = document.getElementById(`${id}${address}`);
    const div = document.querySelector(`#collapseVideo${address}${id}`);  
    const text = document.getElementById(`${address}${id}`);

    if(div.classList.contains('show') == true) {
        img.style.display = 'block';
        text.innerHTML =' ' +' PLAY VIDEO';
        
    } else {
        img.style.display = 'none';
        text.innerHTML = ' '+' HIDE VIDEO';
    }
}