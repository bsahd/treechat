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
  async function checkUpdate() {
    const updateStatus = document.getElementById("updateStatus");
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
});
