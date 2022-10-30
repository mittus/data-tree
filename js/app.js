document.querySelector('main').addEventListener('click', function(e) {
    if (e.target.tagName.toLowerCase() === 'a') {

        let element = e.target;
        let attr = element.getAttribute('href');

        if(Array.from(attr)[0] == '#') {
            e.preventDefault();
            let id = element.closest('li').dataset.id;

            if(!id) id = -1;

            let action = attr.substring(1);

            if(action == 'toggle') {
                let parent = document.querySelector('[data-parent="' + id + '"]');
                if(parent) {
                    if(parent.style.display === 'none') {
                        parent.style.display = '';
                        element.classList.add('expand');
                    } else {
                        parent.style.display = 'none';
                        element.classList.remove('expand');
                    }
                    return false;
                }
            }

            jsonRequest(action, id, element);
        }
    }

    if (e.target.tagName.toLowerCase() === 'h2') {
        let element = e.target.closest('a');
        if(element) {
            let attr = element.getAttribute('href');

            if(Array.from(attr)[0] == '#') {
                let id = element.closest('li').dataset.id;
                let action = attr.substring(1);
                
                jsonRequest(action, id, element);
            }
        }
    }
}, false);

let jsonRequest = (action, id, el) => {
    const request = new Request('/?page=json', {
        method: 'POST',
        body: new URLSearchParams({action: action, id: id})
    });

    const result = fetch(request)
        .then( async (response) => {
            let data = await response.json();

            if (response.status === 200) {
                if(data.html) {
                    if(action == 'add' || action == 'edit' || action == 'show') {
                        let fixed = document.getElementById('fixed');
                        fixed.innerHTML = data.html;
                        fixed.style.display = 'flex';

                        fixed.addEventListener('mousedown', (e) => {
                            if(e.target.getAttribute('id') == 'fixed') {
                                fixed.addEventListener('mouseup', (e) => {
                                    if(e.target.getAttribute('id') == 'fixed') {
                                        fixed.style.display = 'none';
                                    }
                                });
                            }
                        });
                    }
                    if(action == 'toggle') {
                        let li = document.createElement('li');
                        li.setAttribute('data-parent', id);
                        li.innerHTML = data.html;
                        el.closest('li').parentNode.insertBefore(li, el.closest('li').nextSibling);
                        el.classList.add('expand');
                    }
                    if(action == 'remove') {
                        let element = document.querySelector('[data-id="' + id + '"]');
                        let children = document.querySelector('[data-parent="' + id + '"]');

                        if(children) {
                            children.remove();
                        }

                        let parent = element.closest('[data-parent]');

                        element.remove();

                        if(parent) {
                            let parentId = parent.dataset.parent;
                            let container = document.querySelector('[data-parent="' + parentId + '"]');
                            
                            if(!container.children[0].hasChildNodes()) {
                                container.remove();
                                let toggle = document.querySelector('[data-id="' + parentId + '"]')
                                                .getElementsByClassName('toggle');
                                if(toggle) toggle[0].remove();
                            }
                        }
                    }
                }
            } else {
                throw new Error('Wrong API server!');
            }
        });
}