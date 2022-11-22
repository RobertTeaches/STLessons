let signup_Div = null
let secret_Div = null
let signup_Parent = null
let intro = null
document.addEventListener('DOMContentLoaded', () => {
    console.log("Test")
    signup_Div = document.getElementById("signup");
    secret_Div = document.getElementById("secret_form");
    signup_Parent = document.getElementById("all_signup")
    intro = document.getElementById("intro")
}, false)

const secretTest_url = "http://"

function validateInput(input, pattern = /[A-Za-z]/)
{
    return input.match(pattern);
}
let secret;
async function secretForm(form)
{
    secret = form.phrase.value
    if (!validateInput(secret))
        return

    let data = new URLSearchParams();
    data.append("secret", secret);
    //Query Database for 'secret', getting license information back 
    let sResponse = await fetch('check-secret.php', {
        method: "POST",
        body: data 
    })
    sResponse = await sResponse.text()
    console.log(sResponse);
    if(sResponse == "yes")
    {
        signup_Div.hidden = false
        secret_Div.hidden = true
    }
    else if(sResponse == "no")
    {
        document.append("Wrong Secret!");
    }
    //Load variables for User Creation

    //Show Signup Form
}

async function signupForm(form)
{
    var userName = form.uName.value;
    userName = userName.toLowerCase();
    var password = form.psw.value;
    var email = "moodle." + userName + "@sigmateaches.com";
    var fName = form.fName.value;
    var lName = form.lName.value;
    console.log("User was " + userName);

    //Hopefully avoided SQL Injection lmao we'll see
    if(!validateInput(userName, /^[A-Za-z0-9]+$/)) return;
    if(!validateInput(password, /^[A-Za-z0-9]+$/)) return;
    if(!validateInput(fName)) return;
    if(!validateInput(lName)) return;

    let data = new URLSearchParams();
    data.append("secret", secret);
    data.append("username", userName);
    data.append("password", password);
    data.append("firstname", fName);
    data.append("email", email);
    data.append("lastname", lName);
    //Query Database for 'secret', getting license information back 
    let sResponse = await fetch('create-user-account.php', {
        method: "POST",
        body: data 
    });
    sResponse = await sResponse.text();
    console.log(sResponse);

    if(sResponse === "success")
    {
        window.open("https://sigmateaches.moodlecloud.com");
        let data = new URLSearchParams()
        data.append("username", userName)
        data.append("password", password)
        let result = await fetch("student_login.php", {
            method: "POST",
            body: data
        })
        if((await result.text()) === "100")
        {
            window.location = "./student";
        }
    }

    return;


    //Push Student Data to Database, and update License usage
}

function loginButton()
{
    console.log("Hello")
    let loginForm = document.getElementById("login_container");
    loginForm.hidden = false;
}

async function login(form)
{
    let user = form.username.value
    let pass = form.password.value
    let data = new URLSearchParams()
    data.append("username", user)
    data.append("password", pass)

    let result = await fetch("student_login.php", {
        method: "POST",
        body: data
    })
    let text = (await result.text())
    if(text === "100")
    {
        window.location = "./student";
    }
    else if(text === "300"){
        document.getElementById("login-password-error").hidden = false;
        setTimeout(()=>{
            document.getElementById("login-password-error").hidden = true;  
        }, 10000)
    }
    else{
        console.log(text)
    }
}

function signupButton()
{
    signup_Parent.hidden = false
    intro.hidden = true
}
