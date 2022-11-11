

async function logOut()
{
    await fetch("student-logout.php", {
        method: "POST"
    });
    window.location = "../";
}

function goToBounties(){
    window.open("bounties.php", "_self")
}

function goToMinecraftCoding()
{
    window.open("https://sigmateaches.moodlecloud.com")
}
