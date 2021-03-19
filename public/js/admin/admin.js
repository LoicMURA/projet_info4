window.addEventListener('click', (e) => {
    let target = e.target
    if (target.classList.contains('js-delete-product') || target.classList.contains('js-delete-category')) {
        e.preventDefault();
        let path = `../nos-t-shirts/${target.id}`;
        if (target.classList.contains('js-delete-category')) path = `../categorie/${target.id}`
        jsonFetch([path, {'method': 'DELETE'}], () => {
            removeAdminLine(e.target);
        })
    }
})

function removeAdminLine(button) {
    button.parentNode.parentNode.remove();
}