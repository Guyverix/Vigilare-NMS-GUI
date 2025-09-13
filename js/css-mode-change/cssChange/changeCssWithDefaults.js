<script>
document.addEventListener("DOMContentLoaded", function() {
  const themeLink = document.getElementById("theme-css");
  const toggleBtn = document.getElementById("themeToggle");

  const darkCss  = "/css/dark/vigilare-dashboard.css";
  const lightCss = "/css/light/vigilare-dashboard.css";

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

  // Toggle button logic
  toggleBtn.addEventListener("click", function() {
    const current = themeLink.getAttribute("href");
    const newTheme = (current === darkCss) ? lightCss : darkCss;
    themeLink.setAttribute("href", newTheme);
    localStorage.setItem("vigilareTheme", newTheme);
  });
});
</script>
