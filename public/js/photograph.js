navigator.mediaDevices.getUserMedia({video: true})
.then(function (mediaStream) {
    const video = document.querySelector('#camera');
    video.srcObject = mediaStream;
    video.play();
})
.catch(function (err) {
    console.log('Não há permissões para acessar a webcam')
})

function photograph(){
    var canvas = document.querySelector("#imagem-capturada");
    var video = document.querySelector("#camera")
    video.pause()
    video.style.display = "none";
    canvas.height = video.videoHeight;
    canvas.width = video.videoWidth;
    var context = canvas.getContext('2d');
    context.drawImage(video, 0, 0);
    canvas.style.display = "block";

    var buttonHidden = document.querySelector('#botao-capturar');
    buttonHidden.style.display = "none";
}

function reloadPage(){
    document.location.reload(true);
}