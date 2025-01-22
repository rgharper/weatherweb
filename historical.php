<!DOCTYPE html>
<html>

<head>
    <title>Weather Station</title>
    <script src="historical.js" type="text/javascript"></script>
    <link rel="stylesheet" href="style.css">
    <meta charset="utf-8">
    <link rel="icon" href="weather.svg" sizes="any" type="image/svg+xml">
</head>

<body>
    <?php include "nav.php" ?>
    <br>
    <flex-frame>
        <stat-column>
            <div class="fadebox" style="animation-delay: 0ms">
                Between <input type="datetime-local" id="from-input" onchange="changed()">
                and <input type="datetime-local" id="to-input" onchange="changed()">
                <button id="go" onclick="load()">Search!</button>
                <div id="length" style="display: inline"></div>
            </div>
        </stat-column>
    </flex-frame>
    <br>
    <flex-frame>
        
    </flex-frame>
    <br>
    <flex-frame>
        
    </flex-frame>
    <br>
    <flex-frame>
        
    </flex-frame>
</body>

</html>