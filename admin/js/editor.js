const  {__}        = wp.i18n; // Import __() from wp.i18n
const divSearch    = document.getElementById('moviehelper-search');
const divResult    = document.getElementById('moviehelper-query-results');
const searchForm   = document.getElementById('moviehelper-search-form');
let blockMovieList = false;
let blockLinksDiv  = false;


if(movieHelperCommonData.guten_page === true) {
    blockMovieList  = document.getElementById('moviehelper-block-movie-list');
    blockLinksDiv   = document.getElementById('moviehelper-block-links-container');
    let insertBlock = document.getElementById('moviehelper-insert-block-link');

    if(insertBlock !== null) {
        //adds an event listener to the hidden link
        insertBlock.addEventListener('click', event => {
            event.preventDefault();
            movieHelperInsertBlock(event);
        })
    }
}

if(searchForm !== null) {
    searchForm.addEventListener('input', event => {
        //begin the search only if the text to search has more than 2 chars
        if (searchForm.value.length > 2) {
            theMovieDb.search.getMulti({"query": searchForm.value}, movieHelperSuccessGetMulti, movieHelperErrorGetMulti)
        }
        if (searchForm.value.length < 3) {
            if (divResult.innerHTML !== '') {
                movieHelperEmptyDivResult();
            }
        }
    });
}

//function on success
function movieHelperSuccessGetMulti(data) {
    let parsedData   = JSON.parse(data);
    let results      = parsedData.results;

    let movieDiv = '<div class="moviehelper-card-container">';

    if(results.length > 0) {
        results.forEach(function (result, index) {
            if (typeof result !== 'undefined') {
                let name;
                let originalName;
                let date;

                if(result.media_type === 'tv' || result.media_type === 'movie') {
                    if (result.media_type === 'tv') {
                        name          = result.name;
                        originalName  = result.original_name;
                        date          = result.first_air_date;
                    } else {
                        name         = result.title;
                        originalName = result.original_title;
                        date         = result.release_date;
                    }
                    movieDiv += movieHelperLinksReturnCard(name, originalName, result.media_type, result.id,
                        result.poster_path, result.overview, date, result.vote_average, result.vote_count);
                }
            }
        });
    }

    movieDiv += '</div>';
    divResult.innerHTML = movieDiv;

    //get all elements that has class moviehelper-insert-link
    let insertLinkCollection = divResult.querySelectorAll('.moviehelper-insert-link');

    //attacch an event listener to all of them
    if (insertLinkCollection.length > 0) {
        insertLinkCollection.forEach(function (item, index) {
            item.addEventListener('click', event => {
                event.preventDefault();
                movieHelperInsertLink(event, item);
            })
        });
    }
}

/**
 *
 * @param name
 * @param originalName
 * @param mediaType
 * @param id
 * @param poster
 * @param overview
 * @param date
 * @param voteAverage
 * @param voteCount
 * @return {string}
 */
function movieHelperLinksReturnCard (name, originalName, mediaType, id, poster, overview, date, voteAverage, voteCount) {
    if(name !== 'undefined' && typeof name !== 'undefined') {

        let originalNameDiv = movieHelperReturnOriginalNameDiv (name, originalName)
        poster              = movieHelperReturnPoster(poster);
        overview            = movieHelperReturnOverview(overview);
        let link            = 'https://www.themoviedb.org/' + mediaType + '/' + id;

        return `<div class="moviehelper-cards">
                    <div class="moviehelper-card-item">
                        <a href="#" class="moviehelper-insert-link" 
                            data-moviehelper-link="${link}" 
                            data-moviehelper-name="${name}"
                            data-moviehelper-date="${date}"
                            data-moviehelper-average="${voteAverage}"
                            data-moviehelper-count="${voteCount}"
                        >
                            <div class="moviehelper-card-image">
                                <img src="${poster}" 
                                     title="${__('Insert link', 'movie-helper')}"
                                     alt="${name}" 
                                     width="230" 
                                     height="330"
                                >
                            </div>
                        </a>
                        <div class="moviehelper-card-info">
                            <p class="moviehelper-card-info">
                                <a href="${link}" target="_blank" >
                                    ${__('Open in TMDB', 'movie-helper')}
                                    <span class="dashicons dashicons-external"></span>
                                </a>
                            </p>
                            <p class="moviehelper-card-title">${name}</p>
                            ${originalNameDiv}
                            <p class="moviehelper-card-info">${overview}</p>
                        </div>
                    </div>
                </div>`;
    }

    return '';
}

/**
 * Return poster url if exists
 * @param poster
 * @return {string}
 */
function movieHelperReturnPoster (poster) {
    if (poster === null) {
        return movieHelperCommonData.img_dir + 'no-image.svg';
    } else {
        return 'https://image.tmdb.org/t/p/w342/' + poster;
    }
}

/**
 * Return a p tag with the original Name
 * @param name
 * @param originalName
 * @return {string}
 */
function movieHelperReturnOriginalNameDiv(name, originalName) {
    if(name !== originalName) {
        return `<p class="moviehelper-card-subtitle"> 
                    <span class="moviehelper-card-subtitle-first">${__('Original Name:', 'movie-helper')}</span>
                    <span class="moviehelper-card-subtitle-second">${originalName}</span> 
                </p>`;
    }
    return '';
}

/**
 * Return overview text
 *
 * @param overview
 */
function movieHelperReturnOverview(overview) {
    if(overview === 'undefined' && typeof overview === 'undefined' || overview === '') {
        return __('Overview not available', 'movie-helper');
    }
    return overview;
}

/**
 * Insert link into editor
 *
 * @param event
 * @param item  | a href dom element, taken from movie card
 */
function movieHelperInsertLink(event, item) {

    let linkAttribute = document.querySelector('input[name="moviehelper-after-link"]:checked').value;
    let href          = item.dataset.moviehelperLink;
    let name          = item.dataset.moviehelperName;
    let voteAverage   = item.dataset.moviehelperAverage;
    let voteCount     = item.dataset.moviehelperCount;
    let year          = movieHelperGetYear(item.dataset.moviehelperDate);
    let customText    = movieHelperReplaceCustomText(year, voteAverage, voteCount);
    let afterLink     = '';
    let targetBlank   = '';

    if(JSON.parse(movieHelperCommonData.target_blank) === true) {
        targetBlank = 'target="_blank"';
    }

    let spanStyle = 'display: block';
    if(linkAttribute === 'space') {
        spanStyle = 'display: inline'
        afterLink = '&nbsp;';
    }

    let link = `<span style="${spanStyle}">
                    <a href="${href}" title="${name}" ${targetBlank} >${name}</a>
                    &nbsp; ${customText} ${afterLink}</span>`;

    if(movieHelperCommonData.guten_page === true) {
        blockMovieList.innerHTML += link;
        blockLinksDiv.style.display = 'block';
    } else {
        movieHelperClassicEditor(link);
    }

}

/**
 *
 */
function movieHelperClassicEditor (link) {
    //#content is the editor in text mode.
    //if, in page load, visual mode is active, and then we switch from visual to text,
    // tinyMCE.activeEditor still return the object
    //So, I add do this always
    let textEditor = document.getElementById('content');

    if(textEditor !== 'undefined' && typeof textEditor !== 'undefined' && textEditor != null) {
        textEditor.value += link;
    }

    if (tinyMCE.activeEditor != null) {
        //this is for tinymce used in text mode
        tinyMCE.activeEditor.execCommand('mceInsertContent', 0, link);
    }
}

function movieHelperEmptyDivResult () {
    divResult.innerHTML = '';
}

function movieHelperErrorGetMulti(data) {
    console.log(data);

    let message;
    let code;

    if(movieHelperCheckJson(data) === true) {
        data    = JSON.parse(data);
        message = data.status_message;
        code    = data.status_code
    }

    if (code === 7 || code === 10) {
        document.getElementById('moviehelper-search').style.display = 'none';
    }

    divResult.innerHTML = `
        <div class="moviehelper-card-container">
            <div class="moviehelper-error">
                ${__('Error')}: ${message}
            </div>
        </div>
    `;
}

function movieHelperCheckJson(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

/**
* When button insert is clicked, create a core/paragraph block and insert blockMovieList.innerHTML
*
* @param event
*/
function movieHelperInsertBlock(event) {
    let newBlock = wp.blocks.createBlock("core/paragraph", { content: blockMovieList.innerHTML});
    let inserted = wp.data.dispatch("core/block-editor" ).insertBlocks( newBlock );
    blockMovieList.innerHTML = '';
}

/**
 * Return the Year from a date, or the string N/A if date is not set
 *
 * @param date {string}
 * @returns {string|number}
 */
function movieHelperGetYear(date) {

    //also check for undefined, as a STRING
    if(date && date !== 'undefined') {
        return new Date(date).getFullYear();
    }
    return 'N/A';
}

/**
 * Search and replace for supported vars and return a string with values
 *
 * @param date
 * @param voteAverage
 * @param voteCount
 * @returns {any}
 */
function movieHelperReplaceCustomText(date, voteAverage, voteCount) {

    let text    = JSON.parse(movieHelperCommonData.custom_text_link);
    let result  = text;

    if(text) {
        result = text.replaceAll('%year%', date);
        result = result.replaceAll('%vote_average%', voteAverage);
        result = result.replaceAll('%vote_count%', voteCount);
    }

    return result;
}
