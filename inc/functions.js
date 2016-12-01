/**
 * Created by Andrew on 29/11/2016.
 */
/*Delete Functions*/
function deleteGroup(id) {
    if(confirm("Are you sure you want to delete this record?")){
        window.location.href = '?page=delete&type=group&id=' + id;
    }
}

function deletePerm(id) {
    if(confirm("Are you sure you want to delete this record?")){
        window.location.href = '?page=delete&type=perm&id=' + id;
    }
}

function deleteUser(id) {
    if(confirm("Are you sure you want to delete this record?")){
        window.location.href = '?page=delete&type=user&id=' + id;
    }
}

function deletePage(id) {
    if(confirm("Are you sure you want to delete this record?")){
        window.location.href = '?page=delete&type=page&id=' + id;
    }
}
