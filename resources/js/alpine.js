/*
|--------------------------------------------------------------------------
| Directives
|--------------------------------------------------------------------------
*/
const { shell } = require('electron')

Alpine.directive('open-external', (el) => {
    if(el.nodeName !== 'A') {
        return;
    }

    el.addEventListener('click', event => {
        event.preventDefault();
        event.stopPropagation()

        shell.openExternal(el.href);
    });
})

/*
|--------------------------------------------------------------------------
| Magics
|--------------------------------------------------------------------------
*/

Alpine.magic('contextMenu', () => template => {
    const { Menu } = require('@electron/remote');

    let menu = Menu.buildFromTemplate(template)
    menu.popup({ window: remote.getCurrentWindow() })
})
