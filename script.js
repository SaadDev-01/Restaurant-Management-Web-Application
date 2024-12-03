// Sidebar functionality removed
let sidebar = document.querySelector(".sidebar");

const menuItems = document.querySelectorAll(".menu-item");
menuItems.forEach(item => {
    item.addEventListener("click", () => {
        // Hide all content sections
        const sections = document.querySelectorAll(".content-section");
        sections.forEach(section => {
            section.style.display = "none";
        });

        // Show the relevant section based on data-target attribute
        const target = item.getAttribute("data-target");
        const targetSection = document.getElementById(target);
        if (targetSection) {
            targetSection.style.display = "block";
        }
    });
});

// Show the default "home" section on page load
document.getElementById("home").style.display = "block";

// Update active menu styling
menuItems.forEach(item => {
    item.addEventListener("click", () => {
        menuItems.forEach(i => i.classList.remove("active"));
        item.classList.add("active");
    });
});

// Add a class to highlight the selected menu item
const styleTag = document.createElement("style");
styleTag.innerHTML = `
    .menu-item.active {
        background-color: rgba(255, 111, 97, 0.2);
        
    }
`;
document.head.appendChild(styleTag);
