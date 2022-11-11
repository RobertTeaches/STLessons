
window.onload = () => {
    loadSelect()
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
function getBountyIdByName(name) {
    for (const [id, bounty] of Object.entries(bounties)) {
        if (bounty["title"] === name) {
            return id
        }
    }
}
function resetList(el) {
    console.log(el)
    el.value = ""
}
async function getPrintout() {
    let bounty = document.getElementById("select_bounty").value
    let _bounties
    let zip = new JSZip()
    let width = 210
    let height = 297
    if (bounty === "") {
        _bounties = Object.values(bounties)
        for (let b of _bounties) {
            //Get printout of bouty from data
            let doc = new jsPDF("p", "mm", [width, height]);
            let title = b["title"]
            let p = document.createElement("p")
            p.innerHTML = b["description"]
            let description = p.innerText
            var lines =doc.splitTextToSize(description, (width-30));
            let x = (width / 2) - (title.length * 3)
            doc.setFontSize(35)
            doc.text(b["title"], x, 20, {
                //align: "center"
            })
            p.remove()
            doc.setFontSize(14)
            doc.text(lines, 22.5, 50)
            zip.file(b["category"] + "/" + b["title"] + ".pdf", doc.output('blob'))
            //download printout
        }
        zip.generateAsync({ type: 'blob' }).then(function (content) {
            var a = document.createElement("a");
            document.body.appendChild(a);
            a.style = "display:none";
            var url = window.URL.createObjectURL(content);
            a.href = url;
            a.download = "bounties.zip";
            a.click();
            window.URL.revokeObjectURL(url);
            a.remove();
        });
    } else {
        b = bounties[getBountyIdByName(bounty)]
        let doc = new jsPDF("p", "mm", [width, height]);
        let title = b["title"]
        let p = document.createElement("p")
        p.innerHTML = b["description"]
        let description = p.innerText
        var lines =doc.splitTextToSize(description, (width-30));
        let x = (width / 2) - (title.length * 3)
        doc.setFontSize(35)
        doc.text(b["title"], x, 20, {
            //align: "center"
        })
        doc.setFontSize(14)
        doc.text(lines, 22.5, 50)
        doc.save(b["title"] + ".pdf")
    }
}
