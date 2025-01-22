var rpmA = document.getElementById("rpmA")
var rpmB = document.getElementById("rpmB")
var spinnerA = document.getElementById("spinnerA")
var spinnerB = document.getElementById("spinnerB")
console.log(rpmA, rpmB,)

// rpmA.addEventListener("change", onchange, false);
// rpmB.addEventListener("change", onchange, false);

function onchange() {
  var duration = 60 / rpmA.textContent;
  spinnerA.style.animationDuration = duration + 's';
  var duration = 60 / rpmB.textContent;
  spinnerB.style.animationDuration = duration + 's';
}

window.onload = onchange