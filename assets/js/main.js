/* WebExpertees — site interactions */
(function () {
  "use strict";

  // Scroll animations
  if (window.AOS) AOS.init({ duration: 800, once: true, offset: 60 });

  // Navbar background + back-to-top button on scroll
  const nav = document.getElementById("mainNav");
  const toTop = document.querySelector(".back-to-top");
  const onScroll = () => {
    const y = window.scrollY;
    nav.classList.toggle("scrolled", y > 40);
    toTop.classList.toggle("show", y > 500);
  };
  window.addEventListener("scroll", onScroll, { passive: true });
  onScroll();

  // Close mobile menu after clicking a link
  const menu = document.getElementById("navMenu");
  menu.querySelectorAll(".nav-link:not(.dropdown-toggle), .dropdown-item, .btn").forEach((link) => {
    link.addEventListener("click", () => {
      if (menu.classList.contains("show")) bootstrap.Collapse.getOrCreateInstance(menu).hide();
    });
  });

  // Typing effect in hero headline
  const typed = document.querySelector(".typed");
  if (typed && !window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
    const words = JSON.parse(typed.dataset.words);
    let w = 0, c = words[0].length, deleting = true;
    const tick = () => {
      const word = words[w];
      typed.textContent = word.slice(0, c);
      if (deleting) {
        c--;
        if (c < 0) { deleting = false; w = (w + 1) % words.length; c = 0; }
      } else {
        c++;
        if (c > words[w].length) { deleting = true; setTimeout(tick, 1800); return; }
      }
      setTimeout(tick, deleting ? 45 : 90);
    };
    setTimeout(tick, 2500);
  }

  // Animated counters
  const counters = document.querySelectorAll(".counter");
  const countUp = (el) => {
    const target = +el.dataset.target;
    const start = performance.now();
    const dur = 1800;
    const step = (now) => {
      const p = Math.min((now - start) / dur, 1);
      el.textContent = Math.floor(target * (1 - Math.pow(1 - p, 3)));
      if (p < 1) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
  };
  const io = new IntersectionObserver((entries) => {
    entries.forEach((e) => {
      if (e.isIntersecting) { countUp(e.target); io.unobserve(e.target); }
    });
  }, { threshold: 0.6 });
  counters.forEach((c) => io.observe(c));

  // Portfolio filter
  const filterBtns = document.querySelectorAll(".portfolio-filters button");
  const items = document.querySelectorAll(".portfolio-item");
  filterBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      filterBtns.forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");
      const f = btn.dataset.filter;
      items.forEach((item) => {
        item.classList.toggle("hide", f !== "all" && item.dataset.category !== f);
      });
      if (window.AOS) AOS.refresh();
    });
  });

  // AJAX form submit (contact + newsletter) to the PHP endpoints
  const submitForm = async (form, statusEl, onSuccess) => {
    const btn = form.querySelector('[type="submit"]');
    btn.disabled = true;
    statusEl.className = statusEl.className.replace(/\b(is-success|is-error)\b/g, "").trim();
    try {
      const res = await fetch(form.action, { method: "POST", body: new FormData(form) });
      const data = await res.json();
      statusEl.textContent = data.message;
      statusEl.classList.add(data.ok ? "is-success" : "is-error");
      if (data.ok) onSuccess();
    } catch (err) {
      statusEl.textContent = "Something went wrong. Please try again or contact us on WhatsApp.";
      statusEl.classList.add("is-error");
    } finally {
      btn.disabled = false;
    }
  };

  const form = document.getElementById("contactForm");
  if (form) {
    form.addEventListener("submit", (e) => {
      e.preventDefault();
      if (!form.checkValidity()) {
        form.classList.add("was-validated");
        return;
      }
      submitForm(form, document.getElementById("formStatus"), () => {
        form.reset();
        form.classList.remove("was-validated");
      });
    });
  }

  const newsletter = document.getElementById("newsletterForm");
  if (newsletter) {
    newsletter.addEventListener("submit", (e) => {
      e.preventDefault();
      submitForm(newsletter, document.getElementById("newsletterStatus"), () => newsletter.reset());
    });
  }
})();
