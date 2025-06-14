const track = document.querySelector('.feedback-track');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');

let currentIndex = 0;
const totalCards = document.querySelectorAll('.feedback-card').length;
const cardsPerView = 3;

function updateSlider() {
  const cardWidth = document.querySelector('.feedback-card').clientWidth + 20; // 20px margin adjustment
  const moveX = -(cardWidth * currentIndex);
  track.style.transform = `translateX(${moveX}px)`;
}

nextBtn.addEventListener('click', () => {
  if (currentIndex < totalCards - cardsPerView) {
    currentIndex++;
  } else {
    currentIndex = 0;
  }
  updateSlider();
});

prevBtn.addEventListener('click', () => {
  if (currentIndex > 0) {
    currentIndex--;
  } else {
    currentIndex = totalCards - cardsPerView;
  }
  updateSlider();
});
