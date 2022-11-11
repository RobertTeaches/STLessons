window.onload = () => {
    loadSelect()
    loadCategories()
}

function loadSelect() {
    let select = document.getElementById("bounties")
    for (const [id, bounty] of Object.entries(bounties)) {
        //console.log(`${id}: ${bounty}`);
        let option = document.createElement("option")
        option.text = bounty["title"]
        option.id = id
        select.appendChild(option)
    }
    document.getElementById("select_bounty").onchange = (e) => {
        onSelected(getBountyIdByName(e.target.value));
    }

}

let categories = []
function loadCategories() {
    let data = document.getElementById("categories")
    for (const [id, b] of Object.entries(bounties)) {
        if (!categories.includes(b["category"])) {
            categories.push(b["category"])
            let option = document.createElement("option")
            option.text = b["category"]
            data.appendChild(option)
        }
    }

}
function getBountyIdByName(name) {
    for (const [id, bounty] of Object.entries(bounties)) {
        if (bounty["title"] === name) {
            return id
        }
    }
}
let currentBounty;
function onSelected(bountyId) {
    currentBounty = bountyId;
    let bounty = bounties[bountyId]
    let title = document.getElementById("title")
    let reward = document.getElementById("reward")
    let category = document.getElementById("category")
    console.log(bounty)
    title.value = bounty["title"]
    reward.value = bounty["reward"]
    //description.innerHTML = bounty["description"]
    CKEDITOR.instances.description.setData(bounty["description"]);
    category.value = bounty["category"]
}

function hasBountyChanged(bountyId, title, reward, description, category) {
    let bounty = bounties[bountyId]
    if (bounty["title"] !== title) return 1;
    if (bounty["reward"] !== reward) return 2;
    if (bounty["descripton"] !== description) return 3;
    if (bounty["category"] !== category) return 4;
    return 0;
}

function resetList(el) {
    console.log(el)
    el.value = ""
}

async function updateBounty() {
    let title = document.getElementById("title").value
    let reward = document.getElementById("reward").value
    let category = document.getElementById("category").value
    let description = CKEDITOR.instances.description.getData()
    let result = hasBountyChanged(currentBounty, title, reward, description, category)
    if (result > 0) {
        if (confirm(`Are you Sure You Want to Update "${title}?"`)) {
            let data = new URLSearchParams()
            data.append("title", title)
            data.append("description", description)
            data.append("reward", reward)
            data.append("category", category)
            data.append("bountyId", currentBounty)
            let result = await fetch("update-bounty.php", {
                method: "POST",
                body: data
            })
            let t = await result.text()
            if (t == 100) {
                confirm("Bounty Updated")
                document.getElementById("title").value = ""
                document.getElementById("reward").value = ""
                document.getElementById("category").value = ""
                CKEDITOR.instances.description.setData("")
            }
            else {
                console.log(t)
            }
        }
    }
}
