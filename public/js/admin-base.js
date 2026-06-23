window.Admin = {
  lang: 'ar',

  toggleMenu() {
    document.getElementById('mobileMenu')?.classList.toggle('hidden');
  },

  toggleLang() {
    this.lang = this.lang === 'ar' ? 'en' : 'ar';
    document.documentElement.dir = this.lang === 'ar' ? 'rtl' : 'ltr';
  }
};
