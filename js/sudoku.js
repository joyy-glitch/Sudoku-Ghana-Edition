// box shape: 4x4 uses 2x2 boxes, 6x6 uses 2x3 boxes
let boxRows = 2;
let boxCols = (size == 4) ? 2 : 3;

let solution = [];
let puzzle = [];
let grid = [];
let selected = 1;
let finished = false;

function makeEmpty() {
    let g = [];
    for (let r = 0; r < size; r++) {
        let row = [];
        for (let c = 0; c < size; c++) {
            row.push(0);
        }
        g.push(row);
    }
    return g;
}

function copyGrid(g) {
    return g.map(row => row.slice());
}

function shuffle(arr) {
    for (let i = arr.length - 1; i > 0; i--) {
        let j = Math.floor(Math.random() * (i + 1));
        let temp = arr[i];
        arr[i] = arr[j];
        arr[j] = temp;
    }
    return arr;
}

// check if symbol n can go at row r, column c
function canPlace(g, r, c, n) {
    for (let i = 0; i < size; i++) {
        if (g[r][i] == n || g[i][c] == n) {
            return false;
        }
    }
    let startRow = Math.floor(r / boxRows) * boxRows;
    let startCol = Math.floor(c / boxCols) * boxCols;
    for (let i = 0; i < boxRows; i++) {
        for (let j = 0; j < boxCols; j++) {
            if (g[startRow + i][startCol + j] == n) {
                return false;
            }
        }
    }
    return true;
}

// fill the whole grid using backtracking
function fillGrid(g) {
    for (let r = 0; r < size; r++) {
        for (let c = 0; c < size; c++) {
            if (g[r][c] == 0) {
                let nums = [];
                for (let n = 1; n <= size; n++) {
                    nums.push(n);
                }
                shuffle(nums);
                for (let n of nums) {
                    if (canPlace(g, r, c, n)) {
                        g[r][c] = n;
                        if (fillGrid(g)) {
                            return true;
                        }
                        g[r][c] = 0;
                    }
                }
                return false;
            }
        }
    }
    return true;
}

// count solutions, stopping early once we find more than one
function countSolutions(g) {
    for (let r = 0; r < size; r++) {
        for (let c = 0; c < size; c++) {
            if (g[r][c] == 0) {
                let count = 0;
                for (let n = 1; n <= size; n++) {
                    if (canPlace(g, r, c, n)) {
                        g[r][c] = n;
                        count += countSolutions(g);
                        g[r][c] = 0;
                        if (count > 1) {
                            return count;
                        }
                    }
                }
                return count;
            }
        }
    }
    return 1;
}

// make a full grid, then remove cells while keeping one solution
function makePuzzle() {
    solution = makeEmpty();
    fillGrid(solution);
    puzzle = copyGrid(solution);

    let toRemove = (size == 4) ? 8 : 16;
    let cells = [];
    for (let i = 0; i < size * size; i++) {
        cells.push(i);
    }
    shuffle(cells);

    let removed = 0;
    for (let k of cells) {
        if (removed >= toRemove) {
            break;
        }
        let r = Math.floor(k / size);
        let c = k % size;
        let backup = puzzle[r][c];
        puzzle[r][c] = 0;

        if (countSolutions(copyGrid(puzzle)) != 1) {
            puzzle[r][c] = backup;
        } else {
            removed++;
        }
    }
    grid = copyGrid(puzzle);
}

function symbolImage(n) {
    let s = symbols[n - 1];
    return '<img src="images/symbols/' + s.image + '" alt="' + s.name + '" title="' + s.name + '">';
}

function drawBoard() {
    let board = document.getElementById("board");
    board.innerHTML = "";
    board.style.gridTemplateColumns = "repeat(" + size + ", 60px)";

    for (let r = 0; r < size; r++) {
        for (let c = 0; c < size; c++) {
            let cell = document.createElement("div");
            cell.className = "cell";

            if ((c + 1) % boxCols == 0 && c != size - 1) {
                cell.classList.add("box-right");
            }
            if ((r + 1) % boxRows == 0 && r != size - 1) {
                cell.classList.add("box-bottom");
            }

            if (puzzle[r][c] != 0) {
                cell.classList.add("given");
            } else if (grid[r][c] != 0 && grid[r][c] != solution[r][c]) {
                cell.classList.add("wrong");
            }

            if (grid[r][c] != 0) {
                cell.innerHTML = symbolImage(grid[r][c]);
            }

            if (puzzle[r][c] == 0) {
                cell.onclick = function () {
                    placeSymbol(r, c);
                };
            }
            board.appendChild(cell);
        }
    }
}

function drawPalette() {
    let palette = document.getElementById("palette");
    palette.innerHTML = "";

    for (let n = 1; n <= size; n++) {
        let btn = document.createElement("button");
        btn.className = "palette-btn";
        if (n == selected) {
            btn.classList.add("selected");
        }
        btn.innerHTML = symbolImage(n) + "<br>" + symbols[n - 1].name;
        btn.onclick = function () {
            selected = n;
            drawPalette();
        };
        palette.appendChild(btn);
    }

    let eraser = document.createElement("button");
    eraser.className = "palette-btn";
    if (selected == 0) {
        eraser.classList.add("selected");
    }
    eraser.textContent = "Erase";
    eraser.onclick = function () {
        selected = 0;
        drawPalette();
    };
    palette.appendChild(eraser);
}

function placeSymbol(r, c) {
    if (finished) {
        return;
    }
    grid[r][c] = selected;
    drawBoard();
    checkWin();
}

function checkWin() {
    for (let r = 0; r < size; r++) {
        for (let c = 0; c < size; c++) {
            if (grid[r][c] != solution[r][c]) {
                return;
            }
        }
    }
    finished = true;
    document.getElementById("message").textContent = "Ayekoo! You solved it!";
    saveGame();
}

// send the result to PHP without reloading the page
function saveGame() {
    let data = new FormData();
    data.append("level", level);
    data.append("grid_size", size);

    fetch("save_game.php", {
        method: "POST",
        body: data
    })
        .then(response => response.text())
        .then(text => {
            if (text == "saved") {
                document.getElementById("message").textContent += " Your progress has been saved.";
            }
        });
}

makePuzzle();
drawBoard();
drawPalette();