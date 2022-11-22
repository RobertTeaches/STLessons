let logged_password;
let modal_Orig = document.getElementById("modal-original")

window.onload = () => {
    document.getElementById("logout").hidden = true
    if (document.cookie.length < 1) {
        return;
    }
    let cookieSplit = document.cookie.split(";")
    cookieSplit.forEach((cookie) =>{
        let split = cookie.split("=")
        console.table(split)
        if (split[0] === "password") {
            if (split[1].length > 0)
                fastLogin(split[1])
        }
        
    })
}
//↓↓↓↓↓↓↓↓↓↓↓↓On initial page load, these should be the only JS loaded in. ↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
//↓↓↓↓↓↓↓↓↓↓↓↓The rest should be pulled from ./private/access_level_x.js ↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
function validateInput(input, pattern = /[A-Za-z]/) {
    return input.match(pattern);
}
async function fastLogin(cookiePass) {
    let dasboard_Container = document.getElementById("dashboard-container");
    let login_Container = document.getElementById("admin-container")

    let pass = cookiePass
    if (!validateInput(pass, /^[A-Za-z0-9]+$/)) return;
    let data = new URLSearchParams();
    data.append("password", pass);
    //Query Database for 'secret', getting license information back 
    let sResponse = await fetch('admin-login.php', {
        method: "POST",
        body: data
    });
    sResponse = await sResponse.text();


    if (sResponse == "100") {
        login_Container.hidden = true;
        dasboard_Container.hidden = false;
        logged_password = pass;
        sResponse = await fetch('admin-dashboard.php', {
            method: "POST",
            body: data
        });
        sResponse = await sResponse.json();
        dasboard_Container.innerHTML = sResponse["page"];
        let sc = document.createElement("script");
        sc.type = "text/javascript"
        sc.innerHTML = sResponse["js"];
        document.head.appendChild(sc)
        document.getElementById("logout").hidden = false
    }
    else {
        //console.log(await sResponse.text());
    }
}
async function logout() {
    await fetch("logout.php", {
        method: "GET"
    });
    window.location = "./"
}
async function adminLogin(form) {
    let dasboard_Container = document.getElementById("dashboard-container");
    let login_Container = document.getElementById("admin-container")
    let pass = form.admin_password.value
    if (!validateInput(pass, /^[A-Za-z0-9]+$/)) return;
    let data = new URLSearchParams();
    data.append("password", pass);
    //Query Database for 'secret', getting license information back 
    let sResponse = await fetch('admin-login.php', {
        method: "POST",
        body: data
    });
    sResponse = await sResponse.text();


    if (sResponse == "100") {
        login_Container.hidden = true;
        dasboard_Container.hidden = false;
        logged_password = pass;
        sResponse = await fetch('admin-dashboard.php', {
            method: "POST",
            body: data
        });
        sResponse = await sResponse.json();
        dasboard_Container.innerHTML = sResponse["page"];
        let sc = document.createElement("script");
        sc.type = "text/javascript"
        sc.innerHTML = sResponse["js"];
        document.head.appendChild(sc)
        document.getElementById("logout").hidden = false
        
    }
    else {
        console.log(sResponse);
    }
}

//returns modal_copy, containing innerHTML
function showModal(innerHTML) {
    //Duplicate Modal
    if (!modal_Orig) modal_Orig = document.getElementById("modal-original")
    let modal_copy = modal_Orig.cloneNode(true);
    document.body.appendChild(modal_copy)

    let modal_content = modal_copy.firstElementChild.lastElementChild
    let modal_close = modal_copy.firstElementChild.firstElementChild
    modal_content.innerHTML = innerHTML;
    modal_copy.style.display = "block"

    modal_content.style.width = "inerhit";
    modal_close.onclick = function () {
        modal_copy.remove();
    }
    return modal_copy;
}
//↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑On initial page load, these should be the only JS loaded in.↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
//↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑The rest should be pulled from ./private/access_level_x.js↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑

