document.addEventListener("DOMContentLoaded", function () {
    const filterButtons = document.querySelectorAll(".filter-btn");
    const artistCards = document.querySelectorAll(".artist-card");

    filterButtons.forEach(button => {
        button.addEventListener("click", function () {
            const selectedDay = this.dataset.day.trim().toLowerCase();

            filterButtons.forEach(btn => btn.classList.remove("active"));
            this.classList.add("active");

            artistCards.forEach(card => {
                const rawDays = card.dataset.days || "";
                const days = rawDays
                    .split(" ")
                    .map(day => day.trim().toLowerCase())
                    .filter(day => day !== "");

                if (selectedDay === "all" || days.includes(selectedDay)) {
                    card.classList.remove("hidden-card");
                } else {
                    card.classList.add("hidden-card");
                }
            });
        });
    });
});