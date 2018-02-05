var canvas, canvas2, ctx, ctx2, sigCanvas, sigCanvas2, sigCtx, sigCtx2, flag = false,
        prevX = 0,
        currX = 0,
        prevY = 0,
        currY = 0,
        dot_flag = false;
var x = "black",
        y = 2;

function init() {
    canvas = document.getElementById('layer1');
    ctx = canvas.getContext("2d");
    w = canvas.width;
    h = canvas.height;
    img = new Image();
    img.src = 'internal.png';
    img.onload = function () {
        canvas2 = document.getElementById('layer2');
        ctx2 = canvas2.getContext("2d");
        ctx2.drawImage(img, 0, 0);
    }
    sigCanvas = document.getElementById('sigLayer1');
    sigCtx = sigCanvas.getContext("2d");
    img2 = new Image();
    img2.src = 'sigImg.png';
    img2.onload = function () {
        sigCanvas2 = document.getElementById('sigLayer2');
        sigCtx2 = sigCanvas2.getContext("2d");
        sigCtx2.drawImage(img2, 0, 0);
    }

    canvas.addEventListener("mousemove", function (e) {
        findxy('move', e)
    }, false);
    canvas.addEventListener("mousedown", function (e) {
        findxy('down', e)
    }, false);
    canvas.addEventListener("mouseup", function (e) {
        findxy('up', e)
    }, false);
    canvas.addEventListener("mouseout", function (e) {
        findxy('out', e)
    }, false);
    sigCanvas.addEventListener("mousemove", function (e) {
        Sfindxy('move', e)
    }, false);
    sigCanvas.addEventListener("mousedown", function (e) {
        Sfindxy('down', e)
    }, false);
    sigCanvas.addEventListener("mouseup", function (e) {
        Sfindxy('up', e)
    }, false);
    sigCanvas.addEventListener("mouseout", function (e) {
        Sfindxy('out', e)
    }, false);
}

function color(obj) {
    y = 2;
    ctx.globalCompositeOperation = "source-over";
    if (obj.id == "black") {
        x = "black";
    } else if (obj.id == "blue") {
        x = "blue";
    } else {
        x = "white";
        y = 14;
        ctx.globalCompositeOperation = "destination-out";
    }
}

function draw() {
    ctx.beginPath();
    ctx.moveTo(prevX, prevY);
    ctx.lineTo(currX, currY);
    ctx.strokeStyle = x;
    ctx.lineWidth = y;
    ctx.stroke();
    ctx.closePath();
}

function erase() {
    var m = confirm("Want to clear");
    if (m) {
        ctx.clearRect(0, 0, w, h);
        document.getElementById("canvasimg").style.display = "none";
    }
}

function savePic() {
    var dataURL = canvas.toDataURL();
    ctx2.drawImage(canvas, 0, 0);
    document.bodyForm.picURL.value = "" + canvas2.toDataURL();
    document.bodyForm.firstName.value = document.bodyForm.picURL.value;
    img = new Image();
    img.src = 'internal.png';
    img.onload = function () {
        canvas2 = document.getElementById('layer2');
        ctx2 = canvas2.getContext("2d");
        ctx2.drawImage(img, 0, 0);
    }
    saveSig();
}
function speak() {
    alert(canvas.getBoundingClientRect().top + " - ");
}

function findxy(res, e) {
    if (res == 'down') {
        prevX = currX;
        prevY = currY;
        currX = e.clientX - canvas.getBoundingClientRect().left;
        currY = e.clientY - canvas.getBoundingClientRect().top;

        flag = true;
        dot_flag = true;
        if (dot_flag) {
            ctx.beginPath();
            ctx.fillStyle = x;
            ctx.fillRect(currX, currY, 2, 2);
            ctx.closePath();
            dot_flag = false;
        }
    }
    if (res == 'up' || res == "out") {
        flag = false;
    }
    if (res == 'move') {
        if (flag) {
            prevX = currX;
            prevY = currY;
            currX = e.clientX - canvas.getBoundingClientRect().left;
            currY = e.clientY - canvas.getBoundingClientRect().top;
            draw();
        }
    }
}

function Sdraw() {
    sigCtx.beginPath();
    sigCtx.moveTo(prevX, prevY);
    sigCtx.lineTo(currX, currY);
    sigCtx.strokeStyle = "blue";
    sigCtx.lineWidth = 2;
    sigCtx.stroke();
    sigCtx.closePath();
}

function saveSig() {
    document.bodyForm.sigURL.value = "" + sigCanvas.toDataURL();
    document.bodyForm.lastName.value = document.bodyForm.sigURL.value;
}
function Sclear() {
    sigCtx.clearRect(0, 0, sigCanvas.width, sigCanvas.height);
}

function Sfindxy(res, e) {
    if (res == 'down') {
        prevX = currX;
        prevY = currY;
        currX = e.clientX - sigCanvas.getBoundingClientRect().left;
        currY = e.clientY - sigCanvas.getBoundingClientRect().top;
        flag = true;
        dot_flag = true;
        if (dot_flag) {
            sigCtx.beginPath();
            sigCtx.fillStyle = "blue";
            sigCtx.fillRect(currX, currY, 2, 2);
            sigCtx.closePath();
            dot_flag = false;
        }
    }
    if (res == 'up' || res == "out") {
        flag = false;
    }
    if (res == 'move') {
        if (flag) {
            prevX = currX;
            prevY = currY;
            currX = e.clientX - sigCanvas.getBoundingClientRect().left;
            currY = e.clientY - sigCanvas.getBoundingClientRect().top;
            Sdraw();
        }
    }
}