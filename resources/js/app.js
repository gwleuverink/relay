const { shell } = require('electron')

Alpine.directive('default-browser', (el) => {
    if(el.nodeName !== 'A') {
        return;
    }

    el.addEventListener('click', event => {
        event.preventDefault();
        event.stopPropagation()

        shell.openExternal(el.href);
    });


    return (new Date).toLocaleTimeString()
})
