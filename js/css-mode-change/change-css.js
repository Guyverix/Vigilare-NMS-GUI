// --- simple cookie helpers ---
function setCookieSimple(name, value, path = "/", lifetimeSeconds = 31536000) { // 365d
  const d = new Date();
  d.setTime(d.getTime() + lifetimeSeconds * 1000);
  document.cookie = `${encodeURIComponent(name)}=${encodeURIComponent(value)};expires=${d.toUTCString()};path=${path};SameSite=Lax`;
}
function getCookie(name) {
  const key = encodeURIComponent(name) + "=";
  return document.cookie.split(";").map(c => c.trim())
    .find(c => c.startsWith(key))?.substring(key.length) ? decodeURIComponent(
      document.cookie.split(";").map(c => c.trim()).find(c => c.startsWith(key)).substring(key.length)
    ) : null;
}

document.addEventListener("DOMContentLoaded", function () {
  const switchEl = document.getElementById("lightSwitch");
  if (!switchEl) return;

  // Resolve current theme: cookie -> system preference -> default 'light'
  const cookieTheme = getCookie("theme");
  const systemPrefersDark = window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches;
  const currentTheme = cookieTheme || (systemPrefersDark ? "dark" : "light");

  // Set initial switch state (checked = dark)
  switchEl.checked = (currentTheme === "dark");

  // On toggle: set cookie and reload page so templates/head logic can load the right CSS
  switchEl.addEventListener("change", function () {
    const newTheme = switchEl.checked ? "dark" : "light";
    setCookieSimple("theme", newTheme, "/");
    // Force a full reload so the head can swap CSS files server-side
    window.location.reload();
  });
});
