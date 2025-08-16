document.addEventListener('DOMContentLoaded', () => {
  const header = document.querySelector('.site-header');
  if (!header) return;
  window.addEventListener('scroll', () => {
    header.classList.toggle('is-sticky', window.scrollY > 10);
  });

  document.querySelectorAll('.main-nav__item.has-submenu').forEach(item => {
    const link = item.querySelector('.main-nav__link');
    const set = v => link.setAttribute('aria-expanded', v);
    item.addEventListener('mouseenter', () => set('true'));
    item.addEventListener('mouseleave', () => set('false'));
    item.addEventListener('focusin', () => set('true'));
    item.addEventListener('focusout', () => set('false'));
  });
});
