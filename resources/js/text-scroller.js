// Give scrollable text boxes some right padding when they overflow, so the scroll bar doesn't cover
// the text (e.g. the description on project cards). Only elements that actually scroll are touched;
// everything else keeps the padding its classes give it.
function addPaddingToTextBoxes() {
    document.querySelectorAll('p, h2, h3, h4, h5, h6, span').forEach(textBox => {
        const overflowY = getComputedStyle(textBox).overflowY;
        if (overflowY !== 'auto' && overflowY !== 'scroll') {
            return;
        }

        textBox.style.paddingRight = textBox.scrollHeight > textBox.clientHeight ? '16px' : '';
    });
}

addPaddingToTextBoxes()
window.addEventListener('resize', addPaddingToTextBoxes)
