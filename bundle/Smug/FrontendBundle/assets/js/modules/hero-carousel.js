let tries = 0;
let resolved = false;
let maxTries = 10;
let interval = 300;

const tryMount = () => {
  const slidesContainer = document.getElementById("hero-carousel-slides-container");

  if (slidesContainer) {
    const slide = document.querySelector(".hero-carousel-slide");
    try {
      const prevButton = document.getElementById("hero-carousel-slide-arrow-prev");
      const nextButton = document.getElementById("hero-carousel-slide-arrow-next");

      nextButton.addEventListener("click", () => {
        const slideWidth = slide.clientWidth;
        slidesContainer.scrollLeft += slideWidth;
      });
      prevButton.addEventListener("click", () => {
        const slideWidth = slide.clientWidth;
        slidesContainer.scrollLeft -= slideWidth;
      });
    } catch (e) {
      console.log(e);
    }
  }

  if (!resolved && tries < maxTries) {
    tries++;
    setTimeout(tryMount, interval);
  }
};

const observer = new MutationObserver(() => {
  setTimeout(tryMount, 100);
});

const onReady = () => {
  const container = document.body || document.documentElement;
  observer.observe(container, {
    childList: true,
    subtree: true,
    attributes: true,
    characterData: true
  });

  tryMount();
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', onReady);
} else {
  onReady();
}