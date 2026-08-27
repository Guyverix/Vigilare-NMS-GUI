<!-- This is loaded after the bootstrap is loaded -->
<script>
document.addEventListener("DOMContentLoaded", function() {
  const toggleBtn = document.getElementById("themeToggle");
  const themeLink = document.getElementById("theme-css");

  // Try to load previously saved theme
  const savedTheme = localStorage.getItem("vigilareTheme");
  if (savedTheme) {
    themeLink.href = savedTheme;
  }

  toggleBtn.addEventListener("click", function() {
    const darkCss  = "/css/dark/vigilare-dashboard.css";
    const lightCss = "/css/light/vigilare-dashboard.css";
    const current  = themeLink.getAttribute("href");

    const newTheme = (current === darkCss) ? lightCss : darkCss;
    themeLink.setAttribute("href", newTheme);
    localStorage.setItem("vigilareTheme", newTheme);
  });
});
</script>
