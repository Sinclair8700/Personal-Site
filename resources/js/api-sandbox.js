
if(document.querySelector('#token')?.getAttribute('data-token')){
    setInterval(() => {
        fetch('/api/message?token=' + document.querySelector('#token').getAttribute('data-token'))
            .then(response => response.json())
            .then(data => {
                document.querySelector('#messages').innerHTML = data.data.map(message => `<p>${message}</p>`).join('');
            });
    }, 5000);
}
