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
function setupDateFormatting() {
  document.querySelectorAll("[data-unixtime]:not([data-unixtimeprocessed])")
    .forEach((el) => {
      const timestamp = parseInt(el.dataset.unixtime, 10);
      el.textContent = date2str(new Date(timestamp * 1000));
      el.dataset["unixtimeprocessed"] = "true";
      console.log("date formatted for", el);
    });
}

window.addEventListener("DOMContentLoaded", setupDateFormatting);
