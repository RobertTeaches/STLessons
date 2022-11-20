const bounties = [];
let inProgress = {};
let currentBounty = "";
let categories = []
let user = "guest";

let categoryFilter = "None"
window.onload = async (e) =>
{
    //Get user from cookies if it exists
    user = document.cookie
        .split('; ')
        .find((row) => row.startsWith('username='))
        ?.split('=')[1];



    //#region Grabbing bounties from on-site db, and displaying them dynamically
    let response = await fetch("get-bounties.php", {
        method: "GET"
    })
    let allBounties = await response.json();
    let orig = document.getElementById("bounty_orig");
    let parent = document.getElementById("bounties")
    var count = 0
    if (allBounties === "empty")
    {
        orig.querySelector(".title").innerText = "Failed to Get Bounties"
        return
    }
    allBounties.forEach((bounty) =>
    {
        let completed = false
        if (submissions != null)
        {
            for ([id, submission] of Object.entries(submissions))
            {
                if (submission["status"] == "complete" && bounty["longId"] === submission["bountyId"])
                {
                    completed = true
                }
            }
        }
        if (bounty["category"])
        {
            if (!categories.includes(bounty["category"])) categories.push(bounty["category"])
        }
        let bId = "bounty_" + count++;
        bounties[bId] = bounty;
        if (!completed)
        {
            //Create copy and give it new id
            let copy = orig.cloneNode(true);
            parent.appendChild(copy)
            copy.id = bId;

            //Setting Title
            let title = copy.querySelector(".title")
            title.innerText = bounty["title"]
            title.href = copy.id

            //Setting Reward
            let reward = copy.querySelector(".reward")
            reward.innerText = bounty["reward"]

            //Setting Description
            let description = copy.querySelector(".description")
            description.innerHTML = bounty["description"]


            console.log(bounty)
            if (bounty["category"])
                copy.classList.add(bounty["category"])
            copy.onclick = (e) =>
            {
                if (e.target instanceof HTMLParagraphElement || e.target instanceof HTMLLIElement || e.target instanceof HTMLOListElement)
                    return;
                toggleBounty(copy.id)
            }
        }

    })
    parent.removeChild(orig)
    //#endregion

    //#region Setting up Submission Side-bar
    let submissionList = document.getElementById("bounty-submissions")
    orig = submissionList.querySelector(".card");
    if (submissions)
    {
        Object.entries(submissions).forEach(async ([id, submission]) =>
        {
            let sCopy = orig.cloneNode(true)
            let bounty = await getBountyByLongId(submission["bountyId"])
            sCopy.querySelector(".title").innerText = bounty["title"]
            let status = submission["status"]
            sCopy.querySelector(".status").innerText = status
            status = status.replace(" ", "-")
            sCopy.classList.add(status)
            sCopy.classList.add(bounty["category"])
            submissionList.appendChild(sCopy)
            sCopy.onclick = (e) =>
            {
                console.log(e)
                viewSubmission(id)
            }
        })
    }
    else
    {
        orig.remove()
    }
    orig.remove()
    //#endregion


    //#region Searchbar stuff
    let searchbar = document.getElementById("searchbar")
    if (searchbar.value && searchbar.value.length > 0)
    {
        filterBounties(searchbar.value)
    }

    searchbar.oninput = (e) =>
    {
        filterBounties(e.target.value)
    }
    //#endregion

    //#region Category DropDown 
    let select = document.getElementById("category_filter")
    categories.forEach(category =>
    {
        let option = document.createElement("option")
        option.text = category
        option.id = category
        select.appendChild(option)
    })
    select.onchange = e =>
    {
        categoryFilter = e.target.value
        let checkCategory = document.getElementById("use_category")
        if (categoryFilter !== "None")
        {
            checkCategory.checked = false
            checkCategory.disabled = true
            // checkCategory.hidden = true
        }
        else
        {
            checkCategory.checked = true
            checkCategory.disabled = false
            // checkCategory.hidden = false    
        }
        filterBounties()
        // console.log(e.target.value)
    }
    //#endregion
    //#region Handling "click background to close" functionality
    let dialog = document.getElementById("submit_bounty")
    dialog.addEventListener('mousedown', (e) =>
    {
        let x = e.clientX, y = e.clientY
        let box = dialog.getBoundingClientRect()
        if (box.top < y && box.bottom > y && box.left < x && box.right > x)
        {
            return;
        }
        else
        {
            let submission = document.getElementById("submission").value
            if (submission && submission.length > 5)
            {
                if (currentBounty.length > 0)
                {
                    saveSubProgress(currentBounty, submission)
                }
            }
            dialog.close();
        }
    });

    let sdialog = document.getElementById("view_submission")
    sdialog.addEventListener('mousedown', (e) =>
    {
        let x = e.clientX, y = e.clientY
        let box = sdialog.getBoundingClientRect()
        if (box.top < y && box.bottom > y && box.left < x && box.right > x)
        {
            return;
        }
        else
        {
            sdialog.close();
        }
    });
    //#endregion

    //Loading inprogress submissions
    let storedSubmissions = localStorage.getItem("submissions")
    if (storedSubmissions)
    {
        inProgress = JSON.parse(storedSubmissions)
    }
    listView = localStorage.getItem('listView') ?? true
    listView = (listView === "true")
    let toggle = document.getElementById("view_style")
    toggle.checked = !listView
    updateView(false)

}

window.onclose = async (e) =>
{
    localStorage.setItem("submissions", JSON.stringify(inProgress))
}

async function getBountyByLongId(longId)
{
    for (const [shortid, bounty] of Object.entries(bounties))
    {
        if (bounty["longId"] === longId)
        {
            return bounty
        }
    }
}

async function getBountyShortIdByLong(longId)
{
    for (const [shortid, bounty] of Object.entries(bounties))
    {
        if (bounty["longId"] === longId)
        {
            return shortid
        }
    }
}

function saveSubProgress(bountyId, submission)
{
    if (!inProgress[user]) inProgress[user] = {};
    inProgress[user][bountyId] = submission;
    localStorage.setItem("submissions", JSON.stringify(inProgress))
}
function getSubProgress(bountyId)
{
    let progress = "";
    if (inProgress[user] && inProgress[user][bountyId])
    {
        progress = inProgress[user][bountyId]
    }
    return progress;
}
function filterBounties(filter)
{
    filter = filter ?? document.getElementById("searchbar").value
    let parent = document.getElementById("bounties")
    if ((!filter || filter.length === 0) && categoryFilter === "None")
    {
        Array.from(parent.children).forEach(child =>
        {
            child.hidden = false;
        })
        return;
    }

    let checkTitle = document.getElementById("use_title").checked
    let checkDescription = document.getElementById("use_descrip").checked
    let checkReward = document.getElementById("use_reward").checked
    let checkCategory = document.getElementById("use_category").checked
    console.log(checkTitle, checkCategory, checkDescription, checkReward, categoryFilter)
    filter = filter.toLowerCase()
    for (const [key, value] of Object.entries(bounties))
    {
        var b = document.getElementById(key);
        if (!b) continue
        b.hidden = true;
        if (categoryFilter !== "None" && value["category"].toLowerCase() !== categoryFilter)
        {
            continue;
        }
        if (checkTitle && (value["title"].toLowerCase().includes(filter) || filter.includes(value["title"].toLowerCase())))
        {
            b.hidden = false;
            console.log(value["title"] + " contains " + filter)
        }
        if (checkDescription && (value["description"].toLowerCase().includes(filter) || filter.includes(value["description"].toLowerCase())))
        {
            b.hidden = false;
        }
        if (checkCategory && (value["category"].toLowerCase().includes(filter) || filter.includes(value["category"].toLowerCase())))
        {
            b.hidden = false;
        }
        if (checkReward && (value["reward"].toLowerCase().includes(filter) || filter.includes(value["reward"].toLowerCase())))
        {
            b.hidden = false;
        }
    }
}
function toggleBounty(id, jump = true)
{
    let b = document.getElementById(id)
    if (!b) return
    var bounty = b.querySelector(".description")
    bounty.hidden = !bounty.hidden
    if (!listView && !bounty.hidden)
    {
        document.getElementById(id).style.width = "fit-content"
        document.getElementById(id).style.height = "fit-content"
    }
    else if (!listView && bounty.hidden)
    {
        document.getElementById(id).style.width = "25%"
        document.getElementById(id).style.height = "25vh"
    }
    else
    {
        document.getElementById(id).style.removeProperty('width')
        document.getElementById(id).style.removeProperty('height')
    }
    if (jump)
        jumpTo(id)
}

function startSubmission(button, id = null, previousSubmissionId = null)
{
    const shortId = id ?? button.parentNode.id
    console.log(shortId)
    currentBounty = shortId;
    const bounty = bounties[shortId];
    const modal = document.getElementById("submit_bounty");
    modal.showModal();
    let title = modal.querySelector(".title")
    let description = modal.querySelector(".description")
    title.innerHTML = bounty["title"]
    description.innerHTML = bounty["description"]
    let submission = modal.querySelector(".submission")
    let but = modal.querySelector("button")
    but.onclick = e => submitBounty(previousSubmissionId)
    submission.value = getSubProgress(currentBounty)
}

function retrySubmission(id, bId)
{
    getBountyShortIdByLong(bId).then((b)=>{
        
        startSubmission(null, b, id);
    })
}

async function submitBounty(prevSubmissionId = null)
{
    let submission = document.getElementById("submission").value
    let data = new URLSearchParams()
    data.append("submission", submission)
    data.append("username", user)
    if(prevSubmissionId) data.append("previousSubmission", prevSubmissionId)
    data.append("id", bounties[currentBounty].longId)
    let response = await fetch("submit-bounty.php", {
        method: "POST",
        body: data
    })
    let text = await response.text();
    console.log(text)
    if (text === "100")
    {
        //Success
        saveSubProgress(currentBounty, "")
        document.getElementById("submit_bounty").close()
    }
}

function viewSubmission(id)
{
    let submission = submissions[id]
    let dialog = document.getElementById("view_submission");
    let title = dialog.querySelector(".title")
    let bDescription = dialog.querySelector("#submitted_bounty_description")
    let sDescription = dialog.querySelector("#submission_description")
    getBountyByLongId(submission["bountyId"]).then((bounty) =>
    {
        title.innerText = bounty["title"]
        bDescription.innerHTML = bounty["description"]
        sDescription.innerHTML = submission["submission"]

        sDescription.classList.remove('neeeds-work')
        sDescription.classList.remove('complete')
        sDescription.classList.remove('pending')

        sDescription.classList.add(submission["status"].replace(" ", "-"))

        let feedbackContainer = dialog.querySelector("#feedback_container")
        if (submission["feedback"] !== null)
        {
            console.log(submission["feedback"])
            feedbackContainer.style.display = "flex"
            let feedback = feedbackContainer.querySelector(".description")
            feedback.innerHTML = submission["feedback"]
            let retryButton = feedbackContainer.querySelector("#retry_button")
            if (submission["status"] == "complete")
                retryButton.hidden = true
            else
            {
                retryButton.onclick = e => retrySubmission(id, submission["bountyId"])
            }
        }
        else
        {
            feedbackContainer.style.display = "none"
        }
        dialog.showModal()
    })
}

let listView
function updateView(changeView = true)
{
    let container = document.getElementById("bounties")
    let listText = document.getElementById("list-view-text")
    let gridText = document.getElementById("grid-view-text")

    if (changeView)
    {
        listView = !listView
    }

    console.log(listView)
    if (listView == false)
    {
        console.log("false")
        container.className = ""
        container.classList.add("card-list-grid")
        listText.style.fontWeight = "normal"
        gridText.style.fontWeight = "bold"
    }
    else
    {
        container.className = ""
        container.classList.add("card-list")
        gridText.style.fontWeight = "normal"
        listText.style.fontWeight = "bold"

    }
    for ([id, bounty] of Object.entries(bounties))
    {
        toggleBounty(id, false)
        toggleBounty(id, false)
    }
    localStorage.setItem("listView", listView)
}

function jumpTo(anchor_id)
{
    var url = location.href;               //Saving URL without hash.
    location.href = "#" + anchor_id;                 //Navigate to the target element.
    history.replaceState(null, null, url);   //method modifies the current history entry.
}