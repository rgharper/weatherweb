var changed = function (){
    var from = new Date(document.getElementById("from-input").value)
    var to = new Date(document.getElementById("to-input").value)
    var delta = to-from
    console.log(from.valueOf())
    console.log(to.valueOf())
    var unit = " days"
    if (delta > 86400000){
        delta = delta / 86400000
    }
    else if (delta > 3600000) {
        delta = delta / 3600000
        unit = " hours"
    }
    else if (delta > 60000) {
        delta = delta / 60000
        unit = " minutes"
    }
    else if (delta > 1000) {
        delta = delta / 1000
        unit = " seconds"
    }
    length = document.getElementById("length")
    length.textContent = delta + unit
}

var load = function (){
    var from = new Date(document.getElementById("from-input").value)
    var to = new Date(document.getElementById("to-input").value)
}