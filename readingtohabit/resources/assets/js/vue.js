window.Vue = require('vue');
const axios = require('axios');

const app_url = 'https://test.readingtohabit-staging.net/';
objectFitImages();

if (document.getElementById('content') !== null) {
    var content = new Vue({
        delimiters: ['(%', '%)'],
        el: '#content',
        data: function () {
            return {
                word: '',
                search_results: [],
                };
        },
        methods: {
            search_books: function (word) {
                if (word.length > 2) {
                    axios.get("https://www.googleapis.com/books/v1/volumes?q=" + word + "&maxResults=10&orderBy=relevance")
                         .then(response => {
                            if ('items' in response.data) {
                                this.search_results = response.data.items;
                            }
                            else {
                                this.search_results = [];
                            }
                    });
                }
            },
            reset_word: function (word) {
                this.word = '';
            },
        }
    });
}

function to_add_article_form (form_element) {
    var form_name = form_element.name;
    form_element.submit();
}

function check_none_sign(word) {
    var reg = new RegExp(/[!"#$%&'()\*\+\-\.,\/:;<=>?@\[\\\]^_{|}~]/g);
    if (reg.test(word)) {
        return true;
    }
    
    return false;
}

if (document.getElementById('add_article_form') !== null) {
    var add_article_form = new Vue({
        delimiters: ['(%', '%)'],
        el: '#add_article_form',
        data: function () {
            return {
                mail_flag: document.getElementById('default_mail_flag').textContent,
                mail_timing_select: document.getElementById('default_mail_timing_select').textContent,
            };
        },
        computed: {
            mail_timing_not_by_day: function () {
                if (this.mail_timing_select === 'by_day') {
                    return false;
                }

                return true;
            },
            mail_timing_not_by_week: function () {
                if (this.mail_timing_select === 'by_week') {
                    return false;
                }

                return true;
            },
            mail_timing_not_by_month: function () {
                if (this.mail_timing_select === 'by_month') {
                    return false;
                }

                return true;
            },
            mail_on: function () {
                if (this.mail_flag === '1') {
                    return true;
                }

                return false;
            },
            mail_off: function () {
                if (this.mail_flag === '1') {
                    return false;
                }

                return true;
            },
        }
    });
}

if (document.getElementById('show_article') !== null) {
    var show_article = new Vue({
        delimiters: ['(%', '%)'],
        el: '#show_article',
        data: function () {
            return {
                favorite_flag: document.getElementById('favorite_flag').textContent,
                delete_modal: false,
                delete_modal_form: false,
                delete_modal_finish: false,
            };
        },
        computed: {
            favorite: function () {
                if (this.favorite_flag === '1') {
                    return true;
                }

                return false;
            },
            not_favorite: function () {
                if (this.favorite_flag === '0') {
                    return true;
                }

                return false;
            },
        },
        methods: {
            add_favorite: function () {
                var article_id = document.getElementById('article_id').textContent;

                axios.post(app_url + 'add_favorite/' + article_id).then(response => {
                    if (response.data.is_success === true) {
                        this.favorite_flag = '1';
                    }
                });
            },
            delete_favorite: function () {
                var article_id = document.getElementById('article_id').textContent;

                axios.post(app_url + 'delete_favorite/' + article_id).then(response => {
                    if (response.data.is_success === true) {
                        this.favorite_flag = '0';
                    }
                });
            },
            open_delete_modal: function () {
                this.delete_modal = true;
                this.delete_modal_form = true;
            },
            delete_article_do: function () {
                var article_id = document.getElementById('article_id').textContent;
                
                axios.post(app_url + 'delete_article_do/' + article_id).then(response => {
                    if (response.data.is_success === true) {
                        this.delete_modal_form = false;
                        this.delete_modal_finish = true;
                    }
                    else {
                        alert('削除に失敗しました。恐れ入りますが、一定時間を置いて再度お試しくださいませ。');
                        this.delete_modal = false;
                        this.delete_modal_form = false;
                    }
                });
            },
            close_delete_modal: function () {
                this.delete_modal = false;
            },
            redirect_to_index: function () {
                location.href = app_url + 'articles';
            },
        }
    });
}

if (document.getElementById('edit_article_form') !== null) {
    var edit_article_form = new Vue({
        delimiters: ['(%', '%)'],
        el: '#edit_article_form',
        data: function () {
            return {
                mail_flag: document.getElementById('edit_article_mail_flag').textContent,
                mail_timing_select: document.getElementById('edit_article_mail_timing_select').textContent,
            };
        },
        computed: {
            mail_timing_not_by_day: function () {
                if (this.mail_timing_select === 'by_day') {
                    return false;
                }

                return true;
            },
            mail_timing_not_by_week: function () {
                if (this.mail_timing_select === 'by_week') {
                    return false;
                }

                return true;
            },
            mail_timing_not_by_month: function () {
                if (this.mail_timing_select === 'by_month') {
                    return false;
                }

                return true;
            },
            mail_on: function () {
                if (this.mail_flag === '1') {
                    return true;
                }

                return false;
            },
            mail_off: function () {
                if (this.mail_flag === '1') {
                    return false;
                }

                return true;
            },
        }
    });
}

if (document.getElementById('articles') !== null) {
    var show_article = new Vue({
        delimiters: ['(%', '%)'],
        el: '#articles',
        data: function () {
            // 表示対象DOMの設定
            var smaller;
            var larger;

            if (window.innerWidth > 992) {
                smaller = false;
                larger  = true;
            }
            else {
                smaller = true;
                larger  = false;
            }

            // お気に入りフラグの初期値設定
            var list_article_id = document.getElementsByClassName('list_article_id');
            var list_article_favorite_flag = document.getElementsByClassName('list_article_favorite_flag');

            var not_favorite_arr = [];
            var favorite_arr = [];

            for (var i = 0; i < list_article_id.length; i++) {
                const article_id    = list_article_id[i].getAttribute('data');
                const favorite_flag = list_article_favorite_flag[i].getAttribute('data');

                if (favorite_flag === '0') {
                    not_favorite_arr[article_id] = true;
                    favorite_arr[article_id]     = false;
                }
                else if (favorite_flag === '1'){
                    not_favorite_arr[article_id] = false;
                    favorite_arr[article_id]     = true;
                }
                else {
                    not_favorite_arr[article_id] = true;
                    favorite_arr[article_id]     = false;
                }
            }

            return {
                smaller: smaller,
                larger: larger,
                not_favorite: not_favorite_arr,
                favorite: favorite_arr,
                open_menu_flag: false,
                close_menu_flag: true,
                menu_content_flag: true,
                menu_content_hidden_flag: false,
            };
        },
        methods: {
            add_favorite: function () {
                var article_id = event.target.getAttribute('data');

                axios.post(app_url + 'add_favorite/' + article_id).then(response => {
                    if (response.data.is_success === true) {
                        Vue.set(this.favorite, article_id, true);
                        Vue.set(this.not_favorite, article_id, false);
                    }
                });
            },
            delete_favorite: function () {
                var article_id = event.target.getAttribute('data');

                axios.post(app_url + 'delete_favorite/' + article_id).then(response => {
                    if (response.data.is_success === true) {
                        Vue.set(this.not_favorite, article_id, true);
                        Vue.set(this.favorite, article_id, false);
                    }
                });
            },
            open_menu: function () {
                this.open_menu_flag = true;
                this.close_menu_flag = false;
                this.menu_content_flag = true;
                this.menu_content_hidden_flag = false;
            },
            close_menu: function () {
                this.menu_content_flag = false;
                this.menu_content_hidden_flag = true;
                
                setTimeout(this.close_menu_area, 670);
            },
            close_menu_area: function () {
                this.open_menu_flag = false;
                this.close_menu_flag = true;
            },
        }
    });
}

if (document.getElementById('search_articles') !== null) {
    var search_articles = new Vue({
        delimiters: ['(%', '%)'],
        el: '#search_articles',
        data: function () {
            var smaller;
            var larger;

            if (window.innerWidth > 992) {
                smaller = false;
                larger  = true;
            }
            else {
                smaller = true;
                larger  = false;
            }
            
            return {
                smaller: smaller,
                larger: larger,
            };
        },
    });
}

if (document.getElementById('contact') !== null) {
    var contact = new Vue({
        delimiters: ['(%', '%)'],
        el: '#contact',
        data: function () {
            var smaller;
            var larger;

            if (window.innerWidth > 992) {
                smaller = false;
                larger  = true;
            }
            else {
                smaller = true;
                larger  = false;
            }
            
            return {
                smaller: smaller,
                larger: larger,
            };
        },
    });
}

if (document.getElementById('favorites') !== null) {
    var show_article = new Vue({
        delimiters: ['(%', '%)'],
        el: '#favorites',
        data: function () {
            var smaller;
            var larger;

            if (window.innerWidth > 992) {
                smaller = false;
                larger  = true;
            }
            else {
                smaller = true;
                larger  = false;
            }

            return {
                smaller: smaller,
                larger: larger,
            };
        },
        methods: {
            delete_favorite: function () {
                var article_id = event.target.getAttribute('data');

                axios.post(app_url + 'delete_favorite/' + article_id).then(response => {
                    if (response.data.is_success === true) {
                        location.href = 'favorites';
                    }
                });
            },
        }
    });
}

if (document.getElementById('edit_profile') !== null) {
    // objectFitImages();

    var edit_profile = new Vue({
        delimiters: ['(%', '%)'],
        el: '#edit_profile',
        data: function () {
            var smaller;
            var larger;

            if (window.innerWidth > 992) {
                smaller = false;
                larger  = true;
            }
            else {
                smaller = true;
                larger  = false;
            }

            if (document.getElementById('dialog')) {
                dialog = true;
            }
            else {
                dialog = false;
            }

            return {
                smaller: smaller,
                larger: larger,
                dialog: dialog,
                delete_modal: false,
                delete_modal_form: false,
                data: {
                    text: '',
                    image: '',
                    name: '',
                },
            };
        },
        methods: {
            close_dialog: function () {
                this.dialog = false;
            },
            setImage() {
                const file = (event.target.files || event.dataTransfer)[0];
                if (file.type.startsWith('image/')) {
                    this.data.image = window.URL.createObjectURL(file);
                    this.data.name  = file.name;
                    this.data.type  = file.type;
                }
            },
            open_delete_modal: function () {
                this.delete_modal = true;
                this.delete_modal_form = true;
            },
            delete_user_do: function () {
                axios.post(app_url + 'delete_user').then(response => {
                    if (response.data.is_success === true) {
                        this.delete_modal_form = false;
                        this.delete_modal = false;

                        location.href = app_url + 'delete_user_finish';
                    }
                    else {
                        alert('削除に失敗しました。恐れ入りますが、一定時間を置いて再度お試しくださいませ。');
                        this.delete_modal = false;
                        this.delete_modal_form = false;
                    }
                });
            },
            close_delete_modal: function () {
                this.delete_modal_form = false;
                this.delete_modal = false;
            },
        }
    });
}

if (document.getElementById('edit_password') !== null) {
    var edit_password = new Vue({
        delimiters: ['(%', '%)'],
        el: '#edit_password',
        data: function () {
            var smaller;
            var larger;

            if (window.innerWidth > 992) {
                smaller = false;
                larger  = true;
            }
            else {
                smaller = true;
                larger  = false;
            }
            
            if (document.getElementById('dialog') !== null) {
                dialog = true;
            }
            else {
                dialog = false;
            }

            return {
                smaller: smaller,
                larger: larger,
                dialog: dialog,
            };
        },
        methods: {
            close_dialog: function () {
                this.dialog = false;
            },
        },
    });
}

if (document.getElementById('edit_default_mail_timing') !== null) {
    var edit_default_mail_timing = new Vue({
        delimiters: ['(%', '%)'],
        el: '#edit_default_mail_timing',
        data: function () {
            var smaller;
            var larger;

            if (window.innerWidth > 992) {
                smaller = false;
                larger  = true;
            }
            else {
                smaller = true;
                larger  = false;
            }
            
            if (document.getElementById('dialog') !== null) {
                dialog = true;
            }
            else {
                dialog = false;
            }

            return {
                smaller: smaller,
                larger: larger,
                dialog: dialog,
            };
        },
        methods: {
            close_dialog: function () {
                this.dialog = false;
            },
        },
    });
}

if (document.getElementById('header_wrapper') !== null) {
    var privacy_policy = new Vue({
        delimiters: ['(%', '%)'],
        el: '#header_wrapper',
        data: function () {
            var smaller;
            var larger;

            if (window.innerWidth > 992) {
                smaller = false;
                larger  = true;
            }
            else {
                smaller = true;
                larger  = false;
            }
            
            return {
                smaller: smaller,
                larger: larger,
            };
        },
    });
}

if (document.getElementById('rules') !== null) {
    var rules = new Vue({
        delimiters: ['(%', '%)'],
        el: '#rules',
        data: function () {
            var smaller;
            var larger;

            if (window.innerWidth > 992) {
                smaller = false;
                larger  = true;
            }
            else {
                smaller = true;
                larger  = false;
            }

            return {
                smaller: smaller,
                larger: larger,
            };
        },
    });
}

if (document.getElementById('privacy_policy') !== null) {
    var privacy_policy = new Vue({
        delimiters: ['(%', '%)'],
        el: '#privacy_policy',
        data: function () {
            var smaller;
            var larger;

            if (window.innerWidth > 992) {
                smaller = false;
                larger  = true;
            }
            else {
                smaller = true;
                larger  = false;
            }
            
            return {
                smaller: smaller,
                larger: larger,
            };
        },
    });
}
