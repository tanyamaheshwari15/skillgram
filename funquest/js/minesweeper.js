let rows, columns, minesCount, board = [], minesLocation = [], tilesClicked = 0, flagEnabled = false, gameOver = false;

function startGame() {
    // Reset the game state before starting a new game
    gameOver = false;
    tilesClicked = 0;
    minesLocation = []; // Clear previous mines
    const boardElement = document.getElementById("board");
    
    // Clear any previous board content
    boardElement.innerHTML = "";

    // Get user inputs for rows, columns, and mines
    rows = parseInt(document.getElementById("rows").value);
    columns = parseInt(document.getElementById("columns").value);
    minesCount = parseInt(document.getElementById("mines").value);
    document.getElementById("flag-button").addEventListener("click", setFlag);

    // Calculate the board width dynamically based on columns
    const tileWidth = 50;
    const boardWidth = columns * tileWidth + (columns - 1) * 2;
    boardElement.style.width = `${boardWidth}px`;

    // Set the grid layout for rows and columns dynamically
    boardElement.style.gridTemplateRows = `repeat(${rows}, 1fr)`;
    boardElement.style.gridTemplateColumns = `repeat(${columns}, 1fr)`;

    // Create the tiles for the new board
    for (let r = 0; r < rows; r++) {
        for (let c = 0; c < columns; c++) {
            let tile = document.createElement("div");
            tile.id = `${r}-${c}`;
            tile.classList.add("tile");
            tile.addEventListener("click", clickTile); // Add event listener to each tile
            tile.addEventListener("contextmenu", flagTile); // Add right-click event for flagging
            boardElement.appendChild(tile);
        }
    }

    // Set the mines for the new board
    setMines(rows, columns, minesCount);
}

// Set Mines function (randomly places mines)
function setMines(rows, columns, minesCount) {
    let minesLeft = minesCount;

    while (minesLeft > 0) {
        let r = Math.floor(Math.random() * rows);
        let c = Math.floor(Math.random() * columns);
        let id = `${r}-${c}`;

        // Ensure we don't place multiple mines in the same spot
        if (!minesLocation.includes(id)) {
            minesLocation.push(id);
            minesLeft -= 1;
        }
    }

    console.log("Mines placed at:", minesLocation);  // For debugging purposes
}

// Handle tile click
function clickTile() {
    if (gameOver || this.classList.contains("tile-clicked")) {
        return;
    }

    let tile = this;
    let tileId = tile.id;

    // If flagging mode is on, place a flag and do nothing else
    if (flagEnabled) {
        if (tile.innerHTML === "") {
            tile.innerHTML = "🚩"; // Place a flag
        } else if (tile.innerHTML === "🚩") {
            tile.innerHTML = ""; // Remove the flag
        }
        return; // Exit the function to avoid further actions (like revealing a mine)
    }

    // If it's not in flagging mode, proceed with regular game actions
    if (minesLocation.includes(tileId)) {
        // If it's a mine, check if it's flagged
        if (tile.innerHTML === "🚩") {
            // If flagged, do nothing (no game over alert)
            tile.classList.add("mine");
            tile.innerHTML = "💣";  // Show mine emoji
            return;  // Don't show the "Game Over" alert
        } else {
            // If it's not flagged, show the "Game Over" alert
            gameOver = true;
            tile.classList.add("mine");
            tile.innerHTML = "💣";
            revealMines(); // Reveal all mines
            alert("GAME OVER! You clicked on a mine.");
            return; // Exit to prevent further actions
        }
    }
    tile.classList.add("tile-clicked");
    tile.style.backgroundColor = "#d3d3d3"; // Make tile light gray when clicked
    tilesClicked += 1;
    let coords = tile.id.split("-");
    let r = parseInt(coords[0]);
    let c = parseInt(coords[1]);
    revealAdjacentTiles(r, c);

    if (tilesClicked === rows * columns - minesCount) {
        alert("Congratulations! You've cleared the board!");
        gameOver = true;
    }
}

// Reveal all mines
function revealMines() {
    for (let i = 0; i < rows; i++) {
        for (let j = 0; j < columns; j++) {
            let tile = document.getElementById(`${i}-${j}`);
            if (minesLocation.includes(`${i}-${j}`)) {
                tile.classList.add("mine");
                tile.innerHTML = "💣";  // Show bomb emoji for mines
            }
        }
    }
}

// Reveal adjacent tiles
function revealAdjacentTiles(r, c) {
    let tile = document.getElementById(`${r}-${c}`);
    let minesAround = countMinesAround(r, c);

    if (minesAround > 0) {
        tile.classList.add(`number-${minesAround}`);
        tile.innerHTML = getEmojiForNumber(minesAround);
    } else {
        tile.innerHTML = "";
        // Recursively reveal surrounding tiles if no mines are adjacent
        for (let i = r - 1; i <= r + 1; i++) {
            for (let j = c - 1; j <= c + 1; j++) {
                if (i >= 0 && i < rows && j >= 0 && j < columns && !document.getElementById(`${i}-${j}`).classList.contains("tile-clicked")) {
                    revealAdjacentTiles(i, j);
                }
            }
        }
    }
}

// Count mines around a given tile
function countMinesAround(r, c) {
    let count = 0;

    for (let i = r - 1; i <= r + 1; i++) {
        for (let j = c - 1; j <= c + 1; j++) {
            if (i >= 0 && i < rows && j >= 0 && j < columns && minesLocation.includes(`${i}-${j}`)) {
                count++;
            }
        }
    }

    return count;
}

// Get emoji representation for a given number of adjacent mines
function getEmojiForNumber(minesCount) {
    const emojis = [
        "🙂",  // 0 mines
        "1️⃣",  // 1 mine
        "2️⃣",  // 2 mines
        "3️⃣",  // 3 mines
        "4️⃣",  // 4 mines
        "5️⃣",  // 5 mines
        "6️⃣",  // 6 mines
        "7️⃣",  // 7 mines
        "8️⃣"   // 8 mines
    ];
    return emojis[minesCount];
}

// Toggle flag on right-click
function flagTile(event) {
    event.preventDefault(); // Prevent default context menu from appearing

    if (gameOver || this.classList.contains("tile-clicked")) {
        return;
    }

    let tile = this;

    if (flagEnabled) {
        if (tile.innerHTML === "") {
            tile.innerHTML = "🚩"; // Place a flag
        } else if (tile.innerHTML === "🚩") {
            tile.innerHTML = ""; // Remove the flag
        }
    }
}

// Enable or disable flagging mode
function setFlag() {
    flagEnabled = !flagEnabled;
    document.getElementById("flag-button").style.backgroundColor = flagEnabled ? "pink" : "lightgray";
}