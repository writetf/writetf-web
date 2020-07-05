let cacheName = "writetf";
let cacheEntries = [];

fetch('/build/manifest.json').then(function (response) {

  if (response.status !== 200) {
    throw "Looks like the manifest isn't available !";
  }

  return response.json();

}).then(function (json) {
  for (let fileName in json) {
    cacheEntries.push(json[fileName]);
  }
});

self.addEventListener('install', function (event) {
  event.waitUntil(
    caches.open(cacheName).then(function (cache) {
      return cache.addAll(cacheEntries)
    })
  );
});

self.addEventListener('fetch', function (event) {
  if ( event.request.url.indexOf( '/ajax/' ) !== -1 ) {
    return false;
  }
  event.respondWith(
    caches.match(event.request).then(function (response) {
      return response || fetch(event.request);
    })
  );
});
