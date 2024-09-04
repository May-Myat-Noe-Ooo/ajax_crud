//Show Password for registration
function showPwd(){
    var pwd = document.getElementById("pwd");
    var conf_pwd = document.getElementById("conf_pwd");
    if (pwd.type === "password")
        pwd.type = "text";
    else
    pwd.type = "password"

    if (conf_pwd.type === "password")
        conf_pwd.type = "text";
    else
        conf_pwd.type = "password"
}