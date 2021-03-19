let token = document.getElementById('js-card-token');
let cardTable = document.getElementById('js-card-table');
let cardEmptyMessage = document.getElementById('js-card-empty');

/**
 * Redirect to the link in the href attribut
 * @param {Node} element
 */
function redirectFromHref(element)
{
    window.location = window.location.origin + element.getAttribute('href');
}

/**
 * Create the little circle with the number of articles
 * @param {Number} value
 */
function createCardToken(value)
{
    const link = document.getElementById('js-card-link');
    if (token) updateCardToken(value);
    else if (value !== 0) {
        let token = document.createElement('div');
        token.classList.add('nav__shopCount');
        token.id = ('js-card-token');
        token.innerText = value;
        link.appendChild(token);
    } else {
        deleteCardToken();
    }
}

/**
 * Update the little circle with the number of articles
 * @param {Number} value
 */
function updateCardToken(value)
{
    if (token && value > 0) token.innerText = value;
    else if (value > 0) createCardToken(value);
    else deleteCardToken();
}

/**
 * Delete the little circle with the number of articles
 * @param {Number} value
 */
function deleteCardToken()
{
    token?.remove();
}

/**
 * Display a popup with a message for 2 secs
 * @param {String} message
 * @param {String} type
 * @param {Number} time
 */
function popup(message, type, time = 2000)
{
    let popup = document.createElement('div');
    popup.classList.add('popup', `popup--${type}`);
    popup.innerHTML = message;

    document.querySelector('body').appendChild(popup);
    setTimeout(() => {
        popup.remove();
    }, time);
}

function removeCardTable(printEmpty = true)
{
    cardTable?.remove();
    document.getElementById('js-commande-btn')?.remove();
    if (printEmpty) {
        let p = document.createElement('p');
        p.innerText = "Votre panier est vide !";
        p.id = 'js-card-empty';

        document.querySelector('main').appendChild(p);
        cardEmptyMessage = p;
    }
}

function updateCardTable(total)
{
    let table = document.getElementById('js-card-table');
    let body = table.querySelector('tbody');
    let lines = body.querySelectorAll('tr');
    for (let line = 1; line <= lines.length; line++) {
        lines[line - 1].querySelector('th').innerText = line;
    }

    let footer = table.querySelector('tfoot');
    footer.querySelector('.table__center').innerText = getPrice(total);
}

function removeTableLine(line, newTotal)
{
    let body = line.parentNode;
    line.remove();
    if (body.children.length < 1) removeCardTable();
    else {
        updateCardTable(newTotal);
    }
}

function createTableCell(type = 'td', content = '', classes = null, scope = null)
{
    let cell = document.createElement(type);
    cell.innerHTML = content;
    if (classes) cell.classList.add(classes);
    if (scope) cell.setAttribute('scope', scope);

    return cell;
}

function getPrice(price)
{
    let strPrice = price.toFixed(2);
    return strPrice.replace('.', ',') + '€';
}

function getParent(node, type)
{
    let parent = node.parentNode;
    while (parent.tagName !== type && !parent.classList.contains(type) && parent.tagName !== "BODY") {
        parent = parent.parentNode;
    }
    return parent;
}

function printComment(datas)
{

    let comment = document.createElement('div');
    comment.classList.add('card', 'card--comment');

    let supplement = '';
    if (datas.modifiedAt) {
        supplement = `<span class="text-muted"> (modifié le ${formatDate(datas.createdAt)})</span>`;
    }

    comment.innerHTML =
        `<div class="card-header">
            <h5 class="card-title">${datas.title}</h5>
            <span class="text-muted">Par ${datas.author} le ${formatDate(datas.createdAt)}</span>
            ${supplement}
        </div>
        <div class="card-body">
            <p class="card-text">${datas.content}</p>
        </div>
        <div class="card-footer d-flex justify-content-end align-items-center">
            <a id="js-comment-update-${datas.id}" href="#js-form-comment" class="btn btn-primary btn-sm me-2">Modifier le commentaire</a>
            <form method="post" action="/comment/${datas.id}" onsubmit="return confirm('Etes-vous sûr de vouloir supprimer ce commentaire ?');">
                <button class="btn btn-danger btn-sm" id="js-comment-delete">Supprimer le commentaire</button>
            </form>
        </div>`;

    let parent = document.querySelector('.comments');
    let sibling = document.getElementById('js-form-comment');
    parent.insertBefore(comment, sibling);
}

function formatDate(date)
{
    //2021-03-11T15:17:10+00:00
    let ArrDate = date.split('-');
    let time = [...ArrDate[2].matchAll(/(\d{2})/g)];
    let StrDate = `${ArrDate[2].match(/^\d{2}/)[0]}/${ArrDate[1]}/${ArrDate[0]} à ${parseInt(time[1][0]) + 1}:${time[2][0]}`;
    return StrDate;
}

function jsonFetch(headers = [], callback)
{
    fetch(headers[0], {
        method: headers[1].method,
        body: headers[1].body
    })
    .then(response => {
        let type = 'error';
        if (response.ok) {
            response.json().then((json) => {
                if (json.code === 200) {
                    callback(json);
                    type = 'succes';
                }
                popup(json.message, type);
            })
        } else {
            popup('Il y a eu une erreur réseau, veuillez réessayer', type);
        }
    })
}

function setDataAttribut(node, name, value, unique = true)
{
    if (unique) {
        let others = document.querySelectorAll(`[${name}="${value}"]`);
        others?.forEach(box => {
            box.removeAttribute(name);
        })
    }
    node.setAttribute(name, value);
}

function resetCommentForm(form)
{
    form.removeAttribute('data-target');
    form.querySelectorAll('input, textarea').forEach(input => {
        input.value = '';
    })
    form.querySelector('button').innerText = 'Poster le commentaire';
}

//==========================================================================================
// Links to products
const deck = document.querySelector('.card-deck');

let cards = deck?.querySelectorAll('.card');

cards?.forEach(card => {
    card.addEventListener('click', (e) => {
        if (e.target.tagName !== 'A') {
            redirectFromHref(card);
        }
    })
})

// Add some products to the card
let cardAddBtns = document.querySelectorAll('.js-card-btn');

cardAddBtns?.forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.preventDefault();
        let path = btn.getAttribute('href');

        jsonFetch(
            [path, {'method': 'POST'}],
            function(json) {
                createCardToken(Object.keys(json).length - 3);
            }
        );
    })
})

// Remove some products from the card
window.addEventListener('click', (e) => {
    if (e.target.classList.contains('js-card-remove') || e.target.parentNode?.classList.contains('js-card-remove')) {
        e.preventDefault();

        let btn = e.target.classList.contains('js-card-remove') ? e.target : e.target.parentNode;
        let path = btn.getAttribute('href');

        jsonFetch(
            [path, {'method': 'DELETE'}],
            function(json) {
                removeTableLine(btn.parentNode.parentNode, json.total);
                createCardToken(Object.keys(json).length - 3);
            }
        );
    }

    if (e.target.id === 'js-comment-delete') {
        e.preventDefault();

        let path = e.target.parentNode.getAttribute('action');
        let comment = getParent(e.target, 'card--comment');

        jsonFetch(
            [path, {'method': 'DELETE'}],
            function(json) { comment.remove(); }
        );
    }

    if (e.target.id === 'js-comment-add') {
        e.preventDefault();

        let id = window.location.pathname.match(/\d{1,}$/)[0];
        let path = '/comment/new/'+id;

        jsonFetch(
            [path, {'method': 'POST', 'body': new FormData(e.target.parentNode)}],
            function(json) { printComment(json.data); }
        );
    }

    if (e.target.id.match(/js-comment-update-\d{1,}/)) {
        let comment = e.target.parentNode.parentNode;
        setDataAttribut(comment, 'data-update', 'true');
        let form = document.getElementById('js-form-comment').querySelector('form');
        form.setAttribute('data-target', `/comment/${e.target.id.match(/\d{1,}$/)[0]}/edit`);

        form.querySelector('input').value = comment.querySelector('.card-title').innerText;
        form.querySelector('textarea').value = comment.querySelector('.card-text').innerText;

        let btn = form.querySelector('button');
        btn.innerText = 'Modifier le commentaire';
        btn.id = 'js-comment-update';
    }

    if (e.target.id === 'js-comment-update') {
        e.preventDefault();

        let path = e.target.parentNode.getAttribute('data-target');
        let comment = document.querySelector('[data-update="true"]');

        jsonFetch(
            [path, {'method': 'POST', 'body': new FormData(e.target.parentNode)}],
            function(json) {
                comment.remove();
                printComment(json.data);
                resetCommentForm(e.target.parentNode);
            }
        );
    }
})