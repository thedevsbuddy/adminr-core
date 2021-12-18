window.axios = require('axios');
window._ = require('lodash');


window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

var token = document.head.querySelector('meta[name="csrf-token"]');
axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import {createApp} from 'vue'

const app = createApp({});


/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

const files = require.context('./', true, /\.vue$/i);
files.keys().map(key => app.component(key.split('/').pop().split('.')[0], files(key).default));

app.mount('#app');

$(document).on('click', '.delete-item', function (e) {
    e.preventDefault();
    let url = $(this).attr('href');
    let deleteForm = $(this).data('form');
    let message = $(this).data('message');
    let title = $(this).data('title');
    let icon = $(this).data('icon');
    let confirm = $(this).data('confirm');
    Swal.fire({
        title: title ?? 'Are you sure?',
        text: message ?? "You won't be able to revert this!",
        icon: icon ?? 'warning',
        showCancelButton: true,
        confirmButtonColor: '#292a3d',
        cancelButtonColor: '#d33',
        confirmButtonText: confirm ?? 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            if (url == '#' || url == 'javascript:void(0)' || url == 'javascript') {
                $('#' + deleteForm).submit();
            } else {
                window.location.href = url;
            }
        }
    })

});

$(document).on('change', '.file-input', function (e) {
    readFile(this, $(this).data('target'));
});

function readFile(input, target) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $(target).css('background-position', 'center')
                .css('background-repeat', 'no-repeat')
                .css('background-size', 'cover')
                .css('background-image', 'url("' + e.target.result + '")');

            $('#' + input.getAttribute('id')).parent().find('.file-input-icon').fadeOut(200);
        };
        reader.readAsDataURL(input.files[0]);
    }
}


function toggleSidebar(action) {
    if (action === 'minimize') {
        $('#sidebar').addClass('c-sidebar-minimized');
    } else {
        $('#sidebar').removeClass('c-sidebar-minimized');
    }
}

$(window).resize(function () {
    checkWindow();
});
checkWindow();

function checkWindow() {
    if (window.innerWidth < 1200) {
        toggleSidebar('minimize');
    } else {
        toggleSidebar('maximize');
    }
}


$(window).on('resize', function () {
    $('.select2').each(function () {
        $(this).select2();
    });
});