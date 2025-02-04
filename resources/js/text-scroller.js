// if a text box is overflowing add padding on the right to prevent text from being cut off by scroll bar


document.querySelectorAll('p, h2, h3, h4, h5, h6, span').forEach(textBox => {
    const text = textBox.textContent;
    const textHeight = textBox.scrollHeight;
    const boxHeight = textBox.clientHeight;
    if (textHeight > boxHeight) {
        textBox.style.paddingRight = '16px';
    }

});


