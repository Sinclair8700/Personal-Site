let physicsPaint = null;

class PhysicsPaint {
    constructor(canvas) {
        this.canvas = canvas;
        this.ctx = canvas.getContext('2d');
    }

    set_parameters(){
        this.gravity = document.querySelector('#gravity').value;
        this.atom_size = document.querySelector('#atom-size').value;
        this.canvas.classList.remove('hidden');
        document.querySelector('#parameters').classList.add('hidden');
    }
}


if (document.querySelector('#physics-canvas')) {
    const canvas = document.querySelector('#physics-canvas');
    const ctx = canvas.getContext('2d');

    physicsPaint = new PhysicsPaint(canvas);

}

if (document.querySelector('#start-button')) {
    document.querySelector('#start-button').addEventListener('click', () => {
        physicsPaint.set_parameters();
    });
}



