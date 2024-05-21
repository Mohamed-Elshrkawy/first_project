document.addEventListener("DOMContentLoaded", function() {
    const tabs = document.querySelectorAll('.tab');
    const forms = document.querySelectorAll('.form');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const targetTab = tab.getAttribute('data-tab');
            tabs.forEach(t => t.classList.remove('tab-active'));
            forms.forEach(f => f.classList.remove('active-form'));
            tab.classList.add('tab-active');
            document.getElementById(targetTab).classList.add('active-form');
        });
    });
});
