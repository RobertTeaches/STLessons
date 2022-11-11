window.onload = () =>{
        //#region Handling "click background to close" functionality
        let dialog = document.getElementById("submission_view")
        dialog.addEventListener('mousedown', (e) => {
            let x = e.clientX, y = e.clientY
            let box = dialog.getBoundingClientRect()
            if(box.top < y && box.bottom > y && box.left < x && box.right > x)
            {
                return;
            }
            else
            {
                dialog.close();
            }
        });
        let feedback_dialog = document.getElementById("add_feedback")
        feedback_dialog.addEventListener('mousedown', (e) => {
            let x = e.clientX, y = e.clientY
            let box = feedback_dialog.getBoundingClientRect()
            if(box.top < y && box.bottom > y && box.left < x && box.right > x)
            {
                return;
            }
            else
            {
                feedback_dialog.close();
            }
        });
        //#endregion
        let feedback = document.getElementById("feedback_text")
        let feedbackButton = document.getElementById("feedback_button")
        feedback.oninput = (e) =>
        {
            if(e.target.value.length > 0)
            {
                feedbackButton.textContent = "Add Feedback"
                feedbackButton.style.backgroundColor = "var(--yellow-button-color)"
            }
            else
            {
                feedbackButton.textContent = "No"
                feedbackButton.style.backgroundColor = "var(--red-button-color)"
            }
        }
    }
    
function viewSubmission(submissionId)
{
    let submission = submissions[submissionId]
    let di = document.getElementById("submission_view")
    if(submission["submission"])
        di.querySelector(".submission").innerHTML = submission["submission"]
    else
        di.querySelector(".submission").innerHTML = "No Submission :("

    di.querySelector(".user").innerText = submission["username"]
    di.querySelector(".title").innerText = bounties[submission["bounty_id"]]["title"]

    di.querySelector(".needs_work").onclick = () =>{
        needsWork(submissionId);
    }
    di.querySelector(".approve").onclick = () =>{
        approve(submissionId);
    }
    di.showModal()
}

async function approve(submissionId){
    let data = new URLSearchParams();
    data.append("password", logged_password);
    data.append("submissionId", submissionId)
    let res = await fetch("approve-bounty.php", {
        method: 'POST',
        body: data
    })
    if(await res.text() === "100")
    {
        console.log("Success")
        location.reload();
    }

}

function needsWork(submissionId){
    let submission = submissions[submissionId]
    let di = document.getElementById("add_feedback")
    di.querySelector(".submission").innerHTML = submission["submission"]
    di.querySelector(".title").innerHTML = bounties[submission["bounty_id"]]["title"]
    document.getElementById("feedback_button").onclick = () => {
        finalizeFeedback(submissionId)
    }
    di.showModal()
}

async function finalizeFeedback(submissionId)
{
    let feedback = document.getElementById("feedback_text").value
    let data = new URLSearchParams();
    data.append("feedback", feedback);
    data.append("submissionId", submissionId);
    let response = await fetch("needs-work-bounty.php", {
        method: "POST",
        body: data
    })
    let t = await response.text()
    if(t == 100)
    {
        console.log("Feedback Logged")
    }
    else
    {
        console.log(t)
    }
}