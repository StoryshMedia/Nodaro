document.addEventListener("DOMContentLoaded", () => {
  const toggleButtonElement = document.getElementById('mobile-navigation-toggle');
  toggleButtonElement.addEventListener("click", () => {
    // Find the next sibling element which is the dropdown menu
    const dropdownMenu = document.getElementById('mobile-navigation')

    // Toggle the 'hidden' class to show or hide the dropdown menu
    if (dropdownMenu.classList.contains("-left-full")) {
      dropdownMenu.classList.remove("-left-full")
      dropdownMenu.classList.add("left-0")
    } else {
      dropdownMenu.classList.remove("left-0")
      dropdownMenu.classList.add("-left-full")
    }
  })

  const closeToggleButtonElement = document.getElementById('mobile-navigation-close-toggle');
  closeToggleButtonElement.addEventListener("click", () => {
    // Find the next sibling element which is the dropdown menu
    const dropdownMenu = document.getElementById('mobile-navigation')

    // Toggle the 'hidden' class to show or hide the dropdown menu
    if (dropdownMenu.classList.contains("-left-full")) {
      dropdownMenu.classList.remove("-left-full")
      dropdownMenu.classList.add("left-0")
    } else {
      dropdownMenu.classList.remove("left-0")
      dropdownMenu.classList.add("-left-full")
    }
  })

  // Select all dropdown toggle buttons
  const dropdownToggles = document.querySelectorAll(".mobile-navigation-sub-menu-toggle")

  dropdownToggles.forEach((toggle) => {
    toggle.addEventListener("click", () => {
      // Find the next sibling element which is the dropdown menu
      const dropdownSubMenu = toggle.nextElementSibling

      // Toggle the 'hidden' class to show or hide the dropdown menu
      if (dropdownSubMenu.classList.contains("h-0")) {
        // Hide any open dropdown menus before showing the new one
        document.querySelectorAll(".mobile-navigation-sub-menu").forEach((menu) => {
          menu.classList.add("h-0")
        })

        dropdownSubMenu.classList.remove("h-0")
      } else {
        dropdownSubMenu.classList.add("h-0")
      }
    })
  })

  // Optional: Clicking outside of an open dropdown menu closes it
  window.addEventListener("click", (event) => {
    if (!event.target.matches(".dropdown-toggle")) {
      document.querySelectorAll(".dropdown-menu").forEach((menu) => {
        if (!menu.contains(event.target)) {
          menu.classList.add("hidden")
        }
      })
    }
  })
})