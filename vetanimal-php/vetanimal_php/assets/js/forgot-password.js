document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("forgot-form");
  const successBox = document.getElementById("forgot-success");

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const email = document.getElementById("email").value.trim();
    if (!email) return;

    try {
      const res = await fetch("api/auth.php?action=forgot-password", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email }),
      });
      const data = await res.json();
      successBox.textContent =
        data.message ||
        "Si el correo existe en nuestro sistema, te enviamos un enlace de recuperación.";
      successBox.style.display = "block";
      form.style.display = "none";
    } catch (e) {
      alert("Error de red");
    }
  });
});
