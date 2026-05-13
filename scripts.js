const body = document.body;
const nav = document.getElementById('nav');
const themeToggle = document.getElementById('themeToggle');
const menuToggle = document.getElementById('menuToggle');
const sliderTrack = document.getElementById('sliderTrack');
const prevSlide = document.getElementById('prevSlide');
const nextSlide = document.getElementById('nextSlide');
const contactForm = document.getElementById('contactForm');
const contactStatus = document.getElementById('contactStatus');
const nameError = document.getElementById('nameError');
const emailError = document.getElementById('emailError');
const messageError = document.getElementById('messageError');

let slides = [];
let activeIndex = 0;

function setTheme(theme) {
  if (theme === 'light') {
    body.classList.add('light-theme');
    themeToggle.textContent = '☀️';
  } else {
    body.classList.remove('light-theme');
    themeToggle.textContent = '🌙';
  }
  window.localStorage.setItem('theme', theme);
}

function initTheme() {
  const saved = window.localStorage.getItem('theme');
  if (saved === 'light') {
    setTheme('light');
  } else {
    setTheme('dark');
  }
}

function setActiveSlide(index) {
  if (!slides.length) return;
  activeIndex = (index + slides.length) % slides.length;
  slides.forEach((slide, i) => {
    slide.style.display = i === activeIndex ? 'grid' : 'none';
  });
}

function createProjectSlide(project) {
  const card = document.createElement('article');
  card.className = 'slider-card';
  card.innerHTML = `
    ${project.image_url ? `<img src="${project.image_url}" alt="${project.title}">` : ''}
    <div class="project-num">${project.id.toString().padStart(3, '0')}</div>
    <div class="project-name">${project.title}</div>
    <div class="project-desc">${project.description}</div>
    <div class="project-tech">${project.tech.split(',').map(t => `<span class="tech-badge">${t.trim()}</span>`).join('')}</div>
    <div class="project-links">${project.live_url ? `<a href="${project.live_url}" class="project-link" target="_blank">Live Demo ↗</a>` : ''}${project.repo_url ? `<a href="${project.repo_url}" class="project-link" target="_blank">GitHub ↗</a>` : ''}</div>
  `;
  return card;
}

async function loadProjects() {
  try {
    const response = await fetch('projects_api.php');
    if (!response.ok) throw new Error('Unable to load projects');
    const data = await response.json();
    sliderTrack.innerHTML = '';
    slides = data.projects.map(createProjectSlide);
    if (!slides.length) {
      sliderTrack.innerHTML = '<div class="slider-card placeholder"><div class="project-name">No projects found</div><div class="project-desc">Add projects to the database and refresh.</div></div>';
      return;
    }
    slides.forEach(slide => sliderTrack.appendChild(slide));
    setActiveSlide(0);
  } catch (error) {
    sliderTrack.innerHTML = `<div class="slider-card placeholder"><div class="project-name">Fetch failed</div><div class="project-desc">${error.message}</div></div>`;
  }
}

function validateField(input, errorElement, validator) {
  const value = input.value.trim();
  const error = validator(value);
  errorElement.textContent = error;
  return !error;
}

function validateContact() {
  const nameValid = validateField(document.getElementById('contactName'), nameError, value => {
    if (!value) return 'Name is required.';
    if (value.length < 2) return 'Please enter at least 2 characters.';
    return '';
  });
  const emailValid = validateField(document.getElementById('contactEmail'), emailError, value => {
    if (!value) return 'Email is required.';
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) return 'Enter a valid email address.';
    return '';
  });
  const messageValid = validateField(document.getElementById('contactMessage'), messageError, value => {
    if (!value) return 'Message is required.';
    if (value.length < 12) return 'Please enter at least 12 characters.';
    return '';
  });
  return nameValid && emailValid && messageValid;
}

async function submitContact(event) {
  event.preventDefault();
  contactStatus.textContent = '';
  if (!validateContact()) {
    contactStatus.textContent = 'Please fix validation errors first.';
    return;
  }
  const payload = {
    name: document.getElementById('contactName').value.trim(),
    email: document.getElementById('contactEmail').value.trim(),
    message: document.getElementById('contactMessage').value.trim()
  };

  try {
    const response = await fetch('save_contact.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    const result = await response.json();
    if (!response.ok) throw new Error(result.message || 'Unable to submit form.');
    contactStatus.textContent = 'Message sent successfully — thank you!';
    contactForm.reset();
    nameError.textContent = '';
    emailError.textContent = '';
    messageError.textContent = '';
  } catch (error) {
    contactStatus.textContent = error.message;
  }
}

function initCursor() {
  const cursor = document.getElementById('cursor');
  const ring = document.getElementById('cursorRing');
  if (!cursor || !ring) return;
  let mx = 0, my = 0, rx = 0, ry = 0;
  document.addEventListener('mousemove', e => {
    mx = e.clientX;
    my = e.clientY;
    cursor.style.left = `${mx}px`;
    cursor.style.top = `${my}px`;
  });
  const animateRing = () => {
    rx += (mx - rx) * 0.12;
    ry += (my - ry) * 0.12;
    ring.style.left = `${rx}px`;
    ring.style.top = `${ry}px`;
    requestAnimationFrame(animateRing);
  };
  animateRing();
}

function initReveal() {
  const reveals = document.querySelectorAll('.reveal');
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
      }
    });
  }, { threshold: 0.12 });
  reveals.forEach(el => observer.observe(el));
}

function initNavScroll() {
  window.addEventListener('scroll', () => {
    nav.classList.toggle('scrolled', window.scrollY > 60);
  });
}

function initMenu() {
  menuToggle?.addEventListener('click', () => {
    body.classList.toggle('nav-open');
  });
  document.querySelectorAll('.nav-links a').forEach(link => {
    link.addEventListener('click', () => body.classList.remove('nav-open'));
  });
  document.addEventListener('click', event => {
    if (!nav.contains(event.target) && body.classList.contains('nav-open')) {
      body.classList.remove('nav-open');
    }
  });
}

function initEvents() {
  themeToggle?.addEventListener('click', () => {
    setTheme(body.classList.contains('light-theme') ? 'dark' : 'light');
  });
  prevSlide?.addEventListener('click', () => setActiveSlide(activeIndex - 1));
  nextSlide?.addEventListener('click', () => setActiveSlide(activeIndex + 1));
  contactForm?.addEventListener('submit', submitContact);
  ['input', 'change'].forEach(eventName => {
    document.querySelectorAll('#contactName, #contactEmail, #contactMessage').forEach(input => {
      input.addEventListener(eventName, validateContact);
    });
  });
}

window.addEventListener('DOMContentLoaded', () => {
  initTheme();
  initCursor();
  initReveal();
  initNavScroll();
  initMenu();
  initEvents();
  loadProjects();
});
