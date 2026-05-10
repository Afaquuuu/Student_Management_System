document.addEventListener('DOMContentLoaded', function() {
    // Page hooks: each one exists only on the page that needs it.
    const registrationForm = document.getElementById('registrationForm');
    const studentSearch = document.getElementById('studentSearch');
    const studentsTable = document.getElementById('studentsTable');
    const noSearchResults = document.getElementById('noSearchResults');

    // Registration check: quick browser feedback before PHP validation runs.
    if (registrationForm) {
        registrationForm.addEventListener('submit', function(e) {
            const phone = document.getElementById('phone').value.trim();
            const digits = phone.replace(/\D/g, '');

            if (digits.length < 10) {
                alert('Phone number must contain at least 10 digits.');
                e.preventDefault();
                return;
            }

            if (!confirm('Register this student record?')) {
                e.preventDefault();
            }
        });
    }

    // Confirm actions: used by delete buttons so admins pause before removing data.
    document.querySelectorAll('[data-confirm]').forEach(function(link) {
        link.addEventListener('click', function(e) {
            if (!confirm(link.dataset.confirm)) {
                e.preventDefault();
            }
        });
    });

    // Table helper: instant client-side filtering for rows already loaded on the page.
    if (studentSearch && studentsTable) {
        const rows = Array.from(studentsTable.querySelectorAll('tbody tr'));

        studentSearch.addEventListener('input', function() {
            const term = studentSearch.value.trim().toLowerCase();
            let visibleRows = 0;

            rows.forEach(function(row) {
                const isMatch = row.textContent.toLowerCase().includes(term);
                row.classList.toggle('is-hidden', !isMatch);
                if (isMatch) {
                    visibleRows += 1;
                }
            });

            if (noSearchResults) {
                noSearchResults.classList.toggle('is-hidden', visibleRows !== 0 || term === '');
            }
        });
    }
});
