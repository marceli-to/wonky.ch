export default () => ({
  scrolled: false,
  isMobile: false,
  ticking: false,
  lastScrollY: 0,
  logo: null,
  maxWidth: 1024,
  
  init() {
    // Only run on mobile (< lg breakpoint) and homepage
    if (!this.$el.dataset.dynamicHeader) return;

    this.logo = this.$el.querySelector('[data-dynamic-logo]');
    this.checkMobile();
    this.lastScrollY = window.scrollY;
    this.handleScroll();

    window.addEventListener('scroll', () => {
      if (!this.ticking) {
        window.requestAnimationFrame(() => this.handleScroll());
        this.ticking = true;
      }
    }, { passive: true });

    window.addEventListener('resize', () => this.checkMobile());
  },

  checkMobile() {
    const wasMobile = this.isMobile;
    this.isMobile = window.innerWidth < this.maxWidth;

    // If we switched from mobile to desktop, remove inline styles
    if (wasMobile && !this.isMobile) {
      this.$el.style.height = '';
      if (this.logo) {
        this.logo.style.height = '';
      }
    }

    // If we switched to mobile, apply styles immediately
    if (!wasMobile && this.isMobile) {
      this.updateStyles();
    }
  },

  handleScroll() {
    const currentScrollY = window.scrollY;
    const threshold = 40;

    this.scrolled = currentScrollY > threshold;

    this.updateStyles();

    this.lastScrollY = currentScrollY;
    this.ticking = false;
  },

  updateStyles() {
    if (!this.isMobile) return;

    // Update header height
    if (this.scrolled) {
      this.$el.style.height = 'var(--header-height-sm)';
    } else {
      this.$el.style.height = 'var(--header-height-expanded)';
    }

    // Update logo height
    if (this.logo) {
      if (this.scrolled) {
        this.logo.style.height = 'var(--logo-height-sm)';
      } else {
        this.logo.style.height = 'var(--logo-height-expanded)';
      }
    }
  }
});
