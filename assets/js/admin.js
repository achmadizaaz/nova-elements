'use strict';

document.querySelectorAll('[data-nova-widgets]').forEach((form) => {
  const filters = form.querySelector('[data-nova-filters]');
  const search = form.querySelector('[data-nova-search]');
  const category = form.querySelector('[data-nova-category]');
  const empty = form.querySelector('[data-nova-empty]');
  const rows = [...form.querySelectorAll('[data-nova-widget]')];

  function filterWidgets() {
    const query = search.value.trim().toLocaleLowerCase();
    let matches = 0;

    for (const row of rows) {
      const matchesCategory = !category.value || row.dataset.novaWidget === category.value;
      const matchesQuery = row.textContent.toLocaleLowerCase().includes(query);
      row.hidden = !(matchesCategory && matchesQuery);
      matches += Number(!row.hidden);
    }

    empty.hidden = matches > 0;
  }

  search.addEventListener('input', filterWidgets);
  category.addEventListener('change', filterWidgets);
  filters.hidden = false;

  form.querySelectorAll('[data-nova-bulk]').forEach((button) => {
    button.hidden = false;
    button.addEventListener('click', () => {
      const enabled = button.dataset.novaBulk === 'enable';
      for (const row of rows) {
        row.querySelector('input[type="checkbox"]').checked = enabled;
      }
    });
  });
});
