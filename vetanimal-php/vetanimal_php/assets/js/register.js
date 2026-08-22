document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("register-form");
  const errorBox = document.getElementById("register-error");

  function showError(msg) {
    errorBox.textContent = msg;
    errorBox.style.display = "block";
  }

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    errorBox.style.display = "none";

    const nombre = document.getElementById("nombre").value.trim();
    const email = document.getElementById("email").value.trim();
    const telefono = document.getElementById("telefono").value.trim();
    const password = document.getElementById("password").value;
    const password2 = document.getElementById("password2").value;

    if (!nombre || !email || !password) {
      showError("Completá todos los campos obligatorios.");
      return;
    }
    if (password !== password2) {
      showError("Las contraseñas no coinciden.");
      return;
    }
    if (password.length < 6) {
      showError("La contraseña debe tener al menos 6 caracteres.");
      return;
    }

    try {
      const res = await fetch("api/auth.php?action=register", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ nombre, email, password, telefono }),
      });
      const data = await res.json();
      if (!res.ok) {
        showError(data.error || "Error al crear cuenta");
        return;
      }
      window.location.href = "index.php";
    } catch (e) {
      showError("Error de red");
    }
  });
});
