document.addEventListener('DOMContentLoaded', () => {
  // View switching logic
  const gridView = document.getElementById('grid-view');
  const listView = document.getElementById('list-view');
  const accordionView = document.getElementById('accordion-view');
  const gridViewIcon = document.getElementById('grid-view-icon');
  const listViewIcon = document.getElementById('list-view-icon');
  const accordionViewIcon = document.getElementById('accordion-view-icon');

  const savedView = localStorage.getItem('selectedView') || 'accordion';
  changeView(savedView);

  function changeView(view) {
      document.querySelectorAll('.view-mode').forEach(el => el.style.display = 'none');
      document.querySelectorAll('.view-icon').forEach(icon => icon.classList.remove('active'));

      if (view === 'grid' && gridView && gridViewIcon) {
          gridView.style.display = 'block';
          gridViewIcon.classList.add('active');
      } else if (view === 'list' && listView && listViewIcon) {
          listView.style.display = 'block';
          listViewIcon.classList.add('active');
      } else if (view === 'accordion' && accordionView && accordionViewIcon) {
          accordionView.style.display = 'flex';
          accordionViewIcon.classList.add('active');
      }

      localStorage.setItem('selectedView', view);
  }

  if (gridViewIcon) gridViewIcon.onclick = () => changeView('grid');
  if (listViewIcon) listViewIcon.onclick = () => changeView('list');
  if (accordionViewIcon) accordionViewIcon.onclick = () => changeView('accordion');

  // Subscription tab filtering logic
  const tabs = {
      all: document.getElementById('all-tab'),
      expires: document.getElementById('expires-tab'),
      nextBill: document.getElementById('next-bill-tab'),
      lifetime: document.getElementById('lifetime-tab')
  };

  function filterSubscriptions(filterType) {
      const items = document.querySelectorAll('.subscription-item');
      document.querySelectorAll('#subscription-tabs .tab').forEach(tab => tab.classList.remove('active'));

      if (tabs[filterType]) {
          tabs[filterType].classList.add('active');
      }

      items.forEach(item => {
          const statusElement = item.querySelector('.am-list-subscriptions-date');
          const status = statusElement ? statusElement.getAttribute('data-status') : '';

          if (filterType === 'all' || status === filterType) {
              item.style.display = 'block';
          } else {
              item.style.display = 'none';
          }
      });
  }

  Object.keys(tabs).forEach(filterType => {
      if (tabs[filterType]) {
          tabs[filterType].onclick = () => filterSubscriptions(filterType);
      }
  });

  // Default filter
  filterSubscriptions('all');

  // FAQ toggles
  const toggles = document.querySelectorAll(".faq-toggle");
  toggles.forEach(toggle => {
      toggle.addEventListener("click", () => {
          toggle.parentElement.classList.toggle("active");
      });
  });

  // Accordion logic for questions
  const questionElements = document.querySelectorAll(".question");
  questionElements.forEach(question => {
      question.addEventListener("click", () => {
          const active = document.querySelector(".question.active");
          if (active && active !== question) {
              active.classList.remove('active');
              active.nextElementSibling.style.maxHeight = 0;
          }
          question.classList.toggle('active');
          const answer = question.nextElementSibling;
          answer.style.maxHeight = question.classList.contains('active') ? answer.scrollHeight + 'px' : 0;
      });
  });
});
    // Toggle display options when settings button is clicked
    document.getElementById('settings-btn').addEventListener('click', function () {
        document.getElementById('display-options').classList.toggle('hidden');
    });

    // Apply the selected format
    document.getElementById('apply-format').addEventListener('click', function () {
        var selectedFormat = document.getElementById('format-select').value;
        var courseList = document.getElementById('member-subscriptions');

        // Apply format based on the selection
        if (selectedFormat === 'list') {
            courseList.classList.add('list-view');
            courseList.classList.remove('grid-view', 'carousel-view');
        } else if (selectedFormat === 'grid') {
            courseList.classList.add('grid-view');
            courseList.classList.remove('list-view', 'carousel-view');
        } else if (selectedFormat === 'carousel') {
            courseList.classList.add('carousel-view');
            courseList.classList.remove('list-view', 'grid-view');
        }
    });
