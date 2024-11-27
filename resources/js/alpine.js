/*
|--------------------------------------------------------------------------
| Plugins
|--------------------------------------------------------------------------
*/
import collapse from "@alpinejs/collapse";
Alpine.plugin(collapse);

/*
|--------------------------------------------------------------------------
| Directives
|--------------------------------------------------------------------------
*/
const { shell } = require("electron");
window.shell = shell; // We also open stuff from the window

Alpine.directive("open-external", (el) => {
    if (el.nodeName !== "A") {
        return;
    }

    el.addEventListener("click", (event) => {
        event.preventDefault();
        event.stopPropagation();

        shell.openExternal(el.href);
    });
});

/*
|--------------------------------------------------------------------------
| Magics
|--------------------------------------------------------------------------
*/

Alpine.magic("contextMenu", () => (template) => {
    const { Menu } = require("@electron/remote");

    let menu = Menu.buildFromTemplate(template);
    menu.popup({ window: remote.getCurrentWindow() });
});
