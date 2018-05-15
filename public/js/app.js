window.onload = function() {
    function doSubmit(event) {
        var url = document.getElementById("url-input").value.trim()
        if (url === "") {
            return false
        } else {
            urlreg = /^(http|ftp)s?:\/\//
            if (!urlreg.test(url)) {
                document.getElementsByClassName("url-info")[0].style.display = "block"
                document.getElementById("url-input").classList.add("is-danger")
                document.getElementById("url-input").focus()
            } else {
                event.target.classList.add("is-loading")
                axios
                    .post("/", {
                        fullurl: url
                    })
                    .then(function(response) {
                        var res = response.data
                        var btn = document.getElementById("submit-btn")
                        btn.classList.remove("is-loading")
                        if (res.status === "SUCCESS") {
                            document.getElementById("url-input").value = res.url_s
                            btn.classList.remove("is-primary")
                            btn.classList.add("is-info")
                            btn.innerText = "复制"
                            btn.removeEventListener("click", doSubmit)
                            //复制Clipboard.js
                            new ClipboardJS("#submit-btn", {
                                text: function() {
                                    return res.url_s
                                }
                            })
                        } else {
                            var errInfo = document.getElementById('err-msg')
                            errInfo.innerHTML = res.msg
                            errInfo.style.display = "block"
                            document.getElementById("url-input").classList.add("is-danger")
                            document.getElementById("url-input").focus()
                            console.error(res.msg)
                        }
                    })
            }
        }
    }
    //按钮绑定提交事件
    document.getElementById("submit-btn").addEventListener("click", doSubmit)
    //输入框绑定输入事件，输入时消除错误提示
    document.getElementById("url-input").addEventListener("input", function(e) {
        e.target.classList.remove("is-danger")
        document.getElementsByClassName("url-info")[0].style.display = "none"
    })
}
