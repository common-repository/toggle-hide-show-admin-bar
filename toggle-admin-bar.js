document.addEventListener("DOMContentLoaded", function () {
  const adminBar = document.getElementById("wpadminbar");
  if (adminBar) {
    const thsabToggleAdminBarCheckbox = document.querySelector(
      "#thsabToggleAdminBar > input#thsabToggleAdminBarCheckbox"
    );
    const thsabToggleAdminBar = document.getElementById("thsabToggleAdminBar");

    // Apply settings
    if (tabOptions.position === "bottom-right") {
      thsabToggleAdminBar.style.left = "auto";
      thsabToggleAdminBar.style.right = "0";
      thsabToggleAdminBar.style.borderRadius = "10px 0 0 10px";
    } else {
      thsabToggleAdminBar.style.left = "0";
      thsabToggleAdminBar.style.right = "auto";
      thsabToggleAdminBar.style.borderRadius = "0 10px 10px 0";
    }

    thsabToggleAdminBar.style.backgroundColor = tabOptions.background_color;
    thsabToggleAdminBar.style.color = tabOptions.text_color;

    if (tabOptions.behavior === "hide-partially") {
      thsabToggleAdminBar.style.transform = "translateX(-80%)";
      thsabToggleAdminBar.addEventListener("mouseenter", function () {
        thsabToggleAdminBar.style.transform = "translateX(0)";
      });
      thsabToggleAdminBar.addEventListener("mouseleave", function () {
        thsabToggleAdminBar.style.transform = "translateX(-80%)";
      });
    }

    thsabToggleAdminBarCheckbox.addEventListener("change", function () {
      if (this.checked) {
        adminBar.style.display = "none";
        document.body.style.marginTop = "0";
      } else {
        adminBar.style.display = "block";
        document.body.style.marginTop = adminBar.offsetHeight + "px";
      }
    });
  }
});
