<script>
document.addEventListener("DOMContentLoaded", function() {
  const themeLink = document.getElementById("theme-css");
  const toggleBtn = document.getElementById("themeToggle");

  const darkCss  = "/css/vigilare-dashboard.css";
  const lightCss = "/css/vigilare-dashboard-light.css";

  // Helper: update button label (currently disabled)
  function updateToggleLabel(themeHref) {
    // Example minimal labels:
    // toggleBtn.textContent = (themeHref === darkCss) ? "Switch to Light" : "Switch to Dark";

    // Example with icons:
    // toggleBtn.textContent = (themeHref === darkCss) ? "☀️ Light Mode" : "🌙 Dark Mode";
  }

  // Check saved theme
  let savedTheme = localStorage.getItem("vigilareTheme");

  if (!savedTheme) {
    // If no saved theme, use system preference
    const prefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
    savedTheme = prefersDark ? darkCss : lightCss;
    localStorage.setItem("vigilareTheme", savedTheme);
  }

  // Apply saved or system theme
  themeLink.href = savedTheme;
  // updateToggleLabel(savedTheme); // <- enable when ready

  // Toggle button logic
  toggleBtn.addEventListener("click", function() {
    const current = themeLink.getAttribute("href");
    const newTheme = (current === darkCss) ? lightCss : darkCss;
    themeLink.setAttribute("href", newTheme);
    localStorage.setItem("vigilareTheme", newTheme);
    // updateToggleLabel(newTheme); // <- enable when ready
  });
});
</script>
