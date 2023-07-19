<?php
$directory = 'Takeout/Keep';
$files = scandir($directory);
rsort($files);
?>
<html>
<head>
<title>my Keep</title>
<style>
    .note {
        border-radius: 10px;
        overflow: hidden;
    }

    .card {
        width: 33.33%;
        padding: 5px;
        box-sizing: border-box;
        margin-bottom: 0px;
        vertical-align: top;
        font-size: 16px;
    }

    .filter-container {
        display: flex;
        justify-content: center;
        margin-bottom: 0px;
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

    .archived-note {
        display: none;
    }
</style>
</head>
<body>
<div class="filter-wrapper">
    <div class="filter-container">
        <input type="text" id="filter" onkeyup="filterCards()" placeholder="Enter relevance">
    </div>
</div>

<div class="card-container" id="card-container">
    <?php
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) == 'html') {
            $content = file_get_contents($directory . '/' . $file);
            $isArchived = strpos($content, 'Nota archiviata') !== false;
            if ($isArchived) {
                echo '<div class="card archived-note"><div class="card-content note">' . $content . '</div></div>';
            } else {
                echo '<div class="card" id="' . $file . '"><div class="card-content note">' . $content . '</div></div>';
            }
        }
    }
    ?>
</div>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/packery/2.1.2/packery.pkgd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/draggabilly/2.3.0/draggabilly.pkgd.min.js"></script>
<script>
    var cardContainer = document.getElementById('card-container');
    var packery = new Packery(cardContainer, {
        itemSelector: '.card',
        gutter: 0
    });

    var draggableElements = document.getElementsByClassName('card');
    for (var i = 0; i < draggableElements.length; i++) {
        var draggie = new Draggabilly(draggableElements[i]);
        packery.bindDraggabillyEvents(draggie);
    }

    // Save card positions
    function saveLayout() {
        var cardPositions = Array.from(cardContainer.children).reduce(function(acc, card) {
            var rect = card.getBoundingClientRect();
            acc[card.id] = {
                left: rect.left,
                top: rect.top
            };
            return acc;
        }, {});
        localStorage.setItem('cardPositions', JSON.stringify(cardPositions));
    }

    // Restore card positions
    function restoreLayout() {
        var cardPositions = localStorage.getItem('cardPositions');
        if (cardPositions) {
            cardPositions = JSON.parse(cardPositions);
            Object.keys(cardPositions).forEach(function(cardId) {
                var card = document.getElementById(cardId);
                if (card) {
                    var position = cardPositions[cardId];
                    card.style.left = position.left + 'px';
                    card.style.top = position.top + 'px';
                }
            });
        }
    }

    // Save layout on drag or reposition
    packery.on('dragItemPositioned', saveLayout);

    function filterCards() {
        var filterValue = document.getElementById("filter").value.toLowerCase();
        var cards = document.getElementsByClassName("card");

        for (var i = 0; i < cards.length; i++) {
            var cardText = cards[i].getElementsByClassName("card-content")[0].textContent.toLowerCase();
            var isArchived = cards[i].classList.contains('archived-note');

            if (cardText.includes(filterValue) && (!isArchived || filterValue !== '')) {
                cards[i].style.display = "inline-block";
            } else {
                cards[i].style.display = "none";
            }
        }

        packery.layout();
    }

    // Restore layout on page load
    restoreLayout();
</script>
</html>
