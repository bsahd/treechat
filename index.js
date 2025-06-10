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
globalThis.addEventListener("load", () => {
  const updateStatus = document.getElementById("updateStatus");
  const fsenddialog = document.getElementById("fsenddialog");
  const dialogspin = document.getElementById("dialogspin");
  const formsend = document.getElementsByName("formsend")[0];
  async function checkUpdate() {
    try {
      const a = await (await fetch("chathash.php")).text();
      if (CHAT_HASH != a) {
        updateStatus.innerText = "更新があります";
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
  document.querySelectorAll("form").forEach((el) => {
    el.target = "formsend";
    el.addEventListener("submit", (event) => {
      event.preventDefault();
      setTimeout(() => {
        event.target.submit(); // 1秒後に手動で送信
      }, 500);
      dialogspin.classList.remove("done");
      formsend.src = "about:blank";
      fsenddialog.showModal();
    });
    const hiddenv = document.createElement("input");
    hiddenv.type = "hidden";
    hiddenv.name = "style";
    hiddenv.value = "dialog";
    el.append(hiddenv);
  });
  globalThis.addEventListener("message", (response) => {
    if (response.data == "closedialog") {
      fsenddialog.close();
    } else if (response.data == "reload") {
      location.href = "./";
    } else if (response.data == "dialogloaded") {
      dialogspin.classList.add("done");
    }
  });
});
