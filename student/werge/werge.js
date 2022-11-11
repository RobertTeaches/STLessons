let definitelySuperNotTheGuessWordNoCheating = ""
let leftHint = "", leftNumber = 0
let rightHint = "", rightNumber = 0
let guessNumber = 0
let currentGuess = ""
let words = []
let guesses = []
let dictionary = []

let x, y

let minLength = 4, maxLength = 6

// #region Setting Up Game
{

    function resetGame() {
        currentGuess = ""
        guessNumber = 0
        getGuessWord()

        while (!getHintWords()) {
            console.log("Getting New guess word :(")
            getGuessWord()
        }
        setGuessBoxes()
        setHintTexts()
        setupTooltips()
        setKeyboard()
        let nextButton = document.getElementById("next-button")
        nextButton.hidden = true
    }

    function getGuessWord() {
        do {
            definitelySuperNotTheGuessWordNoCheating = words[parseInt((Math.random() * words.length))]
        } while (definitelySuperNotTheGuessWordNoCheating.length > maxLength || definitelySuperNotTheGuessWordNoCheating.length < minLength || hasRepeats(definitelySuperNotTheGuessWordNoCheating))
    }

    function getHintWords() {
        let guessLetters = definitelySuperNotTheGuessWordNoCheating
        let guessLen = guessLetters.length
        let hintAdd, hintMult
        switch (guessLen) {
            case 4:
                hintAdd = 0
                hintMult = 2
                break;
            case 5:
                hintAdd = 2
                hintMult = 2
                break;
            case 6:
                hintAdd = 1
                hintMult = 3
                break;
        }

        let leftLetters = []
        leftNumber = parseInt(hintAdd + Math.ceil(Math.random() * hintMult))
        rightNumber = guessLen - leftNumber
        let rightLetters = []

        for (let l = 0; l < leftNumber; l++) {
            let n = parseInt(Math.random() * guessLetters.length)
            let letter = guessLetters[n]
            guessLetters = guessLetters.replace(letter, "")
            leftLetters.push(letter)
        }
        for (let r = 0; r < rightNumber; r++) {
            let n = parseInt(Math.random() * guessLetters.length)
            let letter = guessLetters[n]
            guessLetters = guessLetters.replace(letter, "")
            rightLetters.push(letter)
        }

        //Get Left Word
        let found = false
        let loopCount = 0
        while (!found) {
            found = true
            let i = parseInt(Math.random() * words.length)
            leftHint = words[i]
            if (leftHint.toLowerCase() === definitelySuperNotTheGuessWordNoCheating.toLowerCase()) {
                found = false
            }
            if (leftHint.length < definitelySuperNotTheGuessWordNoCheating.length) {
                found = false
            }
            if (leftHint.length + 3 <= leftNumber) {
                found = false
            }
            if (hasRepeats(leftHint.toLowerCase()))
                found = false
            for (let l of leftLetters) {
                if (!leftHint.toLowerCase().includes(l)) {
                    found = false
                    break
                }
            }
            for (let r of rightLetters) {
                if (leftHint.toLowerCase().includes(r)) {
                    found = false
                    break
                }
            }
            loopCount++
            if (loopCount > 10_000) {
                console.log("10k fails")
                break
            }
        }

        //Get Right Word
        found = false
        while (!found) {
            found = true
            let i = parseInt(Math.random() * words.length)
            rightHint = words[i]
            if (rightHint.toLowerCase() === definitelySuperNotTheGuessWordNoCheating.toLowerCase()) {
                found = false
            }
            if (rightHint.length < definitelySuperNotTheGuessWordNoCheating.length) {
                found = false
            }
            if (rightHint.length + 3 <= rightNumber)
                found = false;
            if (hasRepeats(rightHint.toLowerCase()))
                found = false
            for (let l of leftLetters) {
                if (rightHint.toLowerCase().includes(l)) {
                    found = false
                    break
                }
            }
            for (let r of rightLetters) {
                if (!rightHint.toLowerCase().includes(r)) {
                    found = false
                    break
                }
            }
            loopCount++
            if (loopCount > 10_000) {
                break
            }
        }
        if (loopCount > 10_000) {
            return false
        }
        else {
            return true
        }
    }

    function setHintTexts() {
        let left = document.getElementById("left-word")
        let leftN = document.getElementById("left-number")
        left.innerText = leftHint
        leftN.innerText = leftNumber

        let right = document.getElementById("right-word")
        let rightN = document.getElementById("right-number")
        right.innerText = rightHint
        rightN.innerText = rightNumber
    }

    function setGuessBoxes() {
        if (definitelySuperNotTheGuessWordNoCheating.length < 1)
            return;
        let numBoxes = definitelySuperNotTheGuessWordNoCheating.length
        let numRows = calculateNumberOfGuesses()


        //#region Clearing old boxes
        let guesses = document.querySelector(".guesses")
        let origRow = guesses.firstElementChild
        let origBox = origRow.firstElementChild

        let d = new DocumentFragment()
        d.append(origBox)
        origRow.innerHTML = ""
        origRow.className = "row"

        origBox.innerText = ""
        origBox.className = "letter-box hoverable"
        d.append(origRow)

        guesses.innerHTML = ""
        guesses.append(origRow)

        //#endregion

        //#region Adding new Boxes depending on row

        for (let r = 1; r < numRows; r++) {
            let cRow = origRow.cloneNode(true)
            cRow.id = "row_" + r
            for (let b = 0; b < numBoxes; b++) {
                let cBox = origBox.cloneNode(true)
                cBox.id = "box_" + r + "_" + b
                cRow.appendChild(cBox)
            }
            guesses.appendChild(cRow)
        }
        origRow.append(origBox)
        for (let b = 1; b < numBoxes; b++) {
            let cBox = origBox.cloneNode(true)
            cBox.id = "box_" + 0 + "_" + b
            origRow.appendChild(cBox)
        }
        origRow.classList.add("current")
    }

    function setKeyboard() {
        let keys = document.querySelectorAll(".keyboard-key")
        keys.forEach(key=>{
            if(!key.className.includes("wide"))
                key.className = "keyboard-key"
        })
    }
}
// #endregion

//#region Playing Game
function playerType(letter) {
    if (definitelySuperNotTheGuessWordNoCheating.length < currentGuess.length + 1)
        return;
    currentGuess += letter.toLowerCase()
    for (let l = 0; l < currentGuess.length; l++) {
        let boxId = "box_" + guessNumber + "_" + l
        let box = document.getElementById(boxId)
        box.innerText = currentGuess[l]
    }
    navigator.vibrate(15)
}
function playerBackspace() {
    currentGuess = currentGuess.substring(0, currentGuess.length - 1)
    navigator.vibrate(25)
    for (let l = 0; l < definitelySuperNotTheGuessWordNoCheating.length; l++) {
        let boxId = "box_" + guessNumber + "_" + l
        let box = document.getElementById(boxId)
        if (l < currentGuess.length)
            box.innerText = currentGuess[l]
        else
            box.innerText = ""
    }
}

function playerGuess() {

    
    if (definitelySuperNotTheGuessWordNoCheating.length != currentGuess.length) {
        navigator.vibrate([15, 20, 25])
        let row = document.getElementById(`row_${guessNumber}`)
        row.className = "row"
        row.classList.add("not-word")
        setTimeout(() => {
            row.className = "row current"
        }, 1000);
        return;
    }

    isRealWord(currentGuess).then((real) => {
        if (real) {
            //Guess is successful

            guesses.push(currentGuess)
            if (currentGuess === definitelySuperNotTheGuessWordNoCheating.toLowerCase()) {
                navigator.vibrate([15, 20, 15, 20, 15, 20, 50])
                //Player Wins!
                playerWin()
                addResultToServer()
                return
            }
            else {
                navigator.vibrate([15, 5, 15])
                for (let b = 0; b < definitelySuperNotTheGuessWordNoCheating.length; b++) {
                    let box = document.getElementById(`box_${guessNumber}_${b}`)
                    let letter = box.innerText.toLowerCase()
                    if (definitelySuperNotTheGuessWordNoCheating.toLowerCase()[b] == letter) {
                        box.classList.add("correct")
                        updateKeyboardHint(letter, "correct")
                    }
                    else if (definitelySuperNotTheGuessWordNoCheating.toLowerCase().includes(letter)) {
                        box.classList.add("wrong-position")
                        updateKeyboardHint(letter, "wrong-position")
                    }
                    else {
                        box.classList.add("not-in-word")
                        updateKeyboardHint(letter, "not-in-word")

                    }
                }
            }
            let row = document.getElementById(`row_${guessNumber}`)
            row.className = "row"
            guessNumber++
   
            if (guessNumber >= calculateNumberOfGuesses()) {
                addResultToServer()
                playerLose()
                return
            }
            row = document.getElementById(`row_${guessNumber}`)
            row.classList.add('current')
            currentGuess = ""
        }
        else {
            let row = document.getElementById(`row_${guessNumber}`)
            row.className = "row"
            row.classList.add("not-word")
            setTimeout(() => {
                row.className = "row current"
            }, 1000);
        }
    })


}

function playerWin() {
    let row = document.getElementById(`row_${guessNumber}`)
    row.className = "row"
    row.classList.add("win")
    let nextButton = document.getElementById("next-button")
    nextButton.className = ""
    nextButton.hidden = false
    nextButton.querySelector("h1").innerText = "You Win!"
}

function playerLose()
{
    let row = document.getElementById(`row_${guessNumber-1}`)
    row.className = "row"
    row.classList.add("not-word")
    let nextButton = document.getElementById("next-button")
    nextButton.hidden = false
    nextButton.className = ""
    nextButton.classList.add("lost-game")
    nextButton.querySelector("h1").innerText = "Good Try! The word was " + definitelySuperNotTheGuessWordNoCheating
}
//#endregion

//#region Utility Methods
function calculateNumberOfGuesses() {
    return 5;
}

async function getDefinition(word) {
    if (dictionary[word]) {
        return dictionary[word]
    }
    let apiURl = `https://api.dictionaryapi.dev/api/v2/entries/en/${word}`
    let response = await fetch(apiURl, {
        method: "GET",
    })
    let definition = await response.json()
    dictionary[word] = definition
    return definition
}

async function isRealWord(word) {

    if (words.includes(word)) return true;
    let definition = await getDefinition(word)
    if (definition["title"]) {
        return false
    }
    return true
}

function hasRepeats(str) {
    return /(.).*\1/.test(str);
}

async function addResultToServer() {
    let data = new URLSearchParams()
    data.append("guessWord", definitelySuperNotTheGuessWordNoCheating)
    data.append("leftHint", leftHint)
    data.append("rightHint", rightHint)
    data.append("numOfGuesses", guessNumber)
    data.append("guesses", guesses)
    let result = await fetch("werge-round-complete.php", {
        method: "POST", 
        body: data
    })
    let t = await result.text()
    console.log(t)
}
//#endregion

//#region Player Hints/Helps
var timeout;
var lockout;
var locked
function setupTooltips() {
    let hoverables = document.querySelectorAll(".hoverable")
    let boxes = document.querySelectorAll(".letter-box")
    let tooltip = document.getElementById("tooltip")
    boxes.forEach(element => {
        element.onclick = () => showToolTip(element, true)
    })
    tooltip.onmouseout = function (e) {
        console.log("left tooltip")
        setTimeout(() => {
            let box = tooltip.getBoundingClientRect()
            if (x > box.left && x < box.right && y > box.top && y < box.bottom) {
            }
            else {
                tooltip.style.display = "none"
                clearTimeout(timeout)
            }
        }, 250);
    }
}
async function showToolTip(element, clicked = false) {
    console.log(clicked)
    if (clicked) clearTimeout(timeout);
    let word = ""
    //Get definition from row letters
    if (element.id.includes("box")) {
        let row = element.parentElement
        if (row)
            for (let h of row.children) {
                word += h.innerText
            }

    }
    else {
        word = element.innerText
    }
    if (word.length < 1)
        return;
    let definition = await getDefinition(word)
    if (definition["title"]) {
        return;
    }
    //Setting tooltip body
    tooltip.style.display = "block"
    tooltip.style.left = (x - 75) + "px"
    tooltip.style.top = (y + 25) + "px"

    let innerText = ""
    let totalDefs = 0
    allDefs:
    for (let d of definition) {
        innerText += d["word"] + ":\n\n"
        for (let m of d["meanings"]) {
            innerText += m["partOfSpeech"] + " -\n\n"
            for (let dd of m["definitions"]) {
                innerText += dd["definition"] + "\n\n"
                totalDefs++
                if (totalDefs > 4) {
                    break allDefs
                }

            }
            innerText += "\n"
        }
        innerText += "\n"
    }
    tooltip.innerText = innerText
    if (totalDefs > 4) {
        let a = document.createElement('a')
        a.href = "https://www.dictionary.com/browse/" + word
        a.innerText = "Read More Definitions Here"
        a.target = "_blank"
        tooltip.appendChild(a)
    }
    lockout = setTimeout(function () {
        locked = true
    }, 250)
}
function showRules() {
    let d = document.getElementById("rules")
    d.showModal()
    d.onclick = (e) => {
        let box = d.getBoundingClientRect()
        let x = e.clientX, y = e.clientY
        console.log(box, x, y)

        if (box.top < y && box.bottom > y && box.left < x && box.right > x) {
            return;
        }
        else {
            d.close()
        }
    }
}

function updateKeyboardHint(letter, status) {
    let key = document.getElementById(letter.toUpperCase())
    if (key.className.includes("correct")) return
    key.className = "keyboard-key"
    key.classList.add(status)
}
//#endregion


window.onload = async (e) => {
    //Set words from list
    let response = await (await fetch("./words.txt")).text()
    words = response.split("\n")
    resetGame()
    setupTooltips()
}

window.onkeydown = (e) => {
    if (e.key == "Backspace") {
        playerBackspace()
    }
    else if (e.key == "Enter") {
        playerGuess()
    }
    else if (e.key.length == 1 && /^[a-zA-Z]*$/.test(e.key)) {
        playerType(e.key)
    }
};

window.onmousemove = (e) => {
    x = e.clientX
    y = e.clientY
}

window.onclick = (e) => {
    let tooltip = document.getElementById("tooltip")
    let box = tooltip.getBoundingClientRect()
    if (x > box.left && x < box.right && y > box.top && y < box.bottom) {

    }
    else {
        tooltip.style.display = "none"
    }
}