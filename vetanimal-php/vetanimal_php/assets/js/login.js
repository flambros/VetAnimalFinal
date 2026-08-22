document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("login-form");
  const errorBox = document.getElementById("login-error");

  function showError(msg) {
    errorBox.textContent = msg;
    errorBox.style.display = "block";
  }

  async function doLogin(email, password) {
    errorBox.style.display = "none";
    try {
      const res = await fetch("api/auth.php?action=login", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password }),
      });
      const data = await res.json();
      if (!res.ok) {
        showError(data.error || "Error al iniciar sesión");
        return false;
      }
      window.location.href = data.user.rol === "veterinario" && window.__loginRedirectVet
        ? "admin/dashboard.php"
        : "index.php";
      return true;
    } catch (e) {
      showError("Error de red");
      return false;
    }
  }

  form.addEventListener("submit", (e) => {
    e.preventDefault();
    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;
    doLogin(email, password);
  });

  document.getElementById("quick-cliente").addEventListener("click", () => {
    document.getElementById("email").value = "agustina.gomez@example.com";
    document.getElementById("password").value = "123456";
    doLogin("agustina.gomez@example.com", "123456");
  });

  document.getElementById("quick-vet").addEventListener("click", () => {
    document.getElementById("email").value = "santiago.mendez@vetanimal.com";
    document.getElementById("password").value = "123456";
    window.__loginRedirectVet = true;
    doLogin("santiago.mendez@vetanimal.com", "123456");
  });
});
