/**
 * Created by Andrew on 29/11/2016.
 */

function toggle_visibility_2(id) {
    toggle_visibility(id);
    toggle_visibility('search');
}

function toggle_visibility(id) {
    var e = document.getElementById(id);
    if(e.style.display == 'block')
        e.style.display = 'none';
    else
        e.style.display = 'block';
}