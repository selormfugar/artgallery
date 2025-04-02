/**
 * Main JavaScript file for Artist Dashboard
 */

import * as bootstrap from "bootstrap"

document.addEventListener("DOMContentLoaded", () => {
  // Toggle sidebar on mobile
  const sidebarToggle = document.querySelector(".sidebar-toggle")
  if (sidebarToggle) {
    sidebarToggle.addEventListener("click", () => {
      document.querySelector(".sidebar").classList.toggle("show")
    })
  }

  // Initialize tooltips
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  tooltipTriggerList.map((tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl))

  // Initialize popovers
  const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
  popoverTriggerList.map((popoverTriggerEl) => new bootstrap.Popover(popoverTriggerEl))

  // Auto-dismiss alerts
  const alertList = document.querySelectorAll(".alert-dismissible")
  alertList.forEach((alert) => {
    setTimeout(() => {
      const bsAlert = new bootstrap.Alert(alert)
      bsAlert.close()
    }, 5000)
  })

  // Form validation
  const forms = document.querySelectorAll(".needs-validation")
  Array.from(forms).forEach((form) => {
    form.addEventListener(
      "submit",
      (event) => {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }

        form.classList.add("was-validated")
      },
      false,
    )
  })

  // Confirm delete actions
  const confirmDeleteButtons = document.querySelectorAll("[data-confirm]")
  confirmDeleteButtons.forEach((button) => {
    button.addEventListener("click", function (e) {
      if (!confirm(this.getAttribute("data-confirm") || "Are you sure you want to delete this item?")) {
        e.preventDefault()
      }
    })
  })
})

