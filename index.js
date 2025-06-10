function date2str(d) {
  return d.toLocaleString("ja", {
    "hour12": false,
    "year": "numeric",
    "month": "2-digit",
    "day": "2-digit",
    "hour": "2-digit",
    "minute": "2-digit",
    "second": "2-digit",
    "timeZoneName": "short",
  });
}
const updateStatus = document.getElementById("updateStatus");
async function checkUpdate() {
  try {
    const a = await (await fetch("chathash.php")).text();
    if (CHAT_HASH != a) {
      updateStatus.innerText = "更新があります";
      document.getElementById("dialogspin").style = "";
      document.getElementsByName("formsend")[0].src = "about:blank";
      document.getElementById("fsenddialog").showModal();
      location.href = "./";
    } else {
      const v = date2str(new Date());
      updateStatus.innerText = "✅" + v + ": 更新なし";
      setTimeout(checkUpdate, 2000);
    }
  } catch {
    updateStatus.innerText = "⛔エラー";
  }
}
setTimeout(checkUpdate, 2000);
globalThis.addEventListener("load", () => {
  document.querySelectorAll("form").forEach((el) => {
    el.target = "formsend";
    el.onsubmit = () => {
      document.getElementById("dialogspin").style = "";
      document.getElementsByName("formsend")[0].src = "about:blank";
      console.log("submit");
      document.getElementById("fsenddialog").showModal();
    };
    const hiddenv = document.createElement("input");
    hiddenv.type = "hidden";
    hiddenv.name = "style";
    hiddenv.value = "dialog";
    el.append(hiddenv);
  });
  globalThis.addEventListener("message", (response) => {
    if (response.data == "closedialog") {
      document.getElementById("fsenddialog").close();
    } else if (response.data == "reload") {
      location.href = "./";
    } else if (response.data == "dialogloaded") {
      document.getElementById("dialogspin").style = "display:none;";
    }
  });
});
