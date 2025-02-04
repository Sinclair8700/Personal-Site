// if a text box is overflowing add padding on the right to prevent text from being cut off by scroll bar
function addPaddingToTextBoxes() {
    document.querySelectorAll('p, h2, h3, h4, h5, h6, span').forEach(textBox => {
        let text = textBox.textContent;
        let textHeight = textBox.scrollHeight;
        let boxHeight = textBox.clientHeight;
        if (textHeight > boxHeight) {
            textBox.style.paddingRight = '16px';
        }
        else {
            textBox.style.paddingRight = '0px';
        }

    });
}

addPaddingToTextBoxes()
window.addEventListener('resize', addPaddingToTextBoxes)


