<?php
$directory = 'Takeout/Keep';
$files = scandir($directory);
rsort($files);
?>
<html>
<head>
<title>my Keep </title>
<style>
	.note {
        border-radius: 10px;
        overflow: hidden;		
	}
    .card-container {
        display: flex;
        flex-wrap: wrap;
    }

    .card {
        width: 33%;
        padding: 10px;
        box-sizing: border-box;
    }

    .card-content {
        border-radius: 10px;
        overflow: hidden;
    }

    .filter-container {
        display: flex;
        justify-content: center;
        margin-bottom: 10px;
    }

    .filter-wrapper {
        width: 100%;
        position: fixed;
        top: 0;
        left: 0;
        background-color: #fff;
        padding: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        z-index: 999;
    }

    #filter {
        width: 80%;
        font-size: 18px;
    }

    #card-container {
        margin-top: 60px;
    }

    .archived-note {
        display: none;
    }
</style>
</head>
<body>
<div class="filter-wrapper">
    <div class="filter-container">
        <input type="text" id="filter" onkeyup="filterCards()" placeholder="search">
    </div>
</div>

<div class="card-container" id="card-container">
    <?php
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) == 'html') {
            $content = file_get_contents($directory . '/' . $file);
            $isArchived = strpos($content, 'Nota archiviata') !== false;
            if ($isArchived) {
                echo '<div class="card archived-note"><div class="card-content">' . $content . '</div></div>';
            } else {
                echo '<div class="card"><div class="card-content">' . $content . '</div></div>';
            }
        }
    }
    ?>
</div>
</body>
<script>
    function filterCards() {
        var filterValue = document.getElementById("filter").value.toLowerCase();
        var cards = document.getElementsByClassName("card");

        for (var i = 0; i < cards.length; i++) {
            var cardText = cards[i].getElementsByClassName("card-content")[0].textContent.toLowerCase();
            var isArchived = cards[i].classList.contains('archived-note');

            if (cardText.includes(filterValue) && (!isArchived || filterValue !== '')) {
                cards[i].style.display = "block";
            } else {
                cards[i].style.display = "none";
            }
        }
    }
</script>
</html>
