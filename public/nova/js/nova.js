
// Nova Checkers basic JS (placeholder for toasts, mobile nav, etc.)
document.addEventListener('DOMContentLoaded', () => {
  const playBtns = document.querySelectorAll('[data-action="play"]');
  playBtns.forEach(b => b.addEventListener('click', () => {
    b.textContent = 'Chargement…';
    setTimeout(()=>{ b.textContent='Lancer une partie'; }, 800);
  }));
});
