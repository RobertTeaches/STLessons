//ACCESS LEVEL 1 ONLY
async function orgData() {
    let data = new URLSearchParams();
    data.append("password", logged_password);
    let sResponse = await fetch('get-all-org-data.php', {
        method: "POST",
        body: data
    });
    let sJson = await sResponse.json();
    let org_container = document.getElementById("org_data_container");
    org_container.hidden = false;
    let org_html = "<table>"
    org_html += "<tr>"

    org_html += `<td> Name </td>`
    org_html += `<td> Limit </td>`
    org_html += `<td> Usage </td>`
    org_html += `<td> Phrase </td>`
    org_html += `<td> Date </td>`
    org_html += `<td> Email </td>`
    org_html += `<td> Admin Pass</td>`

    org_html += "</tr>"
    for (let i = 0; i < sJson.length; i++) {
        let obj = sJson[i];
        org_html += "<tr>"

        org_html += `<td> ${obj["name"]} </td>`
        org_html += `<td> ${obj["limit"]} </td>`
        org_html += `<td> ${obj["usage"]} </td>`
        org_html += `<td> ${obj["phrase"]} </td>`
        org_html += `<td> ${obj["date"]} </td>`
        org_html += `<td> ${obj["email"]} </td>`
        org_html += `<td> ${obj["password"]}</td>`
        org_html += `<td><button onclick=\"getStudents(\'${obj["phrase"]}\')\">Students</button></td>`
        org_html += `<td><button style="background-color: #c90011; margin-left: -20px;" onclick=\"beginOrgDelete(\'${obj["phrase"]}\')\">Remove</button></td>`
        org_html += `<td><button style="background-color: green; margin-left: -20px;" onclick=\"orgBreakdown(\'${obj["phrase"]}\')\">Breakdown</button></td>`
        org_html += "</tr>"
    }
    org_html += "</table>"
    org_container.innerHTML = org_html;
    console.log(sJson);
}

async function orgBreakdown(phrase){
    let data = new URLSearchParams();
    data.append("password", logged_password);
    data.append("phrase", phrase);

    let response = await fetch("get-org-breakdown.php", {
        method: "POST",
        body: data
    });
    let breakdown = await response.json();


    console.table(breakdown["students"]);
    console.table(breakdown["lessonData"]);
    let totalLines;
    let totalTerminal;
    let totalStudents = breakdown.length;
    let conceptsCovered;

    let breakdownHtml = `
        <div>
            <h1>Here is Your Breakdown</h1>
            <p>Your students have written a total of ${breakdown["totalLines"]} lines of code</p>
            <p>Your students have written a total of ${breakdown["totalTerminals"]} Linux Terminal Commands</p>

        </div>
    `;
    showModal(breakdownHtml)
}

async function getStudents(orgPhrase) {
    let data = new URLSearchParams();
    data.append("password", logged_password);
    data.append("phrase", orgPhrase);
    let sResponse = await fetch('get-org-students.php', {
        method: "POST",
        body: data
    });
    let sJson = await sResponse.json();
    let student_html = '<div class="horizontal"><p>Search:</p><input id="student_search" type="text" oninput="filterStudents()"></div>'
    student_html += "<div><table>"
    student_html += "<tr>"
    //Setting up Rows
    student_html += `<td> Name </td>`
    student_html += `<td> User Name </td>`
    student_html += `<td> Password </td>`

    student_html += "</tr><tbody id='student_container'>"
    for (let i = 0; i < sJson.length; i++) {
        let obj = sJson[i];
        student_html += "<tr>"

        student_html += `<td> ${obj["name"]} </td>`
        student_html += `<td> ${obj["user"]} </td>`
        student_html += `<td> ${obj["password"]} </td>`
        student_html += `<td><button style="padding: 10px 2px; background-color: grey; margin-left: -20px;" onclick=\"changeStudentPass(\'${obj["user"]}\')\">Change Pass</button></td>`
        student_html += `<td><button style="padding: 10px 2px; background-color: blue; margin-left: -20px;" onclick=\"changeStudentUser(\'${obj["user"]}\')\">Change User</button></td>`
        student_html += `<td><button style="padding: 10px 2px; background-color: #c90011; margin-left: -20px;" onclick=\"studentDelete(\'${obj["user"]}\')\">Remove</button></td>`
        student_html += `<td><button style="padding: 10px 2px; background-color: green; margin-left: -20px;" onclick=\"getStudentBreakdown(\'${obj["user"]}\')\">Get Breakdown</button></td>`
        student_html += "</tr>"
    }
    student_html += "</tbody></table></div>"
    showModal(student_html)
}

function toggleDropdown(containerID) {
    let container = document.getElementById(containerID);
    container.hidden = !container.hidden;
}

async function addLicense(form) {
    let oName = form.org_name.value;
    let lNum = form.license_number.value;
    let oPhrase = form.org_phrase.value;
    let email = form.org_email.value;

    let data = new URLSearchParams();
    data.append("password", logged_password);
    data.append("name", oName);
    data.append("number", lNum);
    data.append("email", email);
    if (oPhrase) data.append("phrase", oPhrase);

    //Query Database for 'secret', getting license information back 
    let sResponse = await fetch('create-organization.php', {
        method: "POST",
        body: data
    });
    sResponse = await sResponse.text();
    console.log(sResponse);
    if (sResponse === "100") {
        console.log("Success");
        form.reset();
    }
}

async function beginOrgDelete(orgPhrase) {
    console.error("Implement me!");
}

function studentDelete(userName) {
    let confirmHTML =
        `
        <form action=\"javascript:;\" onsubmit=\"confirmDeleteStudent(\'${userName}\', this)\">
            <p>Are you sure you want to delete ${userName}? Re-type the username below to confirm. </p>
            <input type="text" name="confirmation" required>
            <button style="background-color: red;"> Confirm </button>
        </form>
    `;
    showModal(confirmHTML);
}
async function confirmDeleteStudent(userName, form) {
    const confirmName = form.confirmation.value;
    if (!validateInput(confirmName, /^[A-Za-z0-9]+$/)) return;

    if (confirmName.toLowerCase() === userName) {
        //Confirmed deletion
        let data = new URLSearchParams();
        data.append("password", logged_password);
        data.append("user", userName);
        //Query Database for 'secret', getting license information back 
        let sResponse = await fetch('remove-user.php', {
            method: "POST",
            body: data
        });

        sResponse = await sResponse.text();
        console.log(sResponse);
    }
}

function changeStudentPass(userName) {
    let newPassHTML =
        `
    <form action=\"javascript:;\" onsubmit=\"newStudentPass(\'${userName}\', this)\">
        <p>What should the new password be?</p>
        <input type="text" name="password" required>
        <button style="background-color: grey;">Confirm</button>
    </form>

    `;
    showModal(newPassHTML);
}
async function newStudentPass(userName, form) {
    const newPass = form.password.value
    if (!validateInput(newPass, /^[A-Za-z0-9]+$/)) return;
    let data = new URLSearchParams();
    data.append("password", logged_password);
    data.append("user", userName);
    data.append("newPass", newPass);
    //Query Database for 'secret', getting license information back 
    let sResponse = await fetch('change-user-pass.php', {
        method: "POST",
        body: data
    });

    sResponse = await sResponse.text();
    console.log(sResponse);
}

function changeStudentUser(userName) {
    let newPassHTML =
        `
    <form action=\"javascript:;\" onsubmit=\"newStudentUserName(\'${userName}\', this)\">
        <p>What should the new username be?</p>
        <input type="text" name="username" required>
        <button style="background-color: blue;">Confirm</button>
    </form>

    `;
    showModal(newPassHTML);
}
async function newStudentUserName(userName, form) {
    const newUser = form.username.value
    if (!validateInput(newUser, /^[A-Za-z0-9]+$/)) return;
    let data = new URLSearchParams();
    data.append("password", logged_password);
    data.append("user", userName);
    data.append("newUser", newUser);
    //Query Database for 'secret', getting license information back 
    let sResponse = await fetch('change-user-username.php', {
        method: "POST",
        body: data
    });

    sResponse = await sResponse.text();
    console.log(sResponse);
}

function filterStudents() {
    const studentContainer = document.getElementById("student_container")
    const filter = document.getElementById("student_search").value

    studentContainer.childNodes.forEach((node) => {
        //algorithm to determine if we show student or not
        node.hidden = true;
        console.log(node.childNodes[0])
        if (!filter) {
            node.hidden = false;
        }
        if (node.childNodes[0].textContent.includes(filter)) {
            node.hidden = false;
        }
        if (node.childNodes[1].textContent.includes(filter)) {
            node.hidden = false;
        }
    })
}

async function getStudentBreakdown(user) {
    const start = Date.now();
    let data = new URLSearchParams();
    data.append("password", logged_password);
    data.append("user", user);
    //Query Database for 'secret', getting license information back 
    const sResponse = await fetch('get-student-breakdown.php', {
        method: "POST",
        body: data
    });

    data.delete("user")
    const gResponse = await fetch("get-lesson-metadata.php",{
            method: "GET",
    })

    let completedActivities = await sResponse.json();
    //console.log(completedActivities);
    //console.log(await gResponse.text())
    let g = await gResponse.json();
    //console.log(g);
    let studentLines = 0
    let studentTerminals = 0
    let difficulties = {}
    let metaData = g
    completedActivities.forEach((i)=> {
        studentLines += parseInt(metaData[i].linesOfCode)
        studentTerminals += parseInt(metaData[i].terminalCommands)
        
        let dif = metaData[i].difficulty
        if(!difficulties[dif]) difficulties[dif] = 1;
        else difficulties[dif] += 1;

    })



    let modalHTML = `
        <div>
            <p>${user} Has completed ${completedActivities.length} activities.</p>
            <p>In the activies they have completed, ${user} wrote <strong>at least</strong> ${studentLines} lines of code.</p>
            <p>${user} has used <strong>at least</strong> ${studentTerminals} terminal commands.</p>
            <p>Of the activities ${user} completed, 
    `;
    for (const [key, value] of Object.entries(difficulties)) {
        modalHTML += `${value} were ${key}, `
      }
    modalHTML += `</p>
    <h1>View ${user}'s <a rel="noopener noreferrer" target="_blank" href="https://apcentral.collegeboard.org/media/pdf/ap-computer-science-principles-course-at-a-glance.pdf">AP CS P Comparison</a> breakdown below:</h1>
    <canvas id="apChart"></canvas>
    `
    showModal(modalHTML)
    let chart = document.getElementById("apChart")
    let sData = {
        labels: ['Creative Development',
                 'Data',
                 'Algorithms and Programming',
                 'Computer Systems and Networks',
                 'Impact of Computing'],
        datasets: [{
            label: `${user} Breakdown`,
            data: [35,15,48,58,42],
            fill: true,
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgb(255, 99, 132)',
            pointBackgroundColor: 'rgb(255, 99, 132)',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: 'rgb(255, 99, 132)'
        }]
    }
    const myChart = new Chart(chart,
        {
            type: 'radar',
            data: sData,
            options: {
                elements:{
                    line: {
                        borderWidth: 3
                    }
                },
                scales: {
                    r: {
                        suggestedMin: 5,
                        suggestedMax: 100,
                    }
                }
            }
        })
        console.log(chart);
        console.log(`Student has written ${studentLines} lines of code, and used ${studentTerminals} terminal commands`)
        const end = Date.now();
        console.log("Loaded in " + (end - start) + " miliseconds");
}
    
async function syncLessonData()
{
    let data = new URLSearchParams();
    data.append("password", logged_password);

    const sResponse = await fetch('sync-lesson-metadata.php', {
        method: "POST",
        body: data
    });

    let t = await sResponse.text();
    console.log(t)
}
async function syncAllStudentData()
{
    let data = new URLSearchParams();
    data.append("password", logged_password);

    const sResponse = await fetch('sync-student-metadata.php', {
        method: "POST",
        body: data
    });

    let t = await sResponse.text();
    console.log(t)
}

async function gradeBounties()
{
    window.location = `./grade-bounties.php?`
}
function editBounties()
{
    window.location = "./edit-bounties.php";
}