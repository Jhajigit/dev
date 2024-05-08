document.getElementById('navbarToggler').addEventListener('click', function() {
    var isExpanded = this.getAttribute('aria-expanded') === 'true';
    this.setAttribute('aria-expanded', !isExpanded);
});
