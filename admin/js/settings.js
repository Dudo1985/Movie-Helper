const restoreTMDBApi = document.getElementById('moviehelper-default-apikey');

if(restoreTMDBApi !== null) {
    document.getElementById('moviehelper-default-apikey').addEventListener('click', function () {
        document.getElementById('moviehelper-tmdb-apikey').value = 'd4c4f18bb357c68018b409f7f00ab072';
    });
}
