const apiUrl = '/wp-json/wp/v2/event';

function loadEvents() {
  fetch(apiUrl).then(function(response) {
    return response.json();
  }).then(function(data) {
    const items = data;
    for (let i = 0; i < items.length; i++) {
      const item = items[i];
      document.getElementById('events-list').innerHTML += '<div>' + item.title.rendered + '</div>';
    }
  });
}

function loadNews() {
  fetch('/wp-json/wp/v2/news').then(function(response) {
    return response.json();
  }).then(function(result) {
    document.getElementById('news-list').innerHTML = result.length + ' news loaded';
  });
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', function() {
    loadEvents();
    loadNews();
  });
} else {
  loadEvents();
  loadNews();
} 