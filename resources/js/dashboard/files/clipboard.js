document.onpaste = function (event) {
    const items = (event.clipboardData || event.originalEvent.clipboardData).items;

    for (let i = 0; i < items.length; i++) {
        let item = items[i];

        if (item.type.indexOf("image") !== -1) {
            document.getElementById('clipboard-file').data = item.getAsFile();

            document.getElementById('clipboard-button').click();
        }
    }
}