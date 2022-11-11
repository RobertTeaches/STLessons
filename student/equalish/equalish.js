//['12', '+', '13', -, '11']
let leftEquation = ['12', '+', '13', '-', '11']
let rightEquation = ['12', '+', '13', '-', '11']
//"some_id"
let currentBlock = ""


function fillRows()
{
    let leftLen = leftEquation.length
    let rightLen = rightEquation.length
    let leftRow = document.getElementById("left-equation")
    leftRow.innerHTML = ""
    let rightRow = document.getElementById("right-equation")
    rightRow.innerHTML = ""
    let orig = document.getElementById("orig-block")
    for (let x = 0; x < leftLen; x++)
    {
        let copy = orig.cloneNode(true)
        copy.id = `l_${x}`
        copy.hidden = false
        leftRow.appendChild(copy)
    }
    for (let x = 0; x < rightLen; x++)
    {
        let copy = orig.cloneNode(true)
        copy.id = `r_${x}`
        copy.hidden = false
        rightRow.appendChild(copy)
    }
}

function fillBank()
{
    let bank = leftEquation.concat(rightEquation)
    let bankParent = document.getElementById("bank")
    let orig = document.getElementById("orig-block")
    shuffleArray(bank)
    console.log(bank)
    let c = 0
    bankParent.innerHTML = ""
    bank.forEach(element =>
    {
        let clone = orig.cloneNode(true)
        clone.innerText = element
        clone.hidden = false
        clone.id = `b_${c}`
        bankParent.appendChild(clone)
        c++
    });
}

function selectBlock(blockId)
{
    //We are 'holding' a block
    if (currentBlock !== "")
    {
        let old = document.getElementById(currentBlock)
        let newB = document.getElementById(blockId)

        let oldVal = old.innerText
        let newVal = newB.innerText

        old.innerText = newVal
        newB.innerText = oldVal

        old.classList.remove("selected")
        currentBlock = ""
        onBlockChange()
    }
    else
    {
        currentBlock = blockId
        document.getElementById(blockId).classList.add("selected")
    }
}

function onBlockChange()
{
    let left = getRowEq() 
    let right = getRowEq(false) 
    const nextButton = document.getElementById("nextButton")
    if(left.includes("") || right.includes(""))
    {
        nextButton.disabled = true
    }
    else
    {
        try{
            eval(left.join(""))
            eval(right.join(""))
            nextButton.disabled = false
        }
        catch{
            console.log("failed, not a calculatable expression")
        }
        
    }
}

function getRowEq(left = true)
{
    let eq = []
    //Only real acceptable use of :? assignment huh?
    let _currEq = (left)?leftEquation:rightEquation
    let _idPre = (left)?"l":"r"
    for (let i = 0; i < _currEq.length; i++) {
        const element = document.getElementById(`${_idPre}_${i}`)
        eq.push(element.innerText)
    }
    return eq
}

function checkEquations()
{
    let left = getRowEq()
    let right = getRowEq(false)

    try{
        let lRes = eval(left.join(""))
        let rRes = eval(right.join(""))

        lRes = lRes.toFixed(sigFigs)
        rRes = rRes.toFixed(sigFigs)
        console.log(lRes + "\n" + rRes)
        if(lRes === rRes)
        {
            console.log("You win!!")
            setTimeout(() => {
                newRound()
                onBlockChange()
            }, 1000);
            floatWinCard()
        }
        else{
            addWrongX()
        }
    }
    catch{
        console.log("Failed to Calculate with Check Button")
    }
}

let eqLength = 5
function newRound(){
    let got = getEquations(eqLength)
    let c = 10_000
    while (!got && c-- > 0) { 
        got = getEquations() 
        console.log("failed to get!")
    }
    fillRows()
    fillBank()
    clearWrong()
}

function getEquations(len = 3)
{
    let first = generateEquation(len)
    let firstEq = first.equation
    let firstOps = firstEq.find((e) => allOperators.includes(e))
    let resultAimedFor = first.result

    let c = 10_000
    let second = generateEquation(len)
    let found = false
    while (!found)
    {
        found = true
        second = generateEquation(len)
        c--
        //Secondary Conditions to Continue looking
        if (second.result != resultAimedFor)
        {
            found = false
            continue
        }
        if (second.equation.find((e) => firstOps.includes(e)) && c > 2_000)
        {
            found = false
        }
        if ([...firstEq].sort().join(' ') === [...second.equation].sort().join(' '))
        {
            found = false
        }
        if (c < 0)
            return false
    }
    console.log(10_000 - c)

    leftEquation = firstEq
    rightEquation = second.equation
    return true
}

//len must be odd & >3
//[n, o, n, o, n]
//[n, o, n]
//[n, o, n, o, n, o, n]
//[t, f, t, f, t, f, t]
let numberRange = [1, 20]
let allOperators = ['*', /* '/', */ '+', '-']

//0 - Round to nearest whole number
//1+ - Round to this digit
let sigFigs = 1

function generateEquation(len = 3)
{
    //Forcing len to be >3 and odd
    if (len < 3) len = 3
    if (len % 2 == 0) len++

    let eq = []
    let eqStr = ""
    let n = true
    let operators = [...allOperators]
    for (let i = 0; i < len; i++)
    {
        let toAdd
        if (n)
        {
            toAdd = parseInt(numberRange[0] + Math.random() * numberRange[1])
        }
        else
        {
            toAdd = operators[parseInt(Math.random() * operators.length)]
            if (len <= 5)
            {
                while (eq.includes(toAdd))
                {
                    toAdd = operators[parseInt(Math.random() * operators.length)]
                }
            }
        }
        eq.push(toAdd.toString())
        eqStr += toAdd
        n = !n
    }
    let res = eval(eqStr)
    return {
        result: res.toFixed(sigFigs),
        equation: eq
    }
}

//#region Utility Methods
/* Randomize array in-place using Durstenfeld shuffle algorithm */
function shuffleArray(array)
{
    for (var i = array.length - 1; i > 0; i--)
    {
        var j = Math.floor(Math.random() * (i + 1));
        var temp = array[i];
        array[i] = array[j];
        array[j] = temp;
    }
}
//#endregion

function floatWinCard()
{
    let winCard = document.getElementById("win_float")
    winCard.hidden = false
    let winDes = winCard.querySelector(".float_description")
    let res = eval(leftEquation.join(""))
    winDes.innerText = `Both equations evaluate out to ${res.toFixed(sigFigs)}!`
    setTimeout(() => {
        winCard.hidden = true
    }, 9000);
}

///Adds a 'wrong'/incorrect X box to the strip that contains them
function addWrongX()
{
    let x = document.getElementById("orig_wrong")
    let copy = x.cloneNode(true)
    copy.id = ""
    copy.classList.remove('hide')
    let wrong = document.getElementById('wrong')
    wrong.append(copy)
    wrong.classList.remove("hide")
    setTimeout(() => {
        wrong.classList.add("hide")
    }, 1250);
}
clearWrong = () => document.getElementById('wrong').innerHTML = ""


window.onload = () =>
{
    let rules = document.getElementById('rules')
    rules.onclick = e =>{
        let box = rules.getBoundingClientRect()
        let x = e.x, y = e.y
        if(!(x > box.left && x < box.right && y > box.top && y < box.bottom))
        {
            rules.close()
        }
    }

    newRound()
    onBlockChange()
}